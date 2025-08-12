<form class=" container-fluid formModal col-md-12 g-2" action="{{ route('despesas.store') }}" method="POST">
    @csrf
    <div class="row g-2">
        <div class="form-group col-md-6">
            <label for="nome">Nome</label>
            <input type="text" name="nome" class="form-control" required placeholder="Nome da despesa">
        </div>

        <div class="form-group col-md-6">
            <label for="categoria">Categoria</label>
            <select name="categoria" class="form-control" required>
                <option disable selected value="">Selecione uma categoria</option>
                <option value="Estrutura">Estrutural</option>
                <option value="Servicos">Serviços</option>
                <option value="Pessoal">Pessoal</option>
                <option value="Outros">Outros</option>
            </select>
        </div>
        <div class="form-group col-md-5">
            <label for="valor">Valor</label>
            <input type="number" name="valor" class="form-control"  placeholder="R$" step="0.01" min="0">
        </div>

        <div class="form-group col-md-4">
            <label for="data">Data</label>
            <input type="date" name="data_lancamento" class="form-control">
        </div>

        <div class="form-group col-md-3">
            <label for="status">Status</label>
            <select name="status" class="form-control" required>
                <option disable selected value="">Selecione um status</option>
                <option value="pendente">Pendente</option>
                <option value="pago">Pago</option>
                <option value="cancelado">Cancelado</option>
            </select>
        </div> 

        <div class="form-group col-md-12">
            <label for="observacao">Observação</label>
            <input type="text" name="observacao" class="form-control"  placeholder="Informe alguma observação">
        </div>

    </div>
    <div class="col-md-12 d-flex justify-content-end mt-3">
        <button type="submit" class="btn background_sucess">Rgistrar</button>
    </div>
</form>