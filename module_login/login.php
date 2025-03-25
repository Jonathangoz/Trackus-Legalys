<?php
/*use Firebase\JWT\JWT;

$payload = [
    'user_id' => 123,
    'email' => 'usuario@example.com',
    'exp' => time() + 3600, // Expira en 1 hora
];

$secretkey = 'TuClaveSecretaSuperSegura';
$token = JWT::encode($payload, $secretkey, 'HS256'); // para tokens con JWT 
*/
?>
//
<?php
session_start();
require '../module_login/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $correo = trim($_POST['correo']);
    $contraseña = trim($_POST['contrasenia']);
    $nombres = trim($_POST['nombres']);
    $apellidos = trim($_POST['apellidos']);
    $tiporol = trim($_POST['tipo_rol']);

    if (empty($correo) || empty($contraseña)) {
        $_SESSION['error'] = "Por favor, completa todos los campos.";
        header("Location: loggin.php");
        exit;
    }

    try {
        $stmt = $connect->prepare("SELECT id_funcionario, correo, contrasenia, nombres, apellidos, tipo_rol FROM funcionarios WHERE correo = :correo");
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($contraseña,  $user['contrasenia'])) {

                $token = bin2hex(random_bytes(64));
                // firma del token con HMAC
                $secretkey = $contraseña;
                $signature = hash_hmac('sha256', $token, $secretkey);
                $signedToken = $token . '.' . $signature;
                // encriptacion del token (cifrado simetrico con AES)
                $iv = random_bytes(32); // vector de inicializacion (iv)
                $encryptedToken = openssl_encrypt($token, 'AES-256-CBC', $secretkey, 0, $iv);

    
                $_SESSION['loggedin'] = true;
                $_SESSION['id_funcionario'] = $user['id_funcionario'];
                $_SESSION['correo'] = $user['correo'];
                $_SESSION['nombres'] = $user['nombres'];
                $_SESSION['apellidos'] = $user['apellidos'];
                $_SESSION['tipo_rol'] = $user['tipo_rol'];
                echo "Autenticación exitosa. Redirigiendo al dashboard...";
                header("Location: ../dashboard.php");
                exit;
            } else {
                $_SESSION['error'] = "Contraseña incorrecta.";
                echo "Error: " . $_SESSION['error'];
                header("Location: ../loggin.php");
                exit;
            }
        } else {
            $_SESSION['error'] = "Usuario no encontrado.";
            echo "Error: " . $_SESSION['error'];
            header("Location: ../loggin.php");
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        echo "Error en la consulta: " . $_SESSION['error'];
        header("Location: ../loggin.php");
        exit;
    }
}
//error_log(print_r($_POST, true));