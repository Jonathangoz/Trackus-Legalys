<style>

/* Variables de color */
:root {
  --primary-color: #2c3e50;
  --secondary-color: #3498db;
  --background-color: #f8f9fa;
  --text-color: #2c3e50;
  --success-color: #27ae60;
  --error-color: #e74c3c;

  --modal-max-width: 800px;
  --modal-padding: 1rem;
  --btn-radius: 4px;
}

/* Reset básico */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Segoe UI', sans-serif;
}

/* Modal */
.modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0,0,0,0.4);
  z-index: 9999;
  display: flex;
  justify-content: center;
  align-items: center;
  overflow-y: auto;
}

.modal-content {
  margin: auto;
  background: #fff;
  width: 57%;
  height: 93%;
  max-height: none;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  border-radius: 8px;
  overflow: auto;
}

/* Header del modal */
.modal-header {
  padding: var(--modal-padding);
  background: var(--primary-color);
  color: #fff;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.close-btn {
  cursor: pointer;
  font-size: 1.5rem;
}

.close-btn:hover { 
    transform: scale(1.1); 
}

/* Controles (mes, ver, nuevo) */
.calendar-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: var(--modal-padding);
  background: #fff;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.calendar-controls > div {
  display: flex;
  align-items: center;
}

.nav-btn, .nav-btn-1, .nav-btn-2 {
  padding: 0.5rem 1rem;
  border: none;
  background: var(--secondary-color);
  color: #fff;
  border-radius: var(--btn-radius);
  cursor: pointer;
  transition: opacity 0.2s;
}

.nav-btn-1 {
    margin-left: 23%;
}

.nav-btn:hover, .nav-btn-1:hover, .nav-btn-2 { 
    opacity: 0.9; 
}

/* Grid contenedor con scroll horizontal si falta espacio */
.calendar-grid {
  display: grid;
  grid-template-columns: repeat(7, minmax(80px,1fr));
  gap: 2px;
  background: #ddd;
  padding: 2px;
  flex: 1;               /* para hacer scroll vertical si es necesario */
  overflow-y: auto;
  overflow-x: auto;
}

.calendar-day-header {
  display: flex;
}

.calendar-day {
  display: flex;
  align-items: flex-start;
  background: #fff;
  min-height: 100px;
  padding: 0.5rem;
  margin-top: 1px;
  position: relative;
}

.day-number {
  font-weight: 650;
  margin-bottom: 0.5rem;
}

.events-container {
  max-height: calc(100% - 1.5rem);
  overflow-y: auto;
}

/* Formulario */
.event-form {
  padding: var(--modal-padding);
  background: #f1f1f1;
  border-top: 1px solid #ddd;
  overflow-y: auto;
  max-height: 40vh;
}

.form-group { 
    margin-bottom: 1rem; 
}

.form-group label { 
    display: block; margin-bottom: 0.3rem; 
}

.form-control {
  width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: var(--btn-radius);
}

.form-actions {
  display: flex; justify-content: flex-end; gap: 0.5rem;
}

.btn { 
    padding: 0.5rem 1rem; border-radius: var(--btn-radius); cursor: pointer; 
}

.btn-primary { 
    background: var(--secondary-color); color: #fff; 
}

.btn-cancel { 
    background: #95a5a6; color: #fff; 
}

/* Listado mensual */
#eventsListContainer {
  padding: var(--modal-padding);
  background: #fff;
  border-top: 1px solid #ddd;
  max-height: 30vh;
  overflow-y: auto;
}

.event-list-item {
  display: flex; justify-content: space-between; align-items: center;
  padding: 4px 8px; border-bottom: 1px solid #eee; font-size: 0.85rem;
}

.day-event-item { /* si sigues usando por día */
  display: flex; justify-content: space-between; align-items: center;
  padding: 4px 8px; border-bottom: 1px solid #eee; font-size: 0.85rem;
}

.event-list-item button,
.day-event-item button {
  background: none; border: none; cursor: pointer; margin-left: 4px;
}

