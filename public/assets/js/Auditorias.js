
// Base de datos simulada para almacenar los eventos de auditoría
let auditData = [
    {
        id: 1,
        fecha: new Date().toISOString(),
        usuario: 'admin001',
        tipo: 'acceso',
        expediente: 'CC-2024-001',
        descripcion: 'Acceso exitoso al sistema',
        estado: 'exitoso',
        ip: '192.168.1.50'
    },
    {
        id: 2,
        fecha: new Date(Date.now() - 3600000).toISOString(), // Hace 1 hora
        usuario: 'cobrador001',
        tipo: 'consulta',
        expediente: 'CC-2024-002',
        descripcion: 'Consulta de expediente para verificación de datos',
        estado: 'exitoso',
        ip: '192.168.1.45'
    },
    {
        id: 3,
        fecha: new Date(Date.now() - 7200000).toISOString(), // Hace 2 horas
        usuario: 'supervisor001',
        tipo: 'modificacion',
        expediente: 'CC-2024-003',
        descripcion: 'Actualización de monto de deuda',
        estado: 'exitoso',
        ip: '192.168.1.30'
    },
    {
        id: 4,
        fecha: new Date(Date.now() - 10800000).toISOString(), // Hace 3 horas
        usuario: 'admin001',
        tipo: 'acceso',
        expediente: 'N/A',
        descripcion: 'Intento de acceso fallido: credenciales incorrectas',
        estado: 'fallido',
        ip: '192.168.1.100'
    },
    {
        id: 5,
        fecha: new Date(Date.now() - 14400000).toISOString(), // Hace 4 horas
        usuario: 'cobrador001',
        tipo: 'pago',
        expediente: 'CC-2024-004',
        descripcion: 'Registro de pago parcial de $150.000',
        estado: 'exitoso',
        ip: '192.168.1.45'
    }
];

// Configuración de las fechas iniciales para los filtros de rango
const hoy = new Date();
document.getElementById('fechaFin').value = hoy.toISOString().split('T')[0]; // Establece la fecha de fin a hoy
const hace30Dias = new Date(hoy.getTime() - 30 * 24 * 60 * 60 * 1000); // Calcula la fecha de hace 30 días
document.getElementById('fechaInicio').value = hace30Dias.toISOString().split('T')[0]; // Establece la fecha de inicio

// Event listener para el formulario de registro de auditoría
document.getElementById('auditForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Previene el envío por defecto del formulario
    registrarEvento(); // Llama a la función para registrar el evento
});

/**
 * Registra un nuevo evento de auditoría en la base de datos simulada.
 * Recopila los datos del formulario, crea un nuevo objeto de evento,
 * lo añade al inicio del array auditData y actualiza la interfaz.
 */
function registrarEvento() {
    const nuevoEvento = {
        id: auditData.length + 1, // ID único para el nuevo evento
        fecha: new Date().toISOString(), // Fecha y hora actual en formato ISO
        usuario: document.getElementById('usuario').value, // Valor del campo usuario
        tipo: document.getElementById('tipoEvento').value, // Valor del campo tipo de evento
        expediente: document.getElementById('expediente').value || 'N/A', // Valor del campo expediente, o 'N/A' si está vacío
        descripcion: document.getElementById('descripcion').value, // Valor del campo descripción
        estado: 'exitoso', // Estado por defecto del evento
        ip: '192.168.1.' + Math.floor(Math.random() * 255) // IP aleatoria simulada
    };

    auditData.unshift(nuevoEvento); // Añade el nuevo evento al principio del array
    actualizarTablaAuditorias(); // Actualiza la tabla de auditorías
    actualizarTimeline(); // Actualiza la línea de tiempo
    actualizarEstadisticas(); // Actualiza las estadísticas
    
    // Limpiar el formulario después del registro
    document.getElementById('auditForm').reset();
    
    mostrarNotificacion('Evento registrado exitosamente', 'success'); // Muestra una notificación de éxito
}

/**
 * Actualiza la tabla de auditorías con los datos proporcionados.
 * Si no se proporcionan datos, utiliza el array auditData completo.
 * @param {Array} datos - Array de objetos de evento de auditoría.
 */
