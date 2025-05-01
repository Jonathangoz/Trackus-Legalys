document.addEventListener('DOMContentLoaded', () => {
    let currentDate = new Date();
    const calendarGrid = document.getElementById('calendarGrid');
    const modal = document.getElementById('calendarModal');
    const eventForm = document.getElementById('eventForm');

    // Control del modal
    document.getElementById('openCalendar').addEventListener('click', (e) => {
        e.preventDefault();
        modal.style.display = 'block';
        generateCalendar(currentDate);
    });

    document.getElementById('closeModal').addEventListener('click', () => {
        modal.style.display = 'none';
    });

    window.onclick = (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    };

    document.body.addEventListener('click', (e) => {
        if (e.target.matches('#prevMonth')) {
        // Lógica para mes anterior
        }
        if (e.target.matches('#nextMonth')) {
        // Lógica para mes siguiente
        }
    });

    // Navegación del calendario
    document.getElementById('prevMonth').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        generateCalendar(currentDate);
    });

    document.getElementById('nextMonth').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        generateCalendar(currentDate);
    });

    

    // Formulario de eventos
    document.getElementById('newEvent').addEventListener('click', () => {
        eventForm.style.display = 'block';
        document.getElementById('eventDate').valueAsDate = new Date();
    });

    document.getElementById('cancelEvent').addEventListener('click', () => {
        eventForm.style.display = 'none';
    });

    // Generar calendario
    function generateCalendar(date) {
        calendarGrid.innerHTML = '';
        const year = date.getFullYear();
        const month = date.getMonth();
        
        // Cabecera del mes
        document.getElementById('currentMonth').textContent = 
            `${date.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' })}`.toUpperCase();

        // Generar días
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        
        // Días vacíos iniciales
        for (let i = 0; i < firstDay.getDay(); i++) {
            calendarGrid.appendChild(createDayElement(''));
        }

        // Días del mes
        for (let day = 1; day <= lastDay.getDate(); day++) {
            calendarGrid.appendChild(createDayElement(day));
        }

        loadEvents();
    }

    function createDayElement(day) {
        const div = document.createElement('div');
        div.className = 'calendar-day';
        if (day) {
            div.innerHTML = `
                <div class="day-number">${day}</div>
                <div class="event-dots"></div>
            `;
        }
        return div;
    }

    // Guardar evento
    document.getElementById('eventForm').addEventListener('submit', (e) => {
        e.preventDefault();
        const eventData = {
            role: document.getElementById('eventRole').value,
            name: document.getElementById('eventName').value.trim(),
            date: document.getElementById('eventDate').value
        };

        if (!eventData.name || !eventData.date) {
            alert('Por favor complete todos los campos');
            return;
        }

        // Aquí iría la llamada AJAX para guardar
        console.log('Evento guardado:', eventData);
        eventForm.style.display = 'none';
        generateCalendar(currentDate);
    });

    function loadEvents() {
        // Implementar carga de eventos
    }

    // Inicializar calendario
    generateCalendar(currentDate);
});