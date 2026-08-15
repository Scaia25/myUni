<?php
session_start();
session_regenerate_id(true);
require('backend/showServerErrors.php');
require('backend/conn.php');
require('backend/classes/utente.php');
require('backend/classes/spesa.php');

if (!$_SESSION['isLogged']) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];

$query = "SELECT * FROM categorie";
$stmt = $conn->prepare($query);
$stmt->execute();
$categorie = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MyUniversity - Spesa Personale</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="modal-overlay.css" />
</head>

<body>
    <div class="app">

        <!-- SIDEBAR -->
        <aside class="sidebar">
            <nav class="nav">
                <a href="#riepilogo" class="nav-link active" title="Dashboard">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    </svg>
                </a>
                <a href="#cronologia" class="nav-link" title="Statistiche">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="20" x2="18" y2="10" />
                        <line x1="12" y1="20" x2="12" y2="4" />
                        <line x1="6" y1="20" x2="6" y2="14" />
                    </svg>
                </a>
                <a href="#lista" class="nav-link" title="Checklist">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 11l3 3L22 4" />
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                    </svg>
                </a>
            </nav>
            <div class="sidebar-btn">
                <a href="#aggiungi">+ Nuova spesa</a>
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
                                    <input type="text" name="item-name" id="item-name"
                                        placeholder="Es. Supermercato..." />
                                </div>
                                <div class="field">
                                    <label for="item-amount">Importo (€)</label>
                                    <input type="text" name="item-amount" id="item-amount" inputmode="decimal"
                                        placeholder="Es. 36,42" required />
                                </div>
                                <div class="field">
                                    <label for="item-category">Categoria</label>
                                    <select id="item-category" name="item-category">
                                        <?php
                                        foreach ($categorie as $categoria) {
                                            $ID_cat = $categoria['ID'];
                                            $denominazione_cat = $categoria['denominazione'];

                                            echo '<option value="' . $ID_cat . '">' . $denominazione_cat . '</option>';
                                        }
                                        ?>
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
                            <input type="text" id="list-input" name="prodotto" placeholder="Aggiungi articolo..." required />
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
                        <span class="amount">114,50 €</span>
                    </div>
                </div>
            </div>

        </main>

    </div>
    <script src="script.js" defer></script>
</body>

</html>