  // Obtener tokens desde PHP (sólo se exponen variables no sensibles)
    const authToken = '<?= htmlspecialchars($_SESSION['auth_token'] ?? '') ?>';
    const csrfToken = '<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>';

    // Función genérica para llamar a la API
    async function callApi(data) {
      try {
        const res = await fetch('/api', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Auth-Token': authToken
          },
          body: JSON.stringify(Object.assign(data, { csrf_token: csrfToken }))
        });
        if (!res.ok) throw new Error('Error en la petición: ' + res.status);
        return await res.json();
      } catch (err) {
        console.error(err);
        return { error: err.message };
      }
    }

    // Cargar deudores al inicio
    document.addEventListener('DOMContentLoaded', async () => {
      // Listar deudores
      const respDeudores = await callApi({ action: 'listar_deudores' });
      if (!respDeudores.error) {
        let html = '<table><thead><tr><th>ID</th><th>Nombre</th><th>Deuda</th><th>Acciones</th></tr></thead><tbody>';
        respDeudores.deudores.forEach(d => {
          html += `<tr>
                     <td>${d.id}</td>
                     <td>${d.nombre}</td>
                     <td>${d.deuda}</td>
                     <td>
                       <button onclick="activarDeudor(${d.id})">Activar</button>
                       <button onclick="desactivarDeudor(${d.id})">Desactivar</button>
                     </td>
                   </tr>`;
        });
        html += '</tbody></table>';
        document.getElementById('tabla-deudores').innerHTML = html;
      }

      // Listar funcionarios
      const respFunc = await callApi({ action: 'listar_funcionarios' });
      if (!respFunc.error) {
        let htmlF = '<table><thead><tr><th>ID</th><th>Nombre</th><th>Acciones</th></tr></thead><tbody>';
        respFunc.funcionarios.forEach(f => {
          htmlF += `<tr>
                     <td>${f.id}</td>
                     <td>${f.nombre}</td>
                     <td>
                       <button onclick="activarFuncionario(${f.id})">Activar</button>
                       <button onclick="desactivarFuncionario(${f.id})">Desactivar</button>
                     </td>
                   </tr>`;
        });
        htmlF += '</tbody></table>';
        document.getElementById('tabla-funcionarios').innerHTML = htmlF;
      }
    });

    // Funciones para activar/desactivar (deudores)
    async function activarDeudor(id) {
      const resp = await callApi({ action: 'activar_deudor', id });
      if (!resp.error) location.reload();
      else alert(resp.error);
    }
    async function desactivarDeudor(id) {
      const resp = await callApi({ action: 'desactivar_deudor', id });
      if (!resp.error) location.reload();
      else alert(resp.error);
    }

    // Funciones para activar/desactivar (funcionarios)
    async function activarFuncionario(id) {
      const resp = await callApi({ action: 'activar_funcionario', id });
      if (!resp.error) location.reload();
      else alert(resp.error);
    }
    async function desactivarFuncionario(id) {
      const resp = await callApi({ action: 'desactivar_funcionario', id });
      if (!resp.error) location.reload();
      else alert(resp.error);
    }