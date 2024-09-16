<?php
session_start();
if (!isset($_SESSION['logado'])) {
    header('Location: index.php');
    exit();
}

include 'config.php';

// Tratamento das ações: Marcar como pago, editar, excluir
if (isset($_GET['acao'])) {
    $acao = $_GET['acao'];
    $id = intval($_GET['id']);

    if ($acao == 'pago') {
        $sql = "UPDATE trabalhos SET status = 'pago', data_pagamento = NOW() WHERE id = $id";
        $conn->query($sql);
        header('Location: trabalho.php');
        exit();
    } elseif ($acao == 'excluir') {
        $sql = "DELETE FROM trabalhos WHERE id = $id";
        $conn->query($sql);
        header('Location: trabalho.php');
        exit();
    }
}

// Processamento do formulário de edição ou adição
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $descricao = $_POST['descricao'];
    $valor = $_POST['valor'];
    $data_solicitacao = $_POST['data_solicitacao'];
    $data_realizado = $_POST['data_realizado'] ? $_POST['data_realizado'] : NULL;
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id > 0) {
        // Atualizar trabalho existente
        $sql = "UPDATE trabalhos SET descricao = '$descricao', valor = '$valor', data_solicitacao = '$data_solicitacao', data_realizado = '$data_realizado' WHERE id = $id";
        $conn->query($sql);
    } else {
        // Inserir novo trabalho
        $sql = "INSERT INTO trabalhos (descricao, valor, data_solicitacao, data_realizado) VALUES ('$descricao', '$valor', '$data_solicitacao', '$data_realizado')";
        $conn->query($sql);
    }
    header('Location: trabalho.php');
    exit();
}

// Obter lista de trabalhos
$sql = "SELECT * FROM trabalhos ORDER BY data_solicitacao DESC";
$result = $conn->query($sql);

// Se for edição, obter os dados do trabalho
$trabalho_editar = null;
if (isset($_GET['acao']) && $_GET['acao'] == 'editar') {
    $id = intval($_GET['id']);
    $sql_editar = "SELECT * FROM trabalhos WHERE id = $id";
    $result_editar = $conn->query($sql_editar);
    $trabalho_editar = $result_editar->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>🔨 Trabalhos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Botão centralizado para voltar ao Dashboard -->
    <div class="container mt-3 text-center">
        <a href="dashboard.php" class="btn btn-secondary">🏠 Voltar ao Dashboard</a>
    </div>
    <div class="container mt-5">
        <h1 class="text-center">🔨 Trabalhos</h1>
        <!-- Botão de Parcelamento -->
        <div class="text-end mb-3">
            <a href="parcelamento.php" class="btn btn-success">➕ Registrar Parcelamento</a>
        </div>
        <!-- Formulário de Adição/Edição -->
        <div class="mt-4">
            <h3><?php echo $trabalho_editar ? '✏️ Editar Trabalho' : '➕ Adicionar Trabalho'; ?></h3>
            <form method="POST" class="row g-3">
                <?php if ($trabalho_editar) { ?>
                    <input type="hidden" name="id" value="<?php echo $trabalho_editar['id']; ?>">
                <?php } ?>
                <div class="col-md-6">
                    <label for="descricao" class="form-label">📝 Descrição:</label>
                    <input type="text" id="descricao" name="descricao" class="form-control" required value="<?php echo $trabalho_editar['descricao'] ?? ''; ?>">
                </div>
                <div class="col-md-6">
                    <label for="valor" class="form-label">💵 Valor:</label>
                    <input type="number" step="0.01" id="valor" name="valor" class="form-control" required value="<?php echo $trabalho_editar['valor'] ?? ''; ?>">
                </div>
                <div class="col-md-4">
                    <label for="data_solicitacao" class="form-label">📅 Data de Solicitação:</label>
                    <input type="date" id="data_solicitacao" name="data_solicitacao" class="form-control" required value="<?php echo $trabalho_editar['data_solicitacao'] ?? ''; ?>">
                </div>
                <div class="col-md-4">
                    <label for="data_realizado" class="form-label">📆 Data Realizado:</label>
                    <input type="date" id="data_realizado" name="data_realizado" class="form-control" value="<?php echo $trabalho_editar['data_realizado'] ?? ''; ?>">
                </div>
                <div class="col-md-4 align-self-end">
                    <button type="submit" class="btn btn-primary w-100"><?php echo $trabalho_editar ? 'Salvar Alterações 💾' : 'Adicionar Trabalho ➕'; ?></button>
                </div>
            </form>
        </div>
        <!-- Lista de Trabalhos -->
        <table class="table table-striped mt-5">
            <thead class="table-dark">
                <tr>
                    <th>Descrição 📝</th>
                    <th>Valor 💵</th>
                    <th>Data de Solicitação 📅</th>
                    <th>Data Realizado 📆</th>
                    <th>Data de Pagamento 💰</th>
                    <th>Status 📌</th>
                    <th>Ações ⚙️</th>
                </tr>
            </thead>
            <tbody>
                <?php while($trabalho = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $trabalho['descricao']; ?></td>
                    <td>R$ <?php echo number_format($trabalho['valor'], 2, ',', '.'); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($trabalho['data_solicitacao'])); ?></td>
                    <td><?php echo $trabalho['data_realizado'] ? date('d/m/Y', strtotime($trabalho['data_realizado'])) : '-'; ?></td>
                    <td><?php echo $trabalho['data_pagamento'] ? date('d/m/Y', strtotime($trabalho['data_pagamento'])) : '-'; ?></td>
                    <td><?php echo ucfirst($trabalho['status']); ?></td>
                    <td>
                        <?php if ($trabalho['status'] == 'pendente') { ?>
                            <a href="trabalho.php?acao=pago&id=<?php echo $trabalho['id']; ?>" class="btn btn-success btn-sm">Pago ✅</a>
                        <?php } ?>
                        <a href="trabalho.php?acao=editar&id=<?php echo $trabalho['id']; ?>" class="btn btn-warning btn-sm">Editar ✏️</a>
                        <a href="trabalho.php?acao=excluir&id=<?php echo $trabalho['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir?');" class="btn btn-danger btn-sm">Excluir 🗑️</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php include 'footer.php'; ?>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Script opcional -->
</body>
</html>
