<?php
session_start();
if (!isset($_SESSION['logado'])) {
    header('Location: index.php');
    exit();
}

include 'config.php';

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descricao = $_POST['descricao'];
    $valor = $_POST['valor'];
    $data = $_POST['data'];

    $sql = "UPDATE dividas SET descricao = '$descricao', valor = '$valor', data = '$data' WHERE id = $id";
    $conn->query($sql);

    header('Location: dividas_pendentes.php');
    exit();
} else {
    $sql = "SELECT * FROM dividas WHERE id = $id";
    $result = $conn->query($sql);
    $divida = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>✏️ Editar Dívida</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">✏️ Editar Dívida</h1>
        <form method="POST" class="mt-4">
            <div class="mb-3">
                <label for="descricao" class="form-label">📝 Descrição:</label>
                <input type="text" id="descricao" name="descricao" value="<?php echo $divida['descricao']; ?>" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="valor" class="form-label">💵 Valor:</label>
                <input type="number" step="0.01" id="valor" name="valor" value="<?php echo $divida['valor']; ?>" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="data" class="form-label">📅 Data:</label>
                <input type="date" id="data" name="data" value="<?php echo $divida['data']; ?>" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Salvar Alterações 💾</button>
        </form>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
