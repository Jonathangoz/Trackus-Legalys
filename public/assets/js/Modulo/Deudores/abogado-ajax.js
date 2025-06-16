// abogado-ajax.js - Manejo de consultas AJAX
class AbogadoAjax {
    constructor() {
        this.baseUrl = '/api/abogado';
        this.token = this.getStoredToken();
        this.setupInterceptors();
    }

    // Método para obtener token sin usar localStorage directamente
    getStoredToken() {
        try {
            return localStorage.getItem('auth_token') || '';
        } catch (error) {
            console.warn('No se puede acceder a localStorage, usando token vacío');
            return '';
        }
    }

    setupInterceptors() {
        // Interceptor para manejar errores globales
        this.originalFetch = window.fetch;
        window.fetch = (...args) => {
            return this.originalFetch(...args)
                .then(response => {
                    if (response.status === 401) {
                        this.handleUnauthorized();
                    }
                    return response;
                })
                .catch(error => {
                    console.error('Error en petición AJAX:', error);
                    this.handleNetworkError(error);
                    throw error;
                });
        };
    }

    // Método genérico para peticiones AJAX
    async makeRequest(url, options = {}) {
        const config = {
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${this.token}`,
                ...options.headers
            },
            ...options
        };

        try {
            const response = await fetch(`${this.baseUrl}${url}`, config);
            
            if (!response.ok) {
                throw new Error(`Error ${response.status}: ${response.statusText}`);
            }

            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return await response.json();
            }
            
            return await response.text();
        } catch (error) {
            console.error('Error en makeRequest:', error);
            throw error;
        }
    }

    // GET request
    async get(url, params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const fullUrl = queryString ? `${url}?${queryString}` : url;
        
        return this.makeRequest(fullUrl, {
            method: 'GET'
        });
    }

    // POST request
    async post(url, data = {}) {
        return this.makeRequest(url, {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    // PUT request
    async put(url, data = {}) {
        return this.makeRequest(url, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    // DELETE request
    async delete(url) {
        return this.makeRequest(url, {
            method: 'DELETE'
        });
    }

    // PATCH request
    async patch(url, data = {}) {
        return this.makeRequest(url, {
            method: 'PATCH',
            body: JSON.stringify(data)
        });
    }

    // Métodos específicos para el sistema de abogados

    // Obtener casos del abogado
    async obtenerCasos(filtros = {}, paginacion = {}) {
        const params = {
            ...filtros,
            page: paginacion.page || 1,
            limit: paginacion.limit || 10,
            sort: paginacion.sort || '',
            order: paginacion.order || 'asc'
        };

        try {
            const response = await this.get('/casos', params);
            return {
                success: true,
                data: response.data || [],
                total: response.total || 0,
                page: response.page || 1,
                totalPages: response.totalPages || 1
            };
        } catch (error) {
            return {
                success: false,
                error: error.message,
                data: []
            };
        }
    }

    // Obtener detalle de un caso específico
    async obtenerDetalleCaso(casoId) {
        try {
            const response = await this.get(`/casos/${casoId}`);
            return {
                success: true,
                data: response.data || {}
            };
        } catch (error) {
            return {
                success: false,
                error: error.message,
                data: {}
            };
        }
    }

    // Actualizar caso
    async actualizarCaso(casoId, datos) {
        try {
            const response = await this.put(`/casos/${casoId}`, datos);
            return {
                success: true,
                data: response.data || {},
                message: response.message || 'Caso actualizado exitosamente'
            };
        } catch (error) {
            return {
                success: false,
                error: error.message
            };
        }
    }

    // Generar documento
    async generarDocumento(casoId, tipoDocumento, parametros = {}) {
        try {
            const response = await this.post(`/casos/${casoId}/documentos`, {
                tipo: tipoDocumento,
                parametros: parametros
            });
            
            return {
                success: true,
                data: response.data || {},
                documentoId: response.documentoId,
                url: response.url,
                message: response.message || 'Documento generado exitosamente'
            };
        } catch (error) {
            return {
                success: false,
                error: error.message
            };
        }
    }

    // Obtener documentos generados
    async obtenerDocumentosGenerados(filtros = {}) {
        try {
            const response = await this.get('/documentos', filtros);
            return {
                success: true,
                data: response.data || []
            };
        } catch (error) {
            return {
                success: false,
                error: error.message,
                data: []
            };
        }
    }

    // Descargar documento
    async descargarDocumento(documentoId) {
        try {
            const response = await fetch(`${this.baseUrl}/documentos/${documentoId}/download`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${this.token}`
                }
            });

            if (!response.ok) {
                throw new Error(`Error ${response.status}: ${response.statusText}`);
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `documento_${documentoId}.pdf`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);

            return {
                success: true,
                message: 'Documento descargado exitosamente'
            };
        } catch (error) {
            return {
                success: false,
                error: error.message
            };
        }
    }

    // Obtener estadísticas y reportes
    async obtenerEstadisticas(periodo = 'mes') {
        try {
            const response = await this.get('/estadisticas', { periodo });
            return {
                success: true,
                data: response.data || {}
            };
        } catch (error) {
            return {
                success: false,
                error: error.message,
                data: {}
            };
        }
    }

    // Obtener notificaciones
    async obtenerNotificaciones() {
        try {
            const response = await this.get('/notificaciones');
            return {
                success: true,
                data: response.data || []
            };
        } catch (error) {
            return {
                success: false,
                error: error.message,
                data: []
            };
        }
    }

    // Marcar notificación como leída
    async marcarNotificacionLeida(notificacionId) {
        try {
            const response = await this.patch(`/notificaciones/${notificacionId}`, {
                leida: true
            });
            return {
                success: true,
                message: response.message || 'Notificación marcada como leída'
            };
        } catch (error) {
            return {
                success: false,
                error: error.message
            };
        }
    }

    // Marcar todas las notificaciones como leídas
    async marcarTodasNotificacionesLeidas() {
        try {
            const response = await this.patch('/notificaciones/marcar-todas-leidas');
            return {
                success: true,
                message: response.message || 'Todas las notificaciones marcadas como leídas'
            };
        } catch (error) {
            return {
                success: false,
                error: error.message
            };
        }
    }

    // Exportar casos
    async exportarCasos(filtros = {}, formato = 'excel') {
        try {
            const params = {
                ...filtros,
                formato: formato
            };

            const response = await fetch(`${this.baseUrl}/casos/exportar?${new URLSearchParams(params)}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${this.token}`
                }
            });

            if (!response.ok) {
                throw new Error(`Error ${response.status}: ${response.statusText}`);
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `casos_${new Date().toISOString().split('T')[0]}.${formato === 'excel' ? 'xlsx' : 'pdf'}`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);

            return {
                success: true,
                message: 'Casos exportados exitosamente'
            };
        } catch (error) {
            return {
                success: false,
                error: error.message
            };
        }
    }

    // Subir archivo
    async subirArchivo(archivo, casoId = null) {
        try {
            const formData = new FormData();
            formData.append('archivo', archivo);
            if (casoId) {
                formData.append('casoId', casoId);
            }

            const response = await fetch(`${this.baseUrl}/archivos/subir`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${this.token}`
                },
                body: formData
            });

            if (!response.ok) {
                throw new Error(`Error ${response.status}: ${response.statusText}`);
            }

            const result = await response.json();
            return {
                success: true,
                data: result.data || {},
                message: result.message || 'Archivo subido exitosamente'
            };
        } catch (error) {
            return {
                success: false,
                error: error.message
            };
        }
    }

    // Obtener historial de un caso
    async obtenerHistorialCaso(casoId) {
        try {
            const response = await this.get(`/casos/${casoId}/historial`);
            return {
                success: true,
                data: response.data || []
            };
        } catch (error) {
            return {
                success: false,
                error: error.message,
                data: []
            };
        }
    }

    // Agregar comentario al caso
    async agregarComentario(casoId, comentario) {
        try {
            const response = await this.post(`/casos/${casoId}/comentarios`, {
                comentario: comentario
            });
            return {
                success: true,
                data: response.data || {},
                message: response.message || 'Comentario agregado exitosamente'
            };
        } catch (error) {
            return {
                success: false,
                error: error.message
            };
        }
    }

    // Cambiar estado del caso
    async cambiarEstadoCaso(casoId, nuevoEstado, observaciones = '') {
        try {
            const response = await this.patch(`/casos/${casoId}/estado`, {
                estado: nuevoEstado,
                observaciones: observaciones
            });
            return {
                success: true,
                data: response.data || {},
                message: response.message || 'Estado del caso actualizado exitosamente'
            };
        } catch (error) {
            return {
                success: false,
                error: error.message
            };
        }
    }

    // Obtener tipos de trámite disponibles
    async obtenerTiposTramite() {
        try {
            const response = await this.get('/tipos-tramite');
            return {
                success: true,
                data: response.data || []
            };
        } catch (error) {
            return {
                success: false,
                error: error.message,
                data: []
            };
        }
    }

    // Obtener estados disponibles
    async obtenerEstadosDisponibles() {
        try {
            const response = await this.get('/estados');
            return {
                success: true,
                data: response.data || []
            };
        } catch (error) {
            return {
                success: false,
                error: error.message,
                data: []
            };
        }
    }

    // Obtener plantillas de documentos disponibles
    async obtenerPlantillasDocumentos() {
        try {
            const response = await this.get('/plantillas');
            return {
                success: true,
                data: response.data || []
            };
        } catch (error) {
            return {
                success: false,
                error: error.message,
                data: []
            };
        }
    }

    // Obtener datos para reportes
    async obtenerDatosReportes(periodo = 'mes') {
        try {
            const response = await this.get('/reportes', { periodo });
            return {
                success: true,
                data: {
                    casosAsignados: response.casosAsignados || 0,
                    casosCompletados: response.casosCompletados || 0,
                    documentosGenerados: response.documentosGenerados || 0,
                    tiempoPromedio: response.tiempoPromedio || 0,
                    estadosChart: response.estadosChart || [],
                    productividadChart: response.productividadChart || []
                }
            };
        } catch (error) {
            return {
                success: false,
                error: error.message,
                data: {
                    casosAsignados: 0,
                    casosCompletados: 0,
                    documentosGenerados: 0,
                    tiempoPromedio: 0,
                    estadosChart: [],
                    productividadChart: []
                }
            };
        }
    }

    // Buscar casos por término
    async buscarCasos(termino, filtros = {}) {
        try {
            const params = {
                q: termino,
                ...filtros
            };
            
            const response = await this.get('/casos/buscar', params);
            return {
                success: true,
                data: response.data || []
            };
        } catch (error) {
            return {
                success: false,
                error: error.message,
                data: []
            };
        }
    }

    // Obtener resumen del dashboard
    async obtenerResumenDashboard() {
        try {
            const response = await this.get('/dashboard/resumen');
            return {
                success: true,
                data: {
                    totalCasos: response.totalCasos || 0,
                    casosUrgentes: response.casosUrgentes || 0,
                    casosVencidos: response.casosVencidos || 0,
                    notificacionesPendientes: response.notificacionesPendientes || 0
                }
            };
        } catch (error) {
            return {
                success: false,
                error: error.message,
                data: {
                    totalCasos: 0,
                    casosUrgentes: 0,
                    casosVencidos: 0,
                    notificacionesPendientes: 0
                }
            };
        }
    }

    // Verificar conexión con el servidor
    async verificarConexion() {
        try {
            const response = await this.get('/health-check');
            return {
                success: true,
                message: 'Conexión exitosa'
            };
        } catch (error) {
            return {
                success: false,
                error: error.message
            };
        }
    }

    // Manejar errores de autorización
    handleUnauthorized() {
        console.warn('Token expirado o inválido');
        // Aquí puedes implementar la lógica para redirigir al login
        // Por ejemplo: window.location.href = '/login';
        
        // Mostrar mensaje al usuario
        this.mostrarNotificacion('Sesión expirada. Por favor, inicia sesión nuevamente.', 'error');
    }

    // Manejar errores de red
    handleNetworkError(error) {
        console.error('Error de red:', error);
        this.mostrarNotificacion('Error de conexión. Verifica tu conexión a internet.', 'error');
    }

    // Mostrar notificaciones al usuario
    mostrarNotificacion(mensaje, tipo = 'info', duracion = 5000) {
        // Crear elemento de notificación
        const notification = document.createElement('div');
        notification.className = `notification notification-${tipo}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${this.getIconForType(tipo)}"></i>
                <span>${mensaje}</span>
            </div>
            <button class="notification-close">&times;</button>
        `;

        // Agregar al DOM
        document.body.appendChild(notification);

        // Agregar clase para animación
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);

        // Auto-remove después de la duración especificada
        setTimeout(() => {
            this.removeNotification(notification);
        }, duracion);

        // Event listener para cerrar manualmente
        notification.querySelector('.notification-close').addEventListener('click', () => {
            this.removeNotification(notification);
        });
    }

    // Obtener icono según el tipo de notificación
    getIconForType(tipo) {
        const iconos = {
            'success': 'check-circle',
            'error': 'exclamation-circle',
            'warning': 'exclamation-triangle',
            'info': 'info-circle'
        };
        return iconos[tipo] || 'info-circle';
    }

    // Remover notificación
    removeNotification(notification) {
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }

    // Actualizar token de autorización
    actualizarToken(nuevoToken) {
        this.token = nuevoToken;
        try {
            localStorage.setItem('auth_token', nuevoToken);
        } catch (error) {
            console.warn('No se pudo guardar el token en localStorage');
        }
    }

    // Limpiar token de autorización
    limpiarToken() {
        this.token = '';
        try {
            localStorage.removeItem('auth_token');
        } catch (error) {
            console.warn('No se pudo remover el token de localStorage');
        }
    }
}

// Crear instancia global del manejador AJAX
window.abogadoAjax = new AbogadoAjax();