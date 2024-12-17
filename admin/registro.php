<?php
include('conexion.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recoger datos del formulario
    $usuario = $_POST['usuario'];
    $contraseña = $_POST['contraseña'];
    $confirmar_contraseña = $_POST['confirmar_contraseña'];

    // Validaciones básicas
    $errores = [];

    // Verificar que las contraseñas coincidan
    if ($contraseña !== $confirmar_contraseña) {
        $errores[] = "Las contraseñas no coinciden";
    }

    // Verificar si el usuario ya existe
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $stmt->execute([$usuario]);

    if ($stmt->rowCount() > 0) {
        $errores[] = "El nombre de usuario ya está registrado";
    }

    // Si no hay errores, insertar nuevo usuario
    if (empty($errores)) {
        try {
            // Usar consulta preparada con marcadores de posición
            $stmt = $pdo->prepare("INSERT INTO usuarios (usuario, contraseña) VALUES (?, ?)");
            $resultado = $stmt->execute([$usuario, $contraseña]);

            if ($resultado) {
                // Redirigir a página de inicio de sesión con mensaje de éxito
                header("Location: login.php?registro=exitoso");
                exit();
            } else {
                $errores[] = "No se pudo registrar el usuario";
            }
        } catch(PDOException $e) {
            $errores[] = "Error al registrar el usuario: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Arial', sans-serif;
        }
        .registro-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 400px;
            width: 100%;
        }
        .registro-container h2 {
            color: #4a4a4a;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-control {
            border-radius: 25px;
            padding-left: 40px;
        }
        .input-group-text {
            background: none;
            border: none;
            position: absolute;
            z-index: 1;
            color: #888;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
        }
        .input-group {
            position: relative;
            margin-bottom: 15px;
        }
        .btn-registro {
            border-radius: 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-registro:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="registro-container">
        <h2>Crear Cuenta</h2>
        
        <?php 
        // Mostrar errores de validación
        if (!empty($errores)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errores as $error): ?>
                    <p><?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="registro.php">
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="text" class="form-control" name="usuario" placeholder="Nombre de usuario" required>
            </div>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control" name="contraseña" placeholder="Contraseña" required>
            </div>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control" name="confirmar_contraseña" placeholder="Confirmar contraseña" required>
            </div>
            <button type="submit" class="btn btn-registro w-100 text-white">Registrarse</button>
        </form>
        
        <div class="login-link">
            <p class="mt-3">¿Ya tienes una cuenta? <a href="login.php" class="text-decoration-none">Iniciar sesión</a></p>
        </div>
    </div>

    <!-- Bootstrap JS (opcional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>