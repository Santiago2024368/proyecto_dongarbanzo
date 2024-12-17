<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Dongarbanzo</title>
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/main_estilos.css" rel="stylesheet">
    <link rel="stylesheet" href="css/estilos_responsive.css">
    <link rel="stylesheet" href="css/estilos_footer.css">
    <link rel="stylesheet" href="css/perfil_login.css">
</head>
<body>
<div class="navbar navbar-expand-lg bg-light navbar-light">
    <div class="container-fluid">
        <img src="img/logo don garbanzo-.png" alt="Logotipo" class="logo" href="index.php">

        <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
            <div class="navbar-nav ml-auto">
                <a href="index.php" class="nav-item nav-link active">Inicio</a>
                <a href="admin/menu.php" class="nav-item nav-link">Menú</a>
                <a href="acerca de.html" class="nav-item nav-link">Acerca de</a>
                <a href="contact.html" class="nav-item nav-link">Contacto</a>

                <!-- Verificar si el usuario está logueado -->
                <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
                    <div class="profile-container">
                        <?php 
                        // Verificar si hay una imagen de perfil
                        $user_picture = $_SESSION['user_picture'] ?? '';
                        $user_name = $_SESSION['user_name'] ?? '';
                        
                        if (!empty($user_picture)): 
                        ?>
                            <img src="<?php echo htmlspecialchars($user_picture); ?>" 
                                 alt="Foto de perfil" 
                                 class="profile-image" 
                                 onerror="this.onerror=null; this.outerHTML = generateInitialAvatar('<?php echo htmlspecialchars($user_name); ?>');">
                        <?php else: ?>
                            <div class="initial-avatar">
                                <?php 
                                // Obtener la primera inicial del nombre
                                $initial = !empty($user_name) ? substr($user_name, 0, 1) : '?';
                                echo htmlspecialchars($initial); 
                                ?>
                            </div>
                        <?php endif; ?>

                        <div class="profile-dropdown">
                            <div class="text-center mb-2">
                                <span class="text-gray-700 font-medium">
                                    <?php echo htmlspecialchars($user_name); ?>
                                </span>
                            </div>
                            
                            <hr class="my-2">
                            
                            <a href="perfil.php" class="profile-dropdown-item">
                                <i class="fas fa-user"></i>
                                Mi Perfil
                            </a>
                            
                            <a href="logout.php" class="profile-dropdown-item">
                                <i class="fas fa-sign-out-alt"></i>
                                Cerrar Sesión
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Botón de inicio de sesión conservado -->
                   <!-- Botón de inicio de sesión -->
                   <div class="login-button-container">
                        <button class="login-button">
                            <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
                        </button>
                        <div class="login-dropdown">
                            <a href="login/login.php?role=user" class="login-dropdown-item">
                                <i class="fas fa-user"></i> Usuario
                            </a>
                            <a href="admin/login.php?role=admin" class="login-dropdown-item">
                                <i class="fas fa-user-shield"></i> Admin
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Scripts necesarios -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>

<script>
    function generateInitialAvatar(name) {
        // Obtener la primera inicial o '?' si no hay nombre
        const initial = name ? name.charAt(0).toUpperCase() : '?';
        
        // Generar un color de fondo aleatorio
        const colors = ['#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8'];
        const backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        
        return `
            <div class="initial-avatar" style="
                width: 50px;
                height: 50px;
                border-radius: 50%;
                border: 2px solid #ddd;
                background-color: ${backgroundColor};
                color: white;
                display: flex;
                justify-content: center;
                align-items: center;
                font-size: 20px;
                font-weight: bold;
                text-transform: uppercase;
                cursor: pointer;
            ">
                ${initial}
            </div>
        `;
    }
