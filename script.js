// Mostra la modale di errore
function showModalError(errorMessage) {
  const overlay = document.createElement('div');
  overlay.className = 'modal-overlay';

  overlay.innerHTML = `
        <div class="modal-box">
            <div class="modal-icon">!</div>
            <h3>Attenzione</h3>
            <p class="modal-text"></p>
            <button class="modal-btn" onclick="this.closest('.modal-overlay').remove()">Ho capito</button>
        </div>
    `;

  overlay.querySelector('.modal-text').textContent = errorMessage;
  document.body.appendChild(overlay);
}

/* Gestisce la stilizzazione visiva dell'ultima riga di tabella */
function aggiornaBordiTabella() {
  const righe = document.querySelectorAll('.table .riga-spesa');

  righe.forEach(riga => riga.classList.remove('no-border'));

  if (righe.length === 0) return;

  const ultimaRiga = righe[righe.length - 1];
  ultimaRiga.classList.add('no-border');
}

/* Recupera i dati dal server e aggiorna l'interfaccia utente */
async function caricaDashboard() {
  const header = document.querySelector(".header");
  kpi_title = document.querySelector(".kpi-title");
  budget_percentuale = document.querySelector(".badge.badge-lime");
  budget_rimasto = document.getElementById("budget_rimasto");
  budget_mensile = document.getElementById("budget_mensile");
  segments = document.querySelector(".segments");
  const card_header = document.querySelector(".card-header");
  const chart_legend = document.querySelector(".chart-legend");
  const contenitore = document.getElementById("tabella-spese");

  const oggi = new Date();
  const meseCorrente = oggi.toLocaleDateString('it-IT', { month: 'long' });

  kpi_title.textContent = "Budget mensile rimanente (" + meseCorrente + ")";
  headerRiepilogoMensile = document.createElement("h2");

  const euroFormatter = new Intl.NumberFormat("it-IT", { style: "currency", currency: "EUR" });

  try {
    // Richiesta dati utente e spese dal backend
    const responseSpeseMensili = await fetch("backend/get_spese_mensili.php");
    const resultSpeseMensili = await responseSpeseMensili.json();

    const responseUtente = await fetch("backend/get_utente.php");
    const resultUtente = await responseUtente.json();

    const responseCategorie = await fetch("backend/get_categorie.php");
    const resultCategorie = await responseCategorie.json();

    if (resultUtente.status === "success" && resultSpeseMensili.status === "success") {
      const utente = resultUtente.data;

      nomeUtente = document.createElement("h1");
      nomeUtente.textContent = "Gestione spese di " + utente.nome;
      header.appendChild(nomeUtente);

      budget_mensile.textContent = "/ " + euroFormatter.format(utente.budget_mensile);

      // Calcolo totale spese e budget rimanente
      let spesaTotaleMensile = 0;
      resultSpeseMensili.data.forEach(spesa => {
        spesaTotaleMensile += Number(spesa.importo);
      });

      budget_rimasto_numerico = utente.budget_mensile - spesaTotaleMensile.toFixed(2);
      budget_percentuale_numerica = Math.round(budget_rimasto_numerico / utente.budget_mensile * 1000) / 10;
      budget_rimasto.textContent = euroFormatter.format(budget_rimasto_numerico);

      // Aggiornamento indicatore grafico del budget
      palliniPieni = Math.min(Math.max(Math.round(budget_percentuale_numerica / 5), 0), 20);
      budget_percentuale.textContent = budget_percentuale_numerica + "%";
      segments.innerHTML = "<span class='seg filled'></span>".repeat(palliniPieni) + "<span class='seg'></span>".repeat(20 - palliniPieni);

      //aggiornamento grafico delle spese mensili diviso per categoria
      headerRiepilogoMensile.innerHTML = "Spese nel mese di " + meseCorrente + "<br> (" + euroFormatter.format(spesaTotaleMensile) + ")";
      card_header.appendChild(headerRiepilogoMensile);

      resultCategorie.data.forEach(categoria => {

        let spesaCategoria = 0;
        resultSpeseMensili.data.forEach(spesa => {
          if (spesa.id_categoria === categoria.ID)
            spesaCategoria += spesa.importo;
        }
        )

        divGrafico = document.createElement("div");
        divGrafico.className = "grafico grafico-" + categoria.ID;

        legend_item = document.createElement("span");
        legend_item.className = "legend-item";

        dot = document.createElement("span");
        dot.className = "dot dot-" + categoria.ID;

        legend_item.appendChild(dot);
        legend_item.appendChild(document.createTextNode(categoria.denominazione + " (" + euroFormatter.format(spesaCategoria) + ")"));

        spaziatura = document.createElement("div");
        spaziatura.className = "spaziatura";

        span = document.createElement("span");

        barra_traccia = document.createElement("div");
        barra_traccia.className = "barra-traccia";

        barra = document.createElement("div");
        barra.id = "barra-" + categoria.ID;
        barra.className = "barra";

        if (spesaCategoria > 0) {
          percentualeBarra = (spesaCategoria / utente.budget_mensile) * 100
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

      // Popolamento della tabella con le ultime 5 spese
      contenitore.innerHTML = '<tr id="titoli-colonne"><th class="date">Data</th><th class="amount">Importo</th><th class="category">Categoria</th></tr>';

      let i = 0;
      resultSpeseMensili.data.forEach(spesa => {
        if (i >= 5) {
          return;
        }
        const rigaSpesa = document.createElement("tr");
        rigaSpesa.className = "riga-spesa";

        const dataSpesa = document.createElement("td");
        dataSpesa.className = "date";
        const dataObj = new Date(spesa.data + "T00:00:00");
        spesa.data = Intl.DateTimeFormat('it-IT').format(dataObj);
        dataSpesa.textContent = spesa.data;

        const importoSpesa = document.createElement("td");
        importoSpesa.className = "amount";
        spesa.importo = new Intl.NumberFormat("it-IT", { style: "currency", currency: "EUR" }).format(spesa.importo);
        importoSpesa.textContent = spesa.importo;

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

      if (resultSpeseMensili.data.length === 0) {
        contenitore.textContent = "<p style='margin-top: 20px; text-align: center;'>Nessuna spesa registrata</p>";
      }

      aggiornaBordiTabella();
    }
  } catch (error) {
    console.error('Errore durante la fetch:', error);
  }
}

//Caricamento spesa nel db
const formSpesa = document.getElementById("expense-form")
if (formSpesa) {
  formSpesa.addEventListener("submit", async function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    try {
      const responseForm = await fetch("backend/registra_spesa.php", {
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


// Avvio iniziale
caricaDashboard();