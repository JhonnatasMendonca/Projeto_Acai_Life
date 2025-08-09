<form class=" container-fluid formModal col-md-12 g-2" action="{{ route('despesas.update', $despesa->id) }}"  method="POST">
    @csrf
    @method('PUT')
    <div class="row g-2">
        <div class="form-group col-md-6">
            <label for="nome">Nome</label>
            <input type="text" name="nome" class="form-control" required placeholder="Nome da despesa" value="{{ $despesa->nome }}">
        </div>

        <div class="form-group col-md-6">
            <label for="categoria">Categoria</label>
            <select name="categoria" class="form-control" required>
                <option disabled {{ !$despesa->categoria ? 'selected' : '' }} value="">Selecione uma categoria</option>
                <option value="Estrutura" {{ $despesa->categoria == 'Estrutura' ? 'selected' : '' }}>Estrutural</option>
                <option value="Servicos" {{ $despesa->categoria == 'Servicos' ? 'selected' : '' }}>Serviços</option>
                <option value="Pessoal" {{ $despesa->categoria == 'Pessoal' ? 'selected' : '' }}>Pessoal</option>
                <option value="Outros" {{ $despesa->categoria == 'Outros' ? 'selected' : '' }}>Outros</option>
            </select>
        </div>
        <div class="form-group col-md-5">
            <label for="valor">Valor</label>
            <input type="number" name="valor" class="form-control"  placeholder="R$" step="0.01" min="0" value="{{ $despesa->valor }}">
        </div>

        <div class="form-group col-md-4">
            <label for="data">Data</label>
            <input type="date" name="data_lancamento" class="form-control" value="{{ \Carbon\Carbon::parse($despesa->data_lancamento)->format('Y-m-d') }}">
        </div>

        <div class="form-group col-md-3">
            <label for="status">Status</label>
            <select name="status" class="form-control" required>
                <option disabled {{ !$despesa->status ? 'selected' : '' }} value="">Selecione um status</option>
                <option value="pendente" {{ $despesa->status == 'pendente' ? 'selected' : '' }}>Pendente</option>
                <option value="pago" {{ $despesa->status == 'pago' ? 'selected' : '' }}>Pago</option>
                <option value="cancelado" {{ $despesa->status == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
            </select>
        </div> 

        <div class="form-group col-md-12">
            <label for="observacao">Observação</label>
            <input type="text" name="observacao" class="form-control"  placeholder="Informe alguma observação" value="{{ $despesa->observacao }}">
        </div>

    </div>
    <div class="col-md-12 d-flex justify-content-end mt-3">
        <button type="button" class="btn background_cancel">Cancelar</button>
        <button type="submit" class="btn background_sucess">Atualizar</button>
    </div>
</form>