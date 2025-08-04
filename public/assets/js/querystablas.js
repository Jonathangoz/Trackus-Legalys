
  <!-- Incluye aquí tu JS global -->
  <script src="/assets/js/main.js"></script>
  <script>
    // Ejemplo: para llenar la tabla de deudores mediante AJAX seguro:
    document.addEventListener('DOMContentLoaded', () => {
      fetch('/api', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Auth-Token': '<?= htmlspecialchars($_SESSION['auth_token']) ?>'
        },
        body: JSON.stringify({ action: 'listar_deudores' })
      })
      .then(res => res.json())
      .then(json => {
        if (json.deudores) {
          let tabla = '<table><thead><tr><th>ID</th><th>Nombre</th><th>Deuda</th></tr></thead><tbody>';
          json.deudores.forEach(d => {
            tabla += `<tr>
                        <td>${d.id}</td>
                        <td>${d.nombre}</td>
                        <td>${d.deuda}</td>
                      </tr>`;
          });
          tabla += '</tbody></table>';
          document.getElementById('tabla-deudores').innerHTML = tabla;
        }
      })
      .catch(err => {
        console.error('Error al listar deudores:', err);
      });
    });