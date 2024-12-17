<?php
require 'config.php';

if (isset($_SESSION['usuario_google'])) {
    // Obtener el token de acceso actual
    $token = $googleClient->getAccessToken();

    // Revocar el token de acceso en Google
    if ($token) {
        $googleClient->revokeToken($token['access_token']);
    }

    // Limpiar la sesión
    unset($_SESSION['usuario_google']);
}

// Destruir completamente la sesión
session_destroy();

// Redirigir al login
header('Location: index.php');
exit();
