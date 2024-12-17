<?php
session_start();
include('conexion.php');  

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $contraseña = $_POST['contraseña'];

    $sql = "SELECT * FROM usuarios WHERE usuario = :usuario";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
       
        if ($contraseña === $row['contraseña']) {
            $_SESSION['usuario_id'] = $row['id'];
            $_SESSION['usuario_nombre'] = $row['usuario'];
            header("Location: dashboard.php");  
            exit();
        } else {
            $error = "Contraseña incorrecta";
        }
    } else {
        $error = "Usuario no encontrado";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #ff9a00 0%, #ff5200 50%, #ff0000 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Arial', sans-serif;
            margin: 0;
        }
        .login-container {
            background: #ffffff;
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 450px;
        }
        .login-container h2 {
            text-align: center;
            font-weight: bold;
            color: #333;
            margin-bottom: 30px;
        }
        .login-container img {
            display: block;
            margin: 0 auto 20px;
            max-width: 100px;
        }
        .form-control {
            border-radius: 30px;
            padding-left: 50px;
        }
        .input-group-text {
            background: none;
            border: none;
            color: #999;
            position: absolute;
            z-index: 1;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
        }
        .input-group {
            position: relative;
        }
        .btn-login {
            border-radius: 30px;
            background: linear-gradient(135deg, #ff9a00 0%, #ff5200 50%, #ff0000 100%);
            border: none;
            color: white;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #ff0000 0%, #ff5200 50%, #ff9a00 100%);
        }
        .btn-secondary {
            border-radius: 30px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="img/logo.png" alt="Logo">
        <h2>Bienvenido</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="input-group mb-4">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Nombre de usuario" required>
            </div>
            <div class="input-group mb-4">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control" id="contraseña" name="contraseña" placeholder="Contraseña" required>
            </div>
            <button type="submit" class="btn btn-login w-100 mb-3">Iniciar sesión</button>
        </form>

        <a href="../index.php" class="btn btn-secondary w-100 mb-3">Volver al inicio</a>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
