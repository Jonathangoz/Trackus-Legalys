
        // Datos de ejemplo para los procesos de cobro coactivo
        const procesosCobro = [
            {
                id: "RAD-2025-0001",
                deudor: "Empresa Construcciones S.A.S.",
                documento: "900123456-7",
                valor: "$12,500,000",
                fechaInicio: "15/01/2025",
                estado: "En proceso",
                etapas: [
                    {
                        nombre: "Notificación del mandamiento de pago",
                        fecha: "15/01/2025",
                        estado: "Completado",
                        descripcion: "Se envió notificación por correo certificado."
                    },
                    {
                        nombre: "Medidas cautelares",
                        fecha: "30/01/2025",
                        estado: "Completado",
                        descripcion: "Se decretó embargo de cuentas bancarias."
                    },
                    {
                        nombre: "Resolución que ordena seguir adelante la ejecución",
                        fecha: "15/02/2025",
                        estado: "En proceso",
                        descripcion: "Pendiente decisión sobre excepciones."
                    },
                    {
                        nombre: "Liquidación del crédito",
                        fecha: "Pendiente",
                        estado: "Pendiente",
                        descripcion: "No iniciado."
                    },
                    {
                        nombre: "Remate de bienes",
                        fecha: "Pendiente",
                        estado: "Pendiente",
                        descripcion: "No iniciado."
                    }
                ]
            },
            {
                id: "RAD-2025-0002",
                deudor: "Comercializadora El Éxito Ltda.",
                documento: "860123456-1",
                valor: "$8,750,000",
                fechaInicio: "22/01/2025",
                estado: "En proceso",
                etapas: [
                    {
                        nombre: "Notificación del mandamiento de pago",
                        fecha: "22/01/2025",
                        estado: "Completado",
                        descripcion: "Notificación personal realizada."
                    },
                    {
                        nombre: "Resolución de excepciones",
                        fecha: "15/02/2025",
                        estado: "Completado",
                        descripcion: "Se negaron las excepciones presentadas."
                    },
                    {
                        nombre: "Medidas cautelares",
                        fecha: "20/02/2025",
                        estado: "En proceso",
                        descripcion: "En espera de respuesta de entidades financieras."
                    }
                ]
            },
            {
                id: "RAD-2025-0003",
                deudor: "Juan Carlos Rodríguez",
                documento: "79856321",
                valor: "$3,200,000",
                fechaInicio: "05/02/2025",
                estado: "Finalizado",
                etapas: [
                    {
                        nombre: "Notificación del mandamiento de pago",
                        fecha: "05/02/2025",
                        estado: "Completado",
                        descripcion: "Notificación por aviso."
                    },
                    {
                        nombre: "Resolución que ordena seguir adelante la ejecución",
                        fecha: "28/02/2025",
                        estado: "Completado",
                        descripcion: "No se presentaron excepciones."
                    },
                    {
                        nombre: "Pago total de la obligación",
                        fecha: "10/03/2025",
                        estado: "Completado",
                        descripcion: "El deudor realizó el pago total de la obligación."
                    },
                    {
                        nombre: "Auto de terminación del proceso",
                        fecha: "15/03/2025",
                        estado: "Completado",
                        descripcion: "Se terminó el proceso por pago total."
                    }
                ]
            },
            {
                id: "RAD-2025-0004",
                deudor: "Transportes Rápidos S.A.",
                documento: "901234567-8",
                valor: "$25,600,000",
                fechaInicio: "12/02/2025",
                estado: "Suspendido",
                etapas: [
                    {
                        nombre: "Notificación del mandamiento de pago",
                        fecha: "12/02/2025",
                        estado: "Completado",
                        descripcion: "Notificación por correo certificado."
                    },
                    {
                        nombre: "Solicitud de acuerdo de pago",
                        fecha: "01/03/2025",
                        estado: "Completado",
                        descripcion: "Se recibió solicitud de acuerdo de pago."
                    },
                    {
                        nombre: "Suspensión del proceso",
                        fecha: "15/03/2025",
                        estado: "Completado",
                        descripcion: "Se suspendió el proceso por acuerdo de pago."
                    }
                ]
            },
            {
                id: "RAD-2025-0005",
                deudor: "María Fernanda Gómez",
                documento: "52456789",
                valor: "$4,800,000",
                fechaInicio: "20/02/2025",
                estado: "En proceso",
                etapas: [
                    {
                        nombre: "Notificación del mandamiento de pago",
                        fecha: "20/02/2025",
                        estado: "Completado",
                        descripcion: "Notificación personal."
                    },
                    {
                        nombre: "Presentación de excepciones",
                        fecha: "05/03/2025",
                        estado: "Completado",
                        descripcion: "El deudor presentó excepciones."
                    },
                    {
                        nombre: "Resolución de excepciones",
                        fecha: "25/03/2025",
                        estado: "En proceso",
                        descripcion: "En estudio por parte del funcionario ejecutor."
                    }
                ]
            },
            {
                id: "RAD-2025-0006",
                deudor: "Tecnología Avanzada S.A.S.",
                documento: "900987654-3",
                valor: "$18,900,000",
                fechaInicio: "05/03/2025",
                estado: "En proceso",
                etapas: [
                    {
                        nombre: "Notificación del mandamiento de pago",
                        fecha: "05/03/2025",
                        estado: "Completado",
                        descripcion: "Notificación por aviso."
                    },
                    {
                        nombre: "Medidas cautelares",
                        fecha: "20/03/2025",
                        estado: "En proceso",
                        descripcion: "Se decretó embargo de bienes inmuebles."
                    }
                ]
            }
        ];

        // Función para cargar los procesos en la lista
        function cargarProcesos(procesos) {
            const processList = document.querySelector('.process-list');
            const header = processList.querySelector('.process-list-header');
            
            // Limpiar la lista de procesos (mantener el encabezado)
            while (processList.childElementCount > 1) {
                processList.removeChild(processList.lastChild);
            }
            
            // Agregar los nuevos procesos
            procesos.forEach(proceso => {
                const item = document.createElement('div');
                item.className = 'process-item';
                item.dataset.id = proceso.id;
                
                item.innerHTML = `
                    <div>${proceso.id}</div>
                    <div>${proceso.deudor}</div>
                    <div>${proceso.valor}</div>
                    <div>${proceso.fechaInicio}</div>
                    <div>${proceso.estado}</div>
                `;
                
                // Añadir evento click para mostrar detalles
                item.addEventListener('click', () => mostrarDetalles(proceso));
                
                processList.appendChild(item);
            });
        }

        // Función para mostrar los detalles de un proceso
        function mostrarDetalles(proceso) {
            const detailsContainer = document.getElementById('process-details');
            
            // Crear el contenido de los detalles
            detailsContainer.innerHTML = `
                <div class="detail-header">
                    <h3>Proceso: ${proceso.id}</h3>
                    <button class="close-btn" onclick="cerrarDetalles()">×</button>
                </div>
                
                <div class="detail-info">
                    <p><strong>Deudor:</strong> ${proceso.deudor}</p>
                    <p><strong>Documento:</strong> ${proceso.documento}</p>
                    <p><strong>Valor de la obligación:</strong> ${proceso.valor}</p>
                    <p><strong>Fecha de inicio:</strong> ${proceso.fechaInicio}</p>
                    <p><strong>Estado actual:</strong> ${proceso.estado}</p>
                </div>
                
                <div class="process-stages">
                    <h4>Etapas del proceso:</h4>
                    ${proceso.etapas.map(etapa => `
                        <div class="stage">
                            <div class="stage-header">${etapa.nombre}</div>
                            <div class="stage-date">${etapa.fecha}</div>
                            <div class="stage-status status-${etapa.estado.toLowerCase().replace(' ', '-')}">${etapa.estado}</div>
                            <p>${etapa.descripcion}</p>
                        </div>
                    `).join('')}
                </div>
            `;
            
            // Mostrar el contenedor de detalles
            detailsContainer.classList.add('active');
            
            // Desplazarse a los detalles
            detailsContainer.scrollIntoView({ behavior: 'smooth' });
        }

        // Función para cerrar los detalles
        function cerrarDetalles() {
            const detailsContainer = document.getElementById('process-details');
            detailsContainer.classList.remove('active');
        }

        // Función para buscar procesos
        function buscarProcesos() {
            const searchValue = document.getElementById('search-input').value.toLowerCase();
            const searchType = document.getElementById('search-type').value;
            
            if (!searchValue) {
                cargarProcesos(procesosCobro);
                return;
            }
            
            let resultados;
            
            switch (searchType) {
                case 'documento':
                    resultados = procesosCobro.filter(p => p.documento.toLowerCase().includes(searchValue));
                    break;
                case 'radicado':
                    resultados = procesosCobro.filter(p => p.id.toLowerCase().includes(searchValue));
                    break;
                case 'nombre':
                    resultados = procesosCobro.filter(p => p.deudor.toLowerCase().includes(searchValue));
                    break;
                default:
                    resultados = procesosCobro;
            }
            
            cargarProcesos(resultados);
        }

        // Función para filtrar por estado
        function filtrarPorEstado() {
            const filterValue = document.getElementById('filter-status').value;
            
            if (filterValue === 'todos') {
                cargarProcesos(procesosCobro);
                return;
            }
            
            const estadoFiltro = {
                'en-proceso': 'En proceso',
                'finalizado': 'Finalizado',
                'suspendido': 'Suspendido'
            }[filterValue];
            
            const resultados = procesosCobro.filter(p => p.estado === estadoFiltro);
            cargarProcesos(resultados);
        }

        // Inicialización
        document.addEventListener('DOMContentLoaded', () => {
            // Cargar la lista inicial de procesos
            cargarProcesos(procesosCobro);
            
            // Configurar eventos
            document.getElementById('search-btn').addEventListener('click', buscarProcesos);
            document.getElementById('filter-status').addEventListener('change', filtrarPorEstado);
            
            // Configurar paginación
            document.getElementById('prev-page').addEventListener('click', () => {
                alert('Página anterior');
            });
            
            document.getElementById('next-page').addEventListener('click', () => {
                alert('Página siguiente');
            });
            
            // Configurar eventos de teclado para la búsqueda
            document.getElementById('search-input').addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    buscarProcesos();
                }
            });
        });
  