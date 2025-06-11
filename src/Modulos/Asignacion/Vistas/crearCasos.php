<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Caso de Cobro Coactivo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --color-primary: #39a900;
            --color-secondary: #2c7c00;
            --color-light: #f5f9f2;
            --color-gray: #e1e1e1;
            --color-dark: #333;
            --shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f0f4f7;
            color: var(--color-dark);
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--color-primary);
        }
        
        .header h1 {
            color: var(--color-primary);
            font-size: 28px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn-primary {
            background-color: var(--color-primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--color-secondary);
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .form-container {
            background: white;
            border-radius: 8px;
            box-shadow: var(--shadow);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--color-dark);
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--color-gray);
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--color-primary);
            outline: none;
            box-shadow: 0 0 0 2px rgba(57, 169, 0, 0.2);
        }
        
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .form-footer {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--color-gray);
        }
        
        .radio-group {
            display: flex;
            gap: 20px;
        }
        
        .radio-option {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .notification {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            display: none;
        }
        
        .notification.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .notification.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .form-grid {
                gap: 15px;
            }
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .form-footer {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 15px;
            }
            
            .form-container {
                padding: 20px;
            }
            
            .radio-group {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-file-invoice-dollar"></i> Crear Nuevo Caso de Cobro Coactivo</h1>
            <div>
                <a href="/asignacion" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver a Asignación
                </a>
            </div>
        </div>
        
        <div id="notification" class="notification"></div>
        
        <div class="form-container">
            <form id="casoForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="radicado">Radicado *</label>
                        <input type="text" id="radicado" name="radicado" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Tipo de Persona *</label>
                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" id="tipo_persona_natural" name="tipo_persona" value="natural" checked>
                                <label for="tipo_persona_natural">Persona Natural</label>
                            </div>
                            <div class="radio-option">
                                <input type="radio" id="tipo_persona_juridica" name="tipo_persona" value="juridica">
                                <label for="tipo_persona_juridica">Persona Jurídica</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group" id="nombreNaturalGroup">
                        <label for="nombre_apellido">Nombres y Apellidos *</label>
                        <input type="text" id="nombre_apellido" name="nombre_apellido" required>
                    </div>
                    
                    <div class="form-group" id="razonSocialGroup" style="display: none;">
                        <label for="razon_social">Razón Social *</label>
                        <input type="text" id="razon_social" name="razon_social">
                    </div>
                    
                    <div class="form-group">
                        <label for="identificacion">NIT / Cédula *</label>
                        <input type="text" id="identificacion" name="identificacion" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="tipo_tramite">Tipo de Trámite *</label>
                        <select id="tipo_tramite" name="tipo_tramite" required>
                            <option value="">Seleccione un tipo</option>
                            <option value="ejecutivo">Ejecutivo</option>
                            <option value="ordinario">Ordinario</option>
                            <option value="verbal">Verbal</option>
                            <option value="especial">Especial</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="estado_tramite">Estado del Trámite *</label>
                        <select id="estado_tramite" name="estado_tramite" required>
                            <option value="">Seleccione un estado</option>
                            <option value="pendiente">Pendiente</option>
                            <option value="asignado">Asignado</option>
                            <option value="en_proceso">En Proceso</option>
                            <option value="cerrado">Cerrado</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="descripcion">Descripción *</label>
                        <textarea id="descripcion" name="descripcion" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="monto_original">Monto Original de Deuda ($) *</label>
                        <input type="number" id="monto_original" name="monto_original" step="0.01" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="intereses">Intereses Acumulados ($)</label>
                        <input type="number" id="intereses" name="intereses" step="0.01" min="0" value="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="costos">Costos Administrativos ($)</label>
                        <input type="number" id="costos" name="costos" step="0.01" min="0" value="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="monto_total">Monto Total ($)</label>
                        <input type="number" id="monto_total" name="monto_total" step="0.01" min="0" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="fecha_creacion">Fecha de Creación *</label>
                        <input type="date" id="fecha_creacion" name="fecha_creacion" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="fecha_asignacion">Fecha de Asignación</label>
                        <input type="date" id="fecha_asignacion" name="fecha_asignacion">
                    </div>
                    
                    <div class="form-group">
                        <label for="fecha_limite_pago">Fecha Límite de Pago *</label>
                        <input type="date" id="fecha_limite_pago" name="fecha_limite_pago" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="fecha_cierre">Fecha de Cierre</label>
                        <input type="date" id="fecha_cierre" name="fecha_cierre">
                    </div>
                    
                    <div class="form-group">
                        <label for="numero_factura">Número de Factura/Resolución</label>
                        <input type="text" id="numero_factura" name="numero_factura">
                    </div>
                </div>
                
                <div class="form-footer">
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Crear Caso
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
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
    </script>
</body>
</html>