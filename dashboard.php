<?php
session_start();
if (!isset($_SESSION['logado'])) {
    header('Location: index.php');
    exit();
}

include 'config.php';

// Filtrar por mês atual ou período personalizado
$data_inicial = isset($_GET['data_inicial']) ? $_GET['data_inicial'] : date('Y-m-01');
$data_final = isset($_GET['data_final']) ? $_GET['data_final'] : date('Y-m-t');

// Obter total de dívidas pendentes no período
$sql_pendentes = "SELECT SUM(valor) as total_pendentes FROM dividas WHERE status = 'pendente' AND data BETWEEN '$data_inicial' AND '$data_final'";
$result_pendentes = $conn->query($sql_pendentes);
$total_pendentes = $result_pendentes->fetch_assoc()['total_pendentes'] ?? 0.00;

// Obter total de dívidas pagas no período
$sql_pagas = "SELECT SUM(valor) as total_pagas FROM dividas WHERE status = 'pago' AND data_pagamento BETWEEN '$data_inicial' AND '$data_final'";
$result_pagas = $conn->query($sql_pagas);
$total_pagas = $result_pagas->fetch_assoc()['total_pagas'] ?? 0.00;

// Obter total de dívidas no período
$total_dividas_periodo = $total_pendentes + $total_pagas;

// Obter total de trabalhos pendentes no período
$sql_trabalhos_pendentes = "SELECT SUM(valor) as total_trabalhos_pendentes FROM trabalhos WHERE status = 'pendente' AND data_solicitacao BETWEEN '$data_inicial' AND '$data_final'";
$result_trabalhos_pendentes = $conn->query($sql_trabalhos_pendentes);
$total_trabalhos_pendentes = $result_trabalhos_pendentes->fetch_assoc()['total_trabalhos_pendentes'] ?? 0.00;

// Obter total de trabalhos pagos no período
$sql_trabalhos_pagos = "SELECT SUM(valor) as total_trabalhos_pagos FROM trabalhos WHERE status = 'pago' AND data_pagamento BETWEEN '$data_inicial' AND '$data_final'";
$result_trabalhos_pagos = $conn->query($sql_trabalhos_pagos);
$total_trabalhos_pagos = $result_trabalhos_pagos->fetch_assoc()['total_trabalhos_pagos'] ?? 0.00;

// Calcular o total de pagamentos no período (dívidas pagas + trabalhos pagos)
$total_pagamentos_periodo = $total_pagas + $total_trabalhos_pagos;

// Atualizar o total de contas a pagar somando dívidas e trabalhos pendentes no período
$total_contas_a_pagar = $total_pendentes + $total_trabalhos_pendentes;

// Obter a taxa de câmbio BRL para JPY
$api_url = 'https://v6.exchangerate-api.com/v6/SUAAPIGRATIS/latest/BRL'; // Substitua SEU_API_KEY pela sua chave da API
$response = file_get_contents($api_url);
if ($response !== false) {
    $data = json_decode($response, true);
    if ($data['result'] == 'success') {
        $taxa_jpy = $data['conversion_rates']['JPY'];
        // Converter o total de dívidas no período para JPY
        $total_dividas_jpy = $total_dividas_periodo * $taxa_jpy;
    } else {
        // Se a API retornar um erro, definir taxa_jpy como null
        $taxa_jpy = null;
    }
} else {
    // Se a requisição falhar, definir taxa_jpy como null
    $taxa_jpy = null;
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>📊 Dashboard</title>
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
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">📊 Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="dividas_pendentes.php">📋 Dívidas Pendentes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="dividas_pagas.php">💰 Dívidas Pagas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="parcelamento.php">➕ Registrar Parcelamento</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="trabalho.php">🔨 Trabalhos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">🚪 Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Conteúdo Principal -->
    <div class="container mt-5">
        <h1 class="text-center">📊 Dashboard</h1>
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

        <!-- Cards com Resumos -->
        <div class="row">
            <!-- Total de Contas a Pagar no Período -->
            <div class="col-md-4">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-header">📋 Contas a Pagar no Período</div>
                    <div class="card-body">
                        <h5 class="card-title">R$ <?php echo number_format($total_contas_a_pagar, 2, ',', '.'); ?></h5>
                        <p class="card-text">Total de dívidas e trabalhos pendentes no período selecionado.</p>
                    </div>
                </div>
            </div>
            <!-- Total Pago no Período (Dívidas Pagas + Trabalhos Pagos) -->
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">💰 Total Pago no Período</div>
                    <div class="card-body">
                        <h5 class="card-title">R$ <?php echo number_format($total_pagamentos_periodo, 2, ',', '.'); ?></h5>
                        <p class="card-text">Soma das dívidas e trabalhos pagos no período selecionado.</p>
                    </div>
                </div>
            </div>
            <!-- Total de Dívidas no Período -->
            <div class="col-md-4">
                <div class="card text-white bg-secondary mb-3">
                    <div class="card-header">💵 Total de Dívidas no Período</div>
                    <div class="card-body">
                        <h5 class="card-title">
                            R$ <?php echo number_format($total_dividas_periodo, 2, ',', '.'); ?>
                            <?php if (isset($total_dividas_jpy)) { ?>
                                <br>
                                <small>(¥ <?php echo number_format($total_dividas_jpy, 2, ',', '.'); ?>)</small>
                            <?php } ?>
                        </h5>
                        <p class="card-text">Total de dívidas no período selecionado.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabela de Dívidas Pendentes no Período -->
        <h2 class="mt-5">📋 Dívidas Pendentes no Período</h2>
        <?php
        // Obter dívidas pendentes no período
        $sql_dividas_pendentes = "SELECT * FROM dividas WHERE status = 'pendente' AND data BETWEEN '$data_inicial' AND '$data_final' ORDER BY data ASC";
        $result_dividas_pendentes = $conn->query($sql_dividas_pendentes);
        ?>
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Descrição 📝</th>
                    <th>Valor 💵</th>
                    <th>Data 📅</th>
                    <th>Ações ⚙️</th>
                </tr>
            </thead>
            <tbody>
                <?php while($divida = $result_dividas_pendentes->fetch_assoc()) { ?>
                <tr>
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

        <!-- Tabela de Trabalhos Não Pagos no Período -->
        <h2 class="mt-5">🔨 Trabalhos Não Pagos no Período</h2>
        <?php
        // Obter trabalhos não pagos no período
        $sql_trabalhos_nao_pagos = "SELECT * FROM trabalhos WHERE status = 'pendente' AND data_solicitacao BETWEEN '$data_inicial' AND '$data_final' ORDER BY data_solicitacao DESC";
        $result_trabalhos_nao_pagos = $conn->query($sql_trabalhos_nao_pagos);
        ?>
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Descrição 📝</th>
                    <th>Valor 💵</th>
                    <th>Data de Solicitação 📅</th>
                    <th>Data Realizado 📆</th>
                    <th>Ações ⚙️</th>
                </tr>
            </thead>
            <tbody>
                <?php while($trabalho = $result_trabalhos_nao_pagos->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $trabalho['descricao']; ?></td>
                    <td>R$ <?php echo number_format($trabalho['valor'], 2, ',', '.'); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($trabalho['data_solicitacao'])); ?></td>
                    <td><?php echo $trabalho['data_realizado'] ? date('d/m/Y', strtotime($trabalho['data_realizado'])) : '-'; ?></td>
                    <td>
                        <a href="trabalho.php?acao=pago&id=<?php echo $trabalho['id']; ?>" class="btn btn-success btn-sm">Pago ✅</a>
                        <a href="trabalho.php?acao=editar&id=<?php echo $trabalho['id']; ?>" class="btn btn-warning btn-sm">Editar ✏️</a>
                        <a href="trabalho.php?acao=excluir&id=<?php echo $trabalho['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir?');" class="btn btn-danger btn-sm">Excluir 🗑️</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Tabela de Pagamentos no Período -->
        <h2 class="mt-5">💰 Pagamentos no Período</h2>
        <?php
        // Obter pagamentos (dívidas pagas + trabalhos pagos) no período
        // Dívidas Pagas
        $sql_dividas_pagas = "SELECT descricao, valor, data_pagamento as data FROM dividas WHERE status = 'pago' AND data_pagamento BETWEEN '$data_inicial' AND '$data_final'";
        $result_dividas_pagas = $conn->query($sql_dividas_pagas);

        // Trabalhos Pagos
        $sql_trabalhos_pagas = "SELECT descricao, valor, data_pagamento as data FROM trabalhos WHERE status = 'pago' AND data_pagamento BETWEEN '$data_inicial' AND '$data_final'";
        $result_trabalhos_pagas = $conn->query($sql_trabalhos_pagas);

        // Combinar os resultados
        $pagamentos = array();

        while($divida = $result_dividas_pagas->fetch_assoc()) {
            $pagamentos[] = $divida;
        }

        while($trabalho = $result_trabalhos_pagas->fetch_assoc()) {
            $pagamentos[] = $trabalho;
        }

        // Ordenar por data de pagamento
        usort($pagamentos, function($a, $b) {
            return strtotime($b['data']) - strtotime($a['data']);
        });
        ?>
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Descrição 📝</th>
                    <th>Valor 💵</th>
                    <th>Data de Pagamento 💰</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($pagamentos as $pagamento) { ?>
                <tr>
                    <td><?php echo $pagamento['descricao']; ?></td>
                    <td>R$ <?php echo number_format($pagamento['valor'], 2, ',', '.'); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($pagamento['data'])); ?></td>
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
