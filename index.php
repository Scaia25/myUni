<?php
require_once 'backend/config.php';
require('backend/conn.php');
require('backend/classes/utente.php');
require('backend/classes/spesa.php');

if (!isset($_SESSION['isLogged']) || !$_SESSION['isLogged']) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

$query = "SELECT SUM(s.importo) as spesaTotale 
          FROM spese s 
          WHERE s.email_utente = ? AND s.data < DATE_FORMAT(CURRENT_DATE(), '%Y-%m-01')";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
if (!$stmt->execute()) {
    echo "<script>showModalError('Errore di connessione al server');</script>";
    $stmt->close();
    exit();
}
$res = $stmt->get_result()->fetch_assoc();
$spesaTotalePrecedente = (float) ($res['spesaTotale'] ?? 0);
$stmt->close();

$query = "SELECT u.budget_mensile * PERIOD_DIFF(DATE_FORMAT(CURRENT_DATE(), '%Y%m'), DATE_FORMAT(u.data_iscrizione, '%Y%m')) AS budgetTotale 
          FROM utenti u 
          WHERE u.email = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
if (!$stmt->execute()) {
    echo "<script>showModalError('Errore di connessione al server');</script>";
    $stmt->close();
    exit();
}
$res = $stmt->get_result()->fetch_assoc();
$budgetTotalePrecedente = (float) ($res['budgetTotale'] ?? 0);
$stmt->close();

$differenza = $budgetTotalePrecedente - $spesaTotalePrecedente;

$budgetRimaneteMesiPrecedenti = number_format($differenza, 2, ',', '.');
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>myUni</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="css/index.css" />
    <link rel="stylesheet" href="css/modal-overlay.css" />
    <link rel="icon" type="image/jpeg" href="images/favicon.jpeg">
</head>

<body>
    <div class="app">

        <!-- SIDEBAR -->
        <aside class="sidebar">
            <nav class="nav">
                <a class="nav-link active" title="Dashboard">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    </svg>
                </a>
                <a href="spese.php" class="nav-link" title="Tutte le Spese">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="20" x2="18" y2="10" />
                        <line x1="12" y1="20" x2="12" y2="4" />
                        <line x1="6" y1="20" x2="6" y2="14" />
                    </svg>
                </a>
                <a href="settings.php" class="nav-link" title="Settings">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                </a>
            </nav>
            <div class="sidebar-btn">
                <button>+ Nuova spesa</button>
            </div>
        </aside>

        <!-- CONTENUTO PRINCIPALE -->
        <main class="main">

            <header class="header"></header>

            <div class="grid">

                <!-- COLONNA SINISTRA -->
                <div class="col-main">

                    <!-- CARDS KPI -->
                    <div class="kpi-row">

                        <div class="card kpi-white">
                            <div class="kpi-header">
                                <span class="kpi-title"></span>
                                <span class="badge badge-lime"></span>
                            </div>
                            <div class="kpi-values">
                                <span class="amount" id="budget_rimasto"></span>
                                <span class="sub-amount" id="budget_mensile"></span>
                            </div>
                            <div class="segments">
                            </div>
                        </div>
                    </div>

                    <!-- GRAFICO -->
                    <div class="card" id="riepilogo">
                        <div class="card-header">
                        </div>

                        <div class="chart-legend">
                        </div>
                    </div>

                    <!-- FORM SPESA + ULTIME SPESE -->
                    <div class="subgrid">

                        <section class="card" id="aggiungi">
                            <h2>Nuova spesa</h2>
                            <form id="expense-form" class="form" action="index.php" method="POST">
                                <div class="field">
                                    <label for="item-name">Descrizione spesa</label>
                                    <input type="text" name="item-name" id="item-name" placeholder="Es. Supermercato..."
                                        required />
                                </div>
                                <div class="field">
                                    <label for="item-amount">Importo (€)</label>
                                    <input type="text" name="item-amount" id="item-amount" inputmode="decimal"
                                        placeholder="Es. 36,42" required />
                                </div>
                                <div class="field" id="field-category">
                                    <label for="item-category">Categoria</label>
                                    <select id="item-category" name="item-category">
                                    </select>
                                </div>
                                <button class="btn-submit" type="submit">Salva Spesa</button>
                                <p class="form-msg" id="expense-msg"></p>
                            </form>
                        </section>

                        <section class="card" id="cronologia">
                            <h2>Ultime spese</h2>
                            <table class="table" id="tabella-spese">
                                <tr id="titoli-colonne">
                                    <th class="date">Data</th>
                                    <th class="amount">Importo</th>
                                    <th class="category">Categoria</th>
                                </tr>
                            </table>

                        </section>

                    </div>

                </div>

                <!-- COLONNA DESTRA -->
                <aside class="col-side">
                    <div class="card" id="lista">
                        <div class="card-header">
                            <h3>Lista della spesa</h3>
                        </div>
                        <form id="list-form" class="inline-form">
                            <input type="text" id="list-input" name="prodotto" placeholder="Aggiungi articolo..."
                                required />
                            <button class="btn-add" type="submit">+</button>
                        </form>
                        <ul class="todo-list" id="todo-list"></ul>
                        <p class="todo-meta" id="todo-meta"></p>
                    </div>
                </aside>

                <div class="card kpi-lime">
                    <div class="kpi-header">
                        <span class="kpi-title">Avanzati dai mesi precedenti</span>
                    </div>
                    <div class="kpi-values">
                        <span class="amount"><?php echo $budgetRimaneteMesiPrecedenti . " €"; ?></span>
                    </div>
                </div>
            </div>

        </main>

    </div>
    <script src="js/index.js" defer></script>
</body>

</html>