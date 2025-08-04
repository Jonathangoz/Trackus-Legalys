// Configuración global
const API_BASE_URL = 'api/';
let casosData = [];
let currentTab = 'casos-asignados';
let abogadoInfo = null;

// Inicialización de la aplicación
document.addEventListener('DOMContentLoaded', function() {
    verificarSesion();
    cargarCasosAsignados();
    configurarEventListeners();
    establecerFechaMinima();
});

// Verificar sesión del abogado
async function verificarSesion() {
    try {
        const response = await fetch(`${API_BASE_URL}verificar_sesion.php`);
        const data = await response.json();
        
        if (data.success) {
            abogadoInfo = data.abogado;
            document.getElementById('nombreAbogado').innerHTML = 
                `Abogado: <strong>${data.abogado.nombre}</strong>`;
        } else {
            window.location.href = 'login.html';
        }
    } catch (error) {
        console.error('Error verificando sesión:', error);
        mostrarToast('Error de conexión', 'error');
    }
}

// Cerrar sesión
function cerrarSesion() {
    if (confirm('¿Está seguro que desea cerrar sesión?')) {
        fetch(`${API_BASE_URL}cerrar_sesion.php`, {
            method: 'POST'
        }).then(() => {
            window.location.href = 'login.html';
        });
    }
}

// Configurar event listeners
function configurarEventListeners() {
    // Formulario de mandamiento
    document.getElementById('formMandamiento').addEventListener('submit', crearMandamiento);
    
    // Formulario de embargo
    document.getElementById('formEmbargo').addEventListener('submit', registrarEmbargo);
    
    // Formulario de acuerdo
    document.getElementById('formAcuerdo').addEventListener('submit', crearAcuerdo);
    
    // Formulario de recurso
    document.getElementById('formRecurso').addEventListener('submit', registrarRecurso);
    
    // Calcular total en mandamiento
    ['interesesMora', 'costosAdministrativos'].forEach(id => {
        document.getElementById(id).addEventListener('input', calcularTotalMandamiento);
    });
    
    // Cargar entidades según tipo de embargo
    document.getElementById('tipoEmbargo').addEventListener('change', cargarEntidades);
    
    // Cerrar modales al hacer clic fuera
    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    });
}

// Establecer fecha mínima como hoy
function establecerFechaMinima() {
    const today = new Date().toISOString().split('T')[0];
    const fechaInputs = [
        'fechaLimitePago',
        'fechaEjecucionEmbargo',
        'fechaInicioAcuerdo',
        'fechaPresentacionRecurso',
        'fechaResolucionRecurso'
    ];
    
    fechaInputs.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.min = today;
        }
    });
}

// === GESTIÓN DE TABS ===
function mostrarTab(tabId) {
    // Ocultar todos los tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Desactivar todos los botones
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Mostrar tab seleccionado
    document.getElementById(tabId).classList.add('active');
    
    // Activar botón correspondiente
    event.target.classList.add('active');
    
    currentTab = tabId;
    
    // Cargar datos específicos del tab
    switch(tabId) {
        case 'casos-asignados':
            cargarCasosAsignados();
            break;
        case 'gestionar-embargos':
            cargarEntidades();
            break;
    }
}

