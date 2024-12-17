<?php
$host = 'localhost';
$dbname = 'dongarbanzo';
$username = 'root';
$password = '12345';
$port = 3306; 

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Error al conectar: ' . $e->getMessage();
}