</script>



    <!-- Nav Bar End -->

    <!-- Carousel Start -->
    <div class="carousel">
            <div class="container-fluid">
                <div class="owl-carousel">
                    <div class="carousel-item">
                        <div class="carousel-img">
                            <img src="img/garbanzo1.jpg" alt="Image">
                        </div>
                        <div class="carousel-text">
                            <h1>¡Garbanzos sanos y sabrosos <span> para momentos</span> deliciosos!</h1>
                            <p>
                                Nutrición y sabor en cada bocado descubre el poder de nuestros garbanzos disfruta de garbanzos saludables y sabrosos, perfectos para cada momento.
                            </p>
                            <div class="carousel-btn">
                                
            
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="carousel-img">
                            <img src="img/portada.png" alt="Image">
                        </div>
                        <div class="carousel-text">
                            <h1>Los Mejores <span>Ingredientes</span> para Nuestros Pasteles</h1>
                            <p>
                                En Dongarbanzo, seleccionamos cada ingrediente con cuidado para asegurar la máxima frescura y calidad. Disfruta de sabores únicos en cada bocado, hechos con pasión y dedicación.
                            </p>
                            
                            <div class="carousel-btn">

                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="carousel-img">
                            <img src="img/carrusel_3.jpeg" alt="Image">
                        </div>
                        <div class="carousel-text">
                            <h1>Descubre el <span> Sabor de Nuestros Garbanzos</span></h1>
                            <p>
                                ¡Lleva lo mejor de la naturaleza a tu mesa! Nuestros garbanzos son seleccionados con cuidado para ofrecerte un producto de máxima calidad, perfecto para tus recetas favoritas.
                            </p>
                            <div class="carousel-btn">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="about">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="about-img">
                           
                            <!-- Reemplaza el botón de YouTube con un reproductor de video HTML5 -->
                            <img src="img/Nosotros.jpeg" alt="nosotros">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="about-content">
                            <div class="section-header">
                                <p>Sobre Nosotros</p>
                                <h2>Cocinando desde 2011</h2>
                            </div>
                            <div class="about-text">
                                <p>
                                    Dongarbazo nació con la misión de ofrecer comida de calidad excepcional, inspirada en las tradiciones culinarias y preparada con ingredientes frescos y cuidadosamente seleccionados.
                                </p>
                                <p>
                                    Desde 2011, hemos trabajado para brindar una experiencia única, fusionando un ambiente acogedor con un servicio rápido y amable. Nos enorgullecemos de ser un lugar donde cada plato cuenta una historia y cada cliente es parte de nuestra familia.
                                </p>
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- About End -->

        
        
        <!-- Feature Start -->
        <div class="feature">
            <div class="container">
                <div class="row">
                    <div class="col-lg-5">
                        <div class="section-header">
                            <p>¿Porque Elegirnos?</p>
                            <h2>Nuestras características principales</h2>
                        </div>
                        <div class="feature-text">
                            <div class="feature-img">
                                <div class="row">
                                    <div class="col-6">
                                        <img src="img/imagen1-1.png" alt="imagen1-1">
                                    </div>
                                    <div class="col-6">
                                        <img src="img/imagen-5.png" alt="imagen-5">
                                    </div>
                                    <div class="col-6">
                                        <img src="img/imagen-3.png" alt="imagen-3">
                                    </div>
                                    <div class="col-6">
                                        <img src="img/imagen-2.png" alt="imagen-2">
                                    </div>
                                </div>
                            </div>
                            <p>
                                En Dongarbazo, nos destacamos por ofrecer comida de calidad excepcional, preparada con ingredientes frescos y cuidadosamente seleccionados. Nuestra atención rápida y amable garantiza una experiencia única en cada visita. 
                            </p>
                            
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="feature-item">
                                    <i class="flaticon-cooking"></i>
                                    <h3>Nuestros Chefs</h3>
                                    <p>
                                        Utilizan recetas innovadoras y técnicas de cocina únicas para ofrecerte una experiencia culinaria inigualable.
                                    </p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="feature-item">
                                    <i class="flaticon-vegetable"></i>
                                    <h3>Ingredientes frescos y naturales</h3>
                                    <p>
                                        Seleccionamos solo los mejores ingredientes frescos y naturales para preparar cada uno de nuestros pastelitos.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="feature-item">
                                    <i class="flaticon-medal"></i>
                                    <h3>Productos de la mejor calidad</h3>
                                    <p>
                                        Nos comprometemos a ofrecer productos de la más alta calidad en cada bocado que pruebes en nuestro menú.
                                    </p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="feature-item">
                                    <i class="flaticon-meat"></i>
                                    <h3>Vegetales frescos y carne premium</h3>
                                    <p>
                                        Disfruta de nuestras hamburguesas hechas con carne premium y acompañadas de vegetales frescos y crujientes.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="feature-item">
                                    <i class="flaticon-courier"></i>
                                    <h3>Entrega a domicilio rápida</h3>
                                    <p>
                                        Recibe tus pedidos favoritos en la puerta de tu casa con nuestro servicio de entrega rápida y eficiente.
                                    </p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="feature-item">
                                    <i class="flaticon-fruits-and-vegetables"></i>
                                    <h3>Carne molida y baja en grasa</h3>
                                    <p>
                                        Ofrecemos opciones de carne molida de alta calidad y baja en grasa, perfectas para disfrutar sin preocupaciones.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
        <!-- Feature End -->

        <!-- Menu Start -->
        <div class="menu">
            <div class="container">
                <div class="section-header text-center">

                    <h2>Delicioso Menu</h2>
                </div>
                <div class="menu-tab">
                    <ul class="nav nav-pills justify-content-center">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="pill" href="#burgers">Pasteles</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#snacks">Refriguerio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#beverages">Bebidas</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div id="burgers" class="container tab-pane active">
                            <div class="row">
                                <div class="col-lg-7 col-md-12">
                                    <div class="menu-item">
                                        <div class="menu-img">
                                            <img src="img/garbanzo.png" alt="Garbanzo">
                                        </div>
                                        <div class="menu-text">
                                            <h3><span>Garbanzo</span> <strong>$2.400</strong></h3>
                                            <p>Este pastel destaca por su textura suave y su perfil nutritivo, siendo una excelente fuente de proteínas, fibra, y vitaminas esenciales.</p>
                                            </div>
                                    </div>
                                    <div class="menu-item">
                                        <div class="menu-img">
                                            <img src="img/yuca.png" alt="Yuca">
                                        </div>
                                        <div class="menu-text">
                                            <h3><span>Yuca</span> <strong>$3.000</strong></h3>
                                            <p>Un delicioso y suave pastel elaborado con yuca, Su textura tierna y su sabor único lo convierten en una verdadera joya de la cocina tradicional. </p>
                                            
                                        </div>
                                    </div>
                                    <div class="menu-item">
                                        <div class="menu-img">
                                            <img src="img/pollo.png" alt="Pollo">
                                        </div>
                                        <div class="menu-text">
                                            <h3><span>Pollo</span> <strong>$3.000</strong></h3>
                                            <p>Un exquisito pastel relleno de pollo desmenuzado, con una mezcla cremosa y especias que realzan su sabor. </p>
                                            
                                        </div>
                                    </div>
                                    <div class="menu-item">
                                        <div class="menu-img">
                                            <img src="img/texano.png" alt="Texano">
                                        </div>
                                        <div class="menu-text">
                                            <h3><span>Texano</span> <strong>$3.500</strong></h3>
                                            <p>Un delicioso pastel al estilo texano que une lo mejor de dos mundos. Una explosión de sabor perfecta para disfrutar y compartir.                                            </p>
                                        
                                        </div>
                                    </div>
                                    <div class="menu-item">
                                        <div class="menu-img">
                                            <img src="img/maicito.png" alt="maicito">
                                        </div>
                                        <div class="menu-text">
                                            <h3><span>Maicito</span> <strong>$3.400</strong></h3>
                                            <p>Maiz  tierno desgranado,queso mozarella y crema de leche. Disfruta de la combinación perfecta de maíz dulce y queso fundido en cada mordida.</p>
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-5 d-none d-lg-block">
                                    <img src="img/bandeja2.jpg" alt="bandeja2">
                                </div>
                            </div>
                        </div>
                        <div id="snacks" class="container tab-pane fade">
                            <div class="row">
                                <div class="col-lg-7 col-md-12">
                                    <div class="menu-item">
                                        <div class="menu-img">
                                            <img src="img/bolitas de carne.png" alt="mini sandwich pollo">
                                        </div>
                                        <div class="menu-text">
                                            <h3><span>40 Bolitas de carne</span> <strong>$35.000</strong></h3>
                                            <p>Descubre el sabor casero que siempre buscaste. Nuestras deliciosas bolitas de carne, bañadas en una salsa especial. </p>
                                        
                                        </div>
                                    </div>
                                    <div class="menu-item">
                                        <div class="menu-img">
                                            <img src="img/mini perro caliente.png" alt="mini perro caliente">
                                        </div>
                                        <div class="menu-text">
                                            <h3><span>Perros caliente</span> <strong>$4.500</strong></h3>
                                            <p>Para esos antojos irresistibles, prueba nuestros mini perros calientes Con un pan suave, una salchicha jugosa, y coronados con papas crujientes.</p>
                                            
                                        </div>
                                    </div>
                                    <div class="menu-item">
                                        <div class="menu-img">
                                            <img src="img/croissants especial.png" alt="croissants especial">
                                        </div>
                                        <div class="menu-text">
                                            <h3><span>Croissants especial</span> <strong>$4.000</strong></h3>
                                            <p>Disfruta de un sabor inigualable con nuestro exclusivo sándwich especial. Preparado con pan recién horneado, jugoso pollo a la parrilla.</p>
                                            
                                        </div>
                                    </div>
                                    <div class="menu-item">
                                        <div class="menu-img">
                                            <img src="img/chorizo bbq.png" alt="chorizo">
                                        </div>
                                        <div class="menu-text">
                                            <h3><span>Chorizo BQ</span> <strong>$9.500</strong></h3>
                                            <p>Descubre el irresistible sabor de nuestras salchichas bañadas en salsa BBQ Perfectamente cocinadas y cubiertas con una capa brillante de nuestra exclusiva salsa.</p>
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-5 d-none d-lg-block">
                                    <img src="img/bandeja11.jpg" alt="bandeja11">
                                </div>
                            </div>
                        </div>
                        <div id="beverages" class="container tab-pane fade">
                            <div class="row">
                                <div class="col-lg-7 col-md-12">
                                    <div class="menu-item">
                                        <div class="menu-img">
                                            <img src="img/bebida1.jpeg" alt="bebida1">
                                        </div>
                                        <div class="menu-text">
                                            <h3><span>Escoge tu Bebida Familiar Postobon 1.5</span> <strong>$4.900</strong></h3>
                                            <p>Manzana 1.5 Litros Roja Postobon 1.5 Litros Pepsi 1.5 litros Colombiana 1.5 litros uva 1.5 litros..</p>
                                        </div>
                                    </div>
                                    <div class="menu-item">
                                        <div class="menu-img">
                                            <img src="img/bebida2.jpeg" alt="bebida2">
                                        </div>
                                        <div class="menu-text">
                                            <h3><span>Escoge tu Bebida Mr. Tea Familiar</span> <strong>$4.900</strong></h3>
                                            <p>Durazno Y Limon...</p>
                                        </div>
                                    </div>
                                    <div class="menu-item">
                                        <div class="menu-img">
                                            <img src="img/bebida3.jpeg" alt="bebida3">
                                        </div>
                                        <div class="menu-text">
                                            <h3><span>Escoge tu JUGOS HIT PET 500</span> <strong>$ 2.700</strong></h3>
                                            <p>Mora
                                                Naranja Piña Frutas Tropicales Lulo Mango..</p>
                                        </div>
                                    </div>
                                    <div class="menu-item">
                                        <div class="menu-img">
                                            <img src="img/bebida4.jpeg" alt="bebida4">
                                        </div>
                                        <div class="menu-text">
                                            <h3><span>Escoge tu Bebida Pet 400 Postobon</span> <strong>  $ 2.700</strong></h3>
                                            <p>Manzana pet 400 7up pet 400 hipinto pet 400 Colombiana pet 400 pepsi pet 400..</p>
                                        </div>
                                    </div>
                                    <div class="menu-item">
                                        <div class="menu-img">
                                            <img src="img/bebida5.jpeg" alt="bebida5">
                                        </div>
                                        <div class="menu-text">
                                            <h3><span>Natu Malta 1Litro</span> <strong>$5.500</strong></h3>
                                            <p>Disfruta de Natu Malta, la bebida refrescante y nutritiva que te llena de energía con el auténtico sabor de la malta.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-5 d-none d-lg-block">
                                    <img src="img/bebida6.png" alt="bebida6">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Menu End -->

        <!-- Contact Start -->
        <div class="contact">
            <div class="container">
                <div class="section-header text-center">
                    <p>Contacta con nosotros</p>
                    <h2> Cualquier pregunta</h2>
                </div>
                <div class="row align-items-center contact-information">
                    <div class="col-md-6 col-lg-3">
                        <div class="contact-info">
                            <div class="contact-icon">
                                <i class="fa fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-text">
                                <h3>Direccion</h3>
                                <p>Dirección TV 4 #4E-83Barrio  La Ceiba</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="contact-info">
                            <div class="contact-icon">
                                <i class="fa fa-phone-alt"></i>
                            </div>
                            <div class="contact-text">
                                <h3>Telefono</h3>
                                <p>+57 321 9556968</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="contact-info">
                            <div class="contact-icon">
                                <i class="fa fa-envelope"></i>
                            </div>
                            <div class="contact-text">
                                <h3>Hotmail</h3>
                                <p>dongarbanz01</p>
                                <p>@hotmai.com</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="contact-info">
                            <div class="contact-icon">
                                <i class="fa fa-share"></i>
                            </div>
                            <div class="contact-text">
                                <h3>Siguenos</h3>
                                <div class="contact-social">
                                    <a href="https://www.facebook.com/@dongarbanzo.pasabocas/"><i class="fab fa-facebook-f"></i></a>
                                    <a href="https://www.youtube.com/@dongarbanzo3371"><i class="fab fa-youtube"></i></a>
                                    <a href="https://www.instagram.com/dongarbanzo/?igshid=YmMyMTA2M2Y%3D"><i class="fab fa-instagram"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row contact-form">
                    <div class="col-md-6">
                        <iframe src="https://www.google.com/maps/embed?pb=!3m2!1sen!2sco!4v1730497001279!5m2!1sen!2sco!6m8!1m7!1sD6FTZp-HuWg3sDemkbHXsA!2m2!1d7.894836214773207!2d-72.49579537086538!3f126.76794784260346!4f3.1751422498123674!5f0.7820865974627469" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>   
                    </div>
                   
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Contact End -->

