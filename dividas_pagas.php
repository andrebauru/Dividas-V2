<?php
session_start();
if (!isset($_SESSION['logado'])) {
    header('Location: index.php');
    exit();
}

include 'config.php';

// Obter período selecionado ou todos os períodos
$data_inicial = isset($_GET['data_inicial']) ? $_GET['data_inicial'] : '';
$data_final = isset($_GET['data_final']) ? $_GET['data_final'] : '';

// Obter dívidas pagas
if ($data_inicial && $data_final) {
    $sql = "SELECT * FROM dividas WHERE status = 'pago' AND data_pagamento BETWEEN '$data_inicial' AND '$data_final' ORDER BY data_pagamento DESC";
} else {
    $sql = "SELECT * FROM dividas WHERE status = 'pago' ORDER BY data_pagamento DESC";
}
$result = $conn->query($sql);

// Calcular o total já pago em todos os períodos
$sql_total_pago = "SELECT SUM(valor) as total_pago FROM dividas WHERE status = 'pago'";
$result_total_pago = $conn->query($sql_total_pago);
$total_pago = $result_total_pago->fetch_assoc()['total_pago'] ?? 0.00;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>💰 Dívidas Pagas</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Barra de Navegação -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">💰 Gerenciador Financeiro</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"         aria-controls="navbarNav" aria-expanded="false" aria-label="Alterna navegação">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <!-- Outros itens do menu -->
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">📊 Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="dividas_pendentes.php">📋 Dívidas Pendentes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="dividas_pagas.php">💰 Dívidas Pagas</a>
                    </li>
                    <!-- ... -->
                </ul>
            </div>
        </div>
    </nav>

    <!-- Conteúdo Principal -->
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center">
            <h1>💰 Dívidas Pagas</h1>
            <h4>Total Pago: R$ <?php echo number_format($total_pago, 2, ',', '.'); ?></h4>
        </div>
        <!-- Filtro por período -->
        <form method="GET" class="row g-3 my-4">
            <div class="col-md-5">
                <label for="data_inicial" class="form-label">📅 Data Inicial:</label>
                <input type="date" id="data_inicial" name="data_inicial" value="<?php echo $data_inicial; ?>" class="form-control">
            </div>
            <div class="col-md-5">
                <label for="data_final" class="form-label">📅 Data Final:</label>
                <input type="date" id="data_final" name="data_final" value="<?php echo $data_final; ?>" class="form-control">
            </div>
            <div class="col-md-2 align-self-end">
                <button type="submit" class="btn btn-primary w-100">Filtrar 🔍</button>
            </div>
        </form>
        <!-- Tabela de Dívidas Pagas -->
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Descrição 📝</th>
                    <th>Valor 💵</th>
                    <th>Data de Vencimento 📅</th>
                    <th>Data de Pagamento 💰</th>
                    <th>Ações ⚙️</th>
                </tr>
            </thead>
            <tbody>
                <?php while($divida = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $divida['descricao']; ?></td>
                    <td>R$ <?php echo number_format($divida['valor'], 2, ',', '.'); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($divida['data'])); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($divida['data_pagamento'])); ?></td>
                    <td>
                        <a href="reverter_pendente.php?id=<?php echo $divida['id']; ?>" class="btn btn-warning btn-sm">Reverter para Pendente 🔄</a>
                        <a href="editar_divida.php?id=<?php echo $divida['id']; ?>" class="btn btn-primary btn-sm">Editar ✏️</a>
                        <a href="excluir_divida.php?id=<?php echo $divida['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir?');" class="btn btn-danger btn-sm">Excluir 🗑️</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <!-- Footer opcional -->
    <?php include 'footer.php'; ?>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
