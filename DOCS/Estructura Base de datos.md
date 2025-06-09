Explicacion consisa y breve de cada tabla y su funcion, estructura:

Base de Datos:
    -Nombre: GestionCoactivo
    -Construido en: PostgreSQL
    -Tipo: Relacional

Tablas:
    - acuerdos de pago
        * Propositos: Guarda los planes de pago (acuerdos) que se pactan cuando el deudor solicita facilidades una vez emitido el mandamiento o tras un embargo parcial.
        * Columnas Claves:
            id (PK)
            caso_id (FK → casos.id)
            monto_total (capital + intereses al momento de firmar)
            cuotas (p.ej. 4)
            frecuencia ('MENSUAL', 'QUINCENAL', 'SEMANAL')
            interes_condonado (booleano)
            estado_acuerdo (ENUM: 'VIGENTE', 'CUMPLIDO', 'FRUSTRADO')
            fecha_inicio, fecha_fin (calendario del plan)
            creado_en
        * Quien la Utiliza:
            El Abogado, al recibir solicitud de plan de pago, crea un registro con estado = 'VIGENTE'.
            El Coordinador Jurídico aprueba o rechaza (se actualiza estado_acuerdo y fecha_fin).
            El Módulo de seguimiento (cron job) verifica vencimientos de cuotas para generar alertas.
        * Relaciones:
            caso_id → casos

	- asignaciones
        * Propositos: Guarda cada vez que un caso es asignado (o reasignado) a un funcionario (abogado).
        * Columnas Claves:
            id (PK)
            caso_id (FK → casos.id)
            funcionario_id (FK → funcionarios.id) — el abogado que recibe el caso
            asignado_por (FK → funcionarios.id) — quién hizo la asignación (coordinador/u otro)
            estado_asignacion ('ASIGNADO', 'REASIGNADO', 'RETIRADO')
            fecha_asignacion
        * Quien la Utiliza:
            El Coordinador Jurídico o el Admin_Trámites cuando asigna o reasigna un caso a un abogado.
            Permite consultar en la interfaz: “¿A qué abogado(s) estuvo asignado este caso y cuándo?”
            Los reportes de carga: cuántos casos tiene hoy cada abogado.
        * Relaciones:
            caso_id → casos
            funcionario_id → funcionarios (abogado receptor)
            asignado_por → funcionarios (coordinador quien asigna)

	- auditoria
        * Propositos: Registro (log) de eventos de negocio—quién, qué y cuándo hizo cada acción relevante (crear mandamiento, confirmación de embargo, cambio de estado, descarga de archivos, etc.).
        * Columnas Claves:
            id (PK)
            funcionario_id (FK → funcionarios.id) — quién ejecuta la acción
            caso_id (FK → casos.id, opcional si hay un caso asociado)
            accion (p.ej. 'CREAR_MANDAMIENTO', 'REGISTRAR_EMBARGO', 'DESCARGA_DOCUMENTO')
            detalle (JSONB) — datos específicos (monto, fecha, entidad, etc.)
            creado_en
        * Quien la Utiliza:
            Puede ser alimentada automáticamente por Monolog (vía un handler DB) o manualmente en código justo después de cada operación clave.
            Sirve para auditorías internas y trazabilidad de cada paso.
        * Relaciones:
            funcionario_id → funcionarios
            caso_id → casos (si aplica; a veces se loguean acciones generales sin caso específico)

	- casos
        * Propositos: Tabla principal que representa cada expediente de cobro coactivo. Aquí se registra toda la información básica de un caso.
        * Columnas Claves:
            id (PK)
            deudor_id (FK → deudores.id)
            tipo_tramite_id (FK → tipos_tramite.id)
            estado_tramite_id (FK → estados_tramite.id)
            descripcion
            monto_original, intereses_acumulados, costos_administrativos, monto_total (calculado)
            fecha_creacion, fecha_asignacion, fecha_limite_pago, fecha_cierre
            notas
        * Quien la Utiliza:
            El Admin_Trámites, para registrar nuevos casos (estado = INGRESADO) y asignarlos.
            Los abogados, para ver la información del caso asignado y actualizar campos (estado, fechas, montos).
            Módulos automáticos (cron jobs) que disparan embargos cuando vence fecha_limite_pago.
            Módulo de reportes (métricas de cartera).
        * Relaciones:
            deudor_id → deudores
            tipo_tramite_id → tipos_tramite
            estado_tramite_id → estados_tramite
            1:N con asignaciones.caso_id (histórico de asignaciones)
            1:N con embargos.caso_id (un caso puede generar varios embargos)
            1:N con acuerdos_pago.caso_id
            1:N con recursos.caso_id
            1:1 o 1:N con remates.caso_id (depende si un caso remata múltiples bienes o uno solo)
            1:N con documentos.caso_id (mandamientos, actas, etc.)
            1:N con auditoria.caso_id (registro de acciones)

	- deudores
        * Propositos: Almacena la información de las personas naturales o jurídicas que deben una obligación.
        * Columnas Claves:
            id (PK)
            tipo_persona ('NATURAL' o 'JURIDICA')
            cedula_nit (único, identificación)
            nombres / apellidos (solo para NATURAL)
            razon_social (solo para JURÍDICA)
            correo, telefono, password_hash, creado_en
        * Quien la Utiliza:
            El Admin_Trámites la consulta/edita para radicar nuevos casos.
            El Módulo de Login de deudor si los deudores ingresan a un portal (para pactar acuerdos, ver estado de su caso).
        * Relaciones: 1:N con casos.deudor_id — un deudor puede tener varios casos activos.

	- documentos
        * Propositos:  Controla todos los archivos adjuntos a un caso (PDF de Título Ejecutivo, Mandamiento de Pago, Actas de Notificación, Comprobantes de Pago, Acuerdos de Pago firmados, Resoluciones de Recurso, etc.).
        * Columnas Claves:
            id (PK)
            caso_id (FK → casos.id)
            tipo_documento (ENUM: 'TITULO_EJECUTIVO', 'MANDAMIENTO_PAGO', 'EMBARGO_OFICIO', etc.)
            ruta_archivo (varchar) — ruta física o URL en el almacenamiento
            nombre_original, tamano_bytes, mime_type
            estado_documento ('PENDIENTE', 'VALIDADO', 'RECHAZADO')
            subido_por (FK → funcionarios.id) — quién lo sube (auxiliar, abogado)
            subido_en
        * Quien la Utiliza:
            El Admin_Trámites para subir el Título Ejecutivo al crear el caso.
            El Abogado para subir Mandamientos de Pago, Oficios de Embargo, Actas de Notificación, etc.
            El Deudor (si hay portal web) para subir, p.ej., comprobante de pago en depósito judicial.
            El Coordinador valida documentos (estado_documento = 'VALIDADO' o 'RECHAZADO').
        * Relaciones:
            caso_id → casos
            subido_por → funcionarios

	- entidades
        * Propositos: Lista de entidades externas a las que puede dirigirse un embargo: bancos, oficinas de tránsito o cámaras de comercio.
        * Columnas Claves:
            id (PK)
            tipo_entidad (ENUM: 'BANCO', 'TRANSITO', 'CAMARA_COMERCIO')
            nombre (p.ej. 'Bancolombia', 'Secretaría de Movilidad')
            contacto (correo o teléfono)
            creado_en
        * Quien la Utiliza:
            El Abogado para elegir a qué entidad (Banco, Tránsito, Cámara de Comercio) enviar la orden de embargo o medida cautelar.
            El sistema para automatizar envíos de oficios (si se integra con un módulo de correspondencia).
        * Relaciones:
            1:N con embargos.entidad_id.

	- embargos
        * Propositos: Registra cada solicitud de embargo que se hace sobre un caso, especificando la entidad (banco, cámara, tránsito), montos y estado de ejecución.
        * Columnas Claves:
            id (PK)
            caso_id (FK → casos.id)
            entidad_id (FK → entidades.id)
            tipo_embargo ('CUENTA_BANCARIA', 'BIEN_INMUEBLE', 'BIEN_VEHICULO')
            monto_solicitado
            monto_ejecutado (0 hasta que confirme la retención)
            estado_embargo (ENUM: 'SOLICITADO', 'EJECUTADO_TOTAL', 'EJECUTADO_PARCIAL', 'RECHAZADO')
            fecha_solicitud, fecha_ejecucion
        * Quien la Utiliza:
            El Abogado, al enviar oficios de embargo, crea un nuevo registro con estado = 'SOLICITADO'.
            Cuando el banco o registro responde, el Abogado actualiza monto_ejecutado y estado_embargo a 'EJECUTADO_TOTAL' o 'EJECUTADO_PARCIAL'.
            Los reportes de recuperación consultan esta tabla para calcular montos efectivamente cobrados.
        * Relaciones:
            caso_id → casos
            entidad_id → entidades

	- estados_tramites
        * Propositos: Define todos los posibles estados de un expediente en el ciclo de cobro (por ejemplo: INGRESADO, ASIGNADO, EN_ANALISIS, MANDAMIENTO_NOTIFICADO, etc.).
        * Columnas Claves:
            id (PK), nombre (p.ej. 'INGRESADO', 'EMBARGO_SOLICITADO', 'CERRADO_PAGADO', …), descripcion
        * Quien la Utiliza:
            El sistema de gestión (controladores/servicios) actualiza casos.estado_tramite_id cada vez que cambia el estado del expediente.
            Los reportes y la interfaz de monitoreo filtran casos según estos estados.
        * Relaciones:
            1:N con casos.estado_tramite_id.

	- funcionarios
        * Propositos: Guarda al personal interno del SENA que participa en el workflow (coordinador, auxiliares, abogados).
        * Columnas Claves:
            id (PK), rol_id (FK → roles.id), nombres, apellidos, telefono, correo_institucional, password_hash, creado_en
        * Quien la Utiliza:
            El Coordinador Jurídico crea/edita estos registros.
            El Login/Autenticación (backend) valida credenciales.
            Las capas de negocio consultan el rol para saber si puede ejecutar cierta acción (p.ej., solo un ABOGADO puede crear un embargo sobre un caso asignado a él).
        * Relaciones:
            rol_id → referencia a roles.
            1:N con asignaciones.funcionario_id.
            1:N con auditoria.funcionario_id (quién hizo cada acción).
            1:N con documentos.subido_por (quién sube cada documento).
            1:N con audit (Monolog/PHP, para auditoría en BD).

	- recursos
        * Propositos: Almacenar los recursos administrativos que presenta el deudor (reposición, nulidad, tutela) y su estado.
        * Columnas Claves:
            id (PK)
            caso_id (FK → casos.id)
            tipo_recurso (ENUM: 'REPOSICION', 'NULIDAD', 'TUTELA')
            fecha_presentacion
            estado_recurso ('PENDIENTE', 'ADMITIDO', 'NEGADO')
            fecha_resolucion, decisiones
        * Quien la Utiliza:
            El Deudor (vía portal o físicamente), para presentar el recurso.
            El Abogado o Coordinador Jurídico, para registrar la recepción y luego actualizar a 'ADMITIDO' o 'NEGADO'.
            El sistema (cron job) lo usa para suspender cualquier embargo mientras el recurso esté 'PENDIENTE'.
        * Relaciones:
            caso_id → casos
        
	- remates
        * Propositos: Registrar los remates de bienes embargados (principalmente inmuebles o vehículos) cuando el embargo parcial no es suficiente.
        * Columnas Claves:
            id (PK)
            caso_id (FK → casos.id)
            inmueble_descripcion
            valor_avalado
            fecha_programada
            valor_obtenido (NULL hasta la subasta)
            estado_remate (ENUM: 'PROGRAMADO', 'EJECUTADO', 'SIN_POSTOR', 'CANCELADO')
            fecha_ejecucion
        * Quien la Utiliza:
            El Abogado, una vez identifica bienes que pueden rematarse (coordinado con el depósito de remate).
            El Oficial de Remates (externo o interno), cuando responde con el valor efectivamente obtenido.
            El Coordinador, para cerrar el caso si el monto cubre la deuda.
        * Relaciones:
            caso_id → casos

	- roles
        * Propositos: Contiene los distintos perfiles de usuario dentro del sistema.
        * Columnas clave:
            id (PK), nombre (p.ej. ADMIN, AUX_COBRO, ABOGADO, DEUDOR), descripcion
        * Quien la Utiliza:
            Se asigna a cada funcionario (rol interno) y se usa para controlar permisos y lógica de negocio (p.ej., solo AUX_COBRO puede radicar casos; solo ABOGADO puede gestionar embargos).
        * Relaciones:
            1:N con funcionarios.rol_id — un rol puede corresponder a muchos funcionarios.

	- tipos_tramite
        * Propositos: Catálogo de las “naturalezas” de los casos (matrícula, multa administrativa, convenio incumplido, etc.).
        * Columnas Claves:
            id (PK). nombre (p.ej. 'MATRICULA', 'MULTA_ADMINISTRATIVA', etc.), descripcion
        * Quien la Utiliza:
            El Admin_Trámites (o la interfaz de radicación), al crear un nuevo caso siempre elige uno de estos tipos.
            Los reportes (para agrupar casos por tipo de trámite).
        * Relaciones:
            1:N con casos.tipo_tramite_id.

