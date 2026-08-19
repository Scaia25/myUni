// Mostra la modale di errore
function showModalError(errorMessage) {
  const overlay = document.createElement('div');
  overlay.className = 'modal-overlay';

  const isAuthError = errorMessage === "Utente non autenticato";

  let buttonHtml = "";

  if (isAuthError) {
    buttonHtml = `<button class="modal-btn" onclick="this.closest('.modal-overlay').remove(); window.location.href = 'index.php';">Riaccedi</button>`;
  } else {
    buttonHtml = `<button class="modal-btn" onclick="this.closest('.modal-overlay').remove();">Ho capito</button>`;
  }

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

// Scorrimento btn aggiungi spesa verso la section
const aggiungiSpesaBtn = document.querySelector(".sidebar-btn");
const aggiungiSpesaSection = document.getElementById("aggiungi");

aggiungiSpesaBtn.addEventListener('click', () => {
  aggiungiSpesaSection.scrollIntoView({
    behavior: "smooth",
    block: "center"
  });
});


/* Gestisce la stilizzazione visiva dell'ultima riga di tabella */
function aggiornaBordiTabella() {
  const righe = document.querySelectorAll('.table .riga-spesa');

  righe.forEach(riga => riga.classList.remove('no-border'));

  if (righe.length === 0) return;

  const ultimaRiga = righe[righe.length - 1];
  ultimaRiga.classList.add('no-border');
}

/* Aggiorna il testo del contatore della lista della spesa analizzando il DOM */
function aggiornaContatoreTodo() {
  const checkboxes = document.querySelectorAll('.todo-checkbox');
  let articoliRimanenti = 0;

  checkboxes.forEach(cb => {
    if (!cb.checked) {
      articoliRimanenti++;
    }
  });

  const todo_meta = document.getElementById("todo-meta");
  if (!todo_meta) return;

  if (articoliRimanenti > 1) {
    todo_meta.textContent = articoliRimanenti + " articoli ancora da acquistare";
  } else if (articoliRimanenti === 1) {
    todo_meta.textContent = articoliRimanenti + " articolo ancora da acquistare";
  } else {
    todo_meta.textContent = "Nessun articolo da acquistare";
  }
}

/* Sottofunzioni di rendering per la Dashboard */

function renderHeader(utente) {
  const header = document.querySelector(".header");
  if (!header) return;

  header.querySelectorAll("h1").forEach(el => el.remove());
  const nomeUtente = document.createElement("h1");
  nomeUtente.textContent = "Gestione spese di " + utente.nome;
  header.appendChild(nomeUtente);
}

function renderKPIBudget(utente, spesaTotaleMensile, meseCorrente, euroFormatter) {
  const kpi_title = document.querySelector(".kpi-title");
  const budget_percentuale = document.querySelector(".badge.badge-lime");
  const budget_rimasto = document.getElementById("budget_rimasto");
  const budget_mensile = document.getElementById("budget_mensile");
  const segments = document.querySelector(".segments");

  if (kpi_title) {
    kpi_title.textContent = "Budget mensile rimanente (" + meseCorrente + ")";
  }

  if (budget_mensile) {
    budget_mensile.textContent = "/ " + euroFormatter.format(utente.budget_mensile);
  }

  const budget_rimasto_numerico = utente.budget_mensile - Number(spesaTotaleMensile.toFixed(2));
  const budget_percentuale_numerica = Math.round(budget_rimasto_numerico / utente.budget_mensile * 1000) / 10;

  if (budget_rimasto) {
    budget_rimasto.textContent = euroFormatter.format(budget_rimasto_numerico);
  }

  // Generazione dei 20 pallini del budget rimasto
  segments.innerHTML = "<span class='seg'></span>".repeat(20);

  const palliniPieni = Math.min(Math.max(Math.round(budget_percentuale_numerica / 5), 0), 20);
  if (budget_percentuale) {
    budget_percentuale.textContent = budget_percentuale_numerica + "%";
  }
  if (segments) {
    const pallini = document.querySelectorAll('.segments > *');

    setTimeout(() => {
      pallini.forEach((pallino, index) => {
        if (index < palliniPieni) {
          // Passa l'indice al CSS per creare l'effetto progressivo ad onda
          pallino.style.setProperty('--i', index);
          pallino.classList.add("filled");
        }
      });
    }, 50);
  }
}

function renderGraficoCategorie(categorie, speseMensili, utente, spesaTotaleMensile, meseCorrente, euroFormatter) {
  const card_header = document.querySelector(".card-header");
  const chart_legend = document.querySelector(".chart-legend");

  if (card_header) {
    card_header.querySelectorAll("h2").forEach(el => el.remove());
    const headerRiepilogoMensile = document.createElement("h2");
    headerRiepilogoMensile.innerHTML = "Spese nel mese di " + meseCorrente + "<br> (" + euroFormatter.format(spesaTotaleMensile) + ")";
    card_header.appendChild(headerRiepilogoMensile);
  }

  if (chart_legend) {
    chart_legend.replaceChildren();

    categorie.forEach(categoria => {
      let spesaCategoria = 0;
      speseMensili.forEach(spesa => {
        if (spesa.id_categoria === categoria.ID)
          spesaCategoria += Number(spesa.importo);
      });

      const divGrafico = document.createElement("div");
      divGrafico.className = "grafico grafico-" + categoria.ID;

      const legend_item = document.createElement("span");
      legend_item.className = "legend-item";

      const dot = document.createElement("span");
      dot.className = "dot dot-" + categoria.ID;

      legend_item.appendChild(dot);
      legend_item.appendChild(document.createTextNode(categoria.denominazione + " (" + euroFormatter.format(spesaCategoria) + ")"));

      const spaziatura = document.createElement("div");
      spaziatura.className = "spaziatura";

      const span = document.createElement("span");

      const barra_traccia = document.createElement("div");
      barra_traccia.className = "barra-traccia";

      const barra = document.createElement("div");
      barra.id = "barra-" + categoria.ID;
      barra.className = "barra";

      if (spesaCategoria > 0) {
        const percentualeBarra = (spesaCategoria / utente.budget_mensile) * 100;
        barra.style.width = percentualeBarra + "%";
      } else {
        barra.style.width = "1%";
      }

      barra_traccia.appendChild(barra);
      spaziatura.appendChild(span);
      spaziatura.appendChild(barra_traccia);

      divGrafico.appendChild(legend_item);
      divGrafico.appendChild(spaziatura);

      chart_legend.appendChild(divGrafico);
    });
  }
}

function caricaCategorie(categorie) {
  const contenitore = document.getElementById("item-category");
  if (contenitore.options.length > 0) return;

  contenitore.replaceChildren();
  categorie.forEach(categoria => {
    const option = new Option(categoria.denominazione, categoria.ID);
    contenitore.add(option);
  });
}

function renderTabellaSpese(speseMensili, euroFormatter) {
  const contenitore = document.getElementById("tabella-spese");
  if (!contenitore) return;

  contenitore.innerHTML = '<tr id="titoli-colonne"><th class="date">Data</th><th class="amount">Importo</th><th class="category">Categoria</th></tr>';

  if (speseMensili.length === 0) {
    contenitore.innerHTML += "<tr><td colspan='3' style='margin-top: 20px; text-align: center;'>Nessuna spesa registrata</td></tr>";
  } else {
    let i = 0;
    speseMensili.forEach(spesa => {
      if (i >= 5) return;

      const rigaSpesa = document.createElement("tr");
      rigaSpesa.className = "riga-spesa";

      const dataSpesa = document.createElement("td");
      dataSpesa.className = "date";
      const dataObj = new Date(spesa.data + "T00:00:00");
      dataSpesa.textContent = Intl.DateTimeFormat('it-IT').format(dataObj);

      const importoSpesa = document.createElement("td");
      importoSpesa.className = "amount";
      importoSpesa.textContent = euroFormatter.format(spesa.importo);

      const categoriaSpesa = document.createElement("td");
      categoriaSpesa.className = "category";

      const tagSpan = document.createElement("span");
      tagSpan.className = "tag tag-" + spesa.id_categoria;
      tagSpan.textContent = spesa.denominazione;

      categoriaSpesa.appendChild(tagSpan);

      rigaSpesa.appendChild(dataSpesa);
      rigaSpesa.appendChild(importoSpesa);
      rigaSpesa.appendChild(categoriaSpesa);

      contenitore.appendChild(rigaSpesa);

      i++;
    });
  }

  aggiornaBordiTabella();
}

function renderListaSpesa(articoli) {
  const listaSpesa = document.getElementById("todo-list");
  if (!listaSpesa) return;

  listaSpesa.replaceChildren();

  articoli.forEach(articolo => {
    const rigaListaSpesa = document.createElement("li");

    const checkBoxArticolo = document.createElement("input");
    checkBoxArticolo.type = "checkbox";
    checkBoxArticolo.id = "todo-" + articolo.ID;
    checkBoxArticolo.className = "todo-checkbox";
    const isChecked = Number(articolo.checked) === 1;
    checkBoxArticolo.checked = isChecked;

    const descrizioneArticolo = document.createElement("label");
    descrizioneArticolo.htmlFor = "todo-" + articolo.ID;
    descrizioneArticolo.textContent = articolo.descrizione.charAt(0).toUpperCase() + articolo.descrizione.slice(1);

    if (isChecked) {
      descrizioneArticolo.style.textDecoration = "line-through";
    }

    const rimuoviArticolo = document.createElement("button");
    rimuoviArticolo.className = "btn-remove";
    rimuoviArticolo.id = "btn-remove-" + articolo.ID;
    rimuoviArticolo.innerHTML = "&times;";

    rigaListaSpesa.appendChild(checkBoxArticolo);
    rigaListaSpesa.appendChild(descrizioneArticolo);
    rigaListaSpesa.appendChild(rimuoviArticolo);

    listaSpesa.appendChild(rigaListaSpesa);
  });

  aggiornaContatoreTodo();
}

// render del tema d'interfaccia
function renderTemi(utente, temi) {
    const listaTemi = Array.isArray(temi) ? temi : (temi?.data || []);
    const idTemaUtente = Array.isArray(utente) ? utente[0]?.id_tema : utente?.id_tema;

    const temaAttivo = listaTemi.find(tema => 
        String(tema.id_tema ?? tema.ID ?? tema.id ?? tema.idTema) === String(idTemaUtente)
    );

    if (temaAttivo) {
        caricaTema(temaAttivo.colore);
    }
}

// Caricare il tema sull'intera interfaccia
function caricaTema(coloreTema) {
  if (coloreTema) {
    const hex = coloreTema.replace("#", "");
    document.documentElement.style.setProperty("--user-theme", "#" + hex);
  }
}

/* Recupera i dati dal server e aggiorna l'interfaccia utente */
async function caricaDashboard() {
  const oggi = new Date();
  const meseCorrente = oggi.toLocaleDateString('it-IT', { month: 'long' });
  const euroFormatter = new Intl.NumberFormat("it-IT", { style: "currency", currency: "EUR" });

  try {
    // Richiesta dati utente e spese dal backend
    const [responseSpeseMensili, responseUtente, responseCategorie, responseArticoli, responseTemi] = await Promise.all([
      fetch("backend/api/get_spese_mensili.php"),
      fetch("backend/api/get_utente.php"),
      fetch("backend/api/get_categorie.php"),
      fetch("backend/api/get_articoli.php"),
      fetch("/backend/api/get_temi.php")
    ]);

    const resultSpeseMensili = await responseSpeseMensili.json();
    const resultUtente = await responseUtente.json();
    const resultCategorie = await responseCategorie.json();
    const resultArticoli = await responseArticoli.json();
    const resultTemi = await responseTemi.json();

    if (resultUtente.status === "success" && resultSpeseMensili.status === "success" && resultCategorie.status === "success" && resultArticoli.status === "success" && resultTemi.status === "success") {
      const utente = resultUtente.data;
      const speseMensili = resultSpeseMensili.data;
      const categorie = resultCategorie.data;
      const articoli = resultArticoli.data;
      const temi = resultTemi.data;


      // Calcolo totale spese
      let spesaTotaleMensile = 0;
      speseMensili.forEach(spesa => {
        spesaTotaleMensile += Number(spesa.importo);
      });

      // Esecuzione rendering componenti
      renderHeader(utente);
      renderKPIBudget(utente, spesaTotaleMensile, meseCorrente, euroFormatter);
      renderGraficoCategorie(categorie, speseMensili, utente, spesaTotaleMensile, meseCorrente, euroFormatter);
      caricaCategorie(categorie);
      renderTabellaSpese(speseMensili, euroFormatter);
      renderListaSpesa(articoli);
      renderTemi(utente, temi);
    }
  } catch (error) {
    console.error('Errore durante la fetch:', error);
  }
}

// Caricamento spesa nel db
const formSpesa = document.getElementById("expense-form");
if (formSpesa) {
  formSpesa.addEventListener("submit", async function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    try {
      const responseForm = await fetch("backend/api/registra_spesa.php", {
        method: "POST",
        body: formData
      });

      const resultForm = await responseForm.json();

      if (resultForm.status === "success") {
        this.reset();
        await caricaDashboard();
      } else {
        showModalError(resultForm.message);
      }
    } catch (error) {
      console.error("Errore di connessione:", error);
      showModalError("Impossibile connettersi al server!");
    }
  });
}

