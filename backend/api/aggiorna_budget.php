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
    if (empty($_POST['monthly_budget'])) {
        throw new Exception("Compilare il campo con un valore valido.");
    }

    $budgetFormatted = str_replace(",", ".", $_POST['monthly_budget']);

    if (!is_numeric($budgetFormatted) || (float) $budgetFormatted < 0) {
        throw new Exception("Inserire un importo numerico valido per il budget.");
    }

    $budget = (float) $budgetFormatted;

    $query = "UPDATE utenti SET budget_mensile = ? WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ds", $budget, $email);

    if (!$stmt->execute()) {
        throw new Exception("Errore durante l'aggiornamento del budget nel database.");
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Budget mensile aggiornato con successo!'
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