<?php
require '../conexion.php';
$correo = $_POST['correo']; // Correo proporcionado por el usuario
$contraseña_plana = $_POST['contraseña']; // Contraseña proporcionada por el usuario
$hashed_password = password_hash($contraseña_plana, PASSWORD_DEFAULT);

try {
    $stmt = $connect->prepare("INSERT INTO funcionarios (correo, contraseña) VALUES (:correo, :contraseña)");
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':contraseña', $hashed_password);
    $stmt->execute();

    echo "Usuario registrado correctamente.";
} catch (PDOException $e) {
    die("Error al registrar el usuario: " . $e->getMessage());
}

$correo = $_POST['correo'];
$contraseña_plana = $_POST['contraseña'];

try {
    // Obtener el hash almacenado en la base de datos
    $stmt = $connect->prepare("SELECT id_funcionario, correo, contraseña FROM funcionarios WHERE correo = :correo");
    $stmt->bindParam(':correo', $correo);
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar la contraseña
        if (password_verify($contraseña_plana, $user['contraseña'])) {
            // Contraseña válida
            $_SESSION['loggedin'] = true;
            $_SESSION['id_funcionario'] = $user['id_funcionario'];
            $_SESSION['correo'] = $user['correo'];
            header("Location: ../../index.php");
            exit;
        } else {
            // Contraseña incorrecta
            $_SESSION['error'] = "Contraseña incorrecta.";
            header("Location: ../../loggin.php");
            exit;
        }
    } else {
        // Usuario no encontrado
        $_SESSION['error'] = "Usuario no encontrado.";
        header("Location: ../../loggin.php");
        exit;
    }
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}