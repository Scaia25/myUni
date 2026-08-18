// Mostra la modale di errore/avviso dinamica
function showModalError(errorMessage) {
    const overlay = document.createElement('div');
    overlay.className = 'modal-overlay active'; // AGGIUNTO: classe 'active' per mostrare l'overlay CSS

    const isAuthError = errorMessage === "Utente non autenticato";

    const buttonHtml = isAuthError
        ? `<button type="button" class="btn-modal btn-primary" onclick="this.closest('.modal-overlay').remove(); window.location.href = 'index.php';">Riaccedi</button>`
        : `<button type="button" class="btn-modal btn-cancel" onclick="this.closest('.modal-overlay').remove();">Ho capito</button>`;

    overlay.innerHTML = `
        <div class="modal-card modal-sm text-center">
            <div class="modal-icon-badge danger">!</div>
            <h3 class="modal-title">Attenzione</h3>
            <p class="modal-text modal-desc"></p>
            <div class="modal-footer" style="justify-content: center; margin-top: 16px;">
                ${buttonHtml}
            </div>
        </div>
    `;

    overlay.querySelector('.modal-text').textContent = errorMessage;
    document.body.appendChild(overlay);
}

// Mostra il modale dinamico di successo / conferma
function showModalSuccess(successMessage) {
    const overlay = document.createElement('div');
    overlay.className = 'modal-overlay active';

    overlay.innerHTML = `
        <div class="modal-card modal-sm text-center">
            <div class="modal-icon-badge green">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" 
                     stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <h3 class="modal-title">Operazione Riuscita</h3>
            <p class="modal-text modal-desc"></p>
            <div class="modal-footer" style="justify-content: center; margin-top: 16px;">
                <button type="button" class="btn-modal btn-primary" onclick="this.closest('.modal-overlay').remove();">Ottimo</button>
            </div>
        </div>
    `;

    overlay.querySelector('.modal-text').textContent = successMessage;
    document.body.appendChild(overlay);
}

//funzione ucfirst per js ed euroformatter
function ucfirst(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}

const euroFormatter = new Intl.NumberFormat("it-IT", { style: "currency", currency: "EUR" });


//funzione per caricare l'intera dashboard 
async function caricaDashboard() {
    try {
        const responseUtente = await fetch("backend/api/get_utente.php");
        const resultUtente = await responseUtente.json();

        if (resultUtente.status === "success") {
            renderAnagrafica(resultUtente.data);
            renderBudget(resultUtente.data);
        } else {
            const errorMsg = resultSpese.message || resultCategorie.message || "Errore nel caricamento dei dati.";
            showModalError(errorMsg);
        }

    } catch (error) {
        console.error("Errore durante la fetch:", error);
        showModalError("Errore di connessione al server.");
    }
}

//render anagrafica utente
function renderAnagrafica(utente) {
    const nome = document.getElementById("name");
    const cognome = document.getElementById("surname");
    const email = document.getElementById("email");

    nome.value = ucfirst(utente.nome);
    cognome.value = ucfirst(utente.cognome);
    email.value = utente.email;
}

//render budget mensile
function renderBudget(utente) {
    const budget = document.getElementById("monthly-budget");

    budget.value = utente.budget_mensile.replace(".", ",");
}

//funzione per aggiornare anagrafica utente
async function aggiornaAnagrafica(formAnagrafica) {
    const formData = new FormData(formAnagrafica);

    try {
        const responseForm = await fetch("backend/api/aggiorna_anagrafica.php", {
            method: "POST",
            body: formData
        });

        const resultForm = await responseForm.json();

        if (resultForm.status === "success") {
            // Messaggio di conferma in caso di successo
            showModalSuccess(resultForm.message || "Anagrafica aggiornata con successo!");
        } else {
            showModalError(resultForm.message);
        }
    } catch (error) {
        console.error("Errore di connessione:", error);
        showModalError("Impossibile connettersi al server!");
    }
}


//funzione per aggiornare budget mensile utente
async function aggiornaBudget(formBudget) {
    const formData = new FormData(formBudget);

    try {
        const responseForm = await fetch("backend/api/aggiorna_budget.php", {
            method: "POST",
            body: formData
        });

        const resultForm = await responseForm.json();

        if (resultForm.status === "success") {
            // Messaggio di conferma in caso di successo
            showModalSuccess(resultForm.message || "Budget aggiornato con successo!");
        } else {
            showModalError(resultForm.message);
        }
    } catch (error) {
        console.error("Errore di connessione:", error);
        showModalError("Impossibile connettersi al server!");
    }
}


//funzione per aggiornare la password utente
async function aggiornaPassword(formPassword) {
    const formData = new FormData(formPassword);

    try {
        const responseForm = await fetch("backend/api/aggiorna_password.php", {
            method: "POST",
            body: formData
        });

        const resultForm = await responseForm.json();

        if (resultForm.status === "success") {
            closeModal("modal-password");
            formPassword.reset();
            // Messaggio di conferma in caso di successo
            showModalSuccess(resultForm.message || "Password aggiornata con successo!");
        } else {
            closeModal("modal-password");
            formPassword.reset();
            showModalError(resultForm.message);
        }
    } catch (error) {
        formPassword.reset();
        closeModal("modal-password");
        console.error("Errore di connessione:", error);
        showModalError("Impossibile connettersi al server!");
    }
}

//funzioni per gestire i modali di logout e passwordChange
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.add('active');
        modal.setAttribute('aria-hidden', 'false');
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.classList.remove('active');
        modal.setAttribute('aria-hidden', 'true');
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const btnLogout = document.querySelector(".btn-danger-link");
    const btnCambioPassword = document.querySelector(".btn-secondary");

    if (btnLogout) {
        btnLogout.addEventListener("click", (e) => {
            e.preventDefault();
            openModal("modal-logout");
        });
    }

    if (btnCambioPassword) {
        btnCambioPassword.addEventListener("click", (e) => {
            e.preventDefault();
            openModal("modal-password");
        });
    }

    const btnEsci = document.querySelector("#modal-logout .btn-danger");
    if (btnEsci) {
        btnEsci.addEventListener("click", () => {
            window.location.href = "backend/logout.php";
        });
    }

    // Gestione form cambio password
    const formPassword = document.querySelector("#modal-password .modal-form");
    if (formPassword) {
        formPassword.addEventListener("submit", async function (e) {
            e.preventDefault();
            aggiornaPassword(formPassword);
        });
    }

    // Gestione form anagrafica
    const formAnagrafica = document.getElementById("profile-form");
    if (formAnagrafica) {
        formAnagrafica.addEventListener("submit", async function (e) {
            e.preventDefault();
            aggiornaAnagrafica(formAnagrafica);
        });
    }

    // Gestione form budget
    const formBudget = document.getElementById("budget-form");
    if (formBudget) {
        formBudget.addEventListener("submit", async function (e) {
            e.preventDefault();
            aggiornaBudget(formBudget);
        });
    }
});


caricaDashboard();