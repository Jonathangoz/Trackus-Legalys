// abogado.js - Sistema de Gestión Legal
class AbogadoSystem {
    constructor() {
        this.currentPage = 1;
        this.pageSize = 10;
        this.currentFilters = {};
        this.sortColumn = '';
        this.sortDirection = 'asc';
        this.selectedCaseId = null;
        this.mockData = {
            cases: [
                {
                    id: 1,
                    radicado: 'RAD-2025-001234',
                    deudor: 'Juan Carlos Pérez',
                    tipo: 'cobro-persuasivo',
                    estado: 'pendiente',
                    monto: 2500000,
                    fechaAsignacion: '2025-06-10',
                    fechaActualizacion: '2025-06-14',
                    urgente: true,
                    descripcion: 'Cobro de servicios públicos pendientes',
                    documentos: ['Contrato', 'Facturación']
                },
                {
                    id: 2,
                    radicado: 'RAD-2025-001180',
                    deudor: 'María López Hernández',
                    tipo: 'cobro-coactivo',
                    estado: 'en-proceso',
                    monto: 4800000,
                    fechaAsignacion: '2025-06-05',
                    fechaActualizacion: '2025-06-13',
                    urgente: true,
                    descripcion: 'Proceso coactivo por impuestos municipales',
                    documentos: ['Mandamiento', 'Notificación']
                },
                {
                    id: 3,
                    radicado: 'RAD-2025-001156',
                    deudor: 'Empresa ABC S.A.S',
                    tipo: 'embargo',
                    estado: 'completado',
                    monto: 15000000,
                    fechaAsignacion: '2025-05-20',
                    fechaActualizacion: '2025-06-12',
                    urgente: false,
                    descripcion: 'Embargo de bienes inmuebles',
                    documentos: ['Avalúo', 'Oficio']
                }
            ],
            documents: [
                {
                    id: 1,
                    tipo: 'mandamiento',
                    caso: 'RAD-2025-001234',
                    fechaGeneracion: '2025-06-14',
                    estado: 'generado'
                },
                {
                    id: 2,
                    tipo: 'embargo',
                    caso: 'RAD-2025-001180',
                    fechaGeneracion: '2025-06-13',
                    estado: 'enviado'
                }
            ]
        };
        
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadInitialData();
        this.initializeNotifications();
        this.setupResponsiveHandlers();
    }

