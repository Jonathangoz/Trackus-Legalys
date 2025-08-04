document.addEventListener('DOMContentLoaded', () => {
  // Referencias a elementos principales
  const tabs = document.querySelectorAll('.tab-button');
  const tabContents = document.querySelectorAll('.tab-content');

  const pendientesBody = document.getElementById('pendientes-body');
  const correccionesBody = document.getElementById('correcciones-body');

  const modalEdit = document.getElementById('modal-edit');
  const formEdit = document.getElementById('form-edit');
  const btnCancelEdit = document.getElementById('btn-cancel-edit');

  // 1. Funcionalidad de tabs
  tabs.forEach(button => {
    button.addEventListener('click', () => {
      // Quitar clases activas
      tabs.forEach(btn => btn.classList.remove('tab-button--active'));
      tabContents.forEach(content => content.classList.remove('tab-content--active'));

      // Activar la pestaña clickeada
      button.classList.add('tab-button--active');
      const tabToShow = document.getElementById(button.dataset.tab);
      tabToShow.classList.add('tab-content--active');
    });
  });

  // 2. Cargar datos iniciales (simulación o llamada al backend)
  //    En un escenario real, reemplaza con fetch('/api/casos/pendientes') y fetch('/api/casos/correcciones').
  fetch('/src/Modulos/Asignacion/Modelos/asignacion.php')
  const casosPendientes = [
    { id: 1, deudor: 'Juan Pérez', fecha: '2025-06-08' },
    { id: 4, deudor: 'Carlos Rodríguez', fecha: '2025-06-12' },
    { id: 6, deudor: 'Inversiones XYZ S.A.', fecha: '2025-06-13' },
    { id: 6, deudor: 'Inversiones XYZ S.A.', fecha: '2025-06-13' },
    { id: 6, deudor: 'Inversiones XYZ S.A.', fecha: '2025-06-13' },
    { id: 6, deudor: 'Inversiones XYZ S.A.', fecha: '2025-06-13' },
 
  ];
  fetch('/src/Modulos/Asignacion/Modelos/registros.php.php')
  const casosCorrecciones = [
    { id: 7, deudor: 'Andrés Jiménez', comentario: 'Monto de intereses mal calculado' },
    { id: 13, deudor: 'Patricia Castro', comentario: 'Dirección equivocada' },
  
  ];

  function renderPendientes() {
    pendientesBody.innerHTML = '';
    casosPendientes.forEach(caso => {
      const tr = document.createElement('tr');
      tr.dataset.casoId = caso.id;

      tr.innerHTML = `
        <td>${caso.id}</td>
        <td>${caso.deudor}</td>
        <td>
          <select class="select-tipo-tramite">
            <option value="">Seleccionar</option>
            <option value="1">MATRICULA</option>
            <option value="2">MULTA_ADMINISTRATIVA</option>
            <option value="3">CONVENIO_INCUMPLIDO</option>
            <option value="4">APORTE_APRENDIZAJE</option>
            <option value="5">MULTA_AMBIENTAL</option>
            <option value="6">MULTA_MOVILIDAD</option>
            <option value="7">FUNDACION_PROYECTO_SOCIAL</option>
            <option value="8">CONVENIO_INTERNACIONAL</option>
        </td>
        <td>${caso.fecha}</td>
        <td>
          <select class="select-abogado">
            <option value="">Seleccionar</option>
            <option value="3">Laura Martínez</option>
            <option value="4">Diego López</option>
            <option value="5">Sofía Torres</option>
          </select>
        </td>
        <td>
          <button class="btn btn--assign">Asignar</button>
          <button class="btn btn--archive">Archivar</button>
          <button class="btn btn--edit">Modificar</button>
        </td>
      `;
      pendientesBody.appendChild(tr);
    });
  }

  function renderCorrecciones() {
    correccionesBody.innerHTML = '';
    casosCorrecciones.forEach(caso => {
      const tr = document.createElement('tr');
      tr.dataset.casoId = caso.id;

      tr.innerHTML = `
        <td>${caso.id}</td>
        <td>${caso.deudor}</td>
        <td>${caso.comentario}</td>
        <td>
          <button class="btn btn--resolve">Marcar Corregido</button>
          <button class="btn btn--assign">Reasignar</button>
        </td>
      `;
      correccionesBody.appendChild(tr);
    });
  }

  // Inicializar tablas
  renderPendientes();
  renderCorrecciones();

  // 3. Delegación de eventos en "Casos Pendientes"
  pendientesBody.addEventListener('click', e => {
    const target = e.target;
    const row = target.closest('tr');
    if (!row) return;
    const casoId = row.dataset.casoId;

    // A) Asignar
    if (target.classList.contains('btn--assign')) {
      const select = row.querySelector('.select-abogado');
      const funcionarioId = select.value;
      if (!funcionarioId) {
        alert('Seleccione un abogado antes de asignar.');
        return;
      }
      // Llamar al endpoint de asignación (reemplazar URL por la real)
      fetch(`/api/asignaciones`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          caso_id: parseInt(casoId),
          funcionario_id: parseInt(funcionarioId),
          asignado_por: /* ID del admin logueado (se inyecta desde sesión) */ 2,
          estado_asignacion: 'ASIGNADO'
        })
      })
      .then(res => {
        if (!res.ok) throw new Error('Error al asignar');
        return res.json();
      })
      .then(data => {
        // Mover la fila a otro lugar, o mostrar notificación de éxito
        row.remove();
        alert(`Caso ${casoId} asignado correctamente.`);
      })
      .catch(err => {
        console.error(err);
        alert('Falló la asignación. Intente de nuevo.');
      });
    }

    // B) Archivar (solo marca como archivado, no elimina físicamente)
    if (target.classList.contains('btn--archive')) {
      if (!confirm(`¿Está seguro de archivar el caso ${casoId}?`)) return;
      // Llamar a endpoint de archivado (reemplazar URL por la real)
      fetch(`/api/casos/${casoId}/archivar`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ archivado: true })
      })
      .then(res => {
        if (!res.ok) throw new Error('Error al archivar');
        return res.json();
      })
      .then(data => {
        row.remove();
        alert(`Caso ${casoId} archivado.`);
      })
      .catch(err => {
        console.error(err);
        alert('Falló al archivar. Intente de nuevo.');
      });
    }

    // C) Modificar → Abrir modal con datos precargados
    if (target.classList.contains('btn--edit')) {
      // Pre-carga datos del caso (en un escenario real, podrías fetch(`/api/casos/${casoId}`))
      // Aquí usamos valores de ejemplo estáticos:
      const ejemplo = { 
        monto_original: 3000000, 
        intereses_acumulados: 200000, 
        costos_administrativos: 0 
      };
      document.getElementById('edit-caso-id').value = casoId;
      document.getElementById('edit-monto').value = ejemplo.monto_original;
      document.getElementById('edit-intereses').value = ejemplo.intereses_acumulados;
      document.getElementById('edit-costos').value = ejemplo.costos_administrativos;

      modalEdit.classList.remove('hidden');
    }
  });

  // 4. Lógica del Modal "Modificar Caso"
  btnCancelEdit.addEventListener('click', () => {
    modalEdit.classList.add('hidden');
  });

  formEdit.addEventListener('submit', e => {
    e.preventDefault();
    const casoId = document.getElementById('edit-caso-id').value;
    const monto = document.getElementById('edit-monto').value;
    const intereses = document.getElementById('edit-intereses').value;
    const costos = document.getElementById('edit-costos').value;

    // Llamar a endpoint de actualización de caso
    fetch(`/api/casos/${casoId}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        monto_original: parseFloat(monto),
        intereses_acumulados: parseFloat(intereses),
        costos_administrativos: parseFloat(costos)
      })
    })
    .then(res => {
      if (!res.ok) throw new Error('Error al actualizar caso');
      return res.json();
    })
    .then(data => {
      modalEdit.classList.add('hidden');
      alert(`Caso ${casoId} actualizado correctamente.`);
      // Opcional: actualizar en la tabla la fila con nuevos valores
    })
    .catch(err => {
      console.error(err);
      alert('Falló al actualizar. Intente de nuevo.');
    });
  });

  // 5. Delegación de eventos en "Correcciones"
  correccionesBody.addEventListener('click', e => {
    const target = e.target;
    const row = target.closest('tr');
    if (!row) return;
    const casoId = row.dataset.casoId;

    // A) Marcar Corregido
    if (target.classList.contains('btn--resolve')) {
      if (!confirm(`¿Confirmar que el caso ${casoId} ya está corregido?`)) return;
      fetch(`/api/casos/${casoId}/correccion`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ estado: 'NEEDS_REVIEW_BY_LAWYER' })
      })
      .then(res => {
        if (!res.ok) throw new Error('Error al marcar corregido');
        return res.json();
      })
      .then(data => {
        row.remove();
        alert(`Caso ${casoId} marcado como corregido y reenviado al abogado.`);
      })
      .catch(err => {
        console.error(err);
        alert('Falló la marcación. Intente de nuevo.');
      });
    }

    // B) Reasignar desde Correcciones
    if (target.classList.contains('btn--assign')) {
      // Al igual que en la sección de pendientes, mostrar modal de reasignación
      // (por simplicidad, reutilizamos el mismo modal de edición o se abre otro similar)
      alert('Funcionalidad de reasignación desde Correcciones aún no implementada en este demo.');
    }
  });
});
