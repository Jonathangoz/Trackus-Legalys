<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars(\App\Comunes\seguridad\csrf::generarToken(), ENT_QUOTES, 'UTF-8') ?>">
    <meta name="description" content="Sistema de Gestión de Casos Coactivos del SENA Regional Santander - Abogado">
    <title>Gestión de Casos - Abogado</title>
    <!-- Favicon -->
    <link rel="icon" href="/favicon.ico" type="image/x-icon"/>
    <!-- fontawesome -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- estilos CSS -->
    <link rel="stylesheet" href="/assets/CSS/Modulo/Deudores/form_abogado.css">
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
                <a href="/cobrocoactivo" style="text-decoration: none;"><i class="fa-solid fa-house fa-lg" style="color: white;"></i>
                    <div>
                        <span class="text" style="color: white;">Abogados</span>
                    </div>
                </a>
            </div>
            <div class="home">
                <a href="/deudores" style="text-decoration: none;"><i class="fa-solid fa-house fa-lg" style="color: white;"></i>
                    <div>
                        <span class="text" style="color: white;">Deudores</span>
                    </div>
                </a>
            </div>
            <div class="home">
                <a href="/deudores/obligados" style="text-decoration: none;"><i class="fa-solid fa-house fa-lg" style="color: white;"></i>
                    <div>
                        <span class="text" style="color: white;">Obligados al Pago</span>
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
                    <li><a href="/logout" class="sesion"><i class="fa-solid fa-right-from-bracket me-2" style="margin-right: 15px;"></i>Cerrar sesión</a></li>
                </ul>
            </div>
        </div>
    </header>
    <div class="container">

        <main class="main-content">
            <!-- Tabs de Navegación -->
            <div class="tabs">
                <button class="tab-button active" onclick="mostrarTab('casos-asignados')">Casos Asignados</button>
                <button class="tab-button" onclick="mostrarTab('crear-mandamiento')">Crear Mandamiento</button>
                <button class="tab-button" onclick="mostrarTab('gestionar-embargos')">Gestionar Embargos</button>
                <button class="tab-button" onclick="mostrarTab('acuerdos-pago')">Acuerdos de Pago</button>
                <button class="tab-button" onclick="mostrarTab('recursos')">Recursos Administrativos</button>
            </div>

            <!-- Tab: Casos Asignados -->
            <div id="casos-asignados" class="tab-content active">
                <div class="section-header">
                    <h2>Casos Asignados</h2>
                    <button type="button" class="btn-primary" onclick="cargarCasosAsignados()">
                        <i class="icon-refresh"></i> Actualizar Lista
                    </button>
                </div>

                <!-- Filtros -->
                <div class="filters">
                    <div class="filter-group">
                        <label for="filtroEstado">Estado:</label>
                        <select id="filtroEstado" onchange="filtrarCasos()">
                            <option value="">Todos</option>
                            <option value="ASIGNADO">Asignado</option>
                            <option value="EN_ANALISIS">En Análisis</option>
                            <option value="MANDAMIENTO_NOTIFICADO">Mandamiento Notificado</option>
                            <option value="EMBARGO_SOLICITADO">Embargo Solicitado</option>
                            <option value="EMBARGO_EJECUTADO">Embargo Ejecutado</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="filtroDeudor">Deudor:</label>
                        <input type="text" id="filtroDeudor" placeholder="Buscar por nombre o cédula" onkeyup="filtrarCasos()">
                    </div>
                </div>

                <!-- Lista de Casos -->
                <div class="casos-lista" id="casosLista">
                    <div class="loading" id="loadingCasos">Cargando casos...</div>
                </div>
            </div>

            <!-- Tab: Crear Mandamiento -->
            <div id="crear-mandamiento" class="tab-content">
                <div class="section-header">
                    <h2>Crear Mandamiento de Pago</h2>
                </div>

                <form id="formMandamiento" class="form-grid">
                    <div class="form-group">
                        <label for="casoIdMandamiento">Caso ID:</label>
                        <input type="number" id="casoIdMandamiento" required readonly>
                        <button type="button" class="btn-secondary" onclick="buscarCaso()">Buscar Caso</button>
                    </div>

                    <div class="form-group">
                        <label for="deudorInfo">Información del Deudor:</label>
                        <div class="info-box" id="deudorInfo">Seleccione un caso para ver la información</div>
                    </div>

                    <div class="form-group">
                        <label for="montoCapital">Monto Capital:</label>
                        <input type="number" id="montoCapital" step="0.01" required readonly>
                    </div>

                    <div class="form-group">
                        <label for="interesesMora">Intereses de Mora:</label>
                        <input type="number" id="interesesMora" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label for="costosAdministrativos">Costos Administrativos:</label>
                        <input type="number" id="costosAdministrativos" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label for="montoTotalMandamiento">Monto Total:</label>
                        <input type="number" id="montoTotalMandamiento" step="0.01" readonly>
                    </div>

                    <div class="form-group">
                        <label for="fechaLimitePago">Fecha Límite de Pago:</label>
                        <input type="date" id="fechaLimitePago" required>
                    </div>

                    <div class="form-group full-width">
                        <label for="observacionesMandamiento">Observaciones:</label>
                        <textarea id="observacionesMandamiento" rows="3" placeholder="Observaciones adicionales del mandamiento"></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-primary">
                            <i class="icon-save"></i> Crear Mandamiento
                        </button>
                        <button type="button" class="btn-secondary" onclick="generarPDFMandamiento()">
                            <i class="icon-pdf"></i> Generar PDF
                        </button>
                        <button type="reset" class="btn-secondary">Limpiar</button>
                    </div>
                </form>
            </div>

            <!-- Tab: Gestionar Embargos -->
            <div id="gestionar-embargos" class="tab-content">
                <div class="section-header">
                    <h2>Gestionar Embargos</h2>
                </div>

                <form id="formEmbargo" class="form-grid">
                    <div class="form-group">
                        <label for="casoIdEmbargo">Caso ID:</label>
                        <input type="number" id="casoIdEmbargo" required readonly>
                        <button type="button" class="btn-secondary" onclick="buscarCasoEmbargo()">Buscar Caso</button>
                    </div>

                    <div class="form-group">
                        <label for="tipoEmbargo">Tipo de Embargo:</label>
                        <select id="tipoEmbargo" required>
                            <option value="">Seleccionar...</option>
                            <option value="CUENTA_BANCARIA">Cuenta Bancaria</option>
                            <option value="BIEN_INMUEBLE">Bien Inmueble</option>
                            <option value="BIEN_VEHICULO">Bien Vehículo</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="entidadEmbargo">Entidad:</label>
                        <select id="entidadEmbargo" required>
                            <option value="">Seleccionar entidad...</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="montoSolicitado">Monto Solicitado:</label>
                        <input type="number" id="montoSolicitado" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label for="montoEjecutado">Monto Ejecutado:</label>
                        <input type="number" id="montoEjecutado" step="0.01" value="0">
                    </div>

                    <div class="form-group">
                        <label for="estadoEmbargo">Estado del Embargo:</label>
                        <select id="estadoEmbargo" required>
                            <option value="SOLICITADO">Solicitado</option>
                            <option value="EJECUTADO_TOTAL">Ejecutado Total</option>
                            <option value="EJECUTADO_PARCIAL">Ejecutado Parcial</option>
                            <option value="RECHAZADO">Rechazado</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="fechaEjecucionEmbargo">Fecha de Ejecución:</label>
                        <input type="date" id="fechaEjecucionEmbargo">
                    </div>

                    <div class="form-group full-width">
                        <label for="observacionesEmbargo">Observaciones:</label>
                        <textarea id="observacionesEmbargo" rows="3" placeholder="Detalles del embargo"></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            <i class="icon-save"></i> Registrar Embargo
                        </button>
                        <button type="button" class="btn-secondary" onclick="actualizarEmbargo()">
                            <i class="icon-update"></i> Actualizar Estado
                        </button>
                        <button type="reset" class="btn-secondary">Limpiar</button>
                    </div>
                </form>

                <!-- Lista de Embargos del Caso -->
                <div class="embargos-lista" id="embargosLista">
                    <h3>Embargos Registrados</h3>
                    <div id="listaEmbargosContainer"></div>
                </div>
            </div>

            <!-- Tab: Acuerdos de Pago -->
            <div id="acuerdos-pago" class="tab-content">
                <div class="section-header">
                    <h2>Acuerdos de Pago</h2>
                </div>

                <form id="formAcuerdo" class="form-grid">
                    <div class="form-group">
                        <label for="casoIdAcuerdo">Caso ID:</label>
                        <input type="number" id="casoIdAcuerdo" required readonly>
                        <button type="button" class="btn-secondary" onclick="buscarCasoAcuerdo()">Buscar Caso</button>
                    </div>

                    <div class="form-group">
                        <label for="montoTotalAcuerdo">Monto Total:</label>
                        <input type="number" id="montoTotalAcuerdo" step="0.01" required readonly>
                    </div>

                    <div class="form-group">
                        <label for="cuotasAcuerdo">Número de Cuotas:</label>
                        <input type="number" id="cuotasAcuerdo" min="1" max="12" required onchange="calcularCuotas()">
                    </div>

                    <div class="form-group">
                        <label for="frecuenciaAcuerdo">Frecuencia:</label>
                        <select id="frecuenciaAcuerdo" required onchange="calcularCuotas()">
                            <option value="MENSUAL">Mensual</option>
                            <option value="QUINCENAL">Quincenal</option>
                            <option value="SEMANAL">Semanal</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="valorCuota">Valor por Cuota:</label>
                        <input type="number" id="valorCuota" step="0.01" readonly>
                    </div>

                    <div class="form-group">
                        <label for="interesCondonado">¿Condonar Intereses?</label>
                        <div class="checkbox-group">
                            <input type="checkbox" id="interesCondonado">
                            <label for="interesCondonado">Sí, condonar intereses moratorios</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="fechaInicioAcuerdo">Fecha de Inicio:</label>
                        <input type="date" id="fechaInicioAcuerdo" required onchange="calcularFechaFin()">
                    </div>

                    <div class="form-group">
                        <label for="fechaFinAcuerdo">Fecha de Finalización:</label>
                        <input type="date" id="fechaFinAcuerdo" readonly>
                    </div>

                    <div class="form-group full-width">
                        <label for="observacionesAcuerdo">Observaciones:</label>
                        <textarea id="observacionesAcuerdo" rows="3" placeholder="Condiciones especiales del acuerdo"></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            <i class="icon-save"></i> Crear Acuerdo
                        </button>
                        <button type="button" class="btn-secondary" onclick="generarPDFAcuerdo()">
                            <i class="icon-pdf"></i> Generar PDF
                        </button>
                        <button type="reset" class="btn-secondary">Limpiar</button>
                    </div>
                </form>
            </div>

            <!-- Tab: Recursos Administrativos -->
            <div id="recursos" class="tab-content">
                <div class="section-header">
                    <h2>Recursos Administrativos</h2>
                </div>

                <form id="formRecurso" class="form-grid">
                    <div class="form-group">
                        <label for="casoIdRecurso">Caso ID:</label>
                        <input type="number" id="casoIdRecurso" required readonly>
                        <button type="button" class="btn-secondary" onclick="buscarCasoRecurso()">Buscar Caso</button>
                    </div>

                    <div class="form-group">
                        <label for="tipoRecurso">Tipo de Recurso:</label>
                        <select id="tipoRecurso" required>
                            <option value="">Seleccionar...</option>
                            <option value="REPOSICION">Reposición</option>
                            <option value="NULIDAD">Nulidad</option>
                            <option value="TUTELA">Tutela</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="fechaPresentacionRecurso">Fecha de Presentación:</label>
                        <input type="date" id="fechaPresentacionRecurso" required>
                    </div>

                    <div class="form-group">
                        <label for="estadoRecurso">Estado del Recurso:</label>
                        <select id="estadoRecurso" required>
                            <option value="PENDIENTE">Pendiente</option>
                            <option value="ADMITIDO">Admitido</option>
                            <option value="NEGADO">Negado</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="fechaResolucionRecurso">Fecha de Resolución:</label>
                        <input type="date" id="fechaResolucionRecurso">
                    </div>

                    <div class="form-group full-width">
                        <label for="decisionesRecurso">Decisiones/Observaciones:</label>
                        <textarea id="decisionesRecurso" rows="4" placeholder="Detalle de la decisión tomada"></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            <i class="icon-save"></i> Registrar Recurso
                        </button>
                        <button type="button" class="btn-secondary" onclick="actualizarRecurso()">
                            <i class="icon-update"></i> Actualizar Estado
                        </button>
                        <button type="reset" class="btn-secondary">Limpiar</button>
                    </div>
                </form>
            </div>
        </main>

        <!-- Modal para Buscar Casos -->
        <div id="modalBuscarCaso" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Buscar Caso</h3>
                    <span class="close" onclick="cerrarModal('modalBuscarCaso')">&times;</span>
                </div>
                <div class="modal-body">
                    <div class="search-group">
                        <input type="text" id="buscarCasoInput" placeholder="Ingrese ID del caso o nombre del deudor">
                        <button type="button" class="btn-primary" onclick="buscarCasos()">Buscar</button>
                    </div>
                    <div id="resultadosBusqueda" class="resultados-busqueda">
                        <!-- Resultados de búsqueda -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Carga -->
        <div id="modalLoading" class="modal">
            <div class="modal-content loading-modal">
                <div class="spinner"></div>
                <p>Procesando...</p>
            </div>
        </div>

        <!-- Toast para Notificaciones -->
        <div id="toast" class="toast"></div>
    </div>

    <script src="/assets/js/Modulo/Deudores/form_abogado.js"></script> 
    <script src="/assets/js/backToTop.js"></script>
</body>
</html>