<?php
$mesSelecionado = $_GET['mes'] ?? date('m');
$anoSelecionado = $_GET['ano'] ?? date('Y');
$dataInicio = "$anoSelecionado-$mesSelecionado-01";
$dataFim = date("Y-m-t", strtotime($dataInicio));

$stmt = $conn->prepare("SELECT * FROM despesas WHERE data BETWEEN ? AND ? ORDER BY data DESC");
$stmt->bind_param("ss", $dataInicio, $dataFim);
$stmt->execute();
$result = $stmt->get_result();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar'])) {
    $nome = $_POST['nome'];
    $categoria = $_POST['categoria'];
    $valor = floatval($_POST['valor']);
    $data = $_POST['data'];

    $stmt = $conn->prepare("INSERT INTO despesas (nome, categoria, valor, data) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $nome, $categoria, $valor, $data);
    $stmt->execute();
    header("Location: index.php?page=despesas");
    exit;
}


$editando = false;
$despesaEdit = null;

if (isset($_GET['editar'])) {
    $editando = true;
    $id = intval($_GET['editar']);
    $result = $conn->query("SELECT * FROM despesas WHERE id = $id");
    $despesaEdit = $result->fetch_assoc();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar'])) {
    $id = intval($_POST['id']);
    $nome = $_POST['nome'];
    $categoria = $_POST['categoria'];
    $valor = floatval($_POST['valor']);
    $data = $_POST['data'];

    $stmt = $conn->prepare("UPDATE despesas SET nome = ?, categoria = ?, valor = ?, data = ? WHERE id = ?");
    $stmt->bind_param("ssdsi", $nome, $categoria, $valor, $data, $id);
    $stmt->execute();
    header("Location: index.php?page=despesas");
    exit;
}


if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    $conn->query("DELETE FROM despesas WHERE id = $id");
    header("Location: index.php?page=despesas");
    exit;
}


$despesas = $conn->query("SELECT * FROM despesas ORDER BY data DESC");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Controle de Despesas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container py-5">
  <h2 class="mb-4 text-danger"><i class="bi bi-cash-coin"></i> Controle de Despesas Mensais</h2>

  <form method="POST" class="row g-3 mb-4">
    <input type="hidden" name="id" value="<?= $despesaEdit['id'] ?? '' ?>">
    <div class="col-md-4">
      <label class="form-label">Nome da Despesa</label>
      <input type="text" name="nome" class="form-control" required value="<?= $despesaEdit['nome'] ?? '' ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label">Categoria</label>
      <select name="categoria" class="form-select" required>
        <?php
        $cats = ['Alimentação', 'Contas', 'Lazer', 'Educação', 'Transporte', 'Outros'];
        foreach ($cats as $cat):
        ?>
          <option value="<?= $cat ?>" <?= ($despesaEdit['categoria'] ?? '') == $cat ? 'selected' : '' ?>><?= $cat ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label">Valor</label>
      <input type="number" step="0.01" name="valor" class="form-control" required value="<?= $despesaEdit['valor'] ?? '' ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label">Data</label>
      <input type="date" name="data" class="form-control" required value="<?= $despesaEdit['data'] ?? '' ?>">
    </div>
    <div class="col-12 text-end">
      <?php if ($editando): ?>
        <button type="submit" name="atualizar" class="btn btn-warning"><i class="bi bi-pencil-square"></i> Atualizar</button>
        <a href="index.php?page=despesas" class="btn btn-secondary">Cancelar</a>
      <?php else: ?>
        <button type="submit" name="adicionar" class="btn btn-success"><i class="bi bi-plus-circle"></i> Registrar Despesa</button>
      <?php endif; ?>
    </div>
  </form>

  <h4 class="mb-3">Despesas Registradas</h4>
  <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-dark">
        <tr>
          <th>Data</th>
          <th>Nome</th>
          <th>Categoria</th>
          <th>Valor</th>
          <th class="text-center">Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php while($d = $despesas->fetch_assoc()): ?>
        <tr>
          <td><?= date('d/m/Y', strtotime($d['data'])) ?></td>
          <td><?= htmlspecialchars($d['nome']) ?></td>
          <td><?= htmlspecialchars($d['categoria']) ?></td>
          <td>R$ <?= number_format($d['valor'], 2, ',', '.') ?></td>
          <td class="text-center">
            <a href="index.php?page=despesas&editar=<?= $d['id'] ?>" class="btn btn-sm btn-warning me-1"><i class="bi bi-pencil-fill"></i></a>
            <a href="index.php?page=despesas&excluir=<?= $d['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Deseja excluir esta despesa?')"><i class="bi bi-trash-fill"></i></a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
