<form class=" container-fluid formModal col-md-12 g-2" action="{{ route('caixas.store') }}" method="POST">
    @csrf
    <div class="row g-2">
        <div class="form-group col-md-3">
            <label for="valor_abertura">Valor da abertura</label>
            <input type="number" name="valor_abertura" class="form-control" placeholder="R$" value="0" step="0.01">
        </div>

        <div class="form-group col-md-9">
            <label for="observacao">Observação</label>
            <input type="text" name="observacao" class="form-control"  placeholder="Insira uma observação">
        </div>
        

    </div>
    <div class="col-md-12 d-flex justify-content-end mt-3">
        {{-- <button type="button" class="btn background_cancel">Cancelar</button> --}}
        <button type="submit" class="btn background_sucess">Abrir</button>
    </div>
</form>