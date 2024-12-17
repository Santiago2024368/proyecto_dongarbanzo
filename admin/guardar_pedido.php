<?php
header('Content-Type: application/json');

// Configuración de conexión a la base de datos
$conexion = new mysqli('localhost', 'root', '12345', 'dongarbanzo');

// Verificar conexión
if ($conexion->connect_error) {
    die(json_encode(['error' => 'Error de conexión: ' . $conexion->connect_error]));
}

// Recibir datos JSON del frontend
$datos = json_decode(file_get_contents('php://input'), true);

try {
    // Iniciar transacción
    $conexion->begin_transaction();

    // Insertar cliente
    $stmt = $conexion->prepare("INSERT INTO clientes (nombre, telefono, cedula) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", 
        $datos['cliente']['nombre'], 
        $datos['cliente']['telefono'], 
        $datos['cliente']['cedula']
    );
    $stmt->execute();
    $cliente_id = $conexion->insert_id;

    // Insertar pedido
    $stmt = $conexion->prepare("INSERT INTO pedidos (cliente_id, tipo_entrega, metodo_pago, total) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issd", 
        $cliente_id, 
        $datos['pedido']['tipo_entrega'], 
        $datos['pedido']['metodo_pago'], 
        $datos['pedido']['total']
    );
    $stmt->execute();
    $pedido_id = $conexion->insert_id;

    // Insertar detalles del pedido
    $stmt = $conexion->prepare("INSERT INTO detalle_pedidos (pedido_id, producto_id, categoria, nombre_producto, precio, cantidad) VALUES (?, ?, ?, ?, ?, ?)");

    foreach ($datos['pedido']['productos'] as $producto) {
        $stmt->bind_param("iissdi", 
            $pedido_id, 
            $producto['id'], 
            $producto['categoria'], 
            $producto['nombre'], 
            $producto['precio'], 
            $producto['cantidad']
        );
        $stmt->execute();
    }

    // Confirmar transacción
    $conexion->commit();

    echo json_encode(['status' => 'success', 'mensaje' => 'Pedido guardado correctamente']);
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conexion->rollback();
    echo json_encode(['status' => 'error', 'mensaje' => 'Error al procesar el pedido: ' . $e->getMessage()]);
}

$conexion->close();
?>