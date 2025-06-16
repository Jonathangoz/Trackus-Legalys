
        // Datos de ejemplo
        const casesData = {
            "CC-2023-001": {
                id: "CC-2023-001",
                startDate: "15/04/2023",
                concept: "Impuesto Predial",
                amount: "$2,500,000",
                status: "Activo",
                documents: [
                    { name: "Resolución_001.pdf", date: "18/04/2023" },
                    { name: "Mandamiento_001.pdf", date: "20/04/2023" },
                    { name: "Notificación_001.pdf", date: "25/04/2023" }
                ],
                timeline: [
                    { date: "15/04/2023", event: "Inicio del proceso de cobro coactivo" },
                    { date: "18/04/2023", event: "Emisión de la resolución" },
                    { date: "20/04/2023", event: "Emisión del mandamiento de pago" },
                    { date: "25/04/2023", event: "Notificación al deudor" },
                    { date: "10/05/2023", event: "Vencimiento de términos para excepciones" }
                ]
            },
            "CC-2023-002": {
                id: "CC-2023-002",
                startDate: "22/05/2023",
                concept: "Multa de Tránsito",
                amount: "$850,000",
                status: "Pendiente",
                documents: [
                    { name: "Notificación_002.pdf", date: "25/05/2023" },
                    { name: "Mandamiento_002.pdf", date: "28/05/2023" }
                ],
                timeline: [
                    { date: "22/05/2023", event: "Inicio del proceso de cobro coactivo" },
                    { date: "25/05/2023", event: "Notificación al deudor" },
                    { date: "28/05/2023", event: "Emisión del mandamiento de pago" },
                    { date: "15/06/2023", event: "Vencimiento de términos para excepciones" }
                ]
            },
            "CC-2023-003": {
                id: "CC-2023-003",
                startDate: "10/06/2023",
                concept: "Licencia Comercial",
                amount: "$1,200,000",
                status: "Resuelto",
                documents: [
                    { name: "Resolución_003.pdf", date: "12/06/2023" },
                    { name: "Cierre_Caso_003.pdf", date: "12/06/2023" }
                ],
                timeline: [
                    { date: "10/06/2023", event: "Inicio del proceso de cobro coactivo" },
                    { date: "12/06/2023", event: "Pago total de la obligación" },
                    { date: "12/06/2023", event: "Cierre del caso" }
                ]
            },
            "CC-2023-004": {
                id: "CC-2023-004",
                startDate: "05/07/2023",
                concept: "Impuesto de Industria y Comercio",
                amount: "$3,200,000",
                status: "Activo",
                documents: [
                    { name: "Resolución_004.pdf", date: "06/07/2023" },
                    { name: "Mandamiento_004.pdf", date: "08/07/2023" },
                    { name: "Notificación_004.pdf", date: "12/07/2023" }
                ],
                timeline: [
                    { date: "05/07/2023", event: "Inicio del proceso de cobro coactivo" },
                    { date: "06/07/2023", event: "Emisión de la resolución" },
                    { date: "08/07/2023", event: "Emisión del mandamiento de pago" },
                    { date: "12/07/2023", event: "Notificación al deudor" },
                    { date: "05/08/2023", event: "Vencimiento de términos para excepciones" }
                ]
            },
            "CC-2023-005": {
                id: "CC-2023-005",
                startDate: "18/08/2023",
                concept: "Sanción Ambiental",
                amount: "$1,800,000",
                status: "Resuelto",
                documents: [
                    { name: "Resolución_005.pdf", date: "19/08/2023" },
                    { name: "Comprobante_Pago_005.pdf", date: "20/08/2023" },
                    { name: "Cierre_Caso_005.pdf", date: "22/08/2023" }
                ],
                timeline: [
                    { date: "18/08/2023", event: "Inicio del proceso de cobro coactivo" },
                    { date: "19/08/2023", event: "Emisión de la resolución" },
                    { date: "20/08/2023", event: "Pago total de la obligación" },
                    { date: "22/08/2023", event: "Cierre del caso" }
                ]
            }
        };

        // Variables globales
        let currentView = "dashboard";
        let selectedCase = null;
        let selectedFile = null;

        // Elementos DOM
        const dashboardView = document.getElementById("dashboard-view");
        const casosView = document.getElementById("casos-view");
        const documentosView = document.getElementById("documentos-view");
        const dashboardLink = document.getElementById("dashboard-link");
        const casosLink = document.getElementById("casos-link");
        const documentosLink = document.getElementById("documentos-link");
        const viewAllCasesBtn = document.getElementById("view-all-cases");
        const fileUploadArea = document.getElementById("file-upload-area");
        const fileInput = document.getElementById("file-input");
        const selectedFileDiv = document.getElementById("selected-file");
        const fileName = document.getElementById("file-name");
        const removeFileBtn = document.getElementById("remove-file");
        const uploadDocumentBtn = document.getElementById("upload-document");
        const tabs = document.querySelectorAll(".tab");
        const caseDetailModal = document.getElementById("case-detail-modal");
        const closeModalBtns = document.querySelectorAll(".close-modal, .close-modal-btn");
        const notification = document.getElementById("notification");
        const searchCaseInput = document.getElementById("search-case");
        const searchDocumentInput = document.getElementById("search-document");
        const uploadCaseDocumentBtn = document.getElementById("upload-case-document");

        // Navegación
        dashboardLink.addEventListener("click", () => {
            showView("dashboard");
        });

        casosLink.addEventListener("click", () => {
            showView("casos");
        });

        documentosLink.addEventListener("click", () => {
            showView("documentos");
        });

        viewAllCasesBtn.addEventListener("click", () => {
            showView("casos");
        });

        // Función para mostrar la vista seleccionada
        function showView(view) {
            currentView = view;
            dashboardView.style.display = view === "dashboard" ? "block" : "none";
            casosView.style.display = view === "casos" ? "block" : "none";
            documentosView.style.display = view === "documentos" ? "block" : "none";
        }

        // Gestión de archivos
        fileUploadArea.addEventListener("click", () => {
            fileInput.click();
        });

        fileUploadArea.addEventListener("dragover", (e) => {
            e.preventDefault();
            fileUploadArea.style.borderColor = "var(--accent-color)";
        });

        fileUploadArea.addEventListener("dragleave", () => {
            fileUploadArea.style.borderColor = "#ccc";
        });

        fileUploadArea.addEventListener("drop", (e) => {
            e.preventDefault();
            fileUploadArea.style.borderColor = "#ccc";
            
            if (e.dataTransfer.files.length > 0) {
                const file = e.dataTransfer.files[0];
                handleFileSelection(file);
            }
        });

        fileInput.addEventListener("change", () => {
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                handleFileSelection(file);
            }
        });

        function handleFileSelection(file) {
            if (file.type !== "application/pdf") {
                showNotification("Error: Solo se permiten archivos PDF", "error");
                return;
            }

            if (file.size > 10 * 1024 * 1024) { // 10MB
                showNotification("Error: El archivo excede el tamaño máximo de 10MB", "error");
                return;
            }

            selectedFile = file;
            fileName.textContent = file.name;
            selectedFileDiv.style.display = "block";
        }

        removeFileBtn.addEventListener("click", () => {
            selectedFile = null;
            fileName.textContent = "";
            selectedFileDiv.style.display = "none";
            fileInput.value = "";
        });

        uploadDocumentBtn.addEventListener("click", () => {
            const caseId = document.getElementById("case-selector").value;
            const documentType = document.getElementById("document-type").value;
            const description = document.getElementById("document-description").value;

            if (!caseId) {
                showNotification("Por favor, seleccione un caso", "error");
                return;
            }

            if (!documentType) {
                showNotification("Por favor, seleccione un tipo de documento", "error");
                return;
            }

            if (!selectedFile) {
                showNotification("Por favor, seleccione un archivo para subir", "error");
                return;
            }

            // Simulación de carga
            uploadDocumentBtn.disabled = true;
            uploadDocumentBtn.textContent = "Subiendo...";

            setTimeout(() => {
                // Simulación de éxito
                showNotification("Documento subido exitosamente", "success");
                
                // Reiniciar formulario
                document.getElementById("case-selector").value = "";
                document.getElementById("document-type").value = "";
                document.getElementById("document-description").value = "";
                removeFileBtn.click();
                
                uploadDocumentBtn.disabled = false;
                uploadDocumentBtn.textContent = "Subir Documento";
            }, 2000);
        });

        // Tabs
        tabs.forEach(tab => {
            tab.addEventListener("click", () => {
                const activeTab = document.querySelector(".tab.active");
                const activePanel = document.querySelector(".tab-panel.active");
                const targetPanel = document.getElementById(`${tab.dataset.tab}-tab`);
                
                activeTab.classList.remove("active");
                activePanel.classList.remove("active");
                
                tab.classList.add("active");
                targetPanel.classList.add("active");
            });
        });

        // Modal de detalles de caso
        document.querySelectorAll(".view-case").forEach(button => {
            button.addEventListener("click", (e) => {
                const caseId = e.target.closest(".case-row").dataset.id;
                openCaseDetailModal(caseId);
            });
        });

        closeModalBtns.forEach(button => {
            button.addEventListener("click", () => {
                caseDetailModal.classList.remove("active");
            });
        });

        function openCaseDetailModal(caseId) {
            selectedCase = casesData[caseId];
            
            if (!selectedCase) return;
            
            // Actualizar la información del modal
            document.getElementById("modal-case-title").textContent = `Detalles del Caso ${selectedCase.id}`;
            document.getElementById("detail-case-number").textContent = selectedCase.id;
            document.getElementById("detail-start-date").textContent = selectedCase.startDate;
            document.getElementById("detail-concept").textContent = selectedCase.concept;
            document.getElementById("detail-amount").textContent = selectedCase.amount;
            document.getElementById("detail-status").textContent = selectedCase.status;
            
            // Actualizar los documentos
            const caseDocumentsTable = document.getElementById("case-documents");
            caseDocumentsTable.innerHTML = "";
            
            selectedCase.documents.forEach(doc => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${doc.name}</td>
                    <td>${doc.date}</td>
                    <td><button class="btn btn-secondary download-document" data-file="${doc.name}">Descargar</button></td>
                `;
                caseDocumentsTable.appendChild(row);
            });
            
            // Actualizar la línea de tiempo
            const caseTimeline = document.getElementById("case-timeline");
            caseTimeline.innerHTML = "";
            
            selectedCase.timeline.forEach(item => {
                const timelineItem = document.createElement("div");
                timelineItem.className = "timeline-item";
                timelineItem.innerHTML = `
                    <div class="timeline-date">${item.date}</div>
                    <div class="timeline-content">${item.event}</div>
                `;
                caseTimeline.appendChild(timelineItem);
            });
            
            // Mostrar el modal
            caseDetailModal.classList.add("active");
        }

        // Notificaciones
        function showNotification(message, type) {
            notification.textContent = message;
            notification.className = `notification ${type}`;
            notification.style.display = "block";
            
            setTimeout(() => {
                notification.style.display = "none";
            }, 3000);
        }

        // Búsqueda de casos
        searchCaseInput.addEventListener("input", () => {
            const searchValue = searchCaseInput.value.toLowerCase();
            const caseRows = document.querySelectorAll("#all-cases .case-row");
            
            caseRows.forEach(row => {
                const caseId = row.querySelector("td:first-child").textContent.toLowerCase();
                const caseDate = row.querySelector("td:nth-child(2)").textContent.toLowerCase();
                const caseConcept = row.querySelector("td:nth-child(3)").textContent.toLowerCase();
                const caseStatus = row.querySelector(".status-badge").textContent.toLowerCase();
                
                if (caseId.includes(searchValue) || caseDate.includes(searchValue) || 
                    caseConcept.includes(searchValue) || caseStatus.includes(searchValue)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });

        // Búsqueda de documentos
        searchDocumentInput.addEventListener("input", () => {
            const searchValue = searchDocumentInput.value.toLowerCase();
            const documentRows = document.querySelectorAll("#document-list tr");
            
            documentRows.forEach(row => {
                const documentName = row.querySelector("td:first-child").textContent.toLowerCase();
                const documentCase = row.querySelector("td:nth-child(2)").textContent.toLowerCase();
                const documentType = row.querySelector("td:nth-child(3)").textContent.toLowerCase();
                
                if (documentName.includes(searchValue) || documentCase.includes(searchValue) || 
                    documentType.includes(searchValue)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });

        // Descargar documentos
        document.querySelectorAll(".download-document").forEach(button => {
            button.addEventListener("click", (e) => {
                const fileName = e.target.dataset.file;
                downloadDocument(fileName);
            });
        });

        function downloadDocument(fileName) {
            // Simulación de descarga
            showNotification(`Descargando ${fileName}...`, "success");
            
            // En un entorno real, aquí se realizaría la descarga del archivo
            setTimeout(() => {
                showNotification(`${fileName} descargado correctamente`, "success");
            }, 2000);
        }

        // Subir documento desde el modal de caso
        uploadCaseDocumentBtn.addEventListener("click", () => {
            if (!selectedCase) return;
            
            // Cambiar a la vista de documentos y preseleccionar el caso
            showView("documentos");
            document.getElementById("case-selector").value = selectedCase.id;
            
            // Activar la pestaña de subida
            document.querySelector(".tab[data-tab='upload']").click();
            
            // Cerrar el modal
            caseDetailModal.classList.remove("active");
        });

        // Inicializar la aplicación
        function init() {
            // Contar casos
            const activeCases = Object.values(casesData).filter(c => c.status === "Activo").length;
            const resolvedCases = Object.values(casesData).filter(c => c.status === "Resuelto").length;
            const totalCases = Object.keys(casesData).length;
            
            document.getElementById("active-cases").textContent = activeCases;
            document.getElementById("resolved-cases").textContent = resolvedCases;
            document.getElementById("total-cases").textContent = totalCases;
            
            // Configurar eventos para los documentos
            document.querySelectorAll(".download-document").forEach(button => {
                button.addEventListener("click", (e) => {
                    const fileName = e.target.dataset.file;
                    downloadDocument(fileName);
                });
            });
        }

        // Iniciar la aplicación
        init();
    