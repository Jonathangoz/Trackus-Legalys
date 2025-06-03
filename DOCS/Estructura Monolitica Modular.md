Estructura de Carpetas:

    config: env.php

        Contendrá variables de entorno y valida si estan definidas:
            APP_ENV=“production” / “development”
            DB_HOST, DB_NAME, DB_USER, DB_PASS, …
            SMTP_HOST, SMTP_USER, SMTP_PASS, … 
            ONBASE_API_URL, ONBASE_API_KEY, …
            MANTENIMIENTO=true/false, ETC.


    DOCS: Documentacion y Manuales

        Arquitectura.md          # Explica diagrama de módulos, DB, flujos de datos, endpoints
        ProcesosDeNegocio.md     # Descripciones textuales de “cómo se registra un trámite…”
        InterfacesUsuario.md     # Bocetos de pantallas según rol
        ManualUsuario_Final.pdf  # PDF para usuarios finales (abogados, personal admin)
        ManualDesarrollo.md      # Guía para el equipo de dev: instalación, convención de código
        API.md                   # Lista de endpoints (URI, método HTTP, params, ejemplos de JSON)
        GuíaBackup_Restore.md    # Pasos para ejecutar el backup y restaurar.
        Colores_Tipografía.md    # Especificaciones de tipografía, estados y paleta de colores


    logs: Archivo donde el logger global guarda mensajes (Formato DATE)


    public: DocumentRoot de Nginx (Ruta del servidor que mostrara o servira HTTPS)
        
        index.html              # → Landing page estática 
        logging.php             # → Formulario dinámico + procesador de login (Auth)
        index.php               # → Front controller (MVC) para usuarios autenticados
        assets/
            css/
                .css            # Aquí incluyes tipografía, colores, iconografía 
            js/
                .js             # Funciones JS globales (notificaciones en UI, AJAX, etc.)
            img/
                logo.png        # Iconos, logos institucionales, etc.
        Documentos/             # Carpeta pública donde se guardan los archivos subidos (Documentos)
            trámites/           # Ej: uploads/trámites/12345/documento.pdf


    segurity: contiene claves RSA para el Sistema-Servidor
        rsa: llaves publicas y privadas .pem (JAMAS EXPONERLAS, RESTRINGIR SIEMPRE EL ACCESO)


    src: Aquí Va TODO El Código PHP

        Comunes: o carpeta compartida (Shared) CÓDIGO TRANSVERSAL A TODOS LOS MODULOS

           DB: conexion.php             # Singleton de PDO para PostgreSQL
           middleware:
                control_logging.php     # valida formulario login, autentica, encripta, deja pasar segun Rol 
                mantenimiento.php       # Verifica `env("MANTENIMIENTO")`; si true → muestra página mantenimiento
                AuditMiddleware.php     # Intercepta cada request y graba en BD/audit.log
            seguridad: 
                autenticacion.php       # Intercepta rutas y valida `$_SESSION['user_id']` + rol
                csrf.php                # Generación y verificación de tokens
                encriptacion.php        # Funciones de cifrado AES-GCM para datos sensibles
            utilidades:
                helpers.php             # Funciones genéricas: formatear fechas, validaciones extra
                limitarPeticiones.php   # Limita peticiones para evitar ataques DDoS segun consultas, por tiempos
                loggers.php             # Registra mensajes en `logs/app.log`
                InputSanitizer.php      # Sanitiza toda entrada (evitar XSS, SQL Injection)
            validaciones:
                validarEmail.php        # Verifica formato de correo
                validarlogin.php        # valida campos obligatorios en el formulario loggin.php
                
        Modulos: MÓDULOS DEL SISTEMA

            CobroCoactivo: procesos exclusivos para tramites, manejar expedientes, notificar
                 Controladores:
                    TramiteController.php        # Alta, listado, detalle, editar estado
                    ExpedienteController.php     # Detalle de expediente, notificar entidades
                Modelos:
                    Tramite.php                  # Propiedades: id, deudor_id, tipo_deuda, monto, fecha_inicio, estado, abogado_id
                    Expediente.php               # Relacionado a Tramite (número expediente, fechas, historial)
                Servivios:
                    ExpedienteService.php        # Cálculo de plazos, búsqueda rápida (RF 32), seguimiento
                    PlantillaService.php         # Genera documentos desde plantillas, edita, almacena
                routes.php                       # define rutas y rol para el manejo de GET, POST, PUT
                Vistas:                          # Vistas para listado de trámites, detalles de expediente, etc.
            
            Consultas:
                 Controladores:
                    docsController.php           # consulta estado de documentacion y procesos por parte del obliga al pago (usuario final deudor)
                    solicitudesController.php    # si desea solicitar estados actuales o mpeticiones en el proceso (opcional)
                Modelos:
                    docs.php                     # Propiedades: id, deudor_id, tipo_deuda, monto, fecha_inicio, estado, abogado_id
                    solicitud.php                # Relacionado a Tramite (solicitudes, fechas, historial)
                Servivios:
                    docsService.php              # Cálculo de plazos, búsqueda rápida (RF 32), seguimiento
                    solicitudService.php         # Genera documentos desde plantillas, edita, almacena
                routes.php                       # define rutas para el manejo de GET, POST, PUT (segun lo permitido9
                Vistas:                          # Vistas para listado de docs, detalles de solicitudes, etc.   

            Controladores: controlador principal que direciona al modulo segun rol, dando permisos segun lo autorizado
                control_abogado.php              # redirige al modulo correspondiente con sus permisos correspondientes            
                control_admin.php                # redirige al modulo correspondiente con sus permisos correspondientes
                control_adminTramites.php        # redirige al modulo correspondiente con sus permisos correspondientes
                control_usuarios.php             # redirige al modulo correspondiente con sus permisos correspondientes
                control_base.php                 # renderiza la vista en src/Modulos/<$ruta>.php con las variables dadas

            Dashboard: vista principal para admin
                Controladores:
    
                Modelos:
                                                 #
                Servivios:
                                                 #
                routes.php                       # 
                Vistas:                          #

            Documentacion:
                 Controladores:
                    DocumentoController.php      # Subir/descargar, historial, eliminar, ver versiones
                Modelos:
                    Documento.php                # Tabla `documentos`: id, tramite_id, nombre_archivo, filepath, who_uploaded, timestamp
                Servivios:
                    DocumentosService.php        # Cifrado en reposo, versiones, storage en uploads
                routes.php                       # define rutas para el manejo de GET (segun lo permitido)
                Vistas:                          # Opcional: vista de historial de documentos

            Integraciones: Integra el sistema OnBase (Api-onbase)          
                 Controladores:
                    .php                         # 
                Modelos:
                    .php                         # 
                Servivios:
                    DocumentosService.php        # 
                routes.php                       # 
                Vistas:                          # Opcional: vista    

            Notificaciones:
                 Controladores:
                    notificaciones.php           # Permite ver historial de notificaciones, re-enviar, etc.
                Modelos:
                    notificaciones.php           # Tabla `notificaciones`: id, user_id, tramite_id, tipo, estado, timestamp
                Servivios:
                    mailserver.php               # Usa PHPMailer, envía correo
                routes.php                       # define rutas para el manejo de GET,POST (segun lo permitido)
                Vistas:                          # Opcional: configuración de plantillas de correo

            Reportes:
                 Controladores:
                    ReporteController.php        # Genera reportes, aplica filtros.
                Modelos:
                    Reporte.php                  # Lógica de consulta SQL compleja (agregaciones, conteos)
                Servivios:
                    GeneradorReportes.php        # Produce PDF, CSV, Excel
                routes.php                       # define rutas para el manejo de GET,POST (segun lo permitido)
                Vistas:                          # interfaz para seleccionar filtros y ver resultados 

            ResetPassword:
                 Controladores:
                    Resetpass.php                # redirije segun los cambios requeridos
                Modelos:
                    Resetpass.php                # Lógica de consulta SQL
                Servivios:
                    resetpass.php                # Produce PDF, CSV, Excel
                routes.php                       # define rutas para el manejo de POST (segun lo permitido)
                Vistas:                          # interfaz resetpassword

            Search: opcional                     #
                controllers:
                    SearchController.php         # Motores de búsqueda
                services:
                    SearchService.php            # Ejecuta consultas con índices, devuelve resultados paginados
                routes.php                       # define rutas para el manejo de GET (segun lo permitido)
                views:                           # (Opcional)

            Seguridad:
                controllers:
                    LogController.php            # Permite al admin ver logs del sistema (RF 34)
                    AuditoriaController.php      # Ver auditoría de usuarios (RF 33, RF 34)
                services
                    AuditService.php             # Graba en base `auditoria` cada acción (RNF 15)
                routes.php                       # 
                views:                           # (Opcional) mostrar logs y auditoría en tablas

            Usuarios:
                controllers:
                    UsuarioController.php        # CRUD usuarios, edición de perfil, cambio de contraseña
                models:
                    Usuario.php                  # Mapea la tabla `usuarios` (id, nombre, email, pass_hash, rol)
                services:
                    AuthService.php              # Lógica de login, logout, generación de tokens
                    RoleService.php              # Asignar roles, permisos dinámicos
                routes.php                       # Definición de endpoints: define rutas para el manejo de GET, POST (segun lo permitido)
                views:                           # Vistas (formulario login, listados, edición, etc.) 


    vendor: librerias - dependencias instalados con composer
    .env: variables de entorno (JAMAS EXPONER A NADIE, POR SEGURIDAD SE SUBIRAN AL SERVIDOR Y ESTE ARCHIVO SOLO UNO TIENE PERMISOS DE ACCESO)
    .gitignore: obliga a no subir archivos seleccionados al repositorio de GitHub
    .prettierignore: Especifica que archivos o directorios no deben ser fotmateados por prettier
    composer.json: dependencias claves de composer
    composer-lock.json:
    README.md: Descripcion general del sistema y sus funciones