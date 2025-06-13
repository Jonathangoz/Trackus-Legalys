<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Caso de Cobro Coactivo</title>
    <!-- Favicon -->
    <link rel="icon" href="/favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" href="/assets/CSS/Modulo/Asignacion/crearCasos.css"/>
    <!-- fontawesome -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <!-- Botón de volver arriba -->
    <button type="button" id="backToTopBtn" title="Volver arriba">
        <i class="fa-solid fa-arrow-up fa-lg" aria-hidden="true"></i>
    </button>
    <header>
        <div class="logo-container">
            <img src="/assets/images/sena.blanco.png" class="img-sena" alt="Logo SENA">            
            <p>SENA Regional Santander</p>
        </div>
        <div class="title-container">
            <h1>Procesos Cobro Coactivo</h1>
        </div>       
        <div class="user-home">
            <div class="home">
                <a href="/ADMIN_TRAMITE/asignacion" style="text-decoration: none;"><i class="fa-solid fa-house fa-lg" style="color: white;"></i>
                    <div>
                        <span class="text" style="color: white;">Asignacion</span>
                    </div>
                </a>
            </div>
            <div class="home">
                <a href="/ADMIN_TRAMITE/registros" style="text-decoration: none;"><i class="fa-solid fa-house fa-lg" style="color: white;"></i>
                    <div>
                        <span class="text" style="color: white;">Registros</span>
                    </div>
                </a>
            </div>
            <div class="dropdown" id="dropdownContainer">
                <input type="checkbox" id="userToggle" hidden>
                <label for="userToggle" class="dropdown-toggle">
                    <span class="user-name">Bienvenido, Admin</span>
                    <div class="avatar">
                        <i class="fa-solid fa-user fa-lg" style="margin-right: 6px;"></i>
                        <i class="fa-solid fa-caret-down" style="margin-right: 6px;"></i>
                    </div>
                </label>
                <ul class="dropdown-menu">
                    <li><a href="#" class="drop"><i class="fa-solid fa-user me-2" style="margin-right: 15px;"></i>Perfil</a></li>
                    <li><a href="#" class="drop"><i class="fa-solid fa-gear me-2" style="margin-right: 15px;"></i>Configuración</a></li>
                    <li><hr></li>
                    <li><a href="/login" class="sesion"><i class="fa-solid fa-right-from-bracket me-2" style="margin-right: 15px;"></i>Cerrar sesión</a></li>
                </ul>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="header">
            <h1><i class="fas fa-file-invoice-dollar"></i> Crear Nuevo Caso de Cobro Coactivo</h1>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="/assets/js/Modulo/Asignacion/crearCasos.js"></script>
</body>
</html>