    setupEventListeners() {
        // Navegación
        document.querySelectorAll('.nav-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.switchSection(e.target.closest('.nav-btn').dataset.section);
            });
        });

        // Notificaciones
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationDropdown = document.getElementById('notificationDropdown');
        
        if (notificationBtn) {
            notificationBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                notificationDropdown.classList.toggle('show');
            });
        }

        // Cerrar notificaciones al hacer clic fuera
        document.addEventListener('click', () => {
            if (notificationDropdown) {
                notificationDropdown.classList.remove('show');
            }
        });

        // Filtros
        document.getElementById('aplicarFiltros')?.addEventListener('click', () => {
            this.applyFilters();
        });

        document.getElementById('limpiarFiltros')?.addEventListener('click', () => {
            this.clearFilters();
        });

        // Paginación
        document.getElementById('prevPage')?.addEventListener('click', () => {
            this.goToPage(this.currentPage - 1);
        });

        document.getElementById('nextPage')?.addEventListener('click', () => {
            this.goToPage(this.currentPage + 1);
        });

        // Ordenamiento de tabla
        document.querySelectorAll('.sortable').forEach(header => {
            header.addEventListener('click', () => {
                this.sortTable(header.dataset.column);
            });
        });

        // Selector de caso
        document.getElementById('caseSelector')?.addEventListener('change', (e) => {
            this.loadCaseDetail(e.target.value);
        });

        // Refresh caso
        document.getElementById('refreshCase')?.addEventListener('click', () => {
            this.refreshCaseDetail();
        });

        // Generación de documentos
        document.querySelectorAll('.generate-doc-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.generateDocument(e.target.dataset.type);
            });
        });

        // Modal de documento
        document.getElementById('closeDocPreview')?.addEventListener('click', () => {
            this.closeDocPreview();
        });

        document.getElementById('cancelDocPreview')?.addEventListener('click', () => {
            this.closeDocPreview();
        });

        document.getElementById('downloadDoc')?.addEventListener('click', () => {
            this.downloadDocument();
        });

        // Logout
        document.querySelector('.logout-btn')?.addEventListener('click', () => {
            this.logout();
        });
    }

    setupResponsiveHandlers() {
        const handleResize = () => {
            const width = window.innerWidth;
            
            // Ajustes para móvil (320px - 480px)
            if (width <= 480) {
                this.adjustMobileLayout();
            }
            // Ajustes para tablet (481px - 760px)
            else if (width <= 760) {
                this.adjustTabletLayout();
            }
            // Ajustes para desktop pequeño (761px - 1024px)
            else if (width <= 1024) {
                this.adjustSmallDesktopLayout();
            }
            // Desktop grande (>1024px)
            else {
                this.adjustDesktopLayout();
            }
        };

        window.addEventListener('resize', handleResize);
        handleResize(); // Ejecutar al cargar
    }

    adjustMobileLayout() {
        // Simplificar tabla para móvil
        const table = document.querySelector('.cases-table');
        if (table) {
            table.classList.add('mobile-table');
        }

        // Ocultar columnas menos importantes en móvil
        const headers = document.querySelectorAll('.cases-table th');
        const cells = document.querySelectorAll('.cases-table td');
        
        headers.forEach((header, index) => {
            if (index > 3) { // Mostrar solo las primeras 4 columnas
                header.style.display = 'none';
            }
        });

        cells.forEach((cell, index) => {
            if ((index + 1) % 7 > 4 && (index + 1) % 7 !== 0) { // Ocultar columnas no esenciales
                cell.style.display = 'none';
            }
        });
    }

    adjustTabletLayout() {
        const table = document.querySelector('.cases-table');
        if (table) {
            table.classList.remove('mobile-table');
        }

        // Mostrar más columnas en tablet
        const headers = document.querySelectorAll('.cases-table th');
        const cells = document.querySelectorAll('.cases-table td');
        
        headers.forEach(header => {
            header.style.display = '';
        });

        cells.forEach(cell => {
            cell.style.display = '';
        });
    }

    adjustSmallDesktopLayout() {
        // Layout estándar para desktop pequeño
        this.adjustDesktopLayout();
    }

    adjustDesktopLayout() {
        // Layout completo para desktop
        const table = document.querySelector('.cases-table');
        if (table) {
            table.classList.remove('mobile-table');
        }
    }

    switchSection(sectionName) {
        // Ocultar todas las secciones
        document.querySelectorAll('.content-section').forEach(section => {
            section.classList.remove('active');
        });

        // Mostrar la sección seleccionada
        const targetSection = document.getElementById(sectionName);
        if (targetSection) {
            targetSection.classList.add('active');
        }

        // Actualizar navegación
        document.querySelectorAll('.nav-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        const activeBtn = document.querySelector(`[data-section="${sectionName}"]`);
        if (activeBtn) {
            activeBtn.classList.add('active');
        }

        // Cargar datos específicos de la sección
        this.loadSectionData(sectionName);
    }

    loadSectionData(sectionName) {
        switch (sectionName) {
            case 'bandeja':
                this.loadCases();
                break;
            case 'detalle':
                this.loadCaseSelector();
                break;
            case 'documentos':
                this.loadGeneratedDocuments();
                break;
            case 'reportes':
                this.loadReports();
                break;
        }
    }

    loadInitialData() {
        this.loadCases();
        this.updateStats();
        this.loadCaseSelector();
    }

    initializeNotifications() {
        // Simular notificaciones en tiempo real
        setInterval(() => {
            this.checkNewNotifications();
        }, 30000); // Cada 30 segundos
    }

    checkNewNotifications() {
        // Simulación de nuevas notificaciones
        const notifications = [
            'Nuevo caso asignado',
            'Caso próximo a vencer',
            'Documento generado',
            'Pago registrado'
        ];

        if (Math.random() > 0.8) { // 20% de probabilidad
            const randomNotification = notifications[Math.floor(Math.random() * notifications.length)];
            this.showNotification(randomNotification);
        }
    }

    showNotification(message) {
        // Actualizar badge de notificaciones
        const badge = document.querySelector('.notification-badge');
        if (badge) {
            const currentCount = parseInt(badge.textContent) || 0;
            badge.textContent = currentCount + 1;
        }

        // Mostrar toast notification
        this.showToast(message, 'info');
    }

    showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}-circle"></i>
            <span>${message}</span>
        `;

        document.body.appendChild(toast);

        // Mostrar toast
        setTimeout(() => toast.classList.add('show'), 100);

        // Ocultar y remover toast
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => document.body.removeChild(toast), 300);
        }, 3000);
    }

    loadCases() {
        this.showLoading();
        
        // Simular llamada AJAX
        setTimeout(() => {
            const filteredCases = this.applyFiltersToData(this.mockData.cases);
            const sortedCases = this.applySorting(filteredCases);
            const paginatedCases = this.applyPagination(sortedCases);
            
            this.renderCasesTable(paginatedCases);
            this.updatePagination(filteredCases.length);
            this.updateStats();
            this.hideLoading();
        }, 500);
    }

    applyFiltersToData(cases) {
        return cases.filter(caso => {
            let matches = true;

            if (this.currentFilters.tipoTramite && caso.tipo !== this.currentFilters.tipoTramite) {
                matches = false;
            }

            if (this.currentFilters.estadoCaso && caso.estado !== this.currentFilters.estadoCaso) {
                matches = false;
            }

            if (this.currentFilters.fechaDesde) {
                const fechaFiltro = new Date(this.currentFilters.fechaDesde);
                const fechaCaso = new Date(caso.fechaAsignacion);
                if (fechaCaso < fechaFiltro) {
                    matches = false;
                }
            }

            if (this.currentFilters.fechaHasta) {
                const fechaFiltro = new Date(this.currentFilters.fechaHasta);
                const fechaCaso = new Date(caso.fechaAsignacion);
                if (fechaCaso > fechaFiltro) {
                    matches = false;
                }
            }

            return matches;
        });
    }

    applySorting(cases) {
        if (!this.sortColumn) return cases;

        return [...cases].sort((a, b) => {
            let aVal = a[this.sortColumn];
            let bVal = b[this.sortColumn];

            // Manejo especial para montos
            if (this.sortColumn === 'monto') {
                aVal = parseFloat(aVal);
                bVal = parseFloat(bVal);
            }

            // Manejo especial para fechas
            if (this.sortColumn === 'fecha') {
                aVal = new Date(a.fechaActualizacion);
                bVal = new Date(b.fechaActualizacion);
            }

            if (aVal < bVal) return this.sortDirection === 'asc' ? -1 : 1;
            if (aVal > bVal) return this.sortDirection === 'asc' ? 1 : -1;
            return 0;
        });
    }

    applyPagination(cases) {
        const startIndex = (this.currentPage - 1) * this.pageSize;
        const endIndex = startIndex + this.pageSize;
        return cases.slice(startIndex, endIndex);
    }

    renderCasesTable(cases) {
        const tbody = document.getElementById('casesTableBody');
        if (!tbody) return;

        tbody.innerHTML = '';

        cases.forEach(caso => {
            const row = document.createElement('tr');
            row.className = caso.urgente ? 'urgent-case' : '';
            
            row.innerHTML = `
                <td>
                    ${caso.urgente ? '<i class="fas fa-exclamation-triangle urgent-icon"></i>' : ''}
                    ${caso.radicado}
                </td>
                <td>${caso.deudor}</td>
                <td><span class="tipo-badge tipo-${caso.tipo}">${this.getTipoLabel(caso.tipo)}</span></td>
                <td><span class="estado-badge estado-${caso.estado}">${this.getEstadoLabel(caso.estado)}</span></td>
                <td>${this.formatCurrency(caso.monto)}</td>
                <td>${this.formatDate(caso.fechaActualizacion)}</td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn view-btn" onclick="abogadoSystem.viewCase(${caso.id})" title="Ver detalle">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="action-btn edit-btn" onclick="abogadoSystem.editCase(${caso.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn doc-btn" onclick="abogadoSystem.generateCaseDoc(${caso.id})" title="Generar documento">
                            <i class="fas fa-file-pdf"></i>
                        </button>
                    </div>
                </td>
            `;

            tbody.appendChild(row);
        });
    }

    getTipoLabel(tipo) {
        const labels = {
            'cobro-persuasivo': 'Cobro Persuasivo',
            'cobro-coactivo': 'Cobro Coactivo',
            'embargo': 'Embargo'
        };
        return labels[tipo] || tipo;
    }

    getEstadoLabel(estado) {
        const labels = {
            'pendiente': 'Pendiente',
            'en-analisis': 'En Análisis',
            'en-proceso': 'En Proceso',
            'completado': 'Completado'
        };
        return labels[estado] || estado;
    }

    formatCurrency(amount) {
        return new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0
        }).format(amount);
    }

    formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('es-CO');
    }

    updateStats() {
        const totalCases = this.mockData.cases.length;
        const urgentCases = this.mockData.cases.filter(c => c.urgente).length;

        document.getElementById('totalCases').textContent = totalCases;
        document.getElementById('urgentCases').textContent = urgentCases;
    }

    updatePagination(totalRecords) {
        const totalPages = Math.ceil(totalRecords / this.pageSize);
        const startRecord = (this.currentPage - 1) * this.pageSize + 1;
        const endRecord = Math.min(this.currentPage * this.pageSize, totalRecords);

        document.getElementById('showingFrom').textContent = startRecord;
        document.getElementById('showingTo').textContent = endRecord;
        document.getElementById('totalRecords').textContent = totalRecords;

        // Actualizar botones de paginación
        const prevBtn = document.getElementById('prevPage');
        const nextBtn = document.getElementById('nextPage');

        if (prevBtn) prevBtn.disabled = this.currentPage === 1;
        if (nextBtn) nextBtn.disabled = this.currentPage === totalPages;

        // Generar números de paginación
        this.generatePaginationNumbers(totalPages);
    }

    generatePaginationNumbers(totalPages) {
        const container = document.getElementById('paginationNumbers');
        if (!container) return;

        container.innerHTML = '';

        const maxVisible = 5;
        let startPage = Math.max(1, this.currentPage - Math.floor(maxVisible / 2));
        let endPage = Math.min(totalPages, startPage + maxVisible - 1);

        if (endPage - startPage + 1 < maxVisible) {
            startPage = Math.max(1, endPage - maxVisible + 1);
        }

        for (let i = startPage; i <= endPage; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.className = `pagination-number ${i === this.currentPage ? 'active' : ''}`;
            pageBtn.textContent = i;
            pageBtn.addEventListener('click', () => this.goToPage(i));
            container.appendChild(pageBtn);
        }
    }

    goToPage(page) {
        if (page < 1) return;
        this.currentPage = page;
        this.loadCases();
    }

    applyFilters() {
        this.currentFilters = {
            tipoTramite: document.getElementById('tipoTramite')?.value || '',
            estadoCaso: document.getElementById('estadoCaso')?.value || '',
            fechaDesde: document.getElementById('fechaDesde')?.value || '',
            fechaHasta: document.getElementById('fechaHasta')?.value || ''
        };

        this.currentPage = 1; // Reset a primera página
        this.loadCases();
        this.showToast('Filtros aplicados correctamente', 'success');
    }

    clearFilters() {
        document.getElementById('tipoTramite').value = '';
        document.getElementById('estadoCaso').value = '';
        document.getElementById('fechaDesde').value = '';
        document.getElementById('fechaHasta').value = '';

        this.currentFilters = {};
        this.currentPage = 1;
        this.loadCases();
        this.showToast('Filtros limpiados', 'info');
    }

    sortTable(column) {
        if (this.sortColumn === column) {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortColumn = column;
            this.sortDirection = 'asc';
        }

        // Actualizar iconos de ordenamiento
        document.querySelectorAll('.sortable i').forEach(icon => {
            icon.className = 'fas fa-sort';
        });

        const activeHeader = document.querySelector(`[data-column="${column}"] i`);
        if (activeHeader) {
            activeHeader.className = `fas fa-sort-${this.sortDirection === 'asc' ? 'up' : 'down'}`;
        }

        this.loadCases();
    }

    loadCaseSelector() {
        const selector = document.getElementById('caseSelector');
        if (!selector) return;

        selector.innerHTML = '<option value="">Seleccionar caso...</option>';
        
        this.mockData.cases.forEach(caso => {
            const option = document.createElement('option');
            option.value = caso.id;
            option.textContent = `${caso.radicado} - ${caso.deudor}`;
            selector.appendChild(option);
        });
    }

    loadCaseDetail(caseId) {
        if (!caseId) {
            this.showNoCaseSelected();
            return;
        }

        this.selectedCaseId = caseId;
        this.showLoading();

        // Simular llamada AJAX
        setTimeout(() => {
            const caso = this.mockData.cases.find(c => c.id == caseId);
            if (caso) {
                this.renderCaseDetail(caso);
            }
            this.hideLoading();
        }, 300);
    }

    showNoCaseSelected() {
        const container = document.getElementById('caseDetailContainer');
        if (!container) return;

        container.innerHTML = `
            <div class="no-case-selected">
                <i class="fas fa-file-alt"></i>
                <h3>Selecciona un caso para ver los detalles</h3>
                <p>Utiliza el selector superior o haz clic en un caso desde la bandeja</p>
            </div>
        `;
    }

    renderCaseDetail(caso) {
        const container = document.getElementById('caseDetailContainer');
        if (!container) return;

        container.innerHTML = `
            <div class="case-detail-content">
                <div class="case-header">
                    <div class="case-title">
                        <h3>${caso.radicado}</h3>
                        <span class="estado-badge estado-${caso.estado}">${this.getEstadoLabel(caso.estado)}</span>
                        ${caso.urgente ? '<span class="urgent-badge"><i class="fas fa-exclamation-triangle"></i> Urgente</span>' : ''}
                    </div>
                    <div class="case-actions">
                        <button class="action-btn edit-btn" onclick="abogadoSystem.editCase(${caso.id})">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="action-btn doc-btn" onclick="abogadoSystem.generateCaseDoc(${caso.id})">
                            <i class="fas fa-file-pdf"></i> Generar Documento
                        </button>
                    </div>
                </div>

                <div class="case-info-grid">
                    <div class="info-card">
                        <h4><i class="fas fa-user"></i> Información del Deudor</h4>
                        <div class="info-content">
                            <p><strong>Nombre:</strong> ${caso.deudor}</p>
                            <p><strong>Tipo de Trámite:</strong> ${this.getTipoLabel(caso.tipo)}</p>
                            <p><strong>Monto:</strong> ${this.formatCurrency(caso.monto)}</p>
                        </div>
                    </div>

                    <div class="info-card">
                        <h4><i class="fas fa-calendar"></i> Fechas Importantes</h4>
                        <div class="info-content">
                            <p><strong>Fecha de Asignación:</strong> ${this.formatDate(caso.fechaAsignacion)}</p>
                            <p><strong>Última Actualización:</strong> ${this.formatDate(caso.fechaActualizacion)}</p>
                        </div>
                    </div>

                    <div class="info-card">
                        <h4><i class="fas fa-file-alt"></i> Descripción</h4>
                        <div class="info-content">
                            <p>${caso.descripcion}</p>
                        </div>
                    </div>

                    <div class="info-card">
                        <h4><i class="fas fa-paperclip"></i> Documentos Adjuntos</h4>
                        <div class="info-content">
                            <div class="documents-list">
                                ${caso.documentos.map(doc => `
                                    <div class="document-item">
                                        <i class="fas fa-file-pdf"></i>
                                        <span>${doc}</span>
                                        <button class="download-doc-btn" title="Descargar">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="case-timeline">
                    <h4><i class="fas fa-history"></i> Historial del Caso</h4>
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h5>Caso asignado</h5>
                                <p>El caso fue asignado al abogado</p>
                                <small>${this.formatDate(caso.fechaAsignacion)}</small>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h5>En análisis</h5>
                                <p>Revisión de documentos y antecedentes</p>
                                <small>${this.formatDate(caso.fechaActualizacion)}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Actualizar selector de documentos
        document.getElementById('selectedCaseDoc').textContent = caso.radicado;
    }

    refreshCaseDetail() {
        if (this.selectedCaseId) {
            this.loadCaseDetail(this.selectedCaseId);
            this.showToast('Caso actualizado', 'success');
        }
    }

    // Métodos para acciones de casos
    viewCase(caseId) {
        this.switchSection('detalle');
        document.getElementById('caseSelector').value = caseId;
        this.loadCaseDetail(caseId);
    }

    editCase(caseId) {
        this.showToast('Función de edición en desarrollo', 'info');
    }

    generateCaseDoc(caseId) {
        this.switchSection('documentos');
        this.selectedCaseId = caseId;
        const caso = this.mockData.cases.find(c => c.id == caseId);
        if (caso) {
            document.getElementById('selectedCaseDoc').textContent = caso.radicado;
        }
    }

    generateDocument(type) {
        if (!this.selectedCaseId) {
            this.showToast('Selecciona un caso primero', 'error');
            return;
        }

        this.showLoading();

        // Simular generación de documento
        setTimeout(() => {
            const newDoc = {
                id: Date.now(),
                tipo: type,
                caso: this.mockData.cases.find(c => c.id == this.selectedCaseId)?.radicado,
                fechaGeneracion: new Date().toISOString().split('T')[0],
                estado: 'generado'
            };

            this.mockData.documents.push(newDoc);
            this.loadGeneratedDocuments();
            this.showDocPreview(type);
            this.hideLoading();
            this.showToast('Documento generado exitosamente', 'success');
        }, 1500);
    }

    loadGeneratedDocuments() {
        const tbody = document.getElementById('generatedDocsBody');
        if (!tbody) return;

        tbody.innerHTML = '';

        this.mockData.documents.forEach(doc => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${this.getDocumentTypeLabel(doc.tipo)}</td>
                <td>${doc.caso}</td>
                <td>${this.formatDate(doc.fechaGeneracion)}</td>
                <td><span class="estado-badge estado-${doc.estado}">${this.getDocumentStatusLabel(doc.estado)}</span></td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn view-btn" onclick="abogadoSystem.previewDocument(${doc.id})" title="Vista previa">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="action-btn download-btn" onclick="abogadoSystem.downloadGeneratedDoc(${doc.id})" title="Descargar">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    getDocumentTypeLabel(type) {
        const labels = {
            'mandamiento': 'Mandamiento de Pago',
            'embargo': 'Oficio de Embargo',
            'acuerdo': 'Acuerdo de Pago'
        };
        return labels[type] || type;
    }

    getDocumentStatusLabel(status) {
        const labels = {
            'generado': 'Generado',
            'enviado': 'Enviado',
            'firmado': 'Firmado'
        };
        return labels[status] || status;
    }

    showDocPreview(type) {
        const modal = document.getElementById('docPreviewModal');
        const title = document.getElementById('docPreviewTitle');
        const frame = document.getElementById('docPreviewFrame');

        if (modal && title && frame) {
            title.textContent = `Vista Previa - ${this.getDocumentTypeLabel(type)}`;
            
            // Simular contenido del documento
            const docContent = this.generateDocumentContent(type);
            const blob = new Blob([docContent], { type: 'text/html' });
            const url = URL.createObjectURL(blob);
            
            frame.src = url;
            modal.classList.add('show');
        }
    }

    generateDocumentContent(type) {
        const caso = this.mockData.cases.find(c => c.id == this.selectedCaseId);
        if (!caso) return '';

        const templates = {
            'mandamiento': `
                <html>
                <head>
                    <meta charset="UTF-8">
                    <style>
                        body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
                        .header { text-align: center; margin-bottom: 30px; }
                        .content { margin: 20px 0; }
                        .signature { margin-top: 50px; text-align: center; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>MANDAMIENTO DE PAGO</h1>
                        <p><strong>Radicado:</strong> ${caso.radicado}</p>
                    </div>
                    <div class="content">
                        <p><strong>Deudor:</strong> ${caso.deudor}</p>
                        <p><strong>Monto Adeudado:</strong> ${this.formatCurrency(caso.monto)}</p>
                        <p><strong>Concepto:</strong> ${caso.descripcion}</p>
                        
                        <p>Por medio del presente documento se requiere al deudor para que en el término de cinco (5) días hábiles, contados a partir de la notificación del presente mandamiento, proceda a cancelar la suma adeudada.</p>
                        
                        <p>En caso de no cumplir con el pago en el término establecido, se procederá a iniciar el cobro coactivo correspondiente.</p>
                    </div>
                    <div class="signature">
                        <p>_________________________</p>
                        <p>Dra. María González</p>
                        <p>Abogada</p>
                    </div>
                </body>
                </html>
            `,
            'embargo': `
                <html>
                <head>
                    <meta charset="UTF-8">
                    <style>
                        body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
                        .header { text-align: center; margin-bottom: 30px; }
                        .content { margin: 20px 0; }
                        .signature { margin-top: 50px; text-align: center; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>OFICIO DE EMBARGO</h1>
                        <p><strong>Radicado:</strong> ${caso.radicado}</p>
                    </div>
                    <div class="content">
                        <p><strong>Deudor:</strong> ${caso.deudor}</p>
                        <p><strong>Monto:</strong> ${this.formatCurrency(caso.monto)}</p>
                        
                        <p>Se solicita proceder con el embargo de los bienes del deudor mencionado, por la suma indicada más los intereses y costas del proceso.</p>
                        
                        <p>Los bienes a embargar deberán ser suficientes para cubrir el monto de la obligación.</p>
                    </div>
                    <div class="signature">
                        <p>_________________________</p>
                        <p>Dra. María González</p>
                        <p>Abogada</p>
                    </div>
                </body>
                </html>
            `,
            'acuerdo': `
                <html>
                <head>
                    <meta charset="UTF-8">
                    <style>
                        body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
                        .header { text-align: center; margin-bottom: 30px; }
                        .content { margin: 20px 0; }
                        .signature { margin-top: 50px; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>ACUERDO DE PAGO</h1>
                        <p><strong>Radicado:</strong> ${caso.radicado}</p>
                    </div>
                    <div class="content">
                        <p><strong>Deudor:</strong> ${caso.deudor}</p>
                        <p><strong>Monto Total:</strong> ${this.formatCurrency(caso.monto)}</p>
                        
                        <p>Las partes acuerdan el siguiente plan de pagos:</p>
                        <ul>
                            <li>Cuota inicial: ${this.formatCurrency(caso.monto * 0.3)}</li>
                            <li>Saldo: ${this.formatCurrency(caso.monto * 0.7)} en 6 cuotas mensuales</li>
                        </ul>
                        
                        <p>El incumplimiento de este acuerdo dará lugar al cobro inmediato de la totalidad de la deuda.</p>
                    </div>
                    <div class="signature">
                        <table width="100%">
                            <tr>
                                <td width="50%" style="text-align: center;">
                                    <p>_________________________</p>
                                    <p>Dra. María González</p>
                                    <p>Abogada</p>
                                </td>
                                <td width="50%" style="text-align: center;">
                                    <p>_________________________</p>
                                    <p>${caso.deudor}</p>
                                    <p>Deudor</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </body>
                </html>
            `
        };

        return templates[type] || '<p>Plantilla no encontrada</p>';
    }

    closeDocPreview() {
        const modal = document.getElementById('docPreviewModal');
        if (modal) {
            modal.classList.remove('show');
        }
    }

    downloadDocument() {
        const frame = document.getElementById('docPreviewFrame');
        if (frame && frame.src) {
            const link = document.createElement('a');
            link.href = frame.src;
            link.download = 'documento.html';
            link.click();
        }
        this.showToast('Documento descargado', 'success');
    }

    previewDocument(docId) {
        const doc = this.mockData.documents.find(d => d.id === docId);
        if (doc) {
            this.showDocPreview(doc.tipo);
        }
    }

    downloadGeneratedDoc(docId) {
        this.showToast('Descargando documento...', 'info');
        // Simular descarga
        setTimeout(() => {
            this.showToast('Documento descargado exitosamente', 'success');
        }, 1000);
    }

    loadReports() {
        this.showLoading();
        
        // Simular carga de reportes
        setTimeout(() => {
            this.updateReportStats();
            this.generateCharts();
            this.hideLoading();
        }, 800);
    }

    updateReportStats() {
        // Calcular estadísticas
        const totalCases = this.mockData.cases.length;
        const completedCases = this.mockData.cases.filter(c => c.estado === 'completado').length;
        const generatedDocs = this.mockData.documents.length;
        const avgTime = 15; // Días promedio simulado

        // Actualizar elementos del DOM
        document.getElementById('casosAsignados').textContent = totalCases;
        document.getElementById('casosCompletados').textContent = completedCases;
        document.getElementById('documentosGenerados').textContent = generatedDocs;
        document.getElementById('tiempoPromedio').textContent = avgTime;
    }

    generateCharts() {
        // Simular generación de gráficos
        this.generateEstadosChart();
        this.generateProductividadChart();
    }

    generateEstadosChart() {
        const canvas = document.getElementById('estadosChart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        
        // Datos simulados para el gráfico
        const estados = ['Pendiente', 'En Análisis', 'En Proceso', 'Completado'];
        const counts = [5, 3, 2, 1];
        const colors = ['#ff6b6b', '#feca57', '#48dbfb', '#1dd1a1'];

        // Gráfico de dona simple
        const total = counts.reduce((a, b) => a + b, 0);
        let currentAngle = 0;
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;
        const radius = Math.min(centerX, centerY) - 20;

        ctx.clearRect(0, 0, canvas.width, canvas.height);

        counts.forEach((count, index) => {
            const sliceAngle = (count / total) * 2 * Math.PI;
            
            ctx.beginPath();
            ctx.arc(centerX, centerY, radius, currentAngle, currentAngle + sliceAngle);
            ctx.lineTo(centerX, centerY);
            ctx.fillStyle = colors[index];
            ctx.fill();
            
            currentAngle += sliceAngle;
        });

        // Agregar leyenda
        this.addChartLegend('estadosChart', estados, colors);
    }

    generateProductividadChart() {
        const canvas = document.getElementById('productividadChart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        
        // Datos simulados
        const meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'];
        const casos = [8, 12, 15, 10, 18, 11];

        ctx.clearRect(0, 0, canvas.width, canvas.height);

        const padding = 40;
        const chartWidth = canvas.width - 2 * padding;
        const chartHeight = canvas.height - 2 * padding;
        const maxValue = Math.max(...casos);

        // Dibujar ejes
        ctx.strokeStyle = '#ddd';
        ctx.lineWidth = 1;
        
        // Eje Y
        ctx.beginPath();
        ctx.moveTo(padding, padding);
        ctx.lineTo(padding, canvas.height - padding);
        ctx.stroke();
        
        // Eje X
        ctx.beginPath();
        ctx.moveTo(padding, canvas.height - padding);
        ctx.lineTo(canvas.width - padding, canvas.height - padding);
        ctx.stroke();

        // Dibujar barras
        const barWidth = chartWidth / meses.length * 0.6;
        const barSpacing = chartWidth / meses.length;

        ctx.fillStyle = '#3742fa';
        casos.forEach((valor, index) => {
            const barHeight = (valor / maxValue) * chartHeight;
            const x = padding + index * barSpacing + (barSpacing - barWidth) / 2;
            const y = canvas.height - padding - barHeight;
            
            ctx.fillRect(x, y, barWidth, barHeight);
            
            // Etiquetas
            ctx.fillStyle = '#333';
            ctx.font = '12px Arial';
            ctx.textAlign = 'center';
            ctx.fillText(meses[index], x + barWidth / 2, canvas.height - padding + 20);
            ctx.fillText(valor.toString(), x + barWidth / 2, y - 5);
            
            ctx.fillStyle = '#3742fa';
        });
    }

    addChartLegend(canvasId, labels, colors) {
        const canvas = document.getElementById(canvasId);
        const legend = document.createElement('div');
        legend.className = 'chart-legend';
        legend.style.cssText = 'display: flex; justify-content: center; margin-top: 10px; flex-wrap: wrap;';

        labels.forEach((label, index) => {
            const item = document.createElement('div');
            item.style.cssText = 'display: flex; align-items: center; margin: 5px 10px;';
            
            const colorBox = document.createElement('div');
            colorBox.style.cssText = `width: 12px; height: 12px; background-color: ${colors[index]}; margin-right: 5px;`;
            
            const text = document.createElement('span');
            text.textContent = label;
            text.style.fontSize = '12px';
            
            item.appendChild(colorBox);
            item.appendChild(text);
            legend.appendChild(item);
        });

        canvas.parentNode.appendChild(legend);
    }

    showLoading() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.classList.add('show');
        }
    }

    hideLoading() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.classList.remove('show');
        }
    }

    logout() {
        if (confirm('¿Estás seguro que deseas cerrar sesión?')) {
            this.showToast('Cerrando sesión...', 'info');
            setTimeout(() => {
                window.location.href = 'login.html';
            }, 1000);
        }
    }
}

// Inicializar el sistema cuando se carga la página
let abogadoSystem;
document.addEventListener('DOMContentLoaded', () => {
    abogadoSystem = new AbogadoSystem();
});

// Estilos adicionales para toast notifications
const toastStyles = `
<style>
.toast {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #333;
    color: white;
    padding: 12px 20px;
    border-radius: 5px;
    display: flex;
    align-items: center;
    gap: 10px;
    transform: translateX(400px);
    transition: transform 0.3s ease;
    z-index: 10000;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.toast.show {
    transform: translateX(0);
}

.toast-success {
    background: #1dd1a1;
}

.toast-error {
    background: #ff6b6b;
}

.toast-info {
    background: #3742fa;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.loading-overlay.show {
    opacity: 1;
    visibility: visible;
}

.loading-spinner {
    background: white;
    padding: 30px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

.loading-spinner i {
    font-size: 2rem;
    color: #3742fa;
    margin-bottom: 10px;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.modal.show {
    opacity: 1;
    visibility: visible;
}

.modal-content {
    background: white;
    border-radius: 10px;
    max-width: 90%;
    max-height: 90%;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.doc-modal {
    width: 900px;
    height: 700px;
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-body {
    padding: 20px;
    overflow-y: auto;
    max-height: 500px;
}

.modal-footer {
    padding: 20px;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.close-btn {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #666;
}

.close-btn:hover {
    color: #333;
}

@media (max-width: 768px) {
    .doc-modal {
        width: 95%;
        height: 90%;
    }
    
    .toast {
        right: 10px;
        left: 10px;
        transform: translateY(-100px);
    }
    
    .toast.show {
        transform: translateY(0);
    }
}
</style>
`;

// Agregar estilos al documento
document.head.insertAdjacentHTML('beforeend', toastStyles);