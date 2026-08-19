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
    if (empty($_POST['current_password']) || empty($_POST['new_password']) || empty($_POST['confirm_password'])) {
        throw new Exception("Compilare tutti i campi");
    }

    if ($_POST['new_password'] !== $_POST['confirm_password']) {
        throw new Exception("Le password non corrispondo");
    }

    $nuovaPassword = $_POST['new_password'];
    $vecchiaPassword = $_POST['current_password'];

    $query = "SELECT password FROM utenti WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        throw new Exception(("Errore di connessione al server!"));
    }
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if (!$row || !password_verify($vecchiaPassword, $row['password'])) {
        throw new Exception("La passsword attuale è errata!");
    }

    $nuovaPasswordHashata = password_hash($nuovaPassword, PASSWORD_DEFAULT);
    $query = "UPDATE utenti SET password = ? WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $nuovaPasswordHashata, $email);

    if (!$stmt->execute()) {
        throw new Exception("Errore di connessione al server!");
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Password aggiornata con successo!'
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