// Aggiorna articolo spuntato SENZA invocare caricaDashboard() completa
document.addEventListener('change', async (event) => {
  if (event.target && event.target.classList.contains('todo-checkbox')) {
    const el = event.target;
    const id = el.id.split("-")[1];
    const isChecked = el.checked ? 1 : 0;

    // Aggiornamento grafico locale e immediato del testo
    const label = document.querySelector(`label[for="${el.id}"]`);
    if (label) {
      label.style.textDecoration = el.checked ? "line-through" : "none";
    }

    // Ricalcola il contatore in locale senza richieste di rete inutili
    aggiornaContatoreTodo();

    const formData = new FormData();
    formData.append('id', id);
    formData.append('checked', isChecked);

    try {
      const response = await fetch('backend/api/aggiorna_articolo.php', {
        method: "POST",
        body: formData
      });
      const result = await response.json();

      if (result.status !== "success") {
        showModalError(result.message || "Errore durante l'aggiornamento dell'articolo.");
      }
    } catch (error) {
      showModalError("Errore durante l'invio dei dati: " + error);
    }
  }
});

// Aggiunta articoli alla lista della spesa 
const formListaSpesa = document.getElementById("list-form");
if (formListaSpesa) {
  formListaSpesa.addEventListener("submit", async function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    try {
      const responseForm = await fetch("backend/api/registra_articoli.php", {
        method: "POST",
        body: formData
      });

      const resultForm = await responseForm.json();

      if (resultForm.status === "success") {
        this.reset();
        await caricaDashboard();
      } else {
        showModalError(resultForm.message);
      }
    } catch (error) {
      console.error("Errore di connessione:", error);
      showModalError("Impossibile connettersi al server!");
    }
  });
}

// Rimuvoi articolo elimanto SENZA invocare caricaDashboard() completa
document.addEventListener('click', async (event) => {
  if (event.target && event.target.classList.contains('btn-remove')) {
    const el = event.target;
    const id = el.id.split("-")[2];

    const formData = new FormData();
    formData.append('id', id);

    try {
      const response = await fetch('backend/api/rimuovi_articolo.php', {
        method: "POST",
        body: formData
      });
      const result = await response.json();

      if (result.status === "success") {
        const rigaDaRimuovere = el.closest("li");
        if (rigaDaRimuovere) {
          rigaDaRimuovere.classList.add("removing");

          // Attende il completamento dell'animazione (300ms) prima di rimuovere dal DOM
          setTimeout(() => {
            rigaDaRimuovere.remove();
            aggiornaContatoreTodo();
          }, 300);
        }
      } else {
        showModalError(result.message || "Errore durante l'eliminazione dell'articolo.");
      }

      // Ricalcola il contatore e della in locale senza richieste di rete inutili
      aggiornaContatoreTodo();
    } catch (error) {
      showModalError("Errore durante l'invio dei dati: " + error);
    }
  }
});

// Avvio iniziale
document.addEventListener("DOMContentLoaded", () => {
  caricaDashboard();
});