function actualizarTablaAuditorias(datos = auditData) {
    const tbody = document.getElementById('bodyAuditorias');
    tbody.innerHTML = ''; // Limpia el contenido actual de la tabla

    datos.forEach(evento => {
        const fila = document.createElement('tr'); // Crea una nueva fila de tabla
        const fecha = new Date(evento.fecha); // Convierte la fecha del evento a un objeto Date
        
        // Inserta el HTML de la fila con los datos del evento
        fila.innerHTML = `
            <td>${fecha.toLocaleDateString()} ${fecha.toLocaleTimeString()}</td>
            <td>${evento.usuario}</td>
            <td><span class="badge badge-${obtenerColorTipo(evento.tipo)}">${capitalizarPrimera(evento.tipo)}</span></td>
            <td>${evento.expediente}</td>
            <td>${evento.descripcion.substring(0, 50)}${evento.descripcion.length > 50 ? '...' : ''}</td>
            <td><span class="badge badge-${evento.estado === 'exitoso' ? 'success' : 'danger'}">${capitalizarPrimera(evento.estado)}</span></td>
            <td>
                <button class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" onclick="verDetalles(${evento.id})">Ver</button>
            </td>
        `;
        tbody.appendChild(fila); // Añade la fila al cuerpo de la tabla
    });
}

/**
 * Actualiza la línea de tiempo de actividades recientes.
 * Muestra los últimos 10 eventos de auditoría.
 */
function actualizarTimeline() {
    const timeline = document.getElementById('timelineActividades');
    timeline.innerHTML = ''; // Limpia el contenido actual del timeline

    const eventosRecientes = auditData.slice(0, 10); // Obtiene los 10 eventos más recientes
    
    eventosRecientes.forEach(evento => {
        const item = document.createElement('div'); // Crea un nuevo elemento para el timeline
        item.className = 'timeline-item'; // Asigna la clase CSS
        const fecha = new Date(evento.fecha); // Convierte la fecha del evento a un objeto Date
        
        // Inserta el HTML del elemento del timeline
        item.innerHTML = `
            <div class="timeline-content">
                <div class="timeline-date">${fecha.toLocaleDateString()} - ${fecha.toLocaleTimeString()}</div>
                <strong>${capitalizarPrimera(evento.tipo)}</strong> por ${evento.usuario}
                <br>
                <small>${evento.descripcion}</small>
                ${evento.expediente !== 'N/A' ? `<br><small>Expediente: ${evento.expediente}</small>` : ''}
            </div>
        `;
        timeline.appendChild(item); // Añade el elemento al timeline
    });
}

/**
 * Actualiza las estadísticas mostradas en el dashboard.
 * Calcula el total de procesos, actividades de hoy y alertas activas.
 */
function actualizarEstadisticas() {
    document.getElementById('totalProcesos').textContent = auditData.length; // Total de eventos
    
    const hoy = new Date();
    // Filtra los eventos que ocurrieron hoy
    const eventosHoy = auditData.filter(evento => {
        const fechaEvento = new Date(evento.fecha);
        return fechaEvento.toDateString() === hoy.toDateString();
    });
    document.getElementById('procesosHoy').textContent = eventosHoy.length; // Eventos de hoy
    
    // Filtra los accesos fallidos para contar como alertas
    const alertas = auditData.filter(evento => evento.tipo === 'acceso' && evento.estado !== 'exitoso');
    document.getElementById('alertasActivas').textContent = alertas.length + 7; // Suma 7 alertas base para simulación
}

/**
 * Aplica los filtros seleccionados por el usuario a los datos de auditoría
 * y actualiza la tabla.
 */
function aplicarFiltros() {
    const buscar = document.getElementById('buscarAuditoria').value.toLowerCase(); // Texto de búsqueda
    const fechaInicio = document.getElementById('fechaInicio').value; // Fecha de inicio del filtro
    const fechaFin = document.getElementById('fechaFin').value; // Fecha de fin del filtro
    const usuario = document.getElementById('filtroUsuario').value; // Usuario del filtro
    const tipo = document.getElementById('filtroTipo').value; // Tipo de evento del filtro

    // Filtra los datos de auditoría según los criterios
    let datosFiltrados = auditData.filter(evento => {
        let cumple = true; // Bandera para verificar si el evento cumple con los filtros

        // Filtro por texto de búsqueda
        if (buscar) {
            cumple = cumple && (
                evento.descripcion.toLowerCase().includes(buscar) ||
                evento.usuario.toLowerCase().includes(buscar) ||
                evento.expediente.toLowerCase().includes(buscar)
            );
        }

        // Filtro por fecha de inicio
        if (fechaInicio) {
            cumple = cumple && new Date(evento.fecha) >= new Date(fechaInicio);
        }

        // Filtro por fecha de fin (hasta el final del día)
        if (fechaFin) {
            cumple = cumple && new Date(evento.fecha) <= new Date(fechaFin + 'T23:59:59');
        }

        // Filtro por usuario
        if (usuario) {
            cumple = cumple && evento.usuario === usuario;
        }

        // Filtro por tipo de evento
        if (tipo) {
            cumple = cumple && evento.tipo === tipo;
        }

        return cumple; // Retorna true si el evento cumple con todos los filtros
    });

    actualizarTablaAuditorias(datosFiltrados); // Actualiza la tabla con los datos filtrados
    mostrarNotificacion(`Se encontraron ${datosFiltrados.length} registros`, 'info'); // Muestra una notificación
}

