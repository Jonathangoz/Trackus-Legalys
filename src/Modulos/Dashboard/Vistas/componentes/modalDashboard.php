<style>

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Segoe UI', sans-serif;
}

/* MODAL BASE STRUCTURE */
.modal-fade {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  width: 100%;
  background: rgba(0,0,0,0.4);
  z-index: 9999;
  display: flex;
  justify-content: center;
  align-items: center;
  overflow-y: auto;
}

.modal-dialog {
    transform: translateX(18rem);
    width: 57%;
    margin: 1.75rem;
    pointer-events: none;
    overflow: auto;
    z-index: 999;
}

/* 
    MODAL CONTENT STRUCTURE
 */
.modal-content {
    display: flex;
    flex-direction: column;
    width: 100%;
    pointer-events: auto;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid rgba(0, 0, 0, 0.2);
    border-radius: 0.5rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    outline: 0;
}

/* 
    MODAL HEADER
 */
.modal-header {
    display: flex;
    flex-shrink: 0;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1rem;
    border-bottom: 1px solid #dee2e6;
    border-top-left-radius: calc(0.5rem - 1px);
    border-top-right-radius: calc(0.5rem - 1px);
}

.modal-header-custom {
    background: linear-gradient(135deg, #39a900 0%, #39a921 100%);
    color: white;
}

.modal-title {
    margin-bottom: 0;
    line-height: 1.5;
    font-size: 1.25rem;
    font-weight: 500;
}

.btn-close {
    box-sizing: content-box;
    width: 1em;
    height: 1em;
    padding: 0.25em 0.25em;
    color: #000;
    background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='m.235.757 14.014 14.009m0-14.009L.235 14.766'/%3e%3c/svg%3e") center/1em auto no-repeat;
    border: 0;
    border-radius: 0.375rem;
    opacity: 0.5;
    cursor: pointer;
}

.btn-close:hover {
    color: #000;
    text-decoration: none;
    opacity: 0.75;
}

.btn-close-white {
    filter: invert(1) grayscale(100%) brightness(200%);
}

/* 
    MODAL BODY
 */
.modal-body {
    position: relative;
    flex: 1 1 auto;
    padding: 1rem;
}

/* 
    FORM STYLES
 */
.form-row {
    display: flex;
    flex-wrap: wrap;
    margin-right: -0.75rem;
    margin-left: -0.75rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-col-6 {
    flex: 0 0 50%;
    max-width: 50%;
    padding-right: 0.75rem;
    padding-left: 0.75rem;
}

.form-label {
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #212529;
    display: inline-block;
}

.form-control {
    display: block;
    width: 100%;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #212529;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control:focus {
    color: #212529;
    background-color: #fff;
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.form-control:invalid {
    border-color: #dc3545;
}

.form-select {
    display: block;
    width: 100%;
    padding: 0.375rem 2.25rem 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #212529;
    background-color: #fff;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m1 6 7 7 7-7'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 16px 12px;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    appearance: none;
}

.form-select:focus {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* 
    INPUT GROUP (Para contraseñas)
 */
.input-group {
    position: relative;
    display: flex;
    flex-wrap: wrap;
    align-items: stretch;
    width: 100%;
}

.input-group .form-control {
    position: relative;
    flex: 1 1 auto;
    width: 1%;
    min-width: 0;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

.input-group .form-control:focus {
    z-index: 5;
}

.input-group .btn {
    position: relative;
    z-index: 2;
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

/* 
    BUTTONS
 */
.btn {
    display: inline-block;
    font-weight: 400;
    line-height: 1.5;
    color: #212529;
    text-align: center;
    text-decoration: none;
    vertical-align: middle;
    cursor: pointer;
    -webkit-user-select: none;
    user-select: none;
    background-color: transparent;
    border: 1px solid transparent;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    border-radius: 0.375rem;
    transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.btn:hover {
    color: #212529;
}

.btn:focus {
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.btn:disabled {
    pointer-events: none;
    opacity: 0.65;
}

.btn-outline-secondary {
    color: #6c757d;
    border-color: #6c757d;
}

.btn-outline-secondary:hover {
    color: #fff;
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-secondary {
    color: #fff;
    background-color: #6c757d;
    border-color: #6c757d;
}

.btn-secondary:hover {
    color: #fff;
    background-color: #5c636a;
    border-color: #565e64;
}

.btn-success {
    color: #fff;
    background-color: #39a900;
    border-color: #198754;
}

.btn-success:hover {
    color: #fff;
    background-color: #157347;
    border-color: #146c43;
}

/* 
    MODAL FOOTER
 */
.modal-footer {
    display: flex;
    flex-wrap: wrap;
    flex-shrink: 0;
    align-items: center;
    justify-content: flex-end;
    padding: 0.75rem;
    border-top: 1px solid #dee2e6;
    border-bottom-right-radius: calc(0.5rem - 1px);
    border-bottom-left-radius: calc(0.5rem - 1px);
    gap: 0.5rem;
}

/* 
    UTILITY CLASSES
 */
.text-white {
    color: #fff !important;
}

.text-danger {
    color: #dc3545 !important;
}

.small {
    font-size: 0.875em;
}

.mt-1 {
    margin-top: 0.25rem !important;
}

.d-none {
    display: none !important;
}

/* 
    RESPONSIVE DESIGN
 */
@media (max-width: 576px) {
    .modal-dialog {
        margin: 1rem;
    }

    .form-col-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }

    .modal-footer {
        flex-direction: column;
    }

    .modal-footer .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }

    .modal-footer .btn:last-child {
        margin-bottom: 0;
    }
}

@media (min-width: 992px) {
    .modal-dialog-large {
        max-width: 800px;
    }
}

/* ANIMATION STYLES */
.modal.fade {
    transition: opacity 0.15s linear;
}

.modal.fade .modal-dialog {
    transition: transform 0.3s ease-out;
    transform: translate(0, -50px);
}

.modal.show .modal-dialog {
    transform: none;
}

/* DEMO BUTTON */
.demo-btn {
    margin: 20px 0;
    padding: 12px 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    transition: transform 0.2s;
}

.demo-btn:hover {
    transform: translateY(-2px);
}
</style>
<div id="modalUser" onclick="closeModal()"></div>
<!-- Modal para creación de usuarios -->
<div class="modal-fade" id="contentmodal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-custom text-white">
                <h5 class="modal-title" id="userModalLabel">Crear nuevo usuario</h5>
                <span class="close-btn">×</span>
            </div>
            <div class="modal-body">
                <form id="userForm" class="form-row">
                    <div class="form-col-6">
                        <div class="form-group">
                            <label for="userName" class="form-label">Nombre completo *</label>
                            <input type="text" class="form-control" id="userName" required>
                        </div>
                    </div>
                    <div class="form-col-6">
                        <div class="form-group">
                            <label for="userIdentification" class="form-label">Número de identificación *</label>
                            <input type="text" class="form-control" id="userIdentification" required>
                        </div>
                    </div>
                    <div class="form-col-6">
                        <div class="form-group">
                            <label for="userEmail" class="form-label">Correo electrónico *</label>
                            <input type="email" class="form-control" id="userEmail" required>
                        </div>
                    </div>
                    <div class="form-col-6">
                        <div class="form-group">
                            <label for="userPhone" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="userPhone">
                        </div>
                    </div>
                    <div class="form-col-6">
                        <div class="form-group">
                            <label for="userRole" class="form-label">Rol *</label>
                            <select class="form-select" id="userRole" required>
                                <option value="">Seleccione un rol</option>
                                <option value="admin">Administrador</option>
                                <option value="supervisor">Supervisor</option>
                                <option value="cobrador">Cobrador</option>
                                <option value="consulta">Consulta</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col-6">
                        <div class="form-group">
                            <label for="userPassword" class="form-label">Contraseña *</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="userPassword" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('userPassword', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-col-6">
                        <div class="form-group">
                            <label for="userConfirmPassword" class="form-label">Confirmar contraseña *</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="userConfirmPassword" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('userConfirmPassword', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div id="passwordError" class="text-danger small mt-1 d-none">Las contraseñas no coinciden</div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancelar</button>
                <button type="button" class="btn btn-success" id="saveUser" onclick="saveUser()">Guardar</button>
            </div>
        </div>
    </div>
</div>    