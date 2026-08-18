<?php
session_start();
header("Content-Type: application/json");
require_once("../conn.php");

if (!$_SESSION['isLogged']) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Utente non autenticato'
    ]);
    exit();
}

$email = $_SESSION['email'];

try {
    $query = "SELECT * FROM articoli p WHERE p.email_utente = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        throw new Exception("Errore connessione/scaricamento dati db");
    }
    $result = $stmt->get_result();

    $articoli = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode([
        'status' => 'success',
        'data' => $articoli
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

$conn->close();
exit();
?>