/ (raíz del proyecto)
├─ config/
│   └─ env.php
│       # Contendrá variables de entorno:
│       #   APP_ENV=“production” / “development”
│       #   DB_HOST, DB_NAME, DB_USER, DB_PASS, …
│       #   SMTP_HOST, SMTP_USER, SMTP_PASS, … 
│       #   ONBASE_API_URL, ONBASE_API_KEY, …
│       #   MANTENIMIENTO=true/false
│
├─ logs/
│   └─ app.log                # Archivo donde el logger global guarda mensajes
│   └─ audit.log              # (Opcional) Registro de auditoría si no va a BD
│
├─ public/                    # → DocumentRoot de Nginx
│   ├─ index.html             # → Landing page estática (RNF 1 y RU –)
│   ├─ logging.php            # → Formulario dinámico + procesador de login (Auth)
│   ├─ index.php              # → Front controller (MVC) para usuarios autenticados
│   ├─ assets/
│   │    ├─ css/
│   │    │    └─ styles.css   # Aquí incluyes tipografía, colores, iconografía (RNF 17,18)
│   │    ├─ js/
│   │    │    └─ main.js      # Funciones JS globales (notificaciones en UI, AJAX, etc.)
│   │    └─ img/
│   │         └─ logo.png     # Iconos, logos institucionales, etc.
│   ├─ uploads/               # Carpeta pública donde se guardan los archivos subidos (Documentos)
│   │    └─ trámites/         # Ej: uploads/trámites/12345/documento.pdf
│   ├─ favicon.ico
│   └─ robots.txt
│
├─ modules/                   # ——————————— MÓDULOS DEL SISTEMA ———————————
│   │
│   ├─ Usuarios/
│   │   ├─ controllers/
│   │   │    └─ UsuarioController.php        # CRUD usuarios, edición de perfil, cambio de contraseña (RF 11–13)
│   │   ├─ models/
│   │   │    └─ Usuario.php                  # Mapea la tabla `usuarios` (id, nombre, email, pass_hash, rol)
│   │   ├─ services/
│   │   │    ├─ AuthService.php              # Lógica de login, logout, generación de tokens (RNF 5, RNF 7)
│   │   │    └─ RoleService.php              # Asignar roles, permisos dinámicos (RU 3, RU 4, RF 12)
│   │   ├─ routes.php                        # Definición de endpoints:
│   │   │   # '/usuarios/listar' → [UsuarioController, 'listar', 'GET', ['admin']]
│   │   │   # '/usuarios/crear'  → [UsuarioController, 'crear', 'POST', ['admin']]
│   │   └─ views/                            # Vistas (formulario login, listados, edición, etc.)  
│   │
│   ├─ CobroCoactivo/
│   │   ├─ controllers/
│   │   │    ├─ TramiteController.php        # Alta, listado, detalle, editar estado (RF 1–4, 14–18)
│   │   │    └─ ExpedienteController.php     # Detalle de expediente, notificar entidades (RF 6–10)
│   │   ├─ models/
│   │   │    ├─ Tramite.php                  # Propiedades: id, deudor_id, tipo_deuda, monto, fecha_inicio, estado, abogado_id
│   │   │    └─ Expediente.php               # Relacionado a Tramite (número expediente, fechas, historial)
│   │   ├─ services/
│   │   │    ├─ ExpedienteService.php        # Cálculo de plazos, búsqueda rápida (RF 32), seguimiento (RU 12)
│   │   │    └─ PlantillaService.php         # Genera documentos desde plantillas, edita, almacena (RF 7–10)
│   │   ├─ routes.php                        # Ejemplos:
│   │   │   # '/cobro/tramites'                 → [TramiteController, 'listar',      'GET',    ['admin','tramites']]
│   │   │   # '/cobro/tramites/crear'           → [TramiteController, 'crear',       'POST',   ['admin','tramites']]
│   │   │   # '/cobro/tramites/(:num)/asignar'   → [TramiteController, 'asignar',     'POST',   ['admin','tramites']]
│   │   │   # '/cobro/tramites/(:num)/cambiar'   → [TramiteController, 'cambiarEstado','PUT',   ['admin','tramites','abogado']]
│   │   │   # '/cobro/expedientes/(:num)'        → [ExpedienteController,'detalle',    'GET',    ['admin','tramites','abogado']]
│   │   │   # '/cobro/expedientes/(:num)/notificar' → [ExpedienteController,'notificar','POST',['admin','tramites']]
│   │   └─ views/                            # Vistas para listado de trámites, detalles de expediente, etc.
│   │
│   ├─ Documentos/
│   │   ├─ controllers/
│   │   │    └─ DocumentoController.php       # Subir/descargar, historial, eliminar, ver versiones (RF 6–10)
│   │   ├─ models/
│   │   │    └─ Documento.php                 # Tabla `documentos`: id, tramite_id, nombre_archivo, filepath, who_uploaded, timestamp
│   │   ├─ services/
│   │   │    └─ DocumentoService.php          # Cifrado en reposo (RNF 7), versiones, storage en uploads/
│   │   ├─ routes.php                         # Ej:
│   │   │   # '/documentos/subir'     → [DocumentoController,'subir','POST',['admin','tramites','abogado']]
│   │   │   # '/documentos/descargar/(:num)' → [DocumentoController,'descargar','GET',['admin','tramites','abogado']]
│   │   └─ views/                            # Opcional: vista de historial de documentos
│   │
│   ├─ Notificaciones/
│   │   ├─ controllers/
│   │   │    └─ NotificacionController.php    # Permite ver historial de notificaciones, re-enviar, etc.
│   │   ├─ services/
│   │   │    ├─ MailerService.php             # Usa PHPMailer, envía correo (RF 22, RNF 7, RNF 15)
│   │   │    └─ SmsService.php                # (Opcional) si se integra con SMS en el futuro
│   │   ├─ models/
│   │   │    └─ Notificacion.php              # Tabla `notificaciones`: id, user_id, tramite_id, tipo, estado, timestamp
│   │   ├─ routes.php                         # Ej:
│   │   │   # '/notificaciones/email/enviar' → [NotificacionController,'enviarEmail','POST',['admin','tramites','abogado']]
│   │   │   # '/notificaciones/listar'      → [NotificacionController,'listar','GET',['admin','tramites','abogado']]
│   │   └─ views/                            # (Opcional) configuración de plantillas de correo
│   │
│   ├─ Reportes/
│   │   ├─ controllers/
│   │   │    └─ ReporteController.php         # Genera reportes, aplica filtros (RF 19, RF 20, RF 33, RF 34)
│   │   ├─ models/
│   │   │    └─ Reporte.php                   # Lógica de consulta SQL compleja (agregaciones, conteos)
│   │   ├─ services/
│   │   │    └─ GeneradorReportes.php         # Produce PDF, CSV, Excel (RF 21, RNF 4)
│   │   ├─ routes.php                         # Ej:
│   │   │   # '/reportes/casos'  → [ReporteController,'casosCobranza','GET',['admin','tramites']]
│   │   │   # '/reportes/exportar' → [ReporteController,'exportar','POST',['admin','tramites']]
│   │   └─ views/                            # (Opcional) interfaz para seleccionar filtros y ver resultados
│   │
│   ├─ Dashboard/
│   │   ├─ controllers/
│   │   │    └─ DashboardController.php       # Muestra panel con gráficas, alertas, plazos próximos (RF 24, RF 31)
│   │   ├─ services/
│   │   │    └─ DashboardService.php          # Lógica para calcular métricas, usar caché si hace falta (RNF 1, RNF 3)
│   │   ├─ routes.php                         # Ej:
│   │   │   # '/dashboard' → [DashboardController,'index','GET',['admin','tramites','abogado']]
│   │   └─ views/
│   │
│   ├─ Integraciones/
│   │   ├─ controllers/
│   │   │    └─ OnbaseController.php          # Endpoint para recibir data/archivos de ONBASE (RNF 11)
│   │   ├─ services/
│   │   │    └─ OnbaseIntegration.php         # Lógica REST para hablar con API ONBASE (RNF 11)
│   │   ├─ models/
│   │   │    └─ OnbaseMapping.php             # Tabla que guarde metadatos de mapeo ONBASE ↔ Tramite
│   │   ├─ routes.php                         # Ej:
│   │   │   # '/integraciones/onbase/push' → [OnbaseController,'push','POST',['admin']]
│   │   └─ views/                            # (Opcional) interfaz de configuración de ONBASE
│   │
│   ├─ Seguridad/
│   │   ├─ controllers/
│   │   │    ├─ LogController.php             # Permite al admin ver logs del sistema (RF 34)
│   │   │    └─ AuditoriaController.php       # Ver auditoría de usuarios (RF 33, RF 34)
│   │   ├─ services/
│   │   │    └─ AuditService.php              # Graba en base `auditoria` cada acción (RNF 15)
│   │   ├─ routes.php                         # Ej:
│   │   │   # '/seguridad/logs'     → [LogController,'listar','GET',['admin']]
│   │   │   # '/seguridad/auditoria'→ [AuditoriaController,'listar','GET',['admin']]
│   │   └─ views/                            # (Opcional) mostrar logs y auditoría en tablas
│   │
│   └─ Search/                                # (Opcional: puede integrarse en CobroCoactivo)
│       ├─ controllers/
│       │    └─ SearchController.php          # Motores de búsqueda (RF 32, RNF 4)
│       ├─ services/
│       │    └─ SearchService.php             # Ejecuta consultas con índices, devuelve resultados paginados
│       ├─ routes.php                         # Ej:
│       │    # '/search/tramites' → [SearchController,'tramites','GET',['admin','tramites','abogado']]
│       └─ views/
│
├─ shared/                   # ——————— CÓDIGO TRANSVERSAL a todos los módulos ———————
│   ├─ database/
│   │    └─ Conexion.php                    # Singleton de PDO para MySQL/PostgreSQL (RNF 4, RNF 3)
│   │
│   ├─ security/
│   │    ├─ AuthService.php                 # Verificar credenciales, gestionar sesión (RNF 5, RF 25)
│   │    ├─ EncryptionService.php           # Funciones de cifrado AES-GCM para datos sensibles (RNF 7)
│   │    ├─ Csrf.php                        # Generación y verificación de tokens CSRF (RNF 8)
│   │    └─ AuthMiddleware.php              # Intercepta rutas y valida `$_SESSION['user_id']` + rol (RF 25, RF 27)
│   │
│   ├─ middleware/
│   │    ├─ MaintenanceMiddleware.php       # Verifica `env("MANTENIMIENTO")`; si true → muestra página mantenimiento (RNF 6)
│   │    ├─ AuditMiddleware.php             # Intercepta cada request y graba en BD/audit.log (RF 33, RNF 15)
│   │    └─ RoleMiddleware.php              # Valida rutas según rol (RF 27)
│   │
│   ├─ utils/
│   │    ├─ InputSanitizer.php              # Sanitiza toda entrada (evitar XSS, SQL Injection) (RNF 8)
│   │    ├─ Logger.php                      # Registra mensajes en `logs/app.log` (RNF 15)
│   │    ├─ BackupService.php               # Dump DB + cifrado + almacenamiento (RF 29, RNF 10)
│   │    ├─ RestoreService.php              # Restaura la base y archivos desde backup (RF 30)
│   │    ├─ Helpers.php                     # Funciones genéricas: formatear fechas, validaciones extra
│   │    └─ Constants.php                   # Constantes de la aplicación (roles, estados de trámite, etc.)
│   │
│   └─ validation/
│        ├─ PasswordValidator.php           # Reglas de contraseñas seguras (RF 13, RNF 5)
│        ├─ EmailValidator.php              # Verifica formato de correo (RNF 8)
│        └─ FileValidator.php               # Controla tipo y tamaño de archivos subidos (RF 6, RNF 8)
│
├─ vendor/                   # ————— Dependencias gestionadas por Composer —————
│   └─ (PSR-4 autoload de todos los namespaces)
│
├─ docs/                     # ————— DOCUMENTACIÓN Y MANUALES —————
│   ├─ Arquitectura.md          # Explica diagrama de módulos, DB, flujos de datos, endpoints (RNF 12)
│   ├─ ProcesosDeNegocio.md     # Descripciones textuales de “cómo se registra un trámite…” (RNF 12)
│   ├─ InterfacesUsuario.md     # Bocetos de pantallas según rol (RNF 12)
│   ├─ ManualUsuario_Final.pdf  # PDF para usuarios finales (abogados, personal admin) (RNF 13)
│   ├─ ManualDesarrollo.md      # Guía para el equipo de dev: instalación, convención de código (RNF 14)
│   ├─ API.md                   # Lista de endpoints (URI, método HTTP, params, ejemplos de JSON) (RNF 12)
│   ├─ GuíaBackup_Restore.md    # Pasos para ejecutar el backup y restaurar (RF 29–30, RNF 10)
│   └─ Colores_Tipografía.md    # Especificaciones de tipografía, estados y paleta de colores (RNF 17–19)
│
├─ composer.json             # Configuración de autoload PSR-4, dependencias: PHPMailer, PhpSpreadsheet, …
├─ composer.lock
├─ gitignore
└─ readme.md                # Resumen general: descripción del proyecto, cómo levantarlo en local, comandos útiles


                                   ┌──────────────┐
                                   │   roles      │
                                   │(id, nombre)  │
                                   └──────┬───────┘
                                          │
                                          │ 1:N
                                          │
                               ┌──────────▼──────────┐
                               │  funcionarios       │
                               │(id, rol_id, …)      │
                               └──────────┬──────────┘
                                          │       ┌────────────────┐
                       ┌──────────────────┴───┐   │                │
                       │                      │   │                │
                       │                      │   │                │
                ┌──────▼──────┐         ┌─────▼───────┐     ┌──────▼───────┐
                │ asignaciones│         │  auditoria  │     │ documentos   │
                │(id, caso_id,│         │(id, caso_id,│     │(id, caso_id, │
                │ funcionario_id,…)│    │ funcionario_id,…)│(tipo_doc,…,   │
                └──────┬──────┘         └─────────────┘     │ ruta_archivo) │
                       │                                   └──────┬──────┘
                       │  N:1                                     │ N:1
                       │                                         │
                ┌──────▼──────┐                                  │
                │    casos    │◄─────────────────────────────────┘
                │(id, deudor_id,│  1:N
                │ tipo_tramite_id,│
                │ estado_tramite_id,│
                │ …)             │
                └──────┬──────┬───┘
                       │      │
            N:1        │      │       N:1
    ┌──────────────────▼─┐    │    ┌──▼───────────┐
    │      embargos      │    │    │   recursos   │
    │(id, caso_id, …)    │    │    │(id, caso_id,…)│
    └────────────────────┘    │    └──────────────┘
                              │
                              │ 1:N
                              │
                       ┌──────▼─────────────┐
                       │    remates         │
                       │(id, caso_id, …)    │
                       └────────────────────┘


    ┌───────────────┐
    │  tipos_tramite│
    │(id, nombre)   │
    └──────┬────────┘
           │ 1:N
           │
    ┌──────▼──────┐
    │ deudores    │
    │(id, cédula, │
    │ nombre, …)  │
    └──────┬──────┘
           │ 1:N
           │
    ┌──────▼────────┐
    │    casos      │  ← (ya visto en el centro)
    └───────────────┘


    ┌─────────────┐
    │entidades    │
    │(id, tipo,   │
    │ nombre, …)  │
    └─────────────┘

    ┌───────────────┐
    │estados_tramite│
    │(id, nombre)   │
    └───────────────┘


    ┌────────────────┐
    │acuerdos_pago   │
    │(id, caso_id,…) │
    └────────────────┘
