document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('calendarTrigger').addEventListener('click', loadCalendarModal);
});

function loadCalendarModal(e) {
  // Si fuera un <a>, también haríamos e.preventDefault() aquí
  fetch('../public/calendar-event.php')
    .then(res => res.text())
    .then(html => {
      const container = document.getElementById('calendarModalContainer');
      container.innerHTML = html;
      const modal = container.querySelector('.modal');
      const closeBtn = container.querySelector('.close-btn');

      // Mostrar modal
      modal.style.display = 'block';

      // Cerrar al hacer clic en la X
      closeBtn.addEventListener('click', () => container.innerHTML = '');

      // Cerrar al hacer clic fuera del contenido
      modal.addEventListener('click', ev => {
        if (ev.target === modal) container.innerHTML = '';
      });

      initializeCalendar();
    })
    .catch(console.error);
}

let currentDate = new Date();
let calendarGrid, eventForm;

function initializeCalendar() {
  calendarGrid = document.getElementById('calendarGrid');
  eventForm   = document.getElementById('eventForm');

  // Fecha por defecto
  document.getElementById('eventDate').valueAsDate = new Date();

  // Botones de navegación
  document.getElementById('prevMonth').addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    generateCalendar(currentDate);
  });
  document.getElementById('nextMonth').addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    generateCalendar(currentDate);
  });

  document.getElementById('viewEvents').addEventListener('click', toggleEventsList);
  generateCalendar(currentDate);

  // Mostrar/ocultar formulario
  document.getElementById('newEvent').addEventListener('click', () => {
    eventForm.style.display = 'block';
  });
  document.getElementById('cancelEvent').addEventListener('click', () => {
    eventForm.style.display = 'none';
  });

  // Envío de formulario
    eventForm.addEventListener('submit', function(ev) {
      ev.preventDefault();
      const idField = document.getElementById('eventId').value;
      const data = {
        role: document.getElementById('eventRole').value,
        name: document.getElementById('eventName').value.trim(),
        date: document.getElementById('eventDate').value
      };
      if (!data.name || !data.date) {
        return alert('Complete todos los campos');
      }

    // Guardar en SessionStorage (o aquí podrías hacer AJAX a tu PHP)
  let all = JSON.parse(sessionStorage.getItem('calendarEvents') || '[]');

  if (idField) {
    // --- MODO EDITAR ---
    const eid = Number(idField);
    all = all.map(x => x.id === eid ? { ...x, role: data.role, name: data.name, date: data.date } : x);
  } else {
    // --- MODO NUEVO ---
    all.push({ id: Date.now(), ...data });
  }

    // Guardo y limpio form
      sessionStorage.setItem('calendarEvents', JSON.stringify(all));
      eventForm.reset();
      document.getElementById('eventId').value = '';
      eventForm.style.display = 'none';

    // Refrescos UI
      generateCalendar(currentDate);
      const listCont = document.getElementById('eventsListContainer');
      if (listCont && listCont.style.display === 'block') {
        showEventsList();
      }
      });

  // Primera carga
  generateCalendar(currentDate);
}

function toggleEventsList() {
  const cont = document.getElementById('eventsListContainer');
  if (cont.style.display === 'block') {
    cont.style.display = 'none';
    return;
  }
  showEventsList();
}

// Vuelca todos los eventos del mes en el contenedor
function showEventsList() {
  const cont = document.getElementById('eventsListContainer');
  cont.innerHTML = '';            // limpio
  cont.style.display = 'block';   // muestro

  const year  = currentDate.getFullYear();
  const month = currentDate.getMonth();
  const monthName = currentDate.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' });

  cont.innerHTML = `<h4>Eventos de ${monthName.toUpperCase()}</h4>`;

  // Recupero y filtro
  const all = JSON.parse(sessionStorage.getItem('calendarEvents') || '[]')
    .filter(ev => {
      const d = new Date(ev.date);
      return d.getFullYear() === year && d.getMonth() === month;
    });

  if (all.length === 0) {
    cont.innerHTML += `<p><em>No hay eventos este mes.</em></p>`;
    return;
  }

  all.forEach(ev => {
    const item = document.createElement('div');
    item.className = 'event-list-item';
    item.innerHTML = `
      <span><strong>${ev.date}</strong> — ${ev.role}: ${ev.name}</span>
      <span>
        <button class="edit-event-btn" data-id="${ev.id}">✏️</button>
        <button class="delete-event-btn" data-id="${ev.id}">🗑️</button>
      </span>
    `;
    cont.appendChild(item);
  });

  // Botones editar/borrar
  cont.querySelectorAll('.edit-event-btn')
      .forEach(b => b.addEventListener('click', () => editEvent(b.dataset.id)));
  cont.querySelectorAll('.delete-event-btn')
      .forEach(b => b.addEventListener('click', () => {
        // tras borrar, vuelvo a pintar ambos views
        deleteEvent(b.dataset.id);
        showEventsList();
      }));
}

