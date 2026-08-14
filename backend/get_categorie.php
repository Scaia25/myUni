<?php
session_start();
header("Content-Type: application/json");
require_once('conn.php');

if (!$_SESSION['isLogged']) {
    header("Location: login.php");
    exit();
}

$query = "SELECT * FROM categorie";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$categorie = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    'status' => 'success',
    'data' => $categorie
]);

$conn->close();
exit();
?>