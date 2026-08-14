<?php
session_start();
require('backend/conn.php');
require('backend/classes/utente.php');

if ($_SESSION['isLogged']) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $password = $_POST['password'];
    $confermaPassword = $_POST['confirmPassword'];

    try {
        if ($password != $confermaPassword) {
            throw new Exception("Le password non corrispondono!");
        }

        $utente = new Utente($email, $nome, $cognome, $password);
        $password = $utente->getPassword();

        $query = "SELECT * FROM utenti u WHERE u.email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            $res = $stmt->get_result();
            if ($res->num_rows >= 1) {
                throw new Exception("Email già in uso!");
            } else {
                $query = "INSERT INTO utenti (email, nome, cognome, password) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssss", $email, $nome, $cognome, $password);
                if ($stmt->execute()) {
                    $_SESSION['isLogged'] = true;
                    $_SESSION['email'] = $utente->getEmail();
                    header("Location: index.php");
                    exit();
                } else {
                    throw new Exception("Errore connessione/caricamento dati db");
                }
            }
        } else {
            throw new Exception("Errore connessione/caricamento dati db");
        }
    } catch (Exception $e) {
        $error_message = addslashes($e->getMessage());

        echo "<script>
    (function() {
        function showModal() {
            const overlay = document.createElement('div');
            overlay.className = 'modal-overlay';
            
            overlay.innerHTML = `
                <div class=\"modal-box\">
                    <div class=\"modal-icon\">!</div>
                    <h3>Attenzione</h3>
                    <p>{$error_message}</p>
                    <button class=\"modal-btn\" onclick=\"this.closest('.modal-overlay').remove()\">Ho capito</button>
                </div>
            `;
            
            document.body.appendChild(overlay);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', showModal);
        } else {
            showModal();
        }
    })();
</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crea account - myUni</title>
    <link rel="stylesheet" href="access.css">
    <link rel="stylesheet" href="modal-overlay.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        /* Messaggio dinamico per le password */
        .password-feedback {
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 4px;
            display: none;
            /* Nascosto di default */
        }

        .password-feedback.error {
            color: #e53e3e;
            display: block;
        }

        .password-feedback.success {
            color: #38a169;
            display: block;
        }

        /* Stile del pulsante quando è disabilitato */
        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }
    </style>
</head>

<body>

    <div class="login-wrapper">
        <!-- Brand / Header -->
        <div class="brand-header">
            <span class="brand-title">myUni</span>
        </div>

        <!-- Register Card -->
        <div class="card">
            <div class="card-header">
                <h1>Benvenutə!</h1>
                <p>Inserisci i tuoi dati per creare un account</p>
            </div>

            <form class="form" id="registerForm" action="register.php" method="POST">
                <!-- Campo Nome -->
                <div class="field">
                    <label for="nome">Nome</label>
                    <div class="input-wrapper">
                        <input type="text" id="nome" name="nome" placeholder="Mario" required>
                    </div>
                </div>

                <!-- Campo Cognome -->
                <div class="field">
                    <label for="cognome">Cognome</label>
                    <div class="input-wrapper">
                        <input type="text" id="cognome" name="cognome" placeholder="Rossi" required>
                    </div>
                </div>

                <!-- Campo Email -->
                <div class="field">
                    <label for="email">Email</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" placeholder="nome@esempio.it" required
                            autocomplete="email">
                    </div>
                </div>

                <!-- Campo Password -->
                <div class="field">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" placeholder="••••••••" required
                            autocomplete="new-password">
                        <button type="button" class="input-icon toggle-pwd" id="togglePassword"
                            aria-label="Mostra o nascondi password">
                            👁️
                        </button>
                    </div>
                </div>

                <!-- Campo Conferma Password -->
                <div class="field">
                    <label for="confirmPassword">Conferma Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="••••••••"
                            required autocomplete="new-password">
                    </div>
                    <!-- Messaggio di errore/conferma dinamico -->
                    <span id="passwordFeedback" class="password-feedback"></span>
                </div>

                <!-- Pulsante Submit -->
                <button type="submit" class="btn-submit" id="submitBtn">
                    <span>Crea Account</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </button>
            </form>
        </div>

        <!-- Footer Registrazione (Link al Login) -->
        <div class="card-footer">
            Hai già un account? <a href="login.php">Accedi qui</a>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const confirmPassword = document.querySelector('#confirmPassword');
        const passwordFeedback = document.querySelector('#passwordFeedback');
        const submitBtn = document.querySelector('#submitBtn');
        const registerForm = document.querySelector('#registerForm');

        // Toggle visibilità Password
        if (togglePassword && password) {
            togglePassword.addEventListener('click', function () {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.textContent = type === 'password' ? '👁️' : '🙈';
            });
        }

        // Controllo uguaglianza Password
        function checkPasswords() {
            const pwdValue = password.value;
            const confirmValue = confirmPassword.value;

            if (confirmValue === '') {
                passwordFeedback.className = 'password-feedback';
                passwordFeedback.textContent = '';
                submitBtn.disabled = false;
                return true;
            }

            if (pwdValue === confirmValue) {
                passwordFeedback.textContent = '✓ Le password coincidono';
                passwordFeedback.className = 'password-feedback success';
                submitBtn.disabled = false;
                return true;
            } else {
                passwordFeedback.textContent = '✕ Le password non coincidono';
                passwordFeedback.className = 'password-feedback error';
                submitBtn.disabled = true;
                return false;
            }
        }

        // Event listeners per l'input
        password.addEventListener('input', checkPasswords);
        confirmPassword.addEventListener('input', checkPasswords);

        // Impedisce il submit se le password non coincidono (es. invio tramite tastiera)
        registerForm.addEventListener('submit', function (e) {
            if (!checkPasswords() || password.value !== confirmPassword.value) {
                e.preventDefault();
                alert('Controlla che le due password inserite siano identiche.');
            }
        });
    </script>
</body>

</html>