function deleteEvent(id) {
  // 1) Elimino del storage
  let all = JSON.parse(sessionStorage.getItem('calendarEvents') || '[]');
  all = all.filter(x => String(x.id) !== String(id));
  sessionStorage.setItem('calendarEvents', JSON.stringify(all));

  // 2) Refresco calendario
  generateCalendar(currentDate);

  // 3) Si el listado de mes está visible, lo actualizo
  const listCont = document.getElementById('eventsListContainer');
  if (listCont && listCont.style.display === 'block') {
    showEventsList();
  }
}

function editEvent(id) {
  const all = JSON.parse(sessionStorage.getItem('calendarEvents') || '[]');
  const ev  = all.find(x => String(x.id) === String(id));
  if (!ev) return;

  // Relleno el hidden y el resto de campos
  document.getElementById('eventId').value   = ev.id;
  document.getElementById('eventRole').value = ev.role;
  document.getElementById('eventName').value = ev.name;
  document.getElementById('eventDate').value = ev.date;

  // Muestro el form
  eventForm.style.display = 'block';

  // (Opcional) si quieres ocultar el listado cuando editas:
  // document.getElementById('eventsListContainer').style.display = 'none';
} 

function generateCalendar(date) {
  calendarGrid.innerHTML = '';
  const year  = date.getFullYear();
  const month = date.getMonth();
  const monthName = date.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' });

  document.getElementById('currentMonth').textContent = monthName.toUpperCase();

  const firstDay = new Date(year, month, 1).getDay();
  const lastDay  = new Date(year, month + 1, 0).getDate();

  // Cabeceras
  'Dom,Lun,Mar,Mié,Jue,Vie,Sáb'.split(',').forEach(d => {
    const dh = document.createElement('div');
    dh.className = 'calendar-day-header';
    dh.textContent = d;
    calendarGrid.appendChild(dh);
  });

  // Huecos iniciales
  for (let i = 0; i < firstDay; i++) {
    calendarGrid.appendChild(createDayElement(''));
  }
  // Días
  for (let d = 1; d <= lastDay; d++) {
    calendarGrid.appendChild(createDayElement(d));
  }

  loadEvents();
}

function createDayElement(day) {
  const div = document.createElement('div');
  div.className = 'calendar-day';
  if (day) {
    div.innerHTML = `
      <div class="day-number">${day}</div>
      <div class="events-container"></div>
    `;
  }
  return div;
}

function loadEvents() {
  // Vaciar contenedores anteriores
  calendarGrid
    .querySelectorAll('.events-container')
    .forEach(ec => ec.innerHTML = '');

  const events = JSON.parse(sessionStorage.getItem('calendarEvents') || '[]');
  const year  = currentDate.getFullYear();
  const month = currentDate.getMonth();

  events.forEach(ev => {
    const evDate = new Date(ev.date);
    if (evDate.getFullYear() === year && evDate.getMonth() === month) {
      const dayNum = evDate.getDate();
      // Buscamos la casilla cuyo .day-number coincide
      const dayCell = Array.from(calendarGrid.querySelectorAll('.calendar-day'))
        .find(c => c.querySelector('.day-number')?.textContent == dayNum);
      if (dayCell) {
        const container = dayCell.querySelector('.events-container');
        const evItem = document.createElement('div');
        evItem.className = 'event-item';
        evItem.textContent = `${ev.role}: ${ev.name}`;
        container.appendChild(evItem);
      }
    }
  });
}