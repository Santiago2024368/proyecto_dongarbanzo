<?php
// config.php (mantén tu configuración actual)
$host = 'localhost';
$usuario = 'root';
$password = '12345';
$database = 'dongarbanzo';

// conexion.php
class Conexion {
    private $conexion;

    public function __construct() {
        global $host, $usuario, $password, $database;
        $this->conexion = new mysqli($host, $usuario, $password, $database);
        
        if ($this->conexion->connect_error) {
            die("Error de conexión: " . $this->conexion->connect_error);
        }
    }

    public function busquedaRapida($termino, $categoria = null) {
        $resultados = [];
        $categorias = $categoria ? [$categoria] : ['pasteles', 'pasabocas', 'bebidas'];

        foreach ($categorias as $cat) {
            if ($cat === 'pasabocas') {
                $consulta = $this->conexion->prepare("
                    SELECT p.id, p.nombre, p.imagen, p.precio, '$cat' as categoria
                    FROM pasabocas p
                    JOIN categoria c ON p.categoria_id = c.id
                    WHERE p.nombre LIKE ?
                    
                ");
            } else {
                $consulta = $this->conexion->prepare("
                    SELECT id, nombre, imagen, precio, '$cat' as categoria 
                    FROM $cat 
                    WHERE nombre LIKE ? AND habilitado = 'habilitado' 
                    
                ");
            }

            $terminoBusqueda = "%$termino%";
            $consulta->bind_param("s", $terminoBusqueda);
            $consulta->execute();
            $resultado = $consulta->get_result();
            
            while ($fila = $resultado->fetch_assoc()) {
                // Asegúrate de que la imagen tenga una URL completa
                $fila['imagen'] = $fila['imagen'] ? $fila['imagen'] : 'ruta/a/imagen/por/defecto.jpg';
                $resultados[] = $fila;
            }
        }

        return $resultados;
    }
}

// Manejar solicitud AJAX
if (isset($_GET['busqueda']) && isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    $conexion = new Conexion();
    $resultados = $conexion->busquedaRapida(
        $_GET['busqueda'], 
        $_GET['categoria'] ?? null
    );
    echo json_encode($resultados);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Don Garbanzo</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="Free Website Template" name="keywords">
        <meta content="Free Website Template" name="description">
        <!-- Favicon -->
        <link href="img/logo.png" rel="icon">

        <!-- Google Font -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400|Nunito:600,700" rel="stylesheet"> 
        
        <!-- CSS Libraries -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
        <link href="lib/animate/animate.min.css" rel="stylesheet">
        <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
        <link href="lib/flaticon/font/flaticon.css" rel="stylesheet">
        <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />
        

        <!-- Template Stylesheet -->
        <link href="css/style.css" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>
<style>
  .img-item{
    width: 240px;
    height: 200px;
    border-radius: 8%;
  } 

  .page-header {
    background-image: url('img/carousel-1.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    position: relative;
    padding: 50px 0;
}

/* Overlay oscuro */
.page-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); /* Color negro con 50% de opacidad */
}

/* Asegúrate de que el contenido esté por encima del overlay */
.page-header .container {
    position: relative;
    z-index: 1;
}

    .logo{
    width: 350px;
    height: 50;

    }
  

header {
    background: transparent !important;
    box-shadow: none;
}

.carrito-icono {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 15px;
            background-color: #ff9900;
            border-radius: 24px;
            box-shadow: 0 4px 6px rgb(73, 73, 73);
            transition: all 0.3s ease;
        }
        .carrito-icono:hover {
            background-color: #ff9900;
            transform: scale(1.05);
        }
</style>
    <body>
        <!-- Nav Bar Start -->
        <div class="navbar navbar-expand-lg bg-light navbar-light">
            

            <div class="container-fluid">
                <img src="img/logo don garbanzo-.png" alt="Logotipo" class="logo"href="index.html">
                <a href="index.html"></a>
                <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                    
                <div class="1collapse navbar-collapse justify-content-between" id="navbarCollapse">
                    <div class="navbar-nav ml-auto">
                        <a href="../index.php" class="nav-item nav-link active">Inicio</a>
                        <a href="menu.php" class="nav-item nav-link">Menu</a>
                        <a href="../acerca de.html" class="nav-item nav-link active">Acerca de</a>
                        <a href="../contact.html" class="nav-item nav-link active">Contact</a>
                    </div>
                </div>
                 
                <span class="carrito-icono text-gray-700 relative cursor-pointer">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart">
            <circle cx="8" cy="21" r="1"></circle>
            <circle cx="19" cy="21" r="1"></circle>
            <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
        </svg>
        Carrito
    </span>
              
             </div>
              
            </div>
        </div>
        <!-- Nav Bar End -->
        
        
        <!-- Page Header Start -->
        <div class="page-header mb-0">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h2>Menu</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barra de Búsqueda y Filtros -->
    <div class="bg-gray-100 rounded-b-3xl p-4 shadow-sm">
        <div class="flex items-center justify-between max-w-6xl mx-auto">
            <div class="relative flex-grow mr-4">
                <input 
                    id="busqueda" 
                    type="text" 
                    placeholder="Buscar productos..." 
                    class="w-full pl-10 pr-4 py-2 rounded-full border border-gray-300"
                >
                <span class="absolute left-3 top-1/2 transform -translate-y-1/2">🔍</span>
            </div>
            <div class="flex space-x-2">
                <?php 
                $categorias = ['pasteles', 'pasabocas', 'bebidas'];
                foreach($categorias as $categoria):
                ?>
                    <button 
                        data-categoria="<?php echo $categoria; ?>" 
                        class="categoria-filtro px-4 py-2 rounded-full bg-gray-200 text-gray-700 hover:bg-blue-500 hover:text-white">
                        <?php echo ucfirst($categoria); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Cuadrícula de Productos -->
    <div class="container mx-auto p-6">
        <div id="productos" class="grid grid-cols-4 gap-6">
            <!-- Productos se cargarán dinámicamente aquí -->
        </div>
    </div>

    <script src="js/scripts.js"></script>

        


        <!-- Footer End -->

        <a href="#" class="back-to-top"><i class="fa fa-chevron-up"></i></a>

        <!-- JavaScript Libraries -->
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
        <script src="lib/easing/easing.min.js"></script>
        <script src="lib/owlcarousel/owl.carousel.min.js"></script>
        <script src="lib/tempusdominus/js/moment.min.js"></script>
        <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
        <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>
        
         <!-- Contact Start -->
        <!-- Contact End -->


        

        <!-- Footer Start -->
        <div class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="footer-contact">
                                    <h2>Nuestra Direccion</h2>
                                    <p><i class="fa fa-map-marker-alt"></i>Dirección TV 4 #4E-83Barrio  La Ceiba</p>
                                    <p><i class="fa fa-phone-alt"></i>+57 321 9556968</p>
                                    <p><i class="fa fa-envelope"></i>dongarbanz01@hotmai.com</p>
                                    <div class="footer-social">
                                        <a href=""><i class="fab fa-twitter"></i></a>
                                        <a href="https://www.facebook.com/dongarbanzo.pasabocas/?locale=es_LA"><i class="fab fa-facebook-f"></i></a>
                                        <a href=""><i class="fab fa-youtube"></i></a>
                                        <a href=""><i class="fab fa-instagram"></i></a>
                                        <a href=""><i class="fab fa-linkedin-in"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="footer-link">
                                  
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="footer-newsletter">
                            <h2>Boletin</h2>
                            <p>
                                ¡Gracias por visitar a Dongarbanzo! Mantente al tanto de nuestras últimas novedades, recetas y consejos culinarios. Suscríbete a nuestro boletín y sé el primero en recibir contenido exclusivo, promociones y mucho más
                            </p>
                            <div class="form">
                                <input class="form-control" placeholder="Email">
                                <button class="btn custom-btn">Enviar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

        <script>document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('nav ul li a');
    
    // Remove any previous active states
    navLinks.forEach(link => {
        link.removeAttribute('data-active');
    });

    // Set the Menu link to active
    const menuLink = document.querySelector('nav ul li a[href="#"]');
    if (menuLink && menuLink.textContent.trim() === 'Menu') {
        menuLink.setAttribute('data-active', 'true');
    }
});</script>
                
        <!-- Footer End -->

        <a href="#" class="back-to-top"><i class="fa fa-chevron-up"></i></a>

        <!-- JavaScript Libraries -->
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
        <script src="lib/easing/easing.min.js"></script>
        <script src="lib/owlcarousel/owl.carousel.min.js"></script>
        <script src="lib/tempusdominus/js/moment.min.js"></script>
        <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
        <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>
        
        <!-- Contact Javascript File -->
        <script src="mail/jqBootstrapValidation.min.js"></script>
        <script src="mail/contact.js"></script>

        <!-- Template Javascript -->
        <script src="js/main.js"></script>
    </body>
</html>
