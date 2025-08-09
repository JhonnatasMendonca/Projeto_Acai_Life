<style>
    .modal {
        color: #000 !important;
        font-family: "Poppins", sans-serif;
        font-size: 14px;
    }

    .modal-content {
        padding: 0;
        width: 900px;
    }

    .modal-dialog {
        width: 100%;
        max-width: 900px;
    }

    .modal-dialog {
        margin: 0 auto;
    }
    /* .modal-header { */
        /* background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .modal-body {
        background-color: #ffffff;
        color: #000;
    }

    .modal-footer {
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
    }

    

    .modal-title {
        font-size: 1.25rem;
        font-weight: normal;
    }

    .modal-dialog {
        max-width: 600px;
    }

    .modal-dialog {
        margin: 1.75rem auto;
    }

    .modal-header .close {
        margin: -1rem -1rem -1rem auto;
    }

    .modal-header .close span {
        font-size: 1.5rem;
    }

    .modal-footer .btn {
        padding: 0.5rem 1rem;
        font-size: 1rem;
    } */
</style>


<div class="modal fade " id="{{ $id ?? 'modalPadrao' }}" tabindex="-1" role="dialog"
    aria-labelledby="{{ $id ?? 'modalPadrao' }}Label" aria-hidden="true">
    <div class="modal-dialog {{ $size ?? '' }}" role="document">
        <div class="modal-content container">
            <div class="modal-header">
                
                <div class="position_center_row">
                    <div class="line"></div>
                    <h5 style="font-size: 20px;" class="modal-title" id="{{ $id ?? 'modalPadrao' }}Label">{{ $title ?? 'Título' }}</h5>
                    <div class="line"></div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! $slot ?? 'Conteúdo do modal aqui' !!}
            </div>
            @if (empty($hideFooter))
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    {!! $confirmButton ?? '' !!}
                </div>
            @endif
        </div>
    </div>
</div>
