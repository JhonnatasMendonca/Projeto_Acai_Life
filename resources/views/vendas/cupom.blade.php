

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cupom #{{ $cupom['id'] }}</title>
</head>
<body>

    <h1>Cupom #{{ $cupom['id'] }}</h1>

    <button id="btn-qz">Imprimir no PC (QZ Tray)</button>
    <a href="{{ url('/cupom/' . $cupom['id'] . '/download') }}" target="_blank">
        <button>Baixar para Celular (RawBT)</button>
    </a>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qz-tray/2.1.0/qz-tray.js"></script>


    <script>
        document.getElementById('btn-qz').addEventListener('click', function() {
            qz.websocket.connect().then(() => {
                return qz.printers.find();
            }).then(printer => {
                var config = qz.configs.create(printer);

                var data = [
                    '\x1B\x40',
                    '**** Cupom Fiscal ****\n',
                    'Produto A ........ R$ 10,00\n',
                    'Produto B ........ R$ 5,00\n',
                    'Total ............ R$ 15,00\n',
                    '\n\n\n',
                    '\x1D\x56\x00'
                ];

                return qz.print(config, data);

            }).catch(err => console.error(err));
        });
    </script>

</body>
</html>
