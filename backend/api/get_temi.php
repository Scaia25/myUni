<?php
require_once '../config.php';
header("Content-Type: application/json");
require_once('../conn.php');

if (!isset($_SESSION['isLogged']) || !$_SESSION['isLogged']) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Utente non autenticato'
    ]);
    exit();
}

try {
    $query = "SELECT * FROM temi";
    $stmt = $conn->prepare($query);

    if (!$stmt->execute()) {
        throw new Exception("Errore connessione/scaricamento dati db");
    }
    $result = $stmt->get_result();

    $temi = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode([
        'status' => 'success',
        'data' => $temi
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