// === CASOS ASIGNADOS ===
async function cargarCasosAsignados() {
    mostrarLoading('loadingCasos');
    
    try {
        const response = await fetch(`${API_BASE_URL}obtener_casos.php`);
        const data = await response.json();
        
        if (data.success) {
            casosData = data.casos;
            mostrarCasos(casosData);
        } else {
            mostrarToast('Error cargando casos: ' + data.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarToast('Error de conexión al cargar casos', 'error');
    } finally {
        ocultarLoading('loadingCasos');
    }
}

function mostrarCasos(casos) {
    const container = document.getElementById('casosLista');
    
    if (casos.length === 0) {
        container.innerHTML = '<div class="no-data">No hay casos asignados</div>';
        return;
    }
    
    const casosHTML = casos.map(caso => `
        <div class="caso-card" data-id="${caso.id}">
            <div class="caso-header">
                <h3>Caso #${caso.id}</h3>
                <span class="estado-badge estado-${caso.estado.toLowerCase().replace('_', '-')}">${caso.estado_texto}</span>
            </div>
            <div class="caso-body">
                <div class="deudor-info">
                    <strong>${caso.deudor_nombre}</strong>
                    <span>CC: ${caso.deudor_cedula}</span>
                </div>
                <div class="monto-info">
                    <span class="monto">$${formatearMonto(caso.monto_capital)}</span>
                    <span class="fecha">Vence: ${formatearFecha(caso.fecha_vencimiento)}</span>
                </div>
            </div>
            <div class="caso-actions">
                <button class="btn-small btn-primary" onclick="verDetalleCaso(${caso.id})">Ver Detalle</button>
                <button class="btn-small btn-secondary" onclick="seleccionarCaso(${caso.id})">Seleccionar</button>
            </div>
        </div>
    `).join('');
    
    container.innerHTML = casosHTML;
}

function filtrarCasos() {
    const estado = document.getElementById('filtroEstado').value;
    const deudor = document.getElementById('filtroDeudor').value.toLowerCase();
    
    let casosFiltrados = casosData;
    
    if (estado) {
        casosFiltrados = casosFiltrados.filter(caso => caso.estado === estado);
    }
    
    if (deudor) {
        casosFiltrados = casosFiltrados.filter(caso => 
            caso.deudor_nombre.toLowerCase().includes(deudor) ||
            caso.deudor_cedula.includes(deudor)
        );
    }
    
    mostrarCasos(casosFiltrados);
}

// === MANDAMIENTOS ===
function buscarCaso() {
    document.getElementById('modalBuscarCaso').style.display = 'block';
}

async function buscarCasos() {
    const termino = document.getElementById('buscarCasoInput').value;
    if (!termino) {
        mostrarToast('Ingrese un término de búsqueda', 'warning');
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE_URL}buscar_casos.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ termino })
        });
        
        const data = await response.json();
        
        if (data.success) {
            mostrarResultadosBusqueda(data.casos);
        } else {
            mostrarToast(data.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarToast('Error en la búsqueda', 'error');
    }
}

function mostrarResultadosBusqueda(casos) {
    const container = document.getElementById('resultadosBusqueda');
    
    if (casos.length === 0) {
        container.innerHTML = '<div class="no-data">No se encontraron casos</div>';
        return;
    }
    
    const casosHTML = casos.map(caso => `
        <div class="resultado-item" onclick="seleccionarCasoBusqueda(${caso.id}, '${caso.deudor_nombre}', '${caso.deudor_cedula}', ${caso.monto_capital})">
            <div class="resultado-info">
                <strong>Caso #${caso.id}</strong>
                <span>${caso.deudor_nombre} - CC: ${caso.deudor_cedula}</span>
                <span class="monto">$${formatearMonto(caso.monto_capital)}</span>
            </div>
        </div>
    `).join('');
    
    container.innerHTML = casosHTML;
}

function seleccionarCasoBusqueda(id, nombre, cedula, monto) {
    // Para mandamiento
    if (currentTab === 'crear-mandamiento') {
        document.getElementById('casoIdMandamiento').value = id;
        document.getElementById('deudorInfo').innerHTML = `
            <strong>${nombre}</strong><br>
            CC: ${cedula}<br>
            Monto Capital: $${formatearMonto(monto)}
        `;
        document.getElementById('montoCapital').value = monto;
        calcularTotalMandamiento();
    }
    
    // Para embargo
    if (currentTab === 'gestionar-embargos') {
        document.getElementById('casoIdEmbargo').value = id;
        cargarEmbargosDelCaso(id);
    }
    
    // Para acuerdo
    if (currentTab === 'acuerdos-pago') {
        document.getElementById('casoIdAcuerdo').value = id;
        document.getElementById('montoTotalAcuerdo').value = monto;
        calcularCuotas();
    }
    
    // Para recurso
    if (currentTab === 'recursos') {
        document.getElementById('casoIdRecurso').value = id;
    }
    
    cerrarModal('modalBuscarCaso');
    mostrarToast('Caso seleccionado correctamente', 'success');
}

function calcularTotalMandamiento() {
    const capital = parseFloat(document.getElementById('montoCapital').value) || 0;
    const intereses = parseFloat(document.getElementById('interesesMora').value) || 0;
    const costos = parseFloat(document.getElementById('costosAdministrativos').value) || 0;
    
    const total = capital + intereses + costos;
    document.getElementById('montoTotalMandamiento').value = total.toFixed(2);
}

