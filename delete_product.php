<?php
require 'db.php';
$id = intval($_GET['id'] ?? 0);
$pdo->prepare("DELETE FROM products WHERE id=?")->execute([$id]);
header("Location: products.php");
