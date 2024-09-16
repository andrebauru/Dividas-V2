<?php
session_start();
if (!isset($_SESSION['logado'])) {
    header('Location: index.php');
    exit();
}

include 'config.php';

// Paginação
$registros_por_pagina = 25;
$pagina_atual = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
$offset = ($pagina_atual - 1) * $registros_por_pagina;

// Tratamento da exclusão em massa
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['excluir_selecionados'])) {
    if (!empty($_POST['selecionados'])) {
        $ids_para_excluir = implode(',', array_map('intval', $_POST['selecionados']));
        $sql_excluir = "DELETE FROM dividas WHERE id IN ($ids_para_excluir)";
        if ($conn->query($sql_excluir) === TRUE) {
            $mensagem = "Dívidas excluídas com sucesso!";
        } else {
            $mensagem = "Erro ao excluir dívidas: " . $conn->error;
        }
    } else {
        $mensagem = "Nenhuma dívida selecionada para exclusão.";
    }
}

// Obter dívidas pendentes ordenadas por data de vencimento
$sql = "SELECT * FROM dividas WHERE status = 'pendente' ORDER BY data ASC LIMIT $offset, $registros_por_pagina";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>📋 Dívidas Pendentes</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Botão centralizado para voltar ao Dashboard -->
    <div class="container mt-3 text-center">
        <a href="dashboard.php" class="btn btn-secondary">🏠 Voltar ao Dashboard</a>
    </div>
    <div class="container mt-5">
        <h1 class="text-center">📋 Dívidas Pendentes</h1>
        <?php if (isset($mensagem)) { ?>
            <div class="alert alert-info"><?php echo $mensagem; ?></div>
        <?php } ?>
        <div class="d-flex justify-content-between my-4">
            <a href="parcelamento.php" class="btn btn-success">➕ Registrar Parcelamento</a>
            <a href="dividas_pagas.php" class="btn btn-info">💰 Dívidas Pagas</a>
        </div>
        <form method="POST" id="form-exclusao">
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>Descrição 📝</th>
                        <th>Valor 💵</th>
                        <th>Data 📅</th>
                        <th>Ações ⚙️</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($divida = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><input type="checkbox" name="selecionados[]" value="<?php echo $divida['id']; ?>"></td>
                        <td><?php echo $divida['descricao']; ?></td>
                        <td>R$ <?php echo number_format($divida['valor'], 2, ',', '.'); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($divida['data'])); ?></td>
                        <td>
                            <a href="marcar_pago.php?id=<?php echo $divida['id']; ?>" class="btn btn-success btn-sm">Pago ✅</a>
                            <a href="editar_divida.php?id=<?php echo $divida['id']; ?>" class="btn btn-warning btn-sm">Editar ✏️</a>
                            <a href="excluir_divida.php?id=<?php echo $divida['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir?');" class="btn btn-danger btn-sm">Excluir 🗑️</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <!-- Botão para exclusão em massa -->
            <div class="text-end">
                <button type="submit" name="excluir_selecionados" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir as dívidas selecionadas?');">Excluir Selecionados 🗑️</button>
            </div>
        </form>
        <!-- Paginação -->
        <?php
        $sql_total = "SELECT COUNT(*) as total FROM dividas WHERE status = 'pendente'";
        $result_total = $conn->query($sql_total);
        $total = $result_total->fetch_assoc()['total'];
        $total_paginas = ceil($total / $registros_por_pagina);
        ?>
        <nav aria-label="Navegação de página">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $total_paginas; $i++) { ?>
                    <li class="page-item <?php if ($pagina_atual == $i) echo 'active'; ?>">
                        <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php } ?>
            </ul>
        </nav>
    </div>
    <?php include 'footer.php'; ?>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Script para selecionar todos os checkboxes -->
    <script>
        document.getElementById('select-all').onclick = function() {
            var checkboxes = document.getElementsByName('selecionados[]');
            for (var checkbox of checkboxes) {
                checkbox.checked = this.checked;
            }
        }
    </script>
</body>
</html>
