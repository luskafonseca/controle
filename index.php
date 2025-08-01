<?php
// index.php - Roteador simples inicial
include 'includes/db.php';
include 'includes/header.php';

$page = $_GET['page'] ?? 'dashboard';

switch ($page) {
    case 'pessoas':
        include 'views/pessoas.php';
        break;
    case 'despesas':
        include 'views/despesas.php';
        break;
    case 'relatorios':
        include 'views/relatorios.php';
        break;
    default:
        include 'views/dashboard.php';
        break;
}

include 'includes/footer.php';
