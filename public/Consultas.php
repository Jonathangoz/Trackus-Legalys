<?php
  session_start();

  if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
      header("Location: ../logging.php");
      exit;
  }

  if ($_SESSION['tipo_rol'] !== 'USUARIOS') {
    header("Location: ../logging.php");
    session_destroy();
    session_unset();
    exit;
  }
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Consultas</title>
        <link rel="stylesheet" href="CSS/Consultas.css">
        <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    </head>
    
    <body>
        <div class="top">
            <a href="https://www.gov.co" target="_blank" alt="Gov.co" rel="noopener noreferrer">
                <img class="gov" src="https://css.mintic.gov.co/mt/mintic/img/header_govco.png" alt="Gov Co">
            </a>
        </div>
        <div class="container">
                <div class="section-title">
                    <h2>Consulta de Procesos</h2>
                    <p>Consulte el estado actual de su proceso, utilizando su número de identificación o el número del proceso.</p>
                </div>
                <div class="form-container">
                    <h3>Formulario de Consulta</h3>
                    <form>
                        <div class="form-row">
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="tipo-documento">Tipo de Documento</label>
                                    <select id="tipo-documento" required>
                                        <option value="">Seleccione una opción</option>
                                        <option value="cc">Cédula de Ciudadanía</option>
                                        <option value="nit">NIT</option>
                                        <option value="ce">Cédula de Extranjería</option>
                                        <option value="pp">Pasaporte</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="numero-documento">Número de Documento</label>
                                    <input type="text" id="numero-documento" placeholder="Ingrese su número de documento" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="num-proceso">Número de Proceso (Opcional)</label>
                            <input type="text" id="num-proceso" placeholder="Si conoce el número de proceso, ingréselo aquí">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn">Consultar</button>
                        </div>
                    </form>
                </div>
            </div>
    </body>
</html>