<?php

namespace App\Http\Controllers\PainelAdm;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClienteController
{
    // Lista todos os clientes
    public function index()
    {
        return view('painel_adm.cliente.index');
    }

    // Armazena um novo cliente
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nome' => 'nullable|string|max:45',
                'sobrenome' => 'nullable|string|max:60',
                'email' => 'nullable|email|max:100',
                'telefone' => 'nullable|string|max:11',
                'cep' => 'nullable|string|max:8',
                'endereco' => 'nullable|string|max:100',
            ]);
            // Verifica se o cliente já existe pelo email
            if (Cliente::where('email', $validated['email'])->exists()) {
                return redirect()->back()->with('erro', 'Cliente com este email já cadastrado.');
            }

            Cliente::create($validated);

            return redirect()->back()->with('mensagem', 'Cliente cadastrado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao cadastrar cliente: ' . $e->getMessage(), [
                'exception' => $e,
                'input' => $request->all(),
            ]);
            return redirect()->back()->with('erro', 'Erro ao cadastrar cliente.');
        }
    }

    function create()
    {
        return view('painel_adm.cliente.create');
    }

    function edit($id)
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return redirect()->back()->with('erro', 'Cliente não encontrado.');
        }

        return view('painel_adm.cliente.edit', compact('cliente'));
    }

    // Atualiza um cliente existente
    public function update(Request $request, $id)
    {
        try {
            $cliente = Cliente::find($id);

            if (!$cliente) {
                return redirect()->back()->with('erro', 'Cliente não encontrado.');
            }

            $validated = $request->validate([
                'nome' => 'nullable|string|max:45',
                'sobrenome' => 'nullable|string|max:60',
                'email' => 'nullable|email|max:100',
                'telefone' => 'nullable|string|max:11',
                'cep' => 'nullable|string|max:8',
                'endereco' => 'nullable|string|max:100',
            ]);

            // Verifica se o email já está em uso por outro cliente
            if (
                isset($validated['email']) &&
                Cliente::where('email', $validated['email'])
                ->where('id', '!=', $id)
                ->exists()
            ) {
                return redirect()->back()->with('erro', 'Cliente com este email já cadastrado.');
            }

            $cliente->update($validated);

            return redirect()->back()->with('mensagem', 'Cliente atualizado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar cliente: ' . $e->getMessage(), [
                'exception' => $e,
                'input' => $request->all(),
            ]);
            return redirect()->back()->with('erro', 'Erro ao atualizar cliente.');
        }
    }

    // Remove um cliente
    public function destroy($id)
    {
        try {
            $cliente = Cliente::find($id);

            if (!$cliente) {
                return redirect()->back()->with('erro', 'Cliente não encontrado.');
            }

            $cliente->delete();

            return redirect()->back()->with('mensagem', 'Cliente removido com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao remover cliente: ' . $e->getMessage(), [
                'exception' => $e,
                'cliente_id' => $id,
            ]);
            return redirect()->back()->with('erro', 'Erro ao remover cliente.');
        }
    }

    public function consultaDados(Request $request)
    {
        try {
            $clientes = Cliente::all();

            return response()->json($clientes);

        } catch (\Exception $e) {
            Log::error('Erro ao consultar dados do cliente: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return response()->json([
                'error' => 'Erro ao consultar dados do cliente.'
            ], 500);
        }
    }
}
