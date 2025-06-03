<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --choco-green: #2D5016;
            --choco-green-light: #4A7C28;
            --choco-yellow: #F4D03F;
            --choco-blue: #1B4F72;
            --choco-blue-light: #2E86AB;
            --choco-gold: #D4AF37;
            --text-dark: #2C3E50;
            --text-light: #5D6D7E;
            --bg-light: #F8F9FA;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        .hero-section {
            background: linear-gradient(135deg, var(--choco-green) 0%, var(--choco-green-light) 50%, var(--choco-blue) 100%);
            color: white;
            padding: 120px 0;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><defs><radialGradient id="g"><stop offset="20%" stop-color="%23ffffff" stop-opacity="0.1"/><stop offset="50%" stop-color="%23ffffff" stop-opacity="0.05"/><stop offset="100%" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><rect width="100" height="20" fill="url(%23g)"/></svg>');
            opacity: 0.1;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.3rem;
            color: var(--choco-green) !important;
        }

        .navbar {
            background: white !important;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            padding: 1rem 0;
        }

        .navbar-nav .nav-link {
            color: var(--text-dark) !important;
            font-weight: 500;
            margin: 0 0.5rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .navbar-nav .nav-link:hover {
            color: var(--choco-green) !important;
        }

        .navbar-nav .nav-link::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--choco-yellow);
            transition: width 0.3s ease;
        }

        .navbar-nav .nav-link:hover::after {
            width: 100%;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .hero-subtitle {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            opacity: 0.95;
        }

        .btn-primary-custom {
            background: linear-gradient(45deg, var(--choco-yellow), var(--choco-gold));
            border: none;
            color: var(--text-dark);
            font-weight: 600;
            padding: 15px 35px;
            border-radius: 50px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
            color: var(--text-dark);
        }

        .btn-outline-custom {
            border: 2px solid white;
            color: white;
            font-weight: 600;
            padding: 15px 35px;
            border-radius: 50px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .btn-outline-custom:hover {
            background: white;
            color: var(--choco-green);
            transform: translateY(-2px);
        }

        .card-hover {
            transition: all 0.4s ease;
            border: none;
            border-radius: 20px;
            overflow: hidden;
        }

        .card-hover:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .feature-icon {
            font-size: 3.5rem;
            background: linear-gradient(45deg, var(--choco-blue), var(--choco-blue-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1.5rem;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        .section-subtitle {
            color: var(--text-light);
            font-size: 1.1rem;
            margin-bottom: 3rem;
        }

        .process-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            height: 100%;
            position: relative;
            overflow: hidden;
        }

        .process-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--choco-green), var(--choco-yellow), var(--choco-blue));
        }

        .process-number {
            background: linear-gradient(45deg, var(--choco-green), var(--choco-green-light));
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            margin: 0 auto 1.5rem;
        }

        .type-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .type-card:hover {
            border-color: var(--choco-yellow);
            transform: translateY(-5px);
        }

        .type-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .confidence-section {
            background: linear-gradient(135deg, var(--bg-light) 0%, white 100%);
            padding: 80px 0;
        }

        .confidence-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .confidence-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        .confidence-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--choco-green);
        }

        footer {
            background: linear-gradient(135deg, var(--choco-green) 0%, var(--choco-blue) 100%);
            color: white;
            padding: 3rem 0 2rem;
        }

        .footer-brand {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .choco-badge {
            background: linear-gradient(45deg, var(--choco-yellow), var(--choco-gold));
            color: var(--text-dark);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
        }

        .pulse-animation {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .float-animation {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body>
    <!-- Navegación -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-shield-alt me-2"></i>CodeChoco Denuncias
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="nueva-denuncia.php">Nueva Denuncia</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="consultar.php">Consultar Denuncia</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Administración</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container hero-content">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center">
                    <div class="choco-badge mb-4">
                        <i class="fas fa-map-marker-alt me-2"></i>Quibdó, Chocó - Colombia
                    </div>
                    <h1 class="hero-title">Sistema de Denuncias <span style="color: var(--choco-yellow);">CodeChoco</span></h1>
                    <p class="hero-subtitle">Tu voz cuenta en el corazón del Pacífico. Reporta incidentes de manera segura, confidencial y efectiva para construir un Chocó mejor.</p>
                    <div class="mt-5">
                        <a href="nueva-denuncia.php" class="btn btn-primary-custom me-3 pulse-animation">
                            <i class="fas fa-plus-circle me-2"></i>Nueva Denuncia
                        </a>
                        <a href="consultar.php" class="btn btn-outline-custom">
                            <i class="fas fa-search me-2"></i>Consultar Estado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Cómo Funciona -->
    <section class="py-5" style="padding: 100px 0 !important;">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">¿Cómo funciona?</h2>
                <p class="section-subtitle">Proceso simple, seguro y transparente para realizar tu denuncia</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="process-card card-hover">
                        <div class="process-number">1</div>
                        <i class="fas fa-edit feature-icon"></i>
                        <h5 class="fw-bold mb-3">Reporta el Incidente</h5>
                        <p class="text-muted">Completa nuestro formulario seguro con los detalles del incidente. Puedes adjuntar evidencias como fotografías o documentos de soporte.</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="process-card card-hover">
                        <div class="process-number">2</div>
                        <i class="fas fa-code feature-icon"></i>
                        <h5 class="fw-bold mb-3">Recibe tu Código</h5>
                        <p class="text-muted">Obtén inmediatamente un código único de seguimiento que te permitirá consultar el estado y progreso de tu denuncia en cualquier momento.</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="process-card card-hover">
                        <div class="process-number">3</div>
                        <i class="fas fa-eye feature-icon"></i>
                        <h5 class="fw-bold mb-3">Seguimiento Activo</h5>
                        <p class="text-muted">Consulta las actualizaciones, respuestas oficiales y el progreso de tu denuncia a través de nuestro sistema de seguimiento en tiempo real.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tipos de Denuncias -->
    <section class="py-5" style="background: var(--bg-light); padding: 100px 0 !important;">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Tipos de Denuncias</h2>
                <p class="section-subtitle">Puedes reportar diferentes tipos de incidentes que afecten a nuestra comunidad chocoana</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="type-card card-hover">
                        <i class="fas fa-user-times type-icon text-danger"></i>
                        <h6 class="fw-bold">Acoso Laboral</h6>
                        <small class="text-muted">Comportamientos inadecuados en el trabajo</small>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="type-card card-hover">
                        <i class="fas fa-exclamation-triangle type-icon text-warning"></i>
                        <h6 class="fw-bold">Seguridad</h6>
                        <small class="text-muted">Riesgos y condiciones inseguras</small>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="type-card card-hover">
                        <i class="fas fa-gavel type-icon" style="color: var(--choco-blue);"></i>
                        <h6 class="fw-bold">Ético</h6>
                        <small class="text-muted">Violaciones al código de ética</small>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="type-card card-hover">
                        <i class="fas fa-leaf type-icon" style="color: var(--choco-green);"></i>
                        <h6 class="fw-bold">Ambiental</h6>
                        <small class="text-muted">Afectaciones al medio ambiente</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Confidencialidad -->
    <section class="confidence-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center mb-5">
                    <h2 class="section-title">Confidencialidad Garantizada</h2>
                    <p class="section-subtitle">
                        Tu identidad está protegida con los más altos estándares de seguridad. 
                        Todas las denuncias son tratadas con máxima confidencialidad y procesadas 
                        por personal especializado y autorizado.
                    </p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="confidence-card float-animation">
                        <i class="fas fa-lock confidence-icon"></i>
                        <h5 class="fw-bold mb-2">100% Seguro</h5>
                        <p class="text-muted mb-0">Encriptación de datos y protocolos de seguridad avanzados</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="confidence-card float-animation" style="animation-delay: 0.5s;">
                        <i class="fas fa-user-secret confidence-icon"></i>
                        <h5 class="fw-bold mb-2">Anónimo</h5>
                        <p class="text-muted mb-0">Tu identidad permanece protegida durante todo el proceso</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="confidence-card float-animation" style="animation-delay: 1s;">
                        <i class="fas fa-clock confidence-icon"></i>
                        <h5 class="fw-bold mb-2">24/7 Disponible</h5>
                        <p class="text-muted mb-0">Sistema disponible las 24 horas del día, todos los días</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="footer-brand">
                        <i class="fas fa-shield-alt me-2"></i>CodeChoco Denuncias
                    </div>
                    <p class="mb-3">Sistema seguro de reportes y denuncias para el desarrollo sostenible del Chocó.</p>
                    <p class="mb-0">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Quibdó, Departamento del Chocó, Colombia
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="mb-3">
                        <span class="badge" style="background: var(--choco-yellow); color: var(--text-dark);">
                            <i class="fas fa-heart me-1"></i>Construyendo un Chocó mejor
                        </span>
                    </div>
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> CodeChoco. Todos los derechos reservados.</p>
                    <small class="text-light">Comprometidos con la transparencia y el desarrollo sostenible</small>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Efecto de aparición suave al hacer scroll
        window.addEventListener('scroll', function() {
            const elements = document.querySelectorAll('.card-hover, .process-card, .type-card');
            elements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const elementVisible = 150;
                
                if (elementTop < window.innerHeight - elementVisible) {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }
            });
        });

        // Inicializar elementos ocultos
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.card-hover, .process-card, .type-card');
            elements.forEach(element => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(30px)';
                element.style.transition = 'all 0.6s ease';
            });
        });
    </script>
</body>
</html>