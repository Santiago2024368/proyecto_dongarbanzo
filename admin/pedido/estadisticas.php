<?php
// Configuración de conexión
$host = 'localhost';
$usuario = 'root';
$contraseña = '12345';
$base_datos = 'dongarbanzo';

// Conectar a la base de datos
$conexion = mysqli_connect($host, $usuario, $contraseña, $base_datos);

// Verificar conexión
if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Consulta para obtener productos más vendidos
$consulta = "
    SELECT 
        nombre_producto, 
        SUM(cantidad) as total_vendido,
        SUM(cantidad * precio) as total_ingresos
    FROM detalle_pedidos
    GROUP BY nombre_producto
    ORDER BY total_vendido DESC
    LIMIT 10
";

$resultado = mysqli_query($conexion, $consulta);

// Preparar datos para gráficos
$productos = [];
$unidades = [];
$ingresos = [];

while ($fila = mysqli_fetch_assoc($resultado)) {
    $productos[] = $fila['nombre_producto'];
    $unidades[] = $fila['total_vendido'];
    $ingresos[] = $fila['total_ingresos'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gráfica de Ventas</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 800px; 
            margin: 0 auto; 
            padding: 20px; 
        }
        .grafico-container { 
            margin-bottom: 30px; 
            border: 1px solid #ddd; 
            padding: 15px; 
        }
    </style>
</head>
<body>
    <div class="grafico-container">
        <h2>Productos Más Vendidos</h2>
        <canvas id="graficaProductos"></canvas>
    </div>

    <script>
        var ctx = document.getElementById('graficaProductos').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($productos); ?>,
                datasets: [
                    {
                        label: 'Unidades Vendidas',
                        data: <?php echo json_encode($unidades); ?>,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)', // Color azul
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Ingresos ($)',
                        data: <?php echo json_encode($ingresos); ?>,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)', // Color rosa/rojo
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Cantidad / Ingresos'
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Productos Más Vendidos'
                    }
                }
            }
        });
    </script>
</body>
</html>

<?php
// Cerrar conexión
mysqli_close($conexion);
?>