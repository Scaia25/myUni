/**
 * MyUniversity - Gestione Spesa Personale
 * Script di gestione interattiva
 */

document.addEventListener('DOMContentLoaded', () => {
  // === CONFIGURAZIONE E STATO ===
  const DEFAULT_BUDGET = 200.00;

  // Dati iniziali di fallback se il localStorage è vuoto
  const initialExpenses = [
    { id: 1, name: 'Spesa Esselunga', amount: 42.50, date: '2026-07-28', category: 'alimentari' },
    { id: 2, name: 'Detersivo piatti e spugne', amount: 8.00, date: '2026-07-26', category: 'casa' },
    { id: 3, name: 'Shampoo e bagnoschiuma', amount: 12.00, date: '2026-07-24', category: 'igiene' },
    { id: 4, name: 'Caffè e merendine', amount: 23.00, date: '2026-07-21', category: 'alimentari' }
  ];

  const initialTodos = [
    { id: 1, text: 'Latte d d\'avena', done: false },
    { id: 2, text: 'Pasta integrale', done: true },
    { id: 3, text: 'Carta casa', done: false }
  ];

  // Caricamento dallo storage locale o inizializzazione
  let expenses = JSON.parse(localStorage.getItem('myuni_expenses')) || initialExpenses;
  let todos = JSON.parse(localStorage.getItem('myuni_todos')) || initialTodos;

  // === RIFERIMENTI DOM ===
  const budgetValEl = document.getElementById('budget-value');
  const spentValEl = document.getElementById('spent-value');
  const leftValEl = document.getElementById('left-value');
  const progressBar = document.getElementById('progress-bar');
  const progressContainer = progressBar ? progressBar.parentElement : null;
  const progressLabel = document.getElementById('progress-label');

  const expenseForm = document.getElementById('expense-form');
  const expenseMsg = document.getElementById('expense-msg');
  const expenseRows = document.getElementById('expense-rows');

  const listForm = document.getElementById('list-form');
  const listInput = document.getElementById('list-input');
  const todoList = document.getElementById('todo-list');
  const todoMeta = document.getElementById('todo-meta');

  // Imposta la data di oggi come valore predefinito nell'input data
  const dateInput = document.getElementById('item-date');
  if (dateInput) {
    dateInput.value = new Date().toISOString().split('T')[0];
  }

  // === UTILITY ===
  function formatCurrency(amount) {
    return new Intl.NumberFormat('it-IT', {
      style: 'currency',
      currency: 'EUR'
    }).format(amount);
  }

  function formatDate(dateString) {
    if (!dateString) return '-';
    const [year, month, day] = dateString.split('-');
    return `${day}/${month}/${year}`;
  }

  function saveData() {
    localStorage.setItem('myuni_expenses', JSON.stringify(expenses));
    localStorage.setItem('myuni_todos', JSON.stringify(todos));
  }

  // === RENDERING & LOGICA ===

  // 1. Calcolo Budget e Progress Bar
  function updateBudgetUI() {
    const totalSpent = expenses.reduce((acc, curr) => acc + Number(curr.amount), 0);
    const remaining = DEFAULT_BUDGET - totalSpent;
    const percentage = Math.min(Math.round((totalSpent / DEFAULT_BUDGET) * 100), 100);

    budgetValEl.textContent = formatCurrency(DEFAULT_BUDGET);
    spentValEl.textContent = formatCurrency(totalSpent);
    leftValEl.textContent = formatCurrency(remaining);

    // Gestione visuale superamento budget
    const cardLeft = leftValEl.closest('.card');
    if (remaining < 0) {
      cardLeft?.classList.add('is-over');
      progressContainer?.classList.add('is-over');
    } else {
      cardLeft?.classList.remove('is-over');
      progressContainer?.classList.remove('is-over');
    }

    if (progressBar) {
      progressBar.style.width = `${percentage}%`;
      progressBar.parentElement.setAttribute('aria-valuenow', percentage);
    }

    if (progressLabel) {
      progressLabel.textContent = `Utilizzato il ${percentage}% del budget mensile.`;
    }
  }

  // 2. Render Tabella Spese
  function renderExpenses() {
    if (!expenseRows) return;
    expenseRows.innerHTML = '';

    // Ordina per data più recente
    const sortedExpenses = [...expenses].sort((a, b) => new Date(b.date) - new Date(a.date));

    if (sortedExpenses.length === 0) {
      expenseRows.innerHTML = `<tr><td colspan="4" style="text-align:center; color: var(--ink-soft);">Nessuna spesa registrata.</td></tr>`;
      return;
    }

    sortedExpenses.forEach(item => {
      const tr = document.createElement('tr');
      
      const tagClass = item.category ? `tag--${item.category}` : 'tag--altro';

      tr.innerHTML = `
        <td>${formatDate(item.date)}</td>
        <td><strong>${escapeHTML(item.name)}</strong></td>
        <td><span class="tag ${tagClass}">${escapeHTML(item.category)}</span></td>
        <td class="right">${formatCurrency(item.amount)}</td>
      `;
      expenseRows.appendChild(tr);
    });
  }

  // 3. Render Lista della Spesa (Todo)
  function renderTodos() {
    if (!todoList) return;
    todoList.innerHTML = '';

    todos.forEach(todo => {
      const li = document.createElement('li');
      if (todo.done) li.classList.add('is-done');

      li.innerHTML = `
        <input type="checkbox" id="todo-${todo.id}" ${todo.done ? 'checked' : ''}>
        <label for="todo-${todo.id}">${escapeHTML(todo.text)}</label>
        <button class="todo__remove" aria-label="Rimuovi elemento">&times;</button>
      `;

      // Evento Checkbox
      const checkbox = li.querySelector('input[type="checkbox"]');
      checkbox.addEventListener('change', () => {
        todo.done = checkbox.checked;
        saveData();
        renderTodos();
      });

      // Evento Elimina
      const removeBtn = li.querySelector('.todo__remove');
      removeBtn.addEventListener('click', () => {
        todos = todos.filter(t => t.id !== todo.id);
        saveData();
        renderTodos();
      });

      todoList.appendChild(li);
    });

    // Meta-info lista
    if (todoMeta) {
      const remainingCount = todos.filter(t => !t.done).length;
      todoMeta.textContent = `${remainingCount} elementi ancora da acquistare.`;
    }
  }

  // === GESTIONE EVENTI FORM ===

  // Aggiunta Nuova Spesa
  if (expenseForm) {
    expenseForm.addEventListener('submit', (e) => {
      e.preventDefault();

      const nameInput = document.getElementById('item-name');
      const amountInput = document.getElementById('item-amount');
      const categoryInput = document.getElementById('item-category');

      const name = nameInput.value.trim();
      const amount = parseFloat(amountInput.value);
      const date = dateInput.value || new Date().toISOString().split('T')[0];
      const category = categoryInput.value;

      if (!name || isNaN(amount) || amount <= 0) {
        showFormMsg('Inserisci un nome valido e un importo maggiore di 0.', true);
        return;
      }

      const newExpense = {
        id: Date.now(),
        name,
        amount,
        date,
        category
      };

      expenses.push(newExpense);
      saveData();

      // Reset Form
      nameInput.value = '';
      amountInput.value = '';
      showFormMsg('Spesa salvata con successo!');

      // Re-render
      updateBudgetUI();
      renderExpenses();
    });
  }

  // Aggiunta Elemento Lista Spesa
  if (listForm) {
    listForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const text = listInput.value.trim();

      if (!text) return;

      todos.push({
        id: Date.now(),
        text,
        done: false
      });

      saveData();
      renderTodos();
      listInput.value = '';
    });
  }

  // Message Helper
  function showFormMsg(msg, isError = false) {
    if (!expenseMsg) return;
    expenseMsg.textContent = msg;
    expenseMsg.className = `form-msg ${isError ? 'is-error' : ''}`;
    
    setTimeout(() => {
      expenseMsg.textContent = '';
    }, 3000);
  }

  // Security Helper per prevenire XSS negli input utente
  function escapeHTML(str) {
    return str.replace(/[&<>'"]/g, 
      tag => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        "'": '&#39;',
        '"': '&quot;'
      }[tag] || tag)
    );
  }

  // === INIZIALIZZAZIONE AVVIO ===
  updateBudgetUI();
  renderExpenses();
  renderTodos();
});