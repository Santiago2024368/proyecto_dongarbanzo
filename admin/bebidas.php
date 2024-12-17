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

    $sql = "INSERT INTO bebidas (nombre, imagen, precio, habilitado) VALUES (:nombre, :imagen, :precio, 'habilitado')";
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
            $sql = "UPDATE bebidas SET nombre = :nombre, imagen = :imagen, precio = :precio WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':imagen', $imagen);
        } else {
            echo "Error al subir la imagen.";
            exit;
        }
    } else {
        $sql = "UPDATE bebidas SET nombre = :nombre, precio = :precio WHERE id = :id";
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
    $sql_get_image = "SELECT imagen FROM bebidas WHERE id = :id";
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
        $sql = "DELETE FROM bebidas WHERE id = :id";
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
    
    $sql = "UPDATE bebidas SET habilitado = :new_state WHERE id = :id";
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
$sql = "SELECT * FROM bebidas";
$stmt = $pdo->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; padding: 20px; }
        .container { background-color: white; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 30px; }
        .product-image { width: 100px; height: 100px; object-fit: cover; border-radius: 8px; }
        .preview-image { max-width: 200px; max-height: 200px; object-fit: cover; border-radius: 8px; margin-top: 10px; }
        .toggle-switch { position: relative; display: inline-block; width: 50px; height: 24px; }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .toggle-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px; }
        .toggle-slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
        .toggle-switch input:checked + .toggle-slider { background-color: #28a745; }
        .toggle-switch input:checked + .toggle-slider:before { transform: translateX(26px); }
    </style>
</head>
<body>
<div class="container">
    <h1 class="mb-4 text-center">Gestión de Productos</h1>
    <form method="POST" action="dashboard.php" class="text-end mb-4">
        <button type="submit" class="btn btn-danger">Salir</button>
    </form>
    <div class="add-product-form">
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
            <button type="submit" name="add_product" class="btn btn-primary">Agregar Producto</button>
        </form>
    </div>
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
                        <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel">Editar Producto</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="" enctype="multipart/form-data">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <div class="mb-3">
                                                <label for="nombre" class="form-label">Nombre del Producto</label>
                                                <input type="text" class="form-control" name="nombre" value="<?= $row['nombre'] ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="imagen" class="form-label">Imagen</label>
                                                <input type="file" class="form-control" name="imagen" id="imagen_edit_<?= $row['id'] ?>" accept="image/*" onchange="previewImage(event, 'preview_edit_<?= $row['id'] ?>')">
                                                <img id="preview_edit_<?= $row['id'] ?>" class="preview-image" style="display:none;">
                                            </div>
                                            <div class="mb-3">
                                                <label for="precio" class="form-label">Precio</label>
                                                <input type="number" class="form-control" name="precio" value="<?= $row['precio'] ?>" step="0.01" required>
                                            </div>
                                            <button type="submit" name="edit_product" class="btn btn-primary">Guardar Cambios</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
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