async function crearMandamiento(event) {
    event.preventDefault();
    mostrarLoading();
    
    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch(`${API_BASE_URL}crear_mandamiento.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            mostrarToast('Mandamiento creado exitosamente', 'success');
            event.target.reset();
            cargarCasosAsignados();
        } else {
            mostrarToast('Error: ' + result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarToast('Error al crear mandamiento', 'error');
    } finally {
        ocultarLoading();
    }
}

async function generarPDFMandamiento() {
    const casoId = document.getElementById('casoIdMandamiento').value;
    if (!casoId) {
        mostrarToast('Seleccione un caso primero', 'warning');
        return;
    }
    
    mostrarLoading();
    
    try {
        const response = await fetch(`${API_BASE_URL}generar_pdf_mandamiento.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ caso_id: casoId })
        });
        
        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `mandamiento_caso_${casoId}.pdf`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            mostrarToast('PDF generado exitosamente', 'success');
        } else {
            mostrarToast('Error al generar PDF', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarToast('Error al generar PDF', 'error');
    } finally {
        ocultarLoading();
    }
}

// === EMBARGOS ===
function buscarCasoEmbargo() {
    document.getElementById('modalBuscarCaso').style.display = 'block';
}

async function cargarEntidades() {
    const tipoEmbargo = document.getElementById('tipoEmbargo').value;
    const selectEntidad = document.getElementById('entidadEmbargo');
    
    if (!tipoEmbargo) {
        selectEntidad.innerHTML = '<option value="">Seleccionar entidad...</option>';
        return;
    }
    
    try {
        const response = await fetch(`${API_BASE_URL}obtener_entidades.php?tipo=${tipoEmbargo}`);
        const data = await response.json();
        
        if (data.success) {
            let options = '<option value="">Seleccionar entidad...</option>';
            data.entidades.forEach(entidad => {
                options += `<option value="${entidad.id}">${entidad.nombre}</option>`;
            });
            selectEntidad.innerHTML = options;
        }
    } catch (error) {
        console.error('Error cargando entidades:', error);
    }
}

async function cargarEmbargosDelCaso(casoId) {
    try {
        const response = await fetch(`${API_BASE_URL}obtener_embargos.php?caso_id=${casoId}`);
        const data = await response.json();
        
        if (data.success) {
            mostrarEmbargos(data.embargos);
        }
    } catch (error) {
        console.error('Error cargando embargos:', error);
    }
}

function mostrarEmbargos(embargos) {
    const container = document.getElementById('listaEmbargosContainer');
    
    if (embargos.length === 0) {
        container.innerHTML = '<div class="no-data">No hay embargos registrados</div>';
        return;
    }
    
    const embargosHTML = embargos.map(embargo => `
        <div class="embargo-item">
            <div class="embargo-info">
                <strong>${embargo.tipo_embargo}</strong>
                <span>${embargo.entidad_nombre}</span>
                <span class="estado-badge estado-${embargo.estado.toLowerCase()}">${embargo.estado}</span>
            </div>
            <div class="embargo-montos">
                <span>Solicitado: $${formatearMonto(embargo.monto_solicitado)}</span>
                <span>Ejecutado: $${formatearMonto(embargo.monto_ejecutado)}</span>
            </div>
        </div>
    `).join('');
    
    container.innerHTML = embargosHTML;
}

