<?php
session_start();
require_once '../vendor/autoload.php';

// Configuración del cliente de Google
$googleClient = new Google_Client();
$googleClient->setClientId('26696218763-j190fjvms85cfb26s9m3um3hioern0qv.apps.googleusercontent.com');
$googleClient->setClientSecret('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');
$googleClient->setRedirectUri('http://localhost:3000/login/login.php');

// Agregar los alcances necesarios
$googleClient->addScope('email');
$googleClient->addScope('profile');

// Verificar si ya está logueado
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header('Location: ../index.php');
    exit;
}

// Manejar inicio de sesión de Google
if (isset($_GET['code'])) {
    try {
        $token = $googleClient->fetchAccessTokenWithAuthCode($_GET['code']);
        
        if (!isset($token['error'])) {
            $googleClient->setAccessToken($token['access_token']);
            
            // Obtener información del usuario
            $googleService = new Google_Service_Oauth2($googleClient);
            $userInfo = $googleService->userinfo->get();
            
            // Guardar información en sesión
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_email'] = $userInfo->email;
            $_SESSION['user_name'] = $userInfo->name;

            // Manejo robusto de la imagen de perfil
            $profilePicture = $userInfo->picture ?? '';
            $_SESSION['user_picture'] = filter_var($profilePicture, FILTER_VALIDATE_URL) 
                ? $profilePicture 
                : 'img/default-avatar.png';

            // Redirigir al índice
            header('Location: ../index.php');
            exit;
        } else {
            throw new Exception($token['error']);
        }
    } catch (Exception $e) {
        // Manejar errores
        $loginError = "Error al iniciar sesión: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - Dongarbanzo</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <h2 class="text-2xl font-bold mb-6 text-center">Iniciar Sesión</h2>
        
        <?php if (isset($loginError)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <?php echo htmlspecialchars($loginError); ?>
        </div>
        <?php endif; ?>
        
        <a href="<?php echo $googleClient->createAuthUrl(); ?>" 
           class="w-full flex items-center justify-center bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600 transition duration-300">
            <svg class="w-6 h-6 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
                <path fill="#4285F4" d="M45.12 24.5c0-1.56-.14-3.06-.4-4.5H24v8.51h11.84c-.51 2.75-2.06 5.08-4.42 6.62v5.52h7.15c4.16-3.83 6.55-9.47 6.55-16.15z"/>
                <path fill="#34A853" d="M24 46c5.94 0 10.92-1.97 14.56-5.33l-7.15-5.52c-1.97 1.32-4.49 2.1-7.41 2.1-5.7 0-10.54-3.85-12.28-9.03H4.34v5.7C7.96 41.07 15.4 46 24 46z"/>
                <path fill="#FBBC05" d="M11.72 28.22c-.44-1.32-.69-2.72-.69-4.16s.25-2.84.69-4.16V14.2H4.34A23.933 23.933 0 0 0 0 24c0 3.86.93 7.5 2.59 10.73l7.13-5.51z"/>
                <path fill="#EA4335" d="M24 9.5c3.21 0 6.11 1.11 8.38 3.28l6.31-6.31C34.91 2.91 29.93 0 24 0 15.4 0 7.96 4.93 4.34 12.27l7.38 5.73C13.46 13.35 18.3 9.5 24 9.5z"/>
            </svg>
            Iniciar Sesión con Google
        </a>
    </div>
</body>
</html>