<footer style="background-color: #f3f4f6;">
    <div class="container">
        <div class="grid">
            <!-- Sección de Contacto -->
            <div class="card">
                <h2 class="section-title">Nuestra Dirección</h2>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <span style="color: #fbaf32;">📍</span>
                        <span>Dirección TV 4 #4E-83 Barrio La Ceiba</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <span style="color: #fbaf32;">📞</span>
                        <span>+57 321 9556968</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <span style="color: #fbaf32;">✉️</span>
                        <span>dongarbanz01@hotmail.com</span>
                    </div>
                </div>

                <div class="social-icons">
                    <a href="#" class="social-icon">Facebook</a>
                    <a href="#" class="social-icon">YouTube</a>
                    <a href="#" class="social-icon">Instagram</a>
                </div>
            </div>

                <!-- Sección de Boletín -->
                <div class="card">
                    <h2 class="section-title">Boletín</h2>
                    <p style="text-align: center; color: #4a5568; margin-bottom: 1.5rem;">
                        ¡Gracias por visitar Dongarbanzo! Mantente al tanto de nuestras últimas novedades, recetas y consejos culinarios.
                    </p>

                    <form 
                    id="newsletterForm"
                    action="https://formsubmit.co/dongarbanzo93@gmail.com" 
                    method="POST"
                    class="space-y-4"
                >
                    <div class="grid md:grid-cols-2 gap-4">
                        <input 
                            type="text" 
                            name="name" 
                            placeholder="Nombre (opcional)" 
                            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2" 
                            style="border-color: #fbaf32; focus:ring-color: #fbaf32;"
                        >
                        <input 
                            type="email" 
                            name="email" 
                            placeholder="Email" 
                            required
                            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2" 
                            style="border-color: #fbaf32; focus:ring-color: #fbaf32;"
                        >
                    </div>
                    <textarea 
                        name="message" 
                        placeholder="Mensaje (opcional)" 
                        rows="3"
                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2" 
                        style="border-color: #fbaf32; focus:ring-color: #fbaf32;"
                    ></textarea>

                    <input type="hidden" name="_captcha" value="false">
                    <input type="hidden" name="_next" value="">

                    <div 
                        id="formStatus" 
                        class="text-center p-3 rounded-md mb-4 hidden"
                    ></div>

                    <button 
                        type="submit" 
                        class="w-full py-2 rounded-md text-white transition duration-300" 
                        style="background-color: #fbaf32; hover:opacity-80;"
                    >
                        Enviar Mensaje
                    </button>
                </form>
            </div>
        </div>
    </div>
</footer>

   <script src="js/footer.js"></script>
        
    
        <script src="js/newsletter.js"></script>
                
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
