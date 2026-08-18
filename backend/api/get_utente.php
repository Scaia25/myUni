<?php
session_start();
header("Content-Type: application/json");
require_once('../conn.php');

if (!isset($_SESSION['isLogged']) || !$_SESSION['isLogged']) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Utente non autenticato'
    ]);
    exit();
}

$email = $_SESSION['email'];

$query = "SELECT * FROM utenti WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$utente = $result->fetch_assoc();

echo json_encode([
    'status' => 'success',
    'data' => $utente
]);

$conn->close();
exit();
?>