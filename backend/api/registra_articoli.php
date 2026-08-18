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

$descrizione = trim($_POST['prodotto']);

try {
    if (empty($descrizione)) {
        throw new Exception("Inserire un nome valido per l'articolo");
    }

    $query = "INSERT INTO articoli (descrizione, email_utente) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $descrizione, $email);
    if (!$stmt->execute()) {
        throw new Exception("Errore connessione/caricamento dati db");
    }
    echo json_encode([
        'status' => 'success',
        'message' => 'articolo aggiunto con successo!'
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