/* Animaciones */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Media Queries más estrictos */
@media (max-width: 320px) {
  .modal {
    justify-content: center;
    align-items: center;
    overflow-y: auto;
  }
  .calendar-grid { grid-template-columns: repeat(7, minmax(60px,1fr)); }
  .calendar-day { min-height: 80px; padding: 0.3rem; }
  .day-number { font-size: 0.9rem; }
  .nav-btn, .nav-btn-1, .nav-btn-2 { padding: 0.4rem 0.8rem; font-size: 0.9rem; }

}

@media (max-width: 480px) {
  .modal-content {
    width: 95%; 
    max-width: none; 
    height: 100%; 
    max-height: none;
    border-radius: 0; 
    margin: 0;

  }
  .calendar-controls {
    flex-direction: column; align-items: stretch;
  }
  .nav-btn-1, .nav-btn {
    width: 40%; text-align: center;
  }
  .nav-btn-2 {
    width: 20%; text-align: center;
  }
  .calendar-grid { grid-template-columns: repeat(7, minmax(40px,1fr)); }
  .calendar-day { min-height: 60px; padding: 0.2rem; }
  .day-number { font-size: 0.8rem; }
  .event-form { max-height: 50vh; }
  #eventsListContainer { max-height: 40vh; }
}

@keyframes fadeOut {
from { opacity: 1; transform: translateY(0); }
to { opacity: 0; transform: translateY(-20px); }
}

.modal-content {
animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
from { opacity: 0; transform: translateY(-20px); }
to { opacity: 1; transform: translateY(0); }
}

/* Contenedor de eventos dentro de cada día */
.events-container {
  margin-top: 4px;
  max-height: 70px;
  overflow-y: auto;
}

/* Cada evento, con un fondo discreto */
.event-item {
  font-size: 0.7rem;
  margin-bottom: 2px;
  padding: 2px 4px;
  background: var(--secondary-color);
  color: white;
  border-radius: 3px;
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
}

.day-events {
  min-height: 80px;
}

.day-event-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 4px 8px;
  border-bottom: 1px solid #eee;
  font-size: 0.85rem;
}

.day-event-item button {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 0.9rem;
  margin-left: 4px;
}

#eventsListContainer h4 {
  margin-bottom: 0.5rem;
}

.event-list-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 4px 8px;
  border-bottom: 1px solid #eee;
  font-size: 0.85rem;
}

.event-list-item button {
  background: none;
  border: none;
  cursor: pointer;
  margin-left: 4px;
}
</style>

    <div class="main-content" style="margin-left: 50%;">
    <!-- Aquí tu contenido que necesita desplazamiento -->
    </div>

    <!-- Modal del calendario -->
    <div class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Calendario de Eventos</h3>
                <span class="close-btn">×</span>
            </div>
            
            <div class="calendar-controls">
                <div>
                    <button class="nav-btn-2" id="prevMonth">←</button>
                    <span id="currentMonth" style="margin: 0 1rem"></span>
                    <button class="nav-btn-2" id="nextMonth">→</button>
                </div>
                <button class="nav-btn-1" id="viewEvents">Ver Eventos</button>
                <button class="nav-btn" id="newEvent">Nuevo Evento</button>
            </div>

            <div class="calendar-grid" id="calendarGrid"></div>
            <!-- Contenedor para listar los eventos del día clicado -->
            <div id="eventsListContainer" style="display:none; padding:1rem; background:#fff; border-top:1px solid #ddd;"></div>

            <form id="eventForm" class="event-form" style="display: none">
                <input type="hidden" id="eventId" value="">
                <div class="form-group">
                    <label>Rol:</label>
                    <select id="eventRole" class="form-control">
                        <?php
                        $roles = ['Administrador', 'Editor', 'Colaborador', 'Cliente'];
                        foreach ($roles as $role) {
                            echo "<option value='$role'>$role</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Nombre del evento:</label>
                    <input type="text" id="eventName" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Fecha:</label>
                    <input type="date" id="eventDate" class="form-control" required>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-cancel" id="cancelEvent">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>