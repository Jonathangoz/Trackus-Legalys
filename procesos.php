<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesos de Cobro Coactivo - SENA Regional Santander</title>
    <link rel="stylesheet" href="./CSS/procesos.css">
    
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="./proyecto/img/sena.blanco.png" alt="Logo SENA">
            
        </div>
        <h1>Procesos de Cobro Coactivo</h1>
        <p>SENA Regional Santander</p>
    </header>
    
    <div class="container">
        <section class="search-section">
            <h2>Consulta de Procesos</h2>
            <div class="search-box">
                <input type="text" id="search-input" placeholder="Número de documento o radicado">
                <select id="search-type">
                    <option value="documento">Documento de identidad</option>
                    <option value="radicado">Número de radicado</option>
                    <option value="nombre">Nombre del deudor</option>
                </select>
                <button id="search-btn">Buscar</button>
            </div>
        </section>
        
        <section class="stats-section">
            <div class="stat-card">
                <div class="stat-title">Total de Procesos</div>
                <div class="stat-number">238</div>
                <div>Procesos activos</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Recaudo Total</div>
                <div class="stat-number">$1.2M</div>
                <div>Pesos colombianos</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Resueltos este mes</div>
                <div class="stat-number">17</div>
                <div>Procesos</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">En ejecución</div>
                <div class="stat-number">124</div>
                <div>Procesos</div>
            </div>
        </section>
        
        <section class="process-section">
            <div class="process-header">
                <h2>Listado de Procesos</h2>
                <select id="filter-status">
                    <option value="todos">Todos los estados</option>
                    <option value="en-proceso">En proceso</option>
                    <option value="finalizado">Finalizado</option>
                    <option value="suspendido">Suspendido</option>
                </select>
            </div>
            
            <div class="process-list">
                <div class="process-list-header">
                    <div>No. Radicado</div>
                    <div>Deudor</div>
                    <div>Valor</div>
                    <div>Fecha Inicio</div>
                    <div>Estado</div>
                </div>
                <!-- Los items serán cargados dinamicamente con JavaScript -->
            </div>
            
            <div id="process-details" class="process-detail">
                <!-- Detalles del proceso seleccionado serán cargados aquí -->
            </div>
            
            <div class="pagination">
                <button id="prev-page">←</button>
                <button class="active">1</button>
                <button>2</button>
                <button>3</button>
                <button id="next-page">→</button>
            </div>
        </section>
    </div>
    
    <footer>
        <p>© 2025 SENA - Servicio Nacional de Aprendizaje</p>
        <p>Regional Santander - Área de Cobro Coactivo</p>
        <p>Dirección: Calle 16 No. 27-37 Bucaramanga</p>
        <p>Teléfono: (607) 6800600</p>
    </footer>
    <script src="proyecto/js/Procesos.js"></script>
</body>
</html>
