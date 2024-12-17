<?php
require __DIR__ . '/../vendor/autoload.php';

use Dongarbanzo\Newsletter\Newsletter;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $config = require __DIR__ . '/../config/email_config.php';
    $data = json_decode(file_get_contents('php://input'), true);

    // Validaciones
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false, 
            'message' => 'Por favor, ingresa un correo válido'
        ]);
        exit;
    }

    $newsletter = new Newsletter($config);
    $result = $newsletter->subscribe($data);

    echo json_encode($result);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Método no permitido']);