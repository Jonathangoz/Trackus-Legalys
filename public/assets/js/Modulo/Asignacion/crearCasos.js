
document.addEventListener('DOMContentLoaded', function() {
    // Obtener fecha actual para fecha_creacion
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('fecha_creacion').value = today;
    
    // Manejar cambio tipo persona
    const tipoPersonaRadios = document.querySelectorAll('input[name="tipo_persona"]');
    tipoPersonaRadios.forEach(radio => {
        radio.addEventListener('change', toggleTipoPersona);
    });
    
    // Calcular monto total
    const montos = ['monto_original', 'intereses', 'costos'];
    montos.forEach(id => {
        document.getElementById(id).addEventListener('input', calcularTotal);
    });
    
    // Envío del formulario
    document.getElementById('casoForm').addEventListener('submit', crearCaso);
    
    // Inicializar campos
    toggleTipoPersona();
    calcularTotal();
});

function toggleTipoPersona() {
    const tipoPersona = document.querySelector('input[name="tipo_persona"]:checked').value;
    const nombreGroup = document.getElementById('nombreNaturalGroup');
    const razonGroup = document.getElementById('razonSocialGroup');
    
    if (tipoPersona === 'natural') {
        nombreGroup.style.display = 'block';
        razonGroup.style.display = 'none';
        document.getElementById('razon_social').removeAttribute('required');
        document.getElementById('nombre_apellido').setAttribute('required', 'true');
    } else {
        nombreGroup.style.display = 'none';
        razonGroup.style.display = 'block';
        document.getElementById('nombre_apellido').removeAttribute('required');
        document.getElementById('razon_social').setAttribute('required', 'true');
    }
}

function calcularTotal() {
    const original = parseFloat(document.getElementById('monto_original').value) || 0;
    const intereses = parseFloat(document.getElementById('intereses').value) || 0;
    const costos = parseFloat(document.getElementById('costos').value) || 0;
    const total = original + intereses + costos;
    document.getElementById('monto_total').value = total.toFixed(2);
}

function crearCaso(e) {
    e.preventDefault();
    
    const formData = new FormData(document.getElementById('casoForm'));
    const data = Object.fromEntries(formData.entries());
    
    // Validación de fechas
    if (new Date(data.fecha_limite_pago) < new Date(data.fecha_creacion)) {
        showNotification('La fecha límite de pago no puede ser anterior a la fecha de creación', 'error');
        return;
    }
    
    fetch('/asignacion/crearcasos', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            showNotification('Caso creado exitosamente', 'success');
            setTimeout(() => {
                window.location.href = '/asignacion';
            }, 1500);
        } else {
            showNotification(`Error: ${result.message}`, 'error');
        }
    })
    .catch(error => {
        showNotification('Error en la comunicación con el servidor', 'error');
        console.error('Error:', error);
    });
}

function showNotification(message, type) {
    const notification = document.getElementById('notification');
    notification.textContent = message;
    notification.className = `notification ${type}`;
    notification.style.display = 'block';
    
    setTimeout(() => {
        notification.style.display = 'none';
    }, 5000);
}