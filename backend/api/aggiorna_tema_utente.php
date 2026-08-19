<?php
require_once '../config.php';
header("Content-Type: application/json");
require_once("../conn.php");

if (!isset($_SESSION['isLogged']) || !$_SESSION['isLogged']) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Utente non autenticato'
    ]);
    exit();
}

$email = $_SESSION['email'];

try {
    if (!isset($_POST['id']) && empty($_POST['id'])) {
        throw new Exception("Errore nel passaggio dati");
    }

    $idTema = $_POST['id'];

    $query = "UPDATE utenti SET id_tema = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $idTema);

    if (!$stmt->execute()) {
        throw new Exception("Errore di connessione al server!");
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Anagrafica aggiornata con successo!'
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