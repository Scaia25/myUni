<?php
session_start();
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
    if (empty($_POST['name']) || empty($_POST['surname']) || empty($_POST['email'])) {
        throw new Exception("Compilare tutti i campi");
    }

    $nome = $_POST['name'];
    $cognome = $_POST['surname'];
    $nuovaEmail = $_POST['email'];

    $query = "UPDATE utenti SET nome = ?, cognome = ?, email = ? WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $nome, $cognome, $nuovaEmail, $email);

    if (!$stmt->execute()) {
        if ($conn->errno === 1062) {
            throw new Exception("L'email inserita è già utilizzata da un altro account.");
        }
        throw new Exception("Errore di connessione al server!");
    }

    $_SESSION['email'] = $nuovaEmail;

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