    <!-- Modal para creación de usuarios -->
  <div class="modal" id="userModal">
    <div class="modal-dialog">
      <div class="modal-header">
        <h5>Crear nuevo usuario</h5>
        <button class="close-btn" id="closeModal">&times;</button>
      </div>
      <div class="modal-body">
        <form id="userForm">
          <div class="row">
            <div class="col">
              <label for="userName">Nombre completo *</label>
              <input type="text" id="userName" required>
            </div>
            <div class="col">
              <label for="userIdentification">Número de identificación *</label>
              <input type="text" id="userIdentification" required>
            </div>
            <div class="col">
              <label for="userEmail">Correo electrónico *</label>
              <input type="email" id="userEmail" required>
            </div>
            <div class="col">
              <label for="userPhone">Teléfono</label>
              <input type="tel" id="userPhone">
            </div>
            <div class="col">
              <label for="userRole">Rol *</label>
              <select id="userRole" required>
                <option value="">Seleccione un rol</option>
                <option value="admin">Administrador</option>
                <option value="supervisor">Supervisor</option>
                <option value="cobrador">Cobrador</option>
                <option value="consulta">Consulta</option>
              </select>
            </div>
            <div class="col">
              <label for="userPassword">Contraseña *</label>
              <div class="input-group">
                <input type="password" id="userPassword" required>
                <button type="button" class="toggle-password" data-target="userPassword">
                  👁️
                </button>
              </div>
            </div>
            <div class="col">
              <label for="userConfirmPassword">Confirmar contraseña *</label>
              <div class="input-group">
                <input type="password" id="userConfirmPassword" required>
                <button type="button" class="toggle-password" data-target="userConfirmPassword">
                  👁️
                </button>
              </div>
              <div id="passwordError">Las contraseñas no coinciden</div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="cancel" id="cancelBtn">Cancelar</button>
        <button class="save" id="saveUser">Guardar</button>
      </div>
    </div>
  </div>