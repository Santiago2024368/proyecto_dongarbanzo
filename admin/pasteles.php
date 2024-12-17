<?php
include('conexion.php');

// Add Product
if (isset($_POST['add_product'])) {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];

    // Handle file upload for new product
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $upload_dir = 'img/';
        $filename = uniqid() . '_' . basename($_FILES['imagen']['name']);
        $upload_path = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_path)) {
            $imagen = $upload_path;
        } else {
            echo "Error al subir la imagen.";
            exit;
        }
    } else {
        echo "Por favor, seleccione una imagen.";
        exit;
    }

    $sql = "INSERT INTO pasteles (nombre, imagen, precio, habilitado) VALUES (:nombre, :imagen, :precio, 'habilitado')";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':imagen', $imagen);
    $stmt->bindParam(':precio', $precio);

    if ($stmt->execute()) {
        header("Location: ".$_SERVER['PHP_SELF']);  
        exit;
    } else {
        echo "Error al agregar producto.";
    }
}

// Edit Product
if (isset($_POST['edit_product'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];

    // Handle file upload for edited product
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $upload_dir = 'img/';
        $filename = uniqid() . '_' . basename($_FILES['imagen']['name']);
        $upload_path = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_path)) {
            $imagen = $upload_path;
            $sql = "UPDATE pasteles SET nombre = :nombre, imagen = :imagen, precio = :precio WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':imagen', $imagen);
        } else {
            echo "Error al subir la imagen.";
            exit;
        }
    } else {
        $sql = "UPDATE pasteles SET nombre = :nombre, precio = :precio WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
    }

    $stmt->bindParam(':precio', $precio);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        header("Location: ".$_SERVER['PHP_SELF']);  
        exit;
    } else {
        echo "Error al modificar producto.";
    }
}

// Delete Product
if (isset($_POST['delete_product'])) {
    $id = $_POST['id'];

    // First, get the image path to delete the file
    $sql_get_image = "SELECT imagen FROM pasteles WHERE id = :id";
    $stmt_get_image = $pdo->prepare($sql_get_image);
    $stmt_get_image->bindParam(':id', $id);
    $stmt_get_image->execute();
    $image_row = $stmt_get_image->fetch(PDO::FETCH_ASSOC);

    if ($image_row) {
        // Delete the image file from the server
        if (file_exists($image_row['imagen'])) {
            unlink($image_row['imagen']);
        }

        // Delete the product from the database
        $sql = "DELETE FROM pasteles WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            header("Location: ".$_SERVER['PHP_SELF']);  
            exit;
        } else {
            echo "Error al eliminar producto.";
        }
    } else {
        echo "Producto no encontrado.";
    }
}

// Toggle Product State
if (isset($_POST['toggle_state'])) {
    $id = $_POST['id'];
    
    // First, get the current state
    $sql_check_state = "SELECT habilitado FROM pasteles WHERE id = :id";
    $stmt_check_state = $pdo->prepare($sql_check_state);
    $stmt_check_state->bindParam(':id', $id);
    $stmt_check_state->execute();
    $current_state = $stmt_check_state->fetchColumn();

    // Toggle the state
    $new_state = ($current_state == 'habilitado') ? 'deshabilitado' : 'habilitado';
    
    $sql = "UPDATE pasteles SET habilitado = :new_state WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':new_state', $new_state);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        header("Location: ".$_SERVER['PHP_SELF']);  
        exit;
    } else {
        echo "Error al cambiar el estado del producto.";
    }
}

// Fetch Products
$sql = "SELECT * FROM pasteles";
$stmt = $pdo->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Gestión de Productos</title>
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
        .product-image { 
            width: 100px; 
            height: 100px; 
            object-fit: cover; 
            border-radius: 8px; 
        }
        .preview-image { 
            max-width: 200px; 
            max-height: 200px; 
            object-fit: cover; 
            border-radius: 8px; 
            margin-top: 10px; 
        }
        .btn-custom-yellow {
            background-color: var(--primary-yellow);
            color: #000;
        }
        .btn-custom-yellow:hover {
            background-color: #FFC300;
        }
        .toggle-switch { 
            position: relative; 
            display: inline-block; 
            width: 50px; 
            height: 24px; 
        }
        .toggle-switch input { 
            opacity: 0; 
            width: 0; 
            height: 0; 
        }
        .toggle-slider { 
            position: absolute; 
            cursor: pointer; 
            top: 0; 
            left: 0; 
            right: 0; 
            bottom: 0; 
            background-color: #ccc; 
            transition: .4s; 
            border-radius: 34px; 
        }
        .toggle-slider:before { 
            position: absolute; 
            content: ""; 
            height: 18px; 
            width: 18px; 
            left: 3px; 
            bottom: 3px; 
            background-color: white; 
            transition: .4s; 
            border-radius: 50%; 
        }
        .toggle-switch input:checked + .toggle-slider { 
            background-color: #28a745; 
        }
        .toggle-switch input:checked + .toggle-slider:before { 
            transform: translateX(26px); 
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h4 class="mb-4 text-center">Pastelería</h4>
        <nav class="nav flex-column">
            <a href="#" class="nav-link active">
                <i class="me-2">📦</i> Productos
            </a>
            <a href="#" class="nav-link">
                <i class="me-2">📊</i> Estadísticas
            </a>
            <a href="#" class="nav-link">
                <i class="me-2">🛒</i> Pedidos
            </a>
            <a href="dashboard.php" class="nav-link text-danger">
                <i class="me-2">🚪</i> Salir
            </a>
        </nav>
    </div>
    <div class="main-content">
        <div class="container-fluid">
            <h1 class="mb-4">Gestión de Productos</h1>
            <div class="add-product-form mb-4">
                <h2 class="mb-3">Agregar Nuevo Producto</h2>
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="nombre" class="form-label">Nombre del Producto</label>
                            <input type="text" class="form-control" name="nombre" id="nombre" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="imagen" class="form-label">Imagen</label>
                            <input type="file" class="form-control" name="imagen" id="imagen_add" accept="image/*" required onchange="previewImage(event, 'preview_add')">
                            <img id="preview_add" class="preview-image" style="display:none;">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="precio" class="form-label">Precio</label>
                            <input type="number" class="form-control" name="precio" id="precio" step="0.01" required>
                        </div>
                    </div>
                    <button type="submit" name="add_product" class="btn btn-custom-yellow">Agregar Producto</button>
                </form>
            </div>
            <div class="product-list">
                <h2 class="mb-3">Listado de Productos</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Imagen</th>
                                <th>Nombre</th>
                                <th>Precio</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($stmt->rowCount() > 0): ?>
                                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                    <tr>
                                        <td><img src="<?= $row['imagen'] ?>" alt="<?= $row['nombre'] ?>" class="product-image"></td>
                                        <td><?= $row['nombre'] ?></td>
                                        <td>$<?= number_format($row['precio'], 2) ?></td>
                                        <td>
                                            <form action="" method="POST" class="m-0">
                                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                <label class="toggle-switch">
                                                    <input type="checkbox" name="toggle_state" <?= $row['habilitado'] == 'habilitado' ? 'checked' : '' ?> onchange="this.form.submit()">
                                                    <span class="toggle-slider"></span>
                                                </label>
                                            </form>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-warning btn-sm me-2" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">Editar</button>
                                            <form action="" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro que desea eliminar este producto?');">
                                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                <button type="submit" name="delete_product" class="btn btn-danger btn-sm">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <!-- Modal de edición (se mantiene igual que en el código original) -->
                                    <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                        <!-- [Contenido del modal igual al código original] -->
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No hay productos registrados</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewImage(event, previewId) {
            const input = event.target;
            const preview = document.getElementById(previewId);
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.style.display = 'none';
            }
        }
    </script>
</body>
</html>