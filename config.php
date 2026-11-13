<?php
require_once __DIR__ . '/../config.php';
require_login();

$page_title = $page_title ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($page_title) ?> | ACT1 Inventory</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="layout">
    <aside class="sidebar">
        <h2>ACT1 <span>Inventory</span></h2>

        <div class="profile">
            <div class="avatar">
                <?= e(strtoupper(substr($_SESSION['username'], 0, 1))) ?>
            </div>
            <div>
                <strong><?= e($_SESSION['username']) ?></strong>
                <small><?= e(ucfirst($_SESSION['role'])) ?></small>
            </div>
        </div>

        <nav>
            <a href="dashboard.php">▦ Dashboard</a>
            <a href="products.php">▤ Products</a>

            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="users.php">♙ User Management</a>
            <?php endif; ?>

            <a href="logout.php">↪ Logout</a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <h3><?= e($page_title) ?></h3>
            <span>Welcome, <?= e($_SESSION['username']) ?></span>
        </header>

        <section class="content">
