<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar'])) {
    $nome = $_POST['nome'];
    $relacao = $_POST['relacao'];
    $salario = floatval($_POST['salario']);

    $stmt = $conn->prepare("INSERT INTO pessoas (nome, relacao, salario) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $nome, $relacao, $salario);
    $stmt->execute();
    header("Location: index.php?page=pessoas");
    exit;
}


$editando = false;
$pessoaEdit = null;

if (isset($_GET['editar'])) {
    $editando = true;
    $id = intval($_GET['editar']);
    $result = $conn->query("SELECT * FROM pessoas WHERE id = $id");
    $pessoaEdit = $result->fetch_assoc();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['atualizar'])) {
    $id = intval($_POST['id']);
    $nome = $_POST['nome'];
    $relacao = $_POST['relacao'];
    $salario = floatval($_POST['salario']);
    $stmt = $conn->prepare("UPDATE pessoas SET nome = ?, relacao = ?, salario = ? WHERE id = ?");
    $stmt->bind_param("ssdi", $nome, $relacao, $salario, $id);
    $stmt->execute();
    header("Location: index.php?page=pessoas");
    exit;
}


if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    $conn->query("DELETE FROM pessoas WHERE id = $id");
    header("Location: index.php?page=pessoas");
    exit;
}


$pessoas = $conn->query("SELECT * FROM pessoas");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Pessoas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css"> 
</head>
<body>
<div class="container py-5">
    <h2 class="mb-4 text-primary"><i class="bi bi-people-fill"></i> Cadastro de Pessoas da Casa</h2>

    <form method="POST" class="row g-3 mb-4">
        <input type="hidden" name="id" value="<?= $pessoaEdit['id'] ?? '' ?>">
        <div class="col-md-5">
            <label class="form-label">Nome completo</label>
            <input type="text" name="nome" class="form-control" required value="<?= $pessoaEdit['nome'] ?? '' ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Relação</label>
            <input type="text" name="relacao" class="form-control" required value="<?= $pessoaEdit['relacao'] ?? '' ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Salário mensal</label>
            <input type="number" step="0.01" name="salario" class="form-control" required value="<?= $pessoaEdit['salario'] ?? '' ?>">
        </div>
        <div class="col-12 text-end">
            <?php if ($editando): ?>
                <button type="submit" name="atualizar" class="btn btn-warning"><i class="bi bi-pencil-square"></i> Atualizar</button>
                <a href="index.php?page=pessoas" class="btn btn-secondary">Cancelar</a>
            <?php else: ?>
                <button type="submit" name="adicionar" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Adicionar Pessoa</button>
            <?php endif; ?>
        </div>
    </form>

    <h4 class="mb-3">Pessoas Cadastradas</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Nome</th>
                    <th>Relação</th>
                    <th>Salário</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while($p = $pessoas->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($p['nome']) ?></td>
                    <td><?= htmlspecialchars($p['relacao']) ?></td>
                    <td>R$ <?= number_format($p['salario'], 2, ',', '.') ?></td>
                    <td class="text-center">
                        <a href="index.php?page=pessoas&editar=<?= $p['id'] ?>" class="btn btn-sm btn-warning me-1"><i class="bi bi-pencil-fill"></i></a>
                        <a href="index.php?page=pessoas&excluir=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir?')"><i class="bi bi-trash-fill"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
