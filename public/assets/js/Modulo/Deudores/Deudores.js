
    // Sample data for demonstration
    const deudoresData = [
        { id: 1, documento: "1098765432", nombre: "Juan Carlos Gómez", tipo: "Multa", monto: "$2,500,000", fecha: "15/01/2025", estado: "active" },
        { id: 2, documento: "63456789", nombre: "María Fernanda López", tipo: "Incumplimiento de Contrato", monto: "$5,800,000", fecha: "22/02/2025", estado: "pending" },
        { id: 3, documento: "91234567", nombre: "Pedro Antonio Ramírez", tipo: "Deuda Monetaria", monto: "$1,200,000", fecha: "05/03/2025", estado: "resolved" },
        { id: 4, documento: "1005678945", nombre: "Ana María Suárez", tipo: "Convenio", monto: "$3,400,000", fecha: "18/01/2025", estado: "active" },
        { id: 5, documento: "37123456", nombre: "Carlos Eduardo Moreno", tipo: "Multa", monto: "$1,800,000", fecha: "10/02/2025", estado: "pending" },
        { id: 6, documento: "13876543", nombre: "Luisa Fernanda Quintero", tipo: "Incumplimiento de Contrato", monto: "$7,200,000", fecha: "25/02/2025", estado: "active" },
        { id: 7, documento: "1098123456", nombre: "Roberto José Mendoza", tipo: "Deuda Monetaria", monto: "$950,000", fecha: "12/03/2025", estado: "resolved" },
        { id: 8, documento: "63987654", nombre: "Carmen Lucía Jiménez", tipo: "Convenio", monto: "$4,100,000", fecha: "08/01/2025", estado: "active" },
        { id: 9, documento: "91876543", nombre: "Francisco Javier Torres", tipo: "Multa", monto: "$2,300,000", fecha: "20/01/2025", estado: "pending" },
        { id: 10, documento: "1005432198", nombre: "Diana Patricia Ortiz", tipo: "Incumplimiento de Contrato", monto: "$6,500,000", fecha: "15/02/2025", estado: "active" },
        { id: 11, documento: "37654321", nombre: "José Miguel Castro", tipo: "Deuda Monetaria", monto: "$1,750,000", fecha: "03/03/2025", estado: "resolved" },
        { id: 12, documento: "13765432", nombre: "Sandra Milena Duarte", tipo: "Convenio", monto: "$3,800,000", fecha: "28/01/2025", estado: "active" },
        { id: 13, documento: "1098654321", nombre: "Andrés Felipe Rodríguez", tipo: "Multa", monto: "$2,100,000", fecha: "05/02/2025", estado: "pending" },
        { id: 14, documento: "63234567", nombre: "Martha Cecilia Vargas", tipo: "Incumplimiento de Contrato", monto: "$5,200,000", fecha: "17/02/2025", estado: "active" },
        { id: 15, documento: "91345678", nombre: "Ricardo Alberto Mejía", tipo: "Deuda Monetaria", monto: "$1,450,000", fecha: "01/03/2025", estado: "resolved" }
    ];
    
    // Variables for pagination
    let currentPage = 1;
    const rowsPerPage = 5;
    let filteredData = [...deudoresData];
    
    // DOM elements
    const resultsTable = document.getElementById('resultsTable');
    const resultCount = document.getElementById('resultCount');
    const pagination = document.getElementById('pagination');
    const searchBtn = document.getElementById('searchBtn');
    const resetBtn = document.getElementById('resetBtn');
    
    // Initialize the page
    document.addEventListener('DOMContentLoaded', function() {
        displayData(filteredData);
        setupEventListeners();
    });
    
    // Setup event listeners
    function setupEventListeners() {
        searchBtn.addEventListener('click', handleSearch);
        resetBtn.addEventListener('click', handleReset);
    }
    
    // Handle search
    function handleSearch() {
        const documento = document.getElementById('documento').value.toLowerCase();
        const nombre = document.getElementById('nombre').value.toLowerCase();
        const tipoProceso = document.getElementById('tipo-proceso').value.toLowerCase();
        const estado = document.getElementById('estado').value.toLowerCase();
        
        filteredData = deudoresData.filter(item => {
            return (
                (documento === '' || item.documento.toLowerCase().includes(documento)) &&
                (nombre === '' || item.nombre.toLowerCase().includes(nombre)) &&
                (tipoProceso === '' || item.tipo.toLowerCase().includes(tipoProceso)) &&
                (estado === '' || 
                    (estado === 'activo' && item.estado === 'active') ||
                    (estado === 'pendiente' && item.estado === 'pending') ||
                    (estado === 'resuelto' && item.estado === 'resolved'))
            );
        });
        
        currentPage = 1;
        displayData(filteredData);
    }
    
    // Handle reset
    function handleReset() {
        document.getElementById('documento').value = '';
        document.getElementById('nombre').value = '';
        document.getElementById('tipo-proceso').value = '';
        document.getElementById('estado').value = '';
        
        filteredData = [...deudoresData];
        currentPage = 1;
        displayData(filteredData);
    }
    
    // Display data
    function displayData(data) {
        resultCount.textContent = data.length;
        
        // Calculate pagination
        const totalPages = Math.ceil(data.length / rowsPerPage);
        const startIndex = (currentPage - 1) * rowsPerPage;
        const endIndex = Math.min(startIndex + rowsPerPage, data.length);
        const currentData = data.slice(startIndex, endIndex);
        
        // Clear table
        resultsTable.innerHTML = '';
        
        // Generate table rows
        if (currentData.length === 0) {
            resultsTable.innerHTML = `
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2rem;">No se encontraron resultados.</td>
                </tr>
            `;
        } else {
            currentData.forEach(item => {
                const row = document.createElement('tr');
                
                let statusClass = '';
                let statusText = '';
                
                switch(item.estado) {
                    case 'active':
                        statusClass = 'status-active';
                        statusText = 'Activo';
                        break;
                    case 'pending':
                        statusClass = 'status-pending';
                        statusText = 'Pendiente';
                        break;
                    case 'resolved':
                        statusClass = 'status-resolved';
                        statusText = 'Resuelto';
                        break;
                }
                
                row.innerHTML = `
                    <td>${item.documento}</td>
                    <td>${item.nombre}</td>
                    <td>${item.tipo}</td>
                    <td>${item.monto}</td>
                    <td>${item.fecha}</td>
                    <td><span class="status ${statusClass}">${statusText}</span></td>
                `;
                
                resultsTable.appendChild(row);
            });
        }
        
        // Generate pagination
        generatePagination(totalPages);
    }
    
    // Generate pagination buttons
    function generatePagination(totalPages) {
        pagination.innerHTML = '';
        
        if (totalPages <= 1) return;
        
        // Previous button
        const prevBtn = document.createElement('button');
        prevBtn.innerHTML = '&laquo;';
        prevBtn.disabled = currentPage === 1;
        prevBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                displayData(filteredData);
            }
        });
        pagination.appendChild(prevBtn);
        
        // Page buttons
        for (let i = 1; i <= totalPages; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.textContent = i;
            pageBtn.classList.toggle('active', i === currentPage);
            pageBtn.addEventListener('click', () => {
                currentPage = i;
                displayData(filteredData);
            });
            pagination.appendChild(pageBtn);
        }
        
        // Next button
        const nextBtn = document.createElement('button');
        nextBtn.innerHTML = '&raquo;';
        nextBtn.disabled = currentPage === totalPages;
        nextBtn.addEventListener('click', () => {
            if (currentPage < totalPages) {
                currentPage++;
                displayData(filteredData);
            }
        });
        pagination.appendChild(nextBtn);
    }