---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------             

Cómo encaja todo en el flujo de trabajo:

    -Radicación-
        El Admin_Trámites crea un registro en deudores (si no existe) y luego en casos con estado_tramite = INGRESADO.
        Se sube el archivo del Título Ejecutivo a documentos (tipo_documento = 'TITULO_EJECUTIVO').
        El Coordinador Jurídico aprueba, entonces se actualiza casos.estado_tramite_id = ASIGNADO, y se crea un registro en asignaciones.

    -Asignación-
        En asignaciones se guarda el abogado al que se asigna (funcionario_id) y quién lo asignó (asignado_por).
        casos.fecha_asignacion se actualiza con now().

    -Análisis y Mandamiento-
        El Abogado cambia casos.estado_tramite_id a EN_ANALISIS.
        Cuando emite el mandamiento, guarda el PDF en documentos (tipo_documento = 'MANDAMIENTO_PAGO') y actualiza casos.estado_tramite_id = MANDAMIENTO_NOTIFICADO, además de fijar casos.fecha_limite_pago.
        Se agrega una entrada en auditoria para registrar “CREAR_MANDAMIENTO”.

    -Cobro Persuasivo- (si aplica)
        Si usan cobro persuasivo antes de mandamiento, el Abogado registra en casos.estado_tramite_id = COBRO_PERSUASIVO y en auditoria “INICIO_COBRO_PERSUASIVO”.

    -Embargos-
        Si vence fecha_limite_pago sin pago, el sistema (cron job) o el Abogado crea un nuevo registro en embargos con estado_embargo = 'SOLICITADO' y la entidad_id correspondiente (= banco o tránsito).
        Cuando el banco responde, el Abogado actualiza embargos.monto_ejecutado, embargos.estado_embargo = 'EJECUTADO_TOTAL' o 'EJECUTADO_PARCIAL'.
        Se registra “REGISTRAR_EMBARGO” en auditoria.

    -Plan de Pagos-
        Si el deudor solicita un acuerdo antes o después del embargo parcial, el Abogado crea un registro en acuerdos_pago con estado_acuerdo = 'VIGENTE'.
        El Coordinador aprueba (o rechaza) y cambia estado_acuerdo y, en caso de aprobación, actualiza casos.estado_tramite_id = PAGO_VOLUNTARIO.
        Entrada en auditoria: “CREAR_ACUERDO_PAGO”.

    -Recursos Administrativos-
        Si el deudor presenta reposición o tutela, se genera un registro en recursos con estado_recurso = 'PENDIENTE'.
        El Abogado o Coordinador actualiza a 'ADMITIDO' o 'NEGADO'.
        Dependiendo del resultado, se cambia casos.estado_tramite_id = SUSPENDIDO_RECURSO o vuelve a estados previos.
        Entrada en auditoria: “CREAR_RECURSO” o “RESOLVER_RECURSO”.

    -Remate de Bienes-
        Si tras embargo parcial el monto no se cubre, el Abogado crea un registro en remates con estado_remate = 'PROGRAMADO'.
        Al ejecutarse el remate, se actualiza remates.valor_obtenido, remates.estado_remate = 'EJECUTADO', remates.fecha_ejecucion.
        Se actualiza casos.estado_tramite_id = PAGADO_REMATE o, si no se adjudica, podría quedar SIN_POSTOR.
        Entrada en auditoria: “EJECUTAR_REMATE”.

    -Cierre de Caso-
        Una vez el caso está saldado (sea por pago voluntario, embargo total o remate) se actualiza casos.estado_tramite_id = CERRADO_PAGADO.
        casos.fecha_cierre = now().
        Entrada en auditoria: “CIERRE_CASO”.

-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                Resumen General de Uso

Etapa del Flujo	                    Tablas implicadas

    Radicar caso	                    deudores (si es nuevo), casos, documentos (Título Ejecutivo), auditoria
    Asignar a abogado	                casos (update estado), asignaciones, auditoria
    Emitir mandamiento	                casos, documentos (Mandamiento), auditoria
    Solicitar embargo	                embargos, auditoria
    Confirmar embargo	                embargos, casos (posible update de estado o montos), auditoria
    Acuerdo de pago	                    acuerdos_pago, casos, auditoria
    Recursos administrativos	        recursos, casos, auditoria
    Remate de bienes	                remates, casos, auditoria
    Cierre de caso	                    casos, auditoria, (posible update en documentos de levante)