/**
 * Limpia todos los campos de filtro y restablece la tabla de auditorías a su estado original.
 */
function limpiarFiltros() {
    document.getElementById('buscarAuditoria').value = ''; // Limpia el campo de búsqueda
    document.getElementById('filtroUsuario').value = ''; // Limpia el filtro de usuario
    document.getElementById('filtroTipo').value = ''; // Limpia el filtro de tipo de evento
    // Restablece las fechas a los valores por defecto
    const hoy = new Date();
    document.getElementById('fechaFin').value = hoy.toISOString().split('T')[0];
    const hace30Dias = new Date(hoy.getTime() - 30 * 24 * 60 * 60 * 1000);
    document.getElementById('fechaInicio').value = hace30Dias.toISOString().split('T')[0];

    actualizarTablaAuditorias(); // Actualiza la tabla con todos los datos
    mostrarNotificacion('Filtros limpiados', 'info'); // Muestra una notificación
}

/**
 * Muestra los detalles de un evento de auditoría específico en un modal.
 * @param {number} id - El ID del evento de auditoría a mostrar.
 */
function verDetalles(id) {
    const evento = auditData.find(e => e.id === id); // Busca el evento por ID
    if (!evento) return; // Si no se encuentra el evento, sale de la función

    const fecha = new Date(evento.fecha); // Convierte la fecha del evento a un objeto Date
    const contenidoModal = document.getElementById('contenidoModal'); // Contenedor del contenido del modal
    
    // Inserta el HTML con los detalles del evento en el modal
    contenidoModal.innerHTML = `
        <div style="display: grid; gap: 1rem;">
            <div><strong>ID:</strong> ${evento.id}</div>
            <div><strong>Fecha:</strong> ${fecha.toLocaleDateString()} ${fecha.toLocaleTimeString()}</div>
            <div><strong>Usuario:</strong> ${evento.usuario}</div>
            <div><strong>Tipo de Evento:</strong> ${capitalizarPrimera(evento.tipo)}</div>
            <div><strong>Expediente:</strong> ${evento.expediente}</div>
            <div><strong>IP:</strong> ${evento.ip}</div>
            <div><strong>Estado:</strong> <span class="badge badge-${evento.estado === 'exitoso' ? 'success' : 'danger'}">${capitalizarPrimera(evento.estado)}</span></div>
            <div><strong>Descripción:</strong> ${evento.descripcion}</div>
        </div>
        <div style="margin-top: 2rem;">
            <button class="btn btn-primary" onclick="exportarEvento(${id})">Exportar</button>
            <button class="btn btn-secondary" onclick="cerrarModal()">Cerrar</button>
        </div>
    `;

    document.getElementById('modalDetalles').style.display = 'flex'; // Muestra el modal (usando flex para centrar)
}

/**
 * Cierra el modal de detalles de auditoría.
 */
function cerrarModal() {
    document.getElementById('modalDetalles').style.display = 'none'; // Oculta el modal
}

/**
 * Simula la generación de un reporte.
 * Muestra una notificación de proceso y luego de éxito.
 */
function generarReporte() {
    const tipo = document.getElementById('tipoReporte').value; // Tipo de reporte seleccionado
    const periodo = document.getElementById('periodoReporte').value; // Período del reporte
    const formato = document.getElementById('formatoReporte').value; // Formato del reporte

    mostrarNotificacion(`Generando reporte de ${tipo} en formato ${formato.toUpperCase()}...`, 'info'); // Notificación de inicio

    // Simula un retardo para la generación del reporte
    setTimeout(() => {
        mostrarNotificacion('Reporte generado exitosamente', 'success'); // Notificación de éxito
    }, 2000);
}

