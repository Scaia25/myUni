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
try {
    $query = "SELECT s.ID, date(s.data) as data, s.descrizione, s.importo, c.denominazione, s.id_categoria FROM spese s INNER JOIN categorie c ON c.ID = s.id_categoria WHERE s.email_utente = ? ORDER BY s.data DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        throw new Exception("Errore connessione/scaricamento dati db");
    }
    $result = $stmt->get_result();

    $spese = $result->fetch_all(MYSQLI_ASSOC);


    $query = "SELECT year(s.data) as anno FROM spese s WHERE s.email_utente = ? GROUP BY year(s.data) ORDER BY year(s.data) DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        throw new Exception("Errore connessione/scaricamento dati db");
    }
    $result = $stmt->get_result();

    $anni = $result->fetch_all(MYSQLI_ASSOC);


    echo json_encode([
        'status' => 'success',
        'spese' => $spese,
        'anni' => $anni
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