async function registrarEmbargo(event) {
    event.preventDefault();
    mostrarLoading();
    
    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch(`${API_BASE_URL}registrar_embargo.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            mostrarToast('Embargo registrado exitosamente', 'success');
            cargarEmbargosDelCaso(data.casoIdEmbargo);
        } else {
            mostrarToast('Error: ' + result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarToast('Error al registrar embargo', 'error');
    } finally {
        ocultarLoading();
    }
}

// === ACUERDOS DE PAGO ===
function buscarCasoAcuerdo() {
    document.getElementById('modalBuscarCaso').style.display = 'block';
}

function calcularCuotas() {
    const montoTotal = parseFloat(document.getElementById('montoTotalAcuerdo').value) || 0;
    const numeroCuotas = parseInt(document.getElementById('cuotasAcuerdo').value) || 1;
    
    if (montoTotal > 0) {
        const valorCuota = montoTotal / numeroCuotas;
        document.getElementById('valorCuota').value = valorCuota.toFixed(2);
    }
    
    calcularFechaFin();
}

function calcularFechaFin() {
    const fechaInicio = document.getElementById('fechaInicioAcuerdo').value;
    const numeroCuotas = parseInt(document.getElementById('cuotasAcuerdo').value) || 1;
    const frecuencia = document.getElementById('frecuenciaAcuerdo').value;
    
    if (!fechaInicio) return;
    
    const fecha = new Date(fechaInicio);
    let diasAgregar = 0;
    
    switch (frecuencia) {
        case 'SEMANAL':
            diasAgregar = 7 * numeroCuotas;
            break;
        case 'QUINCENAL':
            diasAgregar = 15 * numeroCuotas;
            break;
        case 'MENSUAL':
            diasAgregar = 30 * numeroCuotas;
            break;
    }
    
    fecha.setDate(fecha.getDate() + diasAgregar);
    document.getElementById('fechaFinAcuerdo').value = fecha.toISOString().split('T')[0];
}

async function crearAcuerdo(event) {
    event.preventDefault();
    mostrarLoading();
    
    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData);
    data.interesCondonado = document.getElementById('interesCondonado').checked;
    
    try {
        const response = await fetch(`${API_BASE_URL}crear_acuerdo.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            mostrarToast('Acuerdo creado exitosamente', 'success');
            event.target.reset();
        } else {
            mostrarToast('Error: ' + result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarToast('Error al crear acuerdo', 'error');
    } finally {
        ocultarLoading();
    }
}

async function generarPDFAcuerdo() {
    const casoId = document.getElementById('casoIdAcuerdo').value;
    if (!casoId) {
        mostrarToast('Seleccione un caso primero', 'warning');
        return;
    }
    
    mostrarLoading();
    
    try {
        const response = await fetch(`${API_BASE_URL}generar_pdf_acuerdo.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ caso_id: casoId })
        });
        
        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `acuerdo_pago_caso_${casoId}.pdf`;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            mostrarToast('PDF generado exitosamente', 'success');
        } else {
            mostrarToast('Error al generar PDF', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarToast('Error al generar PDF', 'error');
    } finally {
        ocultarLoading();
    }
}

// === RECURSOS ADMINISTRATIVOS ===
function buscarCasoRecurso() {
    document.getElementById('modalBuscarCaso').style.display = 'block';
}

async function registrarRecurso(event) {
    event.preventDefault();
    mostrarLoading();
    
    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch(`${API_BASE_URL}registrar_recurso.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            mostrarToast('Recurso registrado exitosamente', 'success');
            event.target.reset();
        } else {
            mostrarToast('Error: ' + result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarToast('Error al registrar recurso', 'error');
    } finally {
        ocultarLoading();
    }
}

// === FUNCIONES UTILITARIAS ===
function formatearMonto(monto) {
    return new Intl.NumberFormat('es-CO', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(monto);
}

function formatearFecha(fecha) {
    return new Date(fecha).toLocaleDateString('es-CO');
}

function mostrarLoading(containerId = null) {
    if (containerId) {
        document.getElementById(containerId).style.display = 'block';
    } else {
        document.getElementById('modalLoading').style.display = 'block';
    }
}

function ocultarLoading(containerId = null) {
    if (containerId) {
        document.getElementById(containerId).style.display = 'none';
    } else {
        document.getElementById('modalLoading').style.display = 'none';
    }
}

function mostrarToast(mensaje, tipo = 'info') {
    const toast = document.getElementById('toast');
    toast.textContent = mensaje;
    toast.className = `toast show ${tipo}`;
    
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

function cerrarModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function seleccionarCaso(casoId) {
    const caso = casosData.find(c => c.id === casoId);
    if (caso) {
        // Cambiar a tab de mandamiento y precargar datos
        mostrarTab('crear-mandamiento');
        document.querySelector('[onclick="mostrarTab(\'crear-mandamiento\')"]').click();
        
        // Precargar datos del caso
        document.getElementById('casoIdMandamiento').value = caso.id;
        document.getElementById('deudorInfo').innerHTML = `
            <strong>${caso.deudor_nombre}</strong><br>
            CC: ${caso.deudor_cedula}<br>
            Monto Capital: $${formatearMonto(caso.monto_capital)}
        `;
        document.getElementById('montoCapital').value = caso.monto_capital;
        
        mostrarToast('Caso seleccionado para mandamiento', 'success');
    }
}

async function verDetalleCaso(casoId) {
    try {
        const response = await fetch(`${API_BASE_URL}detalle_caso.php?id=${casoId}`);
        const data = await response.json();
        
        if (data.success) {
            // Aquí puedes implementar un modal con el detalle completo del caso
            console.log('Detalle del caso:', data.caso);
            mostrarToast('Ver detalle en desarrollo', 'info');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarToast('Error al cargar detalle', 'error');
    }
}