<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>myUni - Impostazioni</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="css/settings.css" />
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
                <a href="spese.php" class="nav-link" title="Statistiche">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="20" x2="18" y2="10" />
                        <line x1="12" y1="20" x2="12" y2="4" />
                        <line x1="6" y1="20" x2="6" y2="14" />
                    </svg>
                </a>
                <a class="nav-link active" title="Settings">
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
                <h1>Impostazioni</h1>
            </header>

            <div class="settings-grid">

                <!-- PROFILO UTENTE -->
                <section class="card settings-card">
                    <div class="settings-card-header">
                        <h2>Profilo Utente</h2>
                        <p>Gestisci le informazioni del tuo account</p>
                    </div>

                    <form id="profile-form" class="form settings-form">
                        <div class="field">
                            <label for="name">Nome</label>
                            <input type="text" id="name" name="name" required />
                        </div>
                        <div class="field">
                            <label for="surname">Cognome</label>
                            <input type="text" id="surname" name="surname" required />
                        </div>
                        <div class="field">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required />
                        </div>
                        <button type="submit" class="btn-submit">Salva Modifiche Profilo</button>
                    </form>
                </section>

                <!-- BUDGET E PREFERENZE -->
                <section class="card settings-card">
                    <div class="settings-card-header">
                        <h2>Budget Mensile</h2>
                        <p>Imposta la tua soglia di spesa preferita</p>
                    </div>

                    <form id="budget-form" class="form settings-form">
                        <div class="field">
                            <label for="monthly-budget">Budget Mensile (€)</label>
                            <input type="text" id="monthly-budget" name="monthly_budget" inputmode="decimal"
                                required />
                        </div>
                        <button type="submit" class="btn-submit">Aggiorna Budget</button>
                    </form>
                </section>

                <!-- SICUREZZA -->
                <section class="card settings-card settings-danger-zone">
                    <div class="settings-card-header">
                        <h2>Sicurezza & Account</h2>
                        <p>Gestione della password e logout dall'account</p>
                    </div>

                    <div class="settings-actions">
                        <button type="button" class="btn-secondary">Cambia Password</button>
                        <button class="btn-danger-link">Log out dall'account</button>
                    </div>
                </section>

            </div>

        </main>

        <!-- ==========================================================================
     MODAL 1: CONFERMA LOGOUT
     ========================================================================== -->
        <div class="modal-overlay" id="modal-logout" aria-hidden="true">
            <div class="modal-card modal-sm" role="dialog" aria-labelledby="logout-title">

                <button type="button" class="modal-close" onclick="closeModal('modal-logout')"
                    aria-label="Chiudi">&times;</button>

                <div class="modal-body text-center">
                    <div class="modal-icon-badge danger">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                    </div>

                    <h2 id="logout-title" class="modal-title">Conferma Log Out</h2>
                    <p class="modal-desc">Sei sicuro di volerti disconnettere dal tuo account?</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-modal btn-cancel"
                        onclick="closeModal('modal-logout')">Annulla</button>
                    <a class="btn-modal btn-danger">Esci</a>
                </div>

            </div>
        </div>


        <!-- ==========================================================================
     MODAL 2: CAMBIO PASSWORD
     ========================================================================== -->
        <div class="modal-overlay" id="modal-password" aria-hidden="true">
            <div class="modal-card" role="dialog" aria-labelledby="password-title">

                <button type="button" class="modal-close" onclick="closeModal('modal-password')"
                    aria-label="Chiudi">&times;</button>

                <div class="modal-header">
                    <div class="modal-icon-badge lime">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 id="password-title" class="modal-title">Cambia Password</h2>
                        <p class="modal-desc">Inserisci la tua password attuale e la nuova password per aggiornare le
                            tue credenziali.</p>
                    </div>
                </div>

                <form class="modal-form">
                    <div class="modal-field">
                        <label for="current-password">Password Attuale</label>
                        <input type="password" id="current-password" name="current_password" placeholder="••••••••"
                            required />
                    </div>

                    <div class="modal-field">
                        <label for="new-password">Nuova Password</label>
                        <input type="password" id="new-password" name="new_password" placeholder="Minimo 8 caratteri"
                            required />
                    </div>

                    <div class="modal-field">
                        <label for="confirm-password">Conferma Nuova Password</label>
                        <input type="password" id="confirm-password" name="confirm_password"
                            placeholder="Ripeti nuova password" required />
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn-modal btn-cancel"
                            onclick="closeModal('modal-password')">Annulla</button>
                        <button type="submit" class="btn-modal btn-primary">Aggiorna Password</button>
                    </div>

                </form>

            </div>
        </div>

    </div>

    <script src="js/settings.js" defer></script>
</body>

</html>