/**
 * Simula la apertura de un panel de alertas completo.
 * Muestra una notificación.
 */
function verTodasAlertas() {
    mostrarNotificacion('Abriendo panel de alertas completo...', 'info'); // Notificación
    // Aquí se podría redirigir a una página de alertas o abrir un modal más complejo
}

/**
 * Exporta un evento de auditoría específico como un archivo JSON.
 * @param {number} id - El ID del evento a exportar.
 */
function exportarEvento(id) {
    const evento = auditData.find(e => e.id === id); // Busca el evento por ID
    const json = JSON.stringify(evento, null, 2); // Convierte el objeto evento a una cadena JSON formateada
    
    // Crea un Blob con el contenido JSON
    const blob = new Blob([json], { type: 'application/json' });
    const url = URL.createObjectURL(blob); // Crea una URL para el Blob
    const a = document.createElement('a'); // Crea un elemento <a> para la descarga
    a.href = url; // Asigna la URL al href
    a.download = `evento_${id}_${new Date().toISOString().split('T')[0]}.json`; // Nombre del archivo
    document.body.appendChild(a); // Añade el elemento <a> al DOM (temporalmente)
    a.click(); // Simula un clic para iniciar la descarga
    document.body.removeChild(a); // Elimina el elemento <a> del DOM
    URL.revokeObjectURL(url); // Libera la URL del Blob
    
    mostrarNotificacion('Evento exportado exitosamente', 'success'); // Notificación de éxito
    cerrarModal(); // Cierra el modal después de exportar
}

/**
 * Obtiene la clase CSS de color de insignia según el tipo de evento.
 * @param {string} tipo - El tipo de evento (e.g., 'acceso', 'modificacion').
 * @returns {string} La clase CSS correspondiente.
 */
function obtenerColorTipo(tipo) {
    const colores = {
        'acceso': 'info',
        'consulta': 'info',
        'modificacion': 'warning',
        'notificacion': 'success',
        'pago': 'success',
        'embargo': 'danger',
        'suspension': 'warning'
    };
    return colores[tipo] || 'info'; // Retorna el color correspondiente o 'info' por defecto
}

/**
 * Capitaliza la primera letra de una cadena de texto.
 * @param {string} str - La cadena de texto a capitalizar.
 * @returns {string} La cadena con la primera letra capitalizada.
 */
