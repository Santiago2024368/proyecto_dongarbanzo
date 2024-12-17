<?php
include 'conexion.php'; // Archivo de conexión a la base de datos

// Consulta para obtener pedidos actualizados
$consulta = "
    SELECT p.id, c.nombre, c.telefono, c.cedula, 
           p.fecha, p.tipo_entrega, p.metodo_pago, p.total,
           COUNT(dp.id) as total_productos
    FROM pedidos p
    JOIN clientes c ON p.cliente_id = c.id
    LEFT JOIN detalle_pedidos dp ON p.id = dp.pedido_id
    GROUP BY p.id
    ORDER BY p.fecha DESC
";
$stmt = $pdo->query($consulta);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Retornar respuesta JSON
header('Content-Type: application/json');
echo json_encode($pedidos);
