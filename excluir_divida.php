<?php
session_start();
if (!isset($_SESSION['logado'])) {
    header('Location: index.php');
    exit();
}

include 'config.php';

$id = $_GET['id'];

$sql = "DELETE FROM dividas WHERE id = $id";
$conn->query($sql);

header('Location: dividas_pendentes.php');
exit();
?>