function capitalizarPrimera(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

/**
 * Muestra una notificación temporal en la esquina superior derecha de la pantalla.
 * @param {string} mensaje - El mensaje a mostrar en la notificación.
 * @param {string} tipo - El tipo de notificación ('success', 'error', 'info', 'warning').
 */
function mostrarNotificacion(mensaje, tipo) {
    // Crear elemento de notificación
    const notificacion = document.createElement('div');
    notificacion.className = `alert alert-${tipo === 'success' ? 'success' : tipo === 'error' ? 'danger' : 'warning'}`;
    notificacion.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1100;
        min-width: 300px;
        animation: slideIn 0.3s ease forwards; /* forwards mantiene el estado final de la animación */
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    `;
    notificacion.textContent = mensaje;

    // Agregar estilos de animación si no existen
    if (!document.getElementById('notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    }

    document.body.appendChild(notificacion);

    // Remover después de 3 segundos con animación de salida
    setTimeout(() => {
        notificacion.style.animation = 'slideOut 0.3s ease forwards';
        setTimeout(() => {
            if (notificacion.parentNode) {
                document.body.removeChild(notificacion);
            }
        }, 300);
    }, 3000);
}

/**
 * Simula la adición de eventos de auditoría aleatorios a intervalos regulares.
 * Esto ayuda a demostrar la reactividad del sistema.
 */
function simularEventosAleatorios() {
    const usuarios = ['admin001', 'cobrador001', 'supervisor001', 'auditor001'];
    const tipos = ['acceso', 'consulta', 'modificacion', 'notificacion', 'pago', 'embargo', 'suspension'];
    const descripciones = [
        'Acceso exitoso al sistema',
        'Consulta de expediente realizada',
        'Actualización de datos del deudor',
        'Envío de notificación por correo',
        'Registro de pago parcial',
        'Verificación de documentos',
        'Generación de reporte mensual',
        'Intento de acceso fallido: contraseña incorrecta',
        'Expediente cerrado por finalización de cobro',
        'Nuevo expediente creado para cobro coactivo'
    ];

    setInterval(() => {
        if (Math.random() > 0.7) { // 30% de probabilidad de generar un evento cada 10 segundos
            const nuevoEvento = {
                id: auditData.length + 1,
                fecha: new Date().toISOString(),
                usuario: usuarios[Math.floor(Math.random() * usuarios.length)],
                tipo: tipos[Math.floor(Math.random() * tipos.length)],
                expediente: `CC-2024-${String(Math.floor(Math.random() * 999) + 1).padStart(3, '0')}`,
                descripcion: descripciones[Math.floor(Math.random() * descripciones.length)],
                estado: Math.random() > 0.1 ? 'exitoso' : 'fallido', // 90% de éxito, 10% de fallo
                ip: `192.168.1.${Math.floor(Math.random() * 255)}`
            };

            auditData.unshift(nuevoEvento); // Añade el evento al principio
            if (auditData.length > 100) {
                auditData = auditData.slice(0, 100); // Mantiene solo los últimos 100 eventos para evitar sobrecarga
            }
            
            actualizarTablaAuditorias(); // Actualiza la interfaz
            actualizarTimeline();
            actualizarEstadisticas();
        }
    }, 10000); // Se ejecuta cada 10 segundos
}

/**
 * Exporta todos los datos de auditoría actuales a un archivo CSV.
 */
function exportarCSV() {
    const headers = ['ID', 'Fecha', 'Usuario', 'Tipo', 'Expediente', 'Descripción', 'Estado', 'IP'];
    // Mapea los datos de auditoría a un formato de cadena CSV, manejando comillas dobles en la descripción
    const csvContent = [
        headers.join(','), // Encabezados del CSV
        ...auditData.map(evento => [
            evento.id,
            new Date(evento.fecha).toLocaleString(),
            evento.usuario,
            evento.tipo,
            evento.expediente,
            `"${evento.descripcion.replace(/"/g, '""')}"`, // Escapa comillas dobles en la descripción
            evento.estado,
            evento.ip
        ].join(','))
    ].join('\n'); // Une todas las filas con saltos de línea

    // Crea un Blob y un enlace para descargar el archivo CSV
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `auditoria_sena_${new Date().toISOString().split('T')[0]}.csv`; // Nombre del archivo
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url); // Libera la URL del Blob
    mostrarNotificacion('Datos exportados a CSV exitosamente', 'success');
}

/**
 * Valida la integridad de los datos de auditoría, buscando campos faltantes o inválidos.
 */
function validarIntegridad() {
    const errores = []; // Array para almacenar los errores encontrados
    
    auditData.forEach((evento, index) => {
        // Validación de la fecha
        if (!evento.fecha || isNaN(new Date(evento.fecha).getTime())) {
            errores.push(`Evento ${evento.id}: Fecha inválida`);
        }
        // Validación del usuario
        if (!evento.usuario || evento.usuario.trim() === '') {
            errores.push(`Evento ${evento.id}: Usuario requerido`);
        }
        // Validación del tipo de evento
        if (!evento.tipo || evento.tipo.trim() === '') {
            errores.push(`Evento ${evento.id}: Tipo de evento requerido`);
        }
        // Se podrían añadir más validaciones aquí (e.g., formato de IP, longitud de descripción)
    });

    if (errores.length === 0) {
        mostrarNotificacion('Validación de integridad exitosa', 'success'); // Notificación de éxito
    } else {
        mostrarNotificacion(`Se encontraron ${errores.length} errores de integridad`, 'error'); // Notificación de error
        console.log('Errores encontrados:', errores); // Muestra los errores en la consola
    }
}

/**
 * Analiza los datos de auditoría para detectar patrones sospechosos,
 * como accesos fallidos, intentos repetidos desde una IP o horarios inusuales.
 */
function detectarPatronesSospechosos() {
    const analisis = {
        accesosFallidos: 0,
        intentosRepetidos: {}, // Conteo de intentos por IP
        horariosInusuales: 0, // Eventos fuera del horario normal
        ipsSospechosas: {} // IPs con alta actividad sospechosa
    };

    auditData.forEach(evento => {
        // Contar accesos fallidos
        if (evento.estado === 'fallido' && evento.tipo === 'acceso') {
            analisis.accesosFallidos++;
        }

        // Detectar intentos repetidos por IP
        if (!analisis.intentosRepetidos[evento.ip]) {
            analisis.intentosRepetidos[evento.ip] = 0;
        }
        analisis.intentosRepetidos[evento.ip]++;

        // Detectar horarios inusuales (antes de 6 AM o después de 10 PM)
        const hora = new Date(evento.fecha).getHours();
        if (hora < 6 || hora > 22) {
            analisis.horariosInusuales++;
        }
    });

    // Identificar IPs con actividad sospechosa (más de 10 intentos)
    Object.keys(analisis.intentosRepetidos).forEach(ip => {
        if (analisis.intentosRepetidos[ip] > 10) {
            analisis.ipsSospechosas[ip] = analisis.intentosRepetidos[ip];
        }
    });

    console.log('Análisis de seguridad:', analisis); // Muestra el resultado del análisis en consola
    
    // Muestra una notificación basada en los resultados del análisis
    if (analisis.accesosFallidos > 5 || Object.keys(analisis.ipsSospechosas).length > 0 || analisis.horariosInusuales > 3) {
        mostrarNotificacion('Actividad sospechosa detectada. ¡Revisar!', 'danger');
    } else {
        mostrarNotificacion('No se detectó actividad sospechosa significativa', 'success');
    }
}

// Event listener para la búsqueda en tiempo real en el campo de búsqueda
document.getElementById('buscarAuditoria').addEventListener('input', function(e) {
    // Aplica los filtros si el texto tiene 3 o más caracteres, o si se vacía el campo
    if (e.target.value.length >= 3 || e.target.value.length === 0) {
        aplicarFiltros();
    }
});

// Event listener para cerrar el modal al hacer clic fuera de su contenido
window.addEventListener('click', function(e) {
    const modal = document.getElementById('modalDetalles');
    if (e.target === modal) { // Si el clic fue en el fondo del modal
        cerrarModal();
    }
});

// Event listener para atajos de teclado globales
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { // Tecla Escape para cerrar el modal
        cerrarModal();
    }
    if (e.ctrlKey && e.key === 'e') { // Ctrl + E para exportar CSV
        e.preventDefault(); // Previene la acción por defecto del navegador
        exportarCSV();
    }
    if (e.ctrlKey && e.key === 'r') { // Ctrl + R para generar reporte
        e.preventDefault(); // Previene la acción por defecto del navegador
        generarReporte();
    }
});

/**
 * Agrega botones adicionales al encabezado de la página para funcionalidades rápidas.
 */
function agregarBotonesHeader() {
    const header = document.querySelector('.header'); // Selecciona el encabezado
    const buttonContainer = document.createElement('div'); // Crea un contenedor para los botones
    buttonContainer.style.cssText = 'margin-top: 1rem; display: flex; gap: 1rem; flex-wrap: wrap;'; // Estilos para el contenedor
    
    // Inserta el HTML de los botones con sus respectivas funciones onclick y títulos para atajos
    buttonContainer.innerHTML = `
        <button class="btn btn-success" onclick="exportarCSV()" title="Ctrl+E">📥 Exportar CSV</button>
        <button class="btn btn-primary" onclick="validarIntegridad()">✓ Validar Integridad</button>
        <button class="btn btn-danger" onclick="detectarPatronesSospechosos()">🛡️ Análisis Seguridad</button>
        <button class="btn btn-secondary" onclick="location.reload()">🔄 Actualizar</button>
    `;
    
    header.appendChild(buttonContainer); // Añade el contenedor de botones al encabezado
}

// Inicialización de la aplicación cuando el DOM está completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    actualizarTablaAuditorias(); // Carga la tabla de auditorías
    actualizarTimeline(); // Carga la línea de tiempo
    actualizarEstadisticas(); // Carga las estadísticas
    simularEventosAleatorios(); // Inicia el simulador de eventos aleatorios
    agregarBotonesHeader(); // Añade los botones al encabezado
    
    // Muestra un mensaje de bienvenida después de un breve retraso
    setTimeout(() => {
        mostrarNotificacion('Sistema de Auditorías SENA iniciado correctamente', 'success');
    }, 500);
});

// Actualización automática de estadísticas cada minuto
setInterval(() => {
    actualizarEstadisticas();
}, 60000); // Se ejecuta cada 60 segundos (1 minuto)
