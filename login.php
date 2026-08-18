<?php
session_start();
require('backend/conn.php');
require('backend/classes/utente.php');

if ($_SESSION['isLogged']) {
  header("Location: index.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
  try {
    $email = $_POST['email'];
    $password = $_POST['password'];
    if (isset($email) && isset($password)) {
      $query = "SELECT u.email, u.password FROM utenti u WHERE u.email = ?";
      $stmt = $conn->prepare($query);
      $stmt->bind_param("s", $email);
      $stmt->execute();
      $res = $stmt->get_result();

      if ($res->num_rows == 1) {
        $row = $res->fetch_assoc();
        if (password_verify($password, $row['password'])) {
          $_SESSION['isLogged'] = true;
          $_SESSION['email'] = $email;
          header("Location: index.php");
          exit();
        }
      }

      throw new Exception("Email o password errate!");
    } else {
      throw new Exception("Compilare tutti i campi!");
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
  <title>Accedi al tuo account - myUni</title>
  <link rel="stylesheet" href="css/access.css">
  <link rel="stylesheet" href="css/modal-overlay.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
    rel="stylesheet">
</head>

<body>

  <div class="login-wrapper">
    <!-- Brand / Header -->
    <div class="brand-header">
      <span class="brand-title">myUni</span>
    </div>

    <!-- Login Card -->
    <div class="card">
      <div class="card-header">
        <h1>Bentornatə</h1>
        <p>Inserisci le tue credenziali per accedere</p>
      </div>

      <form class="form" id="loginForm" action="login.php" method="POST">
        <!-- Campo Email -->
        <div class="field">
          <label for="email">Email</label>
          <div class="input-wrapper">
            <input type="email" id="email" name="email" placeholder="nome@esempio.it" required autocomplete="email">
          </div>
        </div>

        <!-- Campo Password -->
        <div class="field">
          <label for="password">Password</label>
          <div class="input-wrapper">
            <input type="password" id="password" name="password" placeholder="••••••••" required
              autocomplete="current-password">
            <button type="button" class="input-icon toggle-pwd" id="togglePassword"
              aria-label="Mostra o nascondi password">
              👁️
            </button>
          </div>
        </div>

        <!-- Opzioni (Ricordami / Password dimenticata) -->
        <!--
        <div class="form-options">
          <label class="remember-me">
            <input type="checkbox" name="remember" id="remember">
            <span>Ricordami</span>
          </label>
        </div>
-->

        <!-- Pulsante Submit -->
        <button type="submit" class="btn-submit">
          <span>Accedi</span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
            stroke-linecap="round" stroke-linejoin="round">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
          </svg>
        </button>
      </form>
    </div>

    <!-- Footer Registrazione -->
    <div class="card-footer">
      Non hai un account? <a href="register.php">Registrati ora</a>
    </div>
  </div>

  <!-- Piccolo Script Frontend Opzionale (Toggle Password UI) -->
  <script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');

    if (togglePassword && password) {
      togglePassword.addEventListener('click', function () {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.textContent = type === 'password' ? '👁️' : '🙈';
      });
    }
  </script>
</body>

</html>