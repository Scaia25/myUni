<?php
session_start();
header("Content-Type: application/json");
require_once("conn.php");

if (!isset($_SESSION['isLogged']) || !$_SESSION['isLogged']) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Utente non autenticato'
    ]);
    exit();
}

try {
    if (!isset($_POST['checked']) || !isset($_POST['id'])) {
        throw new Exception("Errore nel passaggio dati al server");
    }

    $isChecked = (int) $_POST['checked'];
    $ID_prodotto = (int) $_POST['id'];

    $query = "UPDATE articoli SET checked = ? WHERE ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $isChecked, $ID_prodotto);

    if (!$stmt->execute()) {
        throw new Exception("Errore connessione/caricamento dati db");
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'articolo aggiornato con successo!'
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