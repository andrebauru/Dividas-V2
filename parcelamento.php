<?php
session_start();
if (!isset($_SESSION['logado'])) {
    header('Location: index.php');
    exit();
}

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descricao = $_POST['descricao'];
    $valor_total = $_POST['valor_total'];
    $quantidade_parcelas = $_POST['quantidade_parcelas'];
    $data_parcelamento = $_POST['data_parcelamento'];
    $confirmacao = $_POST['confirmacao'];

    if ($confirmacao == 'sim') {
        $valor_parcela = $valor_total / $quantidade_parcelas;

        for ($i = 1; $i <= $quantidade_parcelas; $i++) {
            $data = date('Y-m-d', strtotime("+".($i - 1)." month", strtotime($data_parcelamento)));
            $descricao_parcela = $descricao . " - Parcela " . $i . "/" . $quantidade_parcelas;
            $sql = "INSERT INTO dividas (descricao, valor, data, status) VALUES ('$descricao_parcela', '$valor_parcela', '$data', 'pendente')";
            $conn->query($sql);
        }

        header('Location: dividas_pendentes.php');
        exit();
    } else {
        // Se o usuário cancelar, redireciona para o dashboard
        header('Location: dashboard.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>➕ Registrar Parcelamento</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Script para cálculo automático -->
    <script>
    function calcularParcela() {
        var valorTotal = parseFloat(document.getElementById('valor_total').value);
        var quantidadeParcelas = parseInt(document.getElementById('quantidade_parcelas').value);
        if (!isNaN(valorTotal) && !isNaN(quantidadeParcelas) && quantidadeParcelas > 0) {
            var valorParcela = valorTotal / quantidadeParcelas;
            document.getElementById('valor_parcela').value = valorParcela.toFixed(2);
        } else {
            document.getElementById('valor_parcela').value = '';
        }
    }
    </script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">➕ Registrar Parcelamento</h1>
        <form method="POST" class="mt-4">
            <div class="mb-3">
                <label for="descricao" class="form-label">📝 Descrição:</label>
                <input type="text" id="descricao" name="descricao" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="valor_total" class="form-label">💰 Valor Total:</label>
                <input type="number" step="0.01" id="valor_total" name="valor_total" class="form-control" required oninput="calcularParcela()">
            </div>
            <div class="mb-3">
                <label for="quantidade_parcelas" class="form-label">🔢 Quantidade de Parcelas:</label>
                <input type="number" id="quantidade_parcelas" name="quantidade_parcelas" class="form-control" required oninput="calcularParcela()">
            </div>
            <div class="mb-3">
                <label for="data_parcelamento" class="form-label">📅 Data do Primeiro Vencimento:</label>
                <input type="date" id="data_parcelamento" name="data_parcelamento" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="valor_parcela" class="form-label">💵 Valor da Parcela:</label>
                <input type="text" id="valor_parcela" name="valor_parcela" class="form-control" readonly>
            </div>
            <!-- Botões de Confirmação -->
            <div class="mb-3 text-center">
                <p>Deseja registrar este parcelamento?</p>
                <button type="submit" name="confirmacao" value="sim" class="btn btn-success">Sim ✅</button>
                <button type="submit" name="confirmacao" value="nao" class="btn btn-danger">Não ❌</button>
            </div>
        </form>
    </div>
    <?php include 'footer.php'; ?>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
