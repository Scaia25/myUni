/* rimuove bordo dalla tabella dell'ultime 5 spese dall'ultima spesa */
function aggiornaBordiTabella() {
  const righe = document.querySelectorAll('.table .riga-spesa');

  righe.forEach(riga => riga.classList.remove('no-border'));

  const ultimaRiga = righe[righe.length - 1];
  ultimaRiga.classList.add('no-border');
}

aggiornaBordiTabella();