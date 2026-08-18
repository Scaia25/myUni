// Mostra la modale di errore
function showModalError(errorMessage) {
    const overlay = document.createElement('div');
    overlay.className = 'modal-overlay';

    const isAuthError = errorMessage === "Utente non autenticato";

    let buttonHtml = isAuthError
        ? `<button class="modal-btn" onclick="this.closest('.modal-overlay').remove(); window.location.href = 'index.php';">Riaccedi</button>`
        : `<button class="modal-btn" onclick="this.closest('.modal-overlay').remove();">Ho capito</button>`;

    overlay.innerHTML = `
  <div class="modal-box">
    <div class="modal-icon">!</div>
    <h3>Attenzione</h3>
    <p class="modal-text"></p>
    ${buttonHtml}
  </div>
`;

    overlay.querySelector('.modal-text').textContent = errorMessage;
    document.body.appendChild(overlay);
}

const oggi = new Date();
const annoCorrente = oggi.getFullYear();
const meseCorrente = oggi.getMonth() + 1;

// 1. Variabile di stato globale per conservare i dati scaricati
let speseData = [];

function renderFiltro(categorie, anni) {
    const selectAnni = document.getElementById("select-anno");
    if (!selectAnni) return;

    selectAnni.innerHTML = "";
    let option = new Option("Tutti gli anni", "all");
    selectAnni.add(option);

    anni.forEach(anno => {
        const isSelected = anno.anno === annoCorrente;
        option = new Option(anno.anno, anno.anno, isSelected, isSelected);
        selectAnni.add(option);
    });

    const nomiMesi = [
        "Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno",
        "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre"
    ];

    const selectMesi = document.getElementById("select-mese");
    if (selectMesi) {
        selectMesi.innerHTML = "";
        option = new Option("Tutti i mesi", "all");
        selectMesi.add(option);
        for (let i = 1; i <= 12; i++) {
            const isSelected = i === meseCorrente;
            option = new Option(nomiMesi[i - 1], i, isSelected, isSelected);
            selectMesi.add(option);
        }
    }

    const selectCategorie = document.getElementById("select-categoria");
    if (selectCategorie) {
        selectCategorie.innerHTML = "";
        option = new Option("Tutte le categorie", "all");
        selectCategorie.add(option);
        categorie.forEach(categoria => {
            option = new Option(categoria.denominazione, categoria.ID);
            selectCategorie.add(option);
        });
    }
}

// 2. renderSpese ora usa speseData se non viene passato alcun parametro
function renderSpese(spese = speseData) {
    const valueAnno = document.getElementById("select-anno")?.value ?? "all";
    const valueMese = document.getElementById("select-mese")?.value ?? "all";
    const valueCategoria = document.getElementById("select-categoria")?.value ?? "all";

    const tabellaSpese = document.getElementById("tabella-spese");
    const spesaTotaleElem = document.querySelector(".summary-value"); // Elemento del totale

    if (!tabellaSpese) return;

    tabellaSpese.textContent = "";
    const euroFormatter = new Intl.NumberFormat("it-IT", { style: "currency", currency: "EUR" });

    const speseFiltrate = spese.filter(spesa => {
        if (!spesa.data) return false;

        const [annoSpesa, meseSpesa] = spesa.data.split("-");

        const matchAnno = valueAnno === "all" || annoSpesa === valueAnno;
        const matchMese = valueMese === "all" || String(Number(meseSpesa)) === valueMese;
        const matchCategoria = valueCategoria === "all" || String(spesa.id_categoria) === valueCategoria;

        return matchAnno && matchMese && matchCategoria;
    });

    // Se non ci sono spese filtrate, azzera anche il totale visibile
    if (speseFiltrate.length === 0) {
        const text = document.createElement("p");
        text.className = "empty-message";

        const boldText = document.createElement("strong");
        boldText.textContent = "Nessun risultato corrispondente ai filtri applicati.";
        text.appendChild(boldText);

        tabellaSpese.appendChild(text);

        if (spesaTotaleElem) {
            spesaTotaleElem.textContent = euroFormatter.format(0);
        }
        return;
    }

    const fragment = document.createDocumentFragment();
    let spesaTotaleNumerica = 0;

    speseFiltrate.forEach(spesa => {
        // CONVERSIONE IN NUMERO: evita la concatenazione di stringhe
        const importoNumerico = parseFloat(spesa.importo ?? 0);
        spesaTotaleNumerica += importoNumerico;

        const rigaSpesa = document.createElement("div");
        if (spesa.ID) rigaSpesa.id = `riga-spesa-${spesa.ID}`;
        rigaSpesa.className = "riga-spesa";

        const [year, month, day] = spesa.data.split("-");
        const dataFormattata = `${day}/${month}/${year}`;
        const importoFormattato = euroFormatter.format(importoNumerico);

        rigaSpesa.innerHTML = `
            <div class="riga-spesa-left">
                <div class="riga-spesa-superiore">
                    <p class="description">${escapeHTML(spesa.descrizione || spesa.denominazione || "")}</p>
                </div>
                <div class="riga-spesa-inferiore">
                    <div class="riga-spesa-inferiore-dati">
                        <p class="date">${dataFormattata}</p>
                        <span> | </span>
                        <p class="amount">${importoFormattato}</p>
                        <span> | </span>
                        <p class="category">
                            <span class="tag tag-${spesa.id_categoria}">${escapeHTML(spesa.denominazione || "")}</span>
                        </p>
                    </div>
                </div>
            </div>
            <button class="removeSpesaBtn" ${spesa.ID ? `id="removeSpesa-${spesa.ID}"` : ""}>&times;</button>
        `;

        fragment.appendChild(rigaSpesa);
    });

    tabellaSpese.appendChild(fragment);

    // Aggiorna il totale nel DOM in modo sicuro
    if (spesaTotaleElem) {
        spesaTotaleElem.textContent = euroFormatter.format(spesaTotaleNumerica);
    }
}

function escapeHTML(str) {
    return String(str)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

async function caricaDashboard() {
    try {
        const responseSpese = await fetch("backend/api/get_spese.php");
        const resultSpese = await responseSpese.json();

        const responseCategorie = await fetch("backend/api/get_categorie.php");
        const resultCategorie = await responseCategorie.json();

        if (resultSpese.status === "success" && resultCategorie.status === "success") {
            // Salva l'array nella variabile di stato
            speseData = resultSpese.spese;
            const anni = resultSpese.anni;
            const categorie = resultCategorie.data;

            renderFiltro(categorie, anni);
            disabilitaSelect();
            renderSpese();
        } else {
            const errorMsg = resultSpese.message || resultCategorie.message || "Errore nel caricamento dei dati.";
            showModalError(errorMsg);
        }

    } catch (error) {
        console.error("Errore durante la fetch:", error);
        showModalError("Errore di connessione al server.");
    }
}

function disabilitaSelect() {
    const selectAnni = document.getElementById("select-anno");
    const selectMesi = document.getElementById("select-mese");

    if (!selectAnni || !selectMesi) return;

    if (selectAnni.value === 'all') {
        selectMesi.disabled = true;
        selectMesi.value = "all";
    } else {
        selectMesi.disabled = false;
        selectMesi.value = String(meseCorrente);
    }
}

// 4. Inizializzazione Event Listeners all'interno del DOMContentLoaded
document.addEventListener("DOMContentLoaded", () => {
    caricaDashboard();

    const selectAnni = document.getElementById("select-anno");
    const btnFilter = document.querySelector(".btn-filter");

    if (selectAnni) {
        selectAnni.addEventListener('change', disabilitaSelect);
    }

    if (btnFilter) {
        // Passa la funzione come callback (senza eseguire renderSpese())
        btnFilter.addEventListener("click", () => renderSpese());
    }
});
