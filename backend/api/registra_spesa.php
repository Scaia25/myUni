<?php
session_start();
header("Content-Type: application/json");
require_once("../conn.php");
require_once("../classes/spesa.php");

if (!$_SESSION['isLogged']) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Utente non autenticato'
    ]);
    exit();
}

$email = $_SESSION['email'];

$importo = $_POST['item-amount'];
$descrizione = $_POST['item-name'];
$categoria = $_POST['item-category'];

try {
    $spesa = new Spesa($email, $importo, $descrizione, $categoria);

    $importo = $spesa->getImporto();

    $query = "INSERT INTO spese (email_utente, importo, descrizione, id_categoria) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sdss", $email, $importo, $descrizione, $categoria);
    if (!$stmt->execute()) {
        throw new Exception("Errore connessione/caricamento dati db");
    }
    echo json_encode([
        'status' => 'success',
        'message' => 'spesa aggiunta con successo!'
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