<?php
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de base de datos
$config = [
    'host' => 'localhost',
    'usuario' => 'root',
    'password' => '12345',
    'base_datos' => 'dongarbanzo'
];

class GestorPedidos {
    private $conexion;

    public function __construct($config) {
        try {
            $this->conexion = new PDO(
                "mysql:host={$config['host']};dbname={$config['base_datos']};charset=utf8mb4", 
                $config['usuario'], 
                $config['password']
            );
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public function verificarNuevosPedidos($ultimoPedidoId) {
        $consulta = $this->conexion->prepare("
            SELECT COUNT(*) as nuevos_pedidos
            FROM pedidos 
            WHERE id > :ultimo_pedido_id AND estado_entrega = 'pendiente'
        ");
        $consulta->bindParam(':ultimo_pedido_id', $ultimoPedidoId, PDO::PARAM_INT);
        $consulta->execute();
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        return $resultado['nuevos_pedidos'];
    }

    public function obtenerPedidosPendientes() {
        $consulta = $this->conexion->prepare("
            SELECT p.id, p.fecha, p.tipo_entrega, p.metodo_pago, p.total, 
                   c.nombre AS nombre_cliente
            FROM pedidos p
            JOIN clientes c ON p.cliente_id = c.id
            WHERE p.estado_entrega = 'pendiente'
            ORDER BY p.fecha DESC
        ");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerDetallesPedido($pedido_id) {
        $consulta = $this->conexion->prepare("
            SELECT categoria, nombre_producto, precio, cantidad
            FROM detalle_pedidos
            WHERE pedido_id = :pedido_id
        ");
        $consulta->bindParam(':pedido_id', $pedido_id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public function marcarPedidoEntregado($pedido_id) {
        $consulta = $this->conexion->prepare("
            UPDATE pedidos 
            SET estado_entrega = 'entregado' 
            WHERE id = :pedido_id
        ");
        return $consulta->execute([':pedido_id' => $pedido_id]);
    }
}

// Manejar solicitudes AJAX
$gestor = new GestorPedidos($config);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? null;
    $pedido_id = $_POST['pedido_id'] ?? null;

    if ($accion === 'marcar_entregado' && $pedido_id) {
        $resultado = $gestor->marcarPedidoEntregado($pedido_id);
        echo json_encode(['success' => $resultado]);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $accion = $_GET['accion'] ?? null;
    $pedido_id = $_GET['pedido_id'] ?? null;

    if ($accion === 'detalles' && $pedido_id) {
        $detalles = $gestor->obtenerDetallesPedido($pedido_id);
        echo json_encode($detalles);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-yellow: #FFD700;
            --secondary-red: #DC3545;
            --sidebar-bg: #2C3E50;
            --sidebar-text: #ECF0F1;
        }
        body {
            display: flex;
            height: 100vh;
            overflow: hidden;
            background-color: #F4F6F9;
        }
        .sidebar {
            width: 250px;
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .sidebar .nav-link {
            color: var(--sidebar-text);
            display: flex;
            align-items: center;
            padding: 10px 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .sidebar .nav-link:hover {
            background-color: var(--primary-yellow);
            color: #000;
        }
        .sidebar .nav-link.active {
            background-color: var(--primary-yellow);
            color: #000;
        }
        .main-content {
            flex-grow: 1;
            overflow-y: auto;
            padding: 20px;
            background-color: #F4F6F9;
        }
        .btn-custom-yellow {
            background-color: var(--primary-yellow);
            color: #000;
        }
        .btn-custom-yellow:hover {
            background-color: #FFC300;
        }
        .custom-modal-header {
            background-color: var(--primary-yellow);
            color: #000;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4 class="mb-4 text-center">Pastelería</h4>
        <nav class="nav flex-column">
            
        <a href="pedidos.php" class="nav-link">
                <i class="me-2">📦</i> Pedidos
            </a>
            <a href="pedido/estadisticas.php" class="nav-link">
                <i class="me-2">📈</i> Estadísticas
            </a>
            <a href="dashboard.php" class="nav-link text-danger">
                <i class="me-2">🚪</i> Salir
            </a>
        </nav>
    </div>
    
    <div class="main-content">
        <div class="container-fluid">
            <h1 class="mb-4">Control de Pedidos</h1>
            
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title mb-3">Pedidos Pendientes</h2>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Método Pago</th>
                                    <th>Total</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="lista-pedidos">
                                <?php 
                                $pedidos = $gestor->obtenerPedidosPendientes();
                                foreach ($pedidos as $pedido): 
                                ?>
                                <tr>
                                    <td><?= $pedido['id'] ?></td>
                                    <td><?= $pedido['nombre_cliente'] ?></td>
                                    <td><?= $pedido['fecha'] ?></td>
                                    <td><?= $pedido['tipo_entrega'] ?></td>
                                    <td><?= $pedido['metodo_pago'] ?></td>
                                    <td>$<?= number_format($pedido['total'], 2) ?></td>
                                    <td>
                                        <button onclick="verDetalles(<?= $pedido['id'] ?>)" 
                                                class="btn btn-sm btn-info me-2">
                                            Detalles
                                        </button>
                                        <button onclick="marcarEntregado(<?= $pedido['id'] ?>)" 
                                                class="btn btn-sm btn-success">
                                            Entregar
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function verDetalles(pedidoId) {
        fetch(`?accion=detalles&pedido_id=${pedidoId}`)
            .then(response => response.json())
            .then(detalles => {
                let contenidoHTML = `
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Categoría</th>
                                    <th>Producto</th>
                                    <th>Precio</th>
                                    <th>Cantidad</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${detalles.map(detalle => `
                                    <tr>
                                        <td>${detalle.categoria}</td>
                                        <td>${detalle.nombre_producto}</td>
                                        <td>$${detalle.precio}</td>
                                        <td>${detalle.cantidad}</td>
                                        <td>$${detalle.precio * detalle.cantidad}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;

                Swal.fire({
                    title: `Detalles del Pedido #${pedidoId}`,
                    html: contenidoHTML,
                    showCloseButton: true,
                    showConfirmButton: false,
                    width: '800px'
                });
            });
    }

    function marcarEntregado(pedidoId) {
        Swal.fire({
            title: '¿Marcar pedido como entregado?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            confirmButtonText: 'Sí, entregar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `accion=marcar_entregado&pedido_id=${pedidoId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire(
                            '¡Entregado!',
                            'El pedido ha sido marcado como entregado.',
                            'success'
                        );
                        // Recargar lista de pedidos
                        location.reload();
                    }
                });
            }
        });
    }

    function reproducirSonidoNuevoPedido() {
        const audio = new Audio('notificacion.mp3');
        audio.play().catch(error => console.log('Error reproduciendo sonido'));
    }

    // Actualizar pedidos cada 10 segundos
    setInterval(() => {
        const currentUrl = new URL(window.location.href);
        const timestamp = new Date().getTime();
        currentUrl.searchParams.set('_', timestamp);
        
        fetch(currentUrl.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const newDoc = parser.parseFromString(html, 'text/html');
            const newPedidosList = newDoc.getElementById('lista-pedidos');
            const currentPedidosList = document.getElementById('lista-pedidos');
            
            if (newPedidosList.innerHTML !== currentPedidosList.innerHTML) {
                reproducirSonidoNuevoPedido();
                
                Swal.fire({
                    icon: 'info',
                    title: 'Nuevo Pedido',
                    text: 'Se ha añadido un nuevo pedido a la lista',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
                
                currentPedidosList.innerHTML = newPedidosList.innerHTML;
            }
        });
    }, 10000);
    </script>
</body>
</html>
<?php $gestor = null; ?>