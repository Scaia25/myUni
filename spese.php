<?php
require_once 'backend/config.php';
require('backend/conn.php');

if (!isset($_SESSION['isLogged']) || !$_SESSION['isLogged']) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>myUni - Lista Spese</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="css/spese.css" />
    <link rel="stylesheet" href="css/modal-overlay.css" />
    <link rel="icon" type="image/jpeg" href="images/favicon.jpeg">
</head>

<body>
    <div class="app">

        <!-- SIDEBAR -->
        <aside class="sidebar">
            <nav class="nav">
                <a href="index.php" class="nav-link" title="Dashboard">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    </svg>
                </a>
                <a class="nav-link active" title="Tutte le Spese">
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
        </aside>

        <!-- CONTENUTO PRINCIPALE -->
        <main class="main">

            <header class="header">
                <h1>Storico Spese</h1>
            </header>

            <div class="card expenses-page-card">

                <!-- BARRA DEI FILTRI CON SELECT MESE E ANNO -->
                <div class="filter-bar">
                    <div class="filter-form">
                        <div class="riga-superiore-form">
                            <!-- SELECT ANNO -->
                            <div class="filter-group">
                                <label for="select-anno">Anno</label>
                                <select id="select-anno" name="anno" class="select-filter">
                                </select>
                            </div>

                            <!-- SELECT MESE -->
                            <div class="filter-group">
                                <label for="select-mese">Mese</label>
                                <select id="select-mese" name="mese" class="select-filter">
                                </select>
                            </div>
                        </div>



                        <div class="filter-group" id="filter-group-categoria">
                            <label for="select-categoria">Categoria</label>
                            <select id="select-categoria" name="categoria" class="select-filter">
                            </select>
                        </div>

                        <button class="btn-filter">Filtra</button>
                    </div>

                    <!-- TOTALIZZATORE PERIODO -->
                    <div class="expenses-summary">
                        <span class="summary-label">Spesa totale</span>
                        <span class="summary-value"></span>
                    </div>
                </div>

                <!-- TABELLA DELLE SPESE -->
                <div class="table-container">
                    <div class="table" id="tabella-spese">
                    </div>
                </div>

            </div>

        </main>

    </div>

    <!-- Modale di Conferma Eliminazione -->
    <div id="modal-conferma" class="modal-overlay hidden">
        <div class="modal-box">
            <!-- Icona Cestino -->
            <div class="modal-icon warning">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    <line x1="10" y1="11" x2="10" y2="17"></line>
                    <line x1="14" y1="11" x2="14" y2="17"></line>
                </svg>
            </div>

            <h3>Conferma Eliminazione</h3>
            <p class="modal-text">Sei sicuro di voler eliminare questa spesa? L'azione non potrà essere annullata.</p>

            <div class="modal-actions">
                <button id="btn-annulla-elimina" class="btn-modal btn-cancel">Annulla</button>
                <button id="btn-conferma-elimina" class="btn-modal btn-danger">Elimina</button>
            </div>
        </div>
    </div>

    <script src="js/spese.js" defer></script>
</body>

</html>