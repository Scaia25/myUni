/**
 * Script essenziale per soli effetti ed interattività CSS/DOM
 */

document.addEventListener('DOMContentLoaded', () => {

  // 1. Spunta e Rimuovi elementi Checklist
  const todoList = document.getElementById('todo-list');
  if (todoList) {
    todoList.addEventListener('change', (e) => {
      if (e.target.matches('input[type="checkbox"]')) {
        const li = e.target.closest('li');
        if (li) li.classList.toggle('done', e.target.checked);
        updateMeta();
      }
    });

    todoList.addEventListener('click', (e) => {
      if (e.target.classList.contains('btn-remove')) {
        const li = e.target.closest('li');
        if (li) {
          li.style.opacity = '0';
          li.style.transition = '0.2s';
          setTimeout(() => {
            li.remove();
            updateMeta();
          }, 200);
        }
      }
    });
  }

  function updateMeta() {
    const meta = document.getElementById('todo-meta');
    if (!meta) return;
    const remaining = todoList.querySelectorAll('li:not(.done)').length;
    meta.textContent = `${remaining} elementi ancora da acquistare.`;
  }

  // 2. Animazione Aggiunta Elemento Checklist
  const listForm = document.getElementById('list-form');
  const listInput = document.getElementById('list-input');

  if (listForm && todoList) {
    listForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const text = listInput.value.trim();
      if (!text) return;

      const newId = 'todo-' + Date.now();
      const li = document.createElement('li');
      li.style.opacity = '0';
      li.style.transition = '0.2s';

      li.innerHTML = `
        <input type="checkbox" id="${newId}">
        <label for="${newId}">${escapeHTML(text)}</label>
        <button class="btn-remove">&times;</button>
      `;

      todoList.appendChild(li);
      requestAnimationFrame(() => li.style.opacity = '1');

      listInput.value = '';
      updateMeta();
    });
  }

  // 3. Feedback invio form nuova spesa
  const expenseForm = document.getElementById('expense-form');
  const expenseMsg = document.getElementById('expense-msg');

  if (expenseForm) {
    expenseForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const name = document.getElementById('item-name').value.trim();
      const amount = document.getElementById('item-amount').value;

      if (!name || !amount || amount <= 0) {
        showMsg('Dati inseriti non validi.', true);
        return;
      }

      showMsg('Spesa salvata!');
      expenseForm.reset();
    });
  }

  function showMsg(msg, isError = false) {
    if (!expenseMsg) return;
    expenseMsg.textContent = msg;
    expenseMsg.style.color = isError ? '#e11d48' : '#16a34a';
    setTimeout(() => expenseMsg.textContent = '', 3000);
  }

  function escapeHTML(str) {
    return str.replace(/[&<>'"]/g, tag => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;'
    }[tag] || tag));
  }
});