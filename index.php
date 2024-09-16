<?php
session_start();

// Ativar exibição de erros para depuração
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $senha = $_POST['senha'];
    if ($senha == '123456') {
        $_SESSION['logado'] = true;
        header('Location: dashboard.php');
        exit();
    } else {
        $erro = "Senha incorreta!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>🔐 Página Inicial</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="text-center">💰 Gerenciador de Dívidas</h1>
        <div class="row justify-content-center">
            <div class="col-md-4">
                <form method="POST" class="mt-4">
                    <div class="mb-3">
                        <label for="senha" class="form-label">🔑 Senha:</label>
                        <input type="password" id="senha" name="senha" class="form-control" required>
                    </div>
                    <?php if (isset($erro)) { echo "<p class='text-danger'>$erro</p>"; } ?>
                    <button type="submit" class="btn btn-primary w-100">Entrar 🚀</button>
                </form>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
