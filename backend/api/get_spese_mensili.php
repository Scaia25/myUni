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
    $query = "SELECT date(s.data) as data, s.importo, s.id_categoria, c.denominazione FROM spese s INNER JOIN categorie c ON c.ID = s.id_categoria WHERE s.email_utente = ? AND month(s.data) = month(curdate()) AND year(s.data) = year(curdate()) ORDER BY s.data DESC;";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        throw new Exception("Errore connessione/scaricamento dati db");
    }
    $result = $stmt->get_result();

    $spese_mensili = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode([
        'status' => 'success',
        'data' => $spese_mensili
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