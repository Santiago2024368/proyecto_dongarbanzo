<?php
include('conexion.php');

// Add Category
if (isset($_POST['add_category'])) {
    $nombre = $_POST['nombre'];

    $sql = "INSERT INTO categoria (nombre) VALUES (:nombre)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);

    if ($stmt->execute()) {
        header("Location: ".$_SERVER['PHP_SELF']);  
        exit;
    } else {
        echo "Error al agregar categoría.";
    }
}

// Add Product
if (isset($_POST['add_product'])) {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $categoria_id = $_POST['categoria_id'];

    // Handle file upload for new product
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $upload_dir = 'img/pasabocas/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

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

    $sql = "INSERT INTO pasabocas (nombre, imagen, precio, categoria_id) VALUES (:nombre, :imagen, :precio, :categoria_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':imagen', $imagen);
    $stmt->bindParam(':precio', $precio);
    $stmt->bindParam(':categoria_id', $categoria_id);

    if ($stmt->execute()) {
        header("Location: ".$_SERVER['PHP_SELF']);  
        exit;
    } else {
        echo "Error al agregar producto.";
    }
}

// Delete Product
if (isset($_POST['delete_product'])) {
    $producto_id = $_POST['producto_id'];

    try {
        // First, get the image path to delete the file
        $stmt_img = $pdo->prepare("SELECT imagen FROM pasabocas WHERE id = :id");
        $stmt_img->bindParam(':id', $producto_id);
        $stmt_img->execute();
        $imagen_path = $stmt_img->fetchColumn();

        // Delete the product
        $sql = "DELETE FROM pasabocas WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $producto_id);

        if ($stmt->execute()) {
            // Delete the image file
            if (file_exists($imagen_path)) {
                unlink($imagen_path);
            }

            header("Location: ".$_SERVER['PHP_SELF']);  
            exit;
        } else {
            echo "Error al eliminar producto.";
        }
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Update Product
if (isset($_POST['update_product'])) {
    $producto_id = $_POST['producto_id'];
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $categoria_id = $_POST['categoria_id'];

    // Check if a new image is uploaded
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $upload_dir = 'img/pasabocas/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $filename = uniqid() . '_' . basename($_FILES['imagen']['name']);
        $upload_path = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_path)) {
            // Delete old image
            $stmt_old_img = $pdo->prepare("SELECT imagen FROM pasabocas WHERE id = :id");
            $stmt_old_img->bindParam(':id', $producto_id);
            $stmt_old_img->execute();
            $old_imagen_path = $stmt_old_img->fetchColumn();

            if (file_exists($old_imagen_path)) {
                unlink($old_imagen_path);
            }

            $sql = "UPDATE pasabocas SET nombre = :nombre, precio = :precio, categoria_id = :categoria_id, imagen = :imagen WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':imagen', $upload_path);
        } else {
            echo "Error al subir la imagen.";
            exit;
        }
    } else {
        // Update without changing the image
        $sql = "UPDATE pasabocas SET nombre = :nombre, precio = :precio, categoria_id = :categoria_id WHERE id = :id";
        $stmt = $pdo->prepare($sql);
    }

    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':precio', $precio);
    $stmt->bindParam(':categoria_id', $categoria_id);
    $stmt->bindParam(':id', $producto_id);

    if ($stmt->execute()) {
        header("Location: ".$_SERVER['PHP_SELF']);  
        exit;
    } else {
        echo "Error al actualizar producto.";
    }
}

// Delete Category
if (isset($_POST['delete_category'])) {
    $categoria_id = $_POST['categoria_id'];

    try {
        // First, check if category has any products
        $check_products = $pdo->prepare("SELECT COUNT(*) FROM pasabocas WHERE categoria_id = :categoria_id");
        $check_products->bindParam(':categoria_id', $categoria_id);
        $check_products->execute();

        if ($check_products->fetchColumn() > 0) {
            // If category has products, prevent deletion
            echo "No se puede eliminar la categoría porque tiene productos asociados.";
        } else {
            // Delete the category
            $sql = "DELETE FROM categoria WHERE id = :categoria_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':categoria_id', $categoria_id);

            if ($stmt->execute()) {
                header("Location: ".$_SERVER['PHP_SELF']);  
                exit;
            } else {
                echo "Error al eliminar categoría.";
            }
        }
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}


// Fetch Categories
$sql_categorias = "SELECT * FROM categoria";
$stmt_categorias = $pdo->query($sql_categorias);

// Fetch Products
$sql_productos = "SELECT p.*, c.nombre as categoria_nombre FROM pasabocas p JOIN categoria c ON p.categoria_id = c.id";
$stmt_productos = $pdo->query($sql_productos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pasabocas</title>
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
    </style>
</head>
<body>
    <div class="sidebar">
        <h4 class="mb-4 text-center">Pasabocas</h4>
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
            <h1 class="mb-4">Gestión de Pasabocas</h1>

            <!-- Categorías -->
<div class="categoria-form mb-4">
    <h2 class="mb-3">Agregar Categoría</h2>
    <form method="POST" action="">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="nombre_categoria" class="form-label">Nombre de Categoría</label>
                <input type="text" class="form-control" name="nombre" id="nombre_categoria" required>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" name="add_category" class="btn btn-custom-yellow">Agregar Categoría</button>
            <button type="submit" name="delete_category" class="btn btn-danger" onclick="return confirm('¿Está seguro de eliminar esta categoría?')">Eliminar Categoría</button>
        </div>
    </form>
</div>

            <!-- Productos -->
            <div class="add-product-form mb-4">
                <h2 class="mb-3">Agregar Nuevo Pasaboca</h2>
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="nombre" class="form-label">Nombre del Pasaboca</label>
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
                        <div class="col-md-4 mb-3">
                            <label for="categoria_id" class="form-label">Categoría</label>
                            <select class="form-select" name="categoria_id" id="categoria_id" required>
                                <option value="">Seleccione una categoría</option>
                                <?php while($categoria = $stmt_categorias->fetch(PDO::FETCH_ASSOC)): ?>
                                    <option value="<?= $categoria['id'] ?>"><?= $categoria['nombre'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <button type="submit" name="add_product" class="btn btn-custom-yellow">Agregar Pasaboca</button>
                </form>
            </div>

            <!-- Listado de Productos -->
<div class="product-list">
    <h2 class="mb-3">Listado de Pasabocas</h2>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-light">
                <tr>
                    <th>Imagen</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Categoría</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($stmt_productos->rowCount() > 0): ?>
                    <?php while ($row = $stmt_productos->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td><img src="<?= $row['imagen'] ?>" alt="<?= $row['nombre'] ?>" class="product-image"></td>
                            <td><?= $row['nombre'] ?></td>
                            <td>$<?= number_format($row['precio'], 2) ?></td>
                            <td><?= $row['categoria_nombre'] ?></td>
                            <td>
                                <!-- Edit Modal Trigger -->
                                <button type="button" class="btn btn-warning btn-sm me-2" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">Editar</button>
                                
                                <!-- Delete Product Form -->
                                <form method="POST" action="" style="display:inline;" onsubmit="return confirm('¿Está seguro de eliminar este producto?');">
                                    <input type="hidden" name="producto_id" value="<?= $row['id'] ?>">
                                    <button type="submit" name="delete_product" class="btn btn-danger btn-sm">Eliminar</button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Editar Pasaboca</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <input type="hidden" name="producto_id" value="<?= $row['id'] ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Nombre</label>
                                                <input type="text" class="form-control" name="nombre" value="<?= $row['nombre'] ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Precio</label>
                                                <input type="number" class="form-control" name="precio" value="<?= $row['precio'] ?>" step="0.01" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Categoría</label>
                                                <select class="form-select" name="categoria_id" required>
                                                    <?php 
                                                    // Reset the pointer for categories
                                                    $stmt_categorias->execute();
                                                    while($categoria = $stmt_categorias->fetch(PDO::FETCH_ASSOC)): 
                                                    ?>
                                                        <option value="<?= $categoria['id'] ?>" <?= $categoria['id'] == $row['categoria_id'] ? 'selected' : '' ?>>
                                                            <?= $categoria['nombre'] ?>
                                                        </option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Nueva Imagen</label>
                                                <input type="file" class="form-control" name="imagen" accept="image/*" onchange="previewImage(event, 'preview_edit<?= $row['id'] ?>')">
                                                <img id="preview_edit<?= $row['id'] ?>" class="preview-image mt-2" src="<?= $row['imagen'] ?>" style="max-width: 200px;">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" name="update_product" class="btn btn-primary">Guardar Cambios</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No hay pasabocas registrados</td>
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