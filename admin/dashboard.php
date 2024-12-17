<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "12345";
$dbname = "dongarbanzo";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query para contar pedidos pendientes
$pendingOrdersQuery = "SELECT COUNT(*) as pending_count FROM pedidos WHERE estado_entrega = 'pendiente'";
$pendingOrdersResult = $conn->query($pendingOrdersQuery);

// Obtener el número de pedidos pendientes
$pendingOrdersCount = 0;
if ($pendingOrdersResult->num_rows > 0) {
    $row = $pendingOrdersResult->fetch_assoc();
    $pendingOrdersCount = $row['pending_count'];
}

// Fetch product lists
$drinksQuery = "SELECT nombre, precio FROM bebidas WHERE habilitado = 'habilitado'";
$drinksResult = $conn->query($drinksQuery);

$cakesQuery = "SELECT nombre, precio FROM pasteles WHERE habilitado = 'habilitado'";
$cakesResult = $conn->query($cakesQuery);

$pasabocasQuery = "SELECT nombre, precio FROM pasabocas";
$pasabocasResult = $conn->query($pasabocasQuery);

// Count products
$drinksCount = $drinksResult->num_rows;
$cakesCount = $cakesResult->num_rows;
$pasabocasCount = $pasabocasResult->num_rows;

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }

        .notification-badge {
    position: relative;
    top: -5px;
    right: -5px;
    background-color: #f44336; /* Rojo vibrante */
    color: white;
    border-radius: 50%;
    padding: 8px 12px;
    font-size: 14px;
    font-weight: bold;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Sombra suave */
    animation: bounce 0.5s ease infinite alternate; /* Animación de rebote */
}

@keyframes bounce {
    0% {
        transform: translateY(0);
    }
    100% {
        transform: translateY(-5px);
    }
}

a {
    position: relative; /* Necesario para posicionar la notificación relativa al enlace */
}

.notification-badge {
    top: -5px;
    right: -5px;
    background-color: #e91e63; /* Un tono moderno de rojo */
    font-size: 14px;
    font-weight: 600;
    padding: 5px 10px;
    border-radius: 50%;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    min-width: 20px;
    height: 20px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
}

    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-logo">
            Admin Panel
        </div>
        <div class="sidebar-menu">
            <a href="#dashboard">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
            <a href="#usuarios">
                <i class="bi bi-people"></i> Usuarios
            </a>
            <a href="pedidos.php">
    <i class="bi bi-box-seam"></i> Pedidos
    <?php if ($pendingOrdersCount > 0): ?>
        <span class="notification-badge"><?php echo $pendingOrdersCount; ?></span>
    <?php endif; ?>

        <a href="bebidas.php" style="color: white; text-decoration: none; display: flex; align-items: center; margin-bottom: 10px;">
        <img src="img/icono2-2.png" alt="Gaseosa" style="margin-right: 10px; width: 35px; height: 35px;"> Bebidas
    </a>
    <a href="pasteles.php" style="color: white; text-decoration: none; display: flex; align-items: center; margin-bottom: 10px;">
        <img src="img/iconodepastel1.png" alt="Empanada" style="margin-right: 10px; width: 35px; height: 35px;"> Pasteles
    </a>
    <a href="pasabocas.php" style="color: white; text-decoration: none; display: flex; align-items: center; margin-bottom: 10px;">
        <img src="img/icono3-3.png" alt="Pasabocas" style="margin-right: 10px; width: 35; height: 35px;"> Pasabocas
    </a>
</nav>

        </div>
    </div>
    
    <div class="main-content">
        <div class="header">
            <div class="header-title">Dashboard Principal</div>
            <div class="header-actions">
                <i class="bi bi-bell"></i>
                <i class="bi bi-person-circle"></i>
                <a class="bi bi-box-arrow-right"href="../index.php"></a>
            </div>
        </div>
        
        <div class="dashboard-content">
            <div class="dashboard-grid">
                <div class="dashboard-card" onclick="openModal('drinks')">
                <img src="img/icono2.png" alt="Pasabocas" class="dashboard-card-icon" style="width:70px; height:70px;">
                    <h3>Bebidas</h3>
                    <p><?php echo $drinksCount; ?></p>
                </div>
                <div class="dashboard-card" onclick="openModal('cakes')">
                <img src="img/iconodepastel.png" alt="Pasabocas" class="dashboard-card-icon" style="width:70px; height:70px;">
                    <h3>Pasteles</h3>
                    <p><?php echo $cakesCount; ?></p>
                </div>
                <div class="dashboard-card" onclick="openModal('pasabocas')">
                <img src="img/icono3.png" alt="Pasabocas" class="dashboard-card-icon" style="width:70px; height:70px;">
                    <h3>Pasabocas</h3>
                    <p><?php echo $pasabocasCount; ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Drinks -->
    <div id="drinksModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('drinks')">&times;</span>
            <h2>Bebidas Registradas</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Precio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($drinksResult->num_rows > 0) {
                        while($row = $drinksResult->fetch_assoc()) {
                            echo "<tr><td>".$row['nombre']."</td><td>$".$row['precio']."</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No hay bebidas registradas</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Cakes -->
    <div id="cakesModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('cakes')">&times;</span>
            <h2>Pasteles Registrados</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Precio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($cakesResult->num_rows > 0) {
                        while($row = $cakesResult->fetch_assoc()) {
                            echo "<tr><td>".$row['nombre']."</td><td>$".$row['precio']."</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No hay pasteles registrados</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Pasabocas -->
    <div id="pasabocasModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('pasabocas')">&times;</span>
            <h2>Pasabocas Registrados</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Precio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($pasabocasResult->num_rows > 0) {
                        while($row = $pasabocasResult->fetch_assoc()) {
                            echo "<tr><td>".$row['nombre']."</td><td>$".$row['precio']."</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No hay pasabocas registrados</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function openModal(type) {
            document.getElementById(type + 'Modal').style.display = "block";
        }

        function closeModal(type) {
            document.getElementById(type + 'Modal').style.display = "none";
        }

        // Close modal if clicked outside
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = "none";
            }
        }
    </script>
</body>
</html>