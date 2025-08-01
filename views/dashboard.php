<?php
$mesSelecionado = $_GET['mes'] ?? date('m');
$anoSelecionado = $_GET['ano'] ?? date('Y');
$dataInicio = "$anoSelecionado-$mesSelecionado-01";
$dataFim = date("Y-m-t", strtotime($dataInicio));


$nomesMeses = [
    '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril',
    '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto',
    '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
];

$totalReceita = $conn->query("SELECT SUM(salario) as total FROM pessoas")->fetch_assoc()['total'] ?? 0;

$stmt = $conn->prepare("SELECT SUM(valor) as total FROM despesas WHERE data BETWEEN ? AND ?");
$stmt->bind_param("ss", $dataInicio, $dataFim);
$stmt->execute();
$stmt->bind_result($totalGasto);
$stmt->fetch();
$stmt->close();

$sobra = $totalReceita - $totalGasto;

$categorias = [];
$valores = [];
$stmt = $conn->prepare("
    SELECT categoria, SUM(valor) as total 
    FROM despesas 
    WHERE data BETWEEN ? AND ?
    GROUP BY categoria
");
$stmt->bind_param("ss", $dataInicio, $dataFim);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $categorias[] = $row['categoria'];
    $valores[] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel Financeiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container py-5">
    <h2 class="mb-4 text-primary"><i class="bi bi-speedometer2"></i> Painel Financeiro</h2>

    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-3">
            <label for="mes" class="form-label">Mês</label>
            <select name="mes" id="mes" class="form-select">
                <?php foreach ($nomesMeses as $num => $nome): ?>
                    <option value="<?= $num ?>" <?= $mesSelecionado == $num ? 'selected' : '' ?>>
                        <?= $nome ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label for="ano" class="form-label">Ano</label>
            <select name="ano" id="ano" class="form-select">
                <?php for ($a = date('Y') - 5; $a <= date('Y') + 1; $a++): ?>
                    <option value="<?= $a ?>" <?= $anoSelecionado == $a ? 'selected' : '' ?>><?= $a ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-md-2 align-self-end">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Filtrar</button>
        </div>
    </form>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-success">
                <div class="card-body">
                    <h5 class="card-title text-success"><i class="bi bi-cash-stack"></i> Total Recebido</h5>
                    <p class="fs-4">R$ <?= number_format($totalReceita, 2, ',', '.') ?></p>
                    <small class="text-muted">Salários somados</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-danger">
                <div class="card-body">
                    <h5 class="card-title text-danger"><i class="bi bi-cart-x"></i> Total Gasto</h5>
                    <p class="fs-4">R$ <?= number_format($totalGasto, 2, ',', '.') ?></p>
                    <small class="text-muted">Despesas no período</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-primary">
                <div class="card-body">
                    <h5 class="card-title text-primary"><i class="bi bi-wallet2"></i> Sobra</h5>
                    <p class="fs-4">R$ <?= number_format($sobra, 2, ',', '.') ?></p>
                    <small class="text-muted">Diferença entre receita e despesa</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title"><i class="bi bi-bar-chart-line"></i> Gastos por Categoria</h5>
            <canvas id="graficoCategorias" height="100"></canvas>
        </div>
    </div>
</div>

<script>
const ctx = document.getElementById('graficoCategorias');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($categorias) ?>,
        datasets: [{
            label: 'Gastos por Categoria (R$)',
            data: <?= json_encode($valores) ?>,
            backgroundColor: '#0d6efd'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'R$ ' + context.formattedValue.replace('.', ',');
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'R$ ' + value.toLocaleString('pt-BR');
                    }
                }
            }
        }
    }
});
</script>
</body>
</html>
