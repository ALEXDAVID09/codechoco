<?php
require_once 'config.php';

$denuncia = null;
$actualizaciones = [];
$fotos = [];
$error = null;

// Procesar búsqueda
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['codigo'])) {
    try {
        $pdo = conectarDB();
        $codigo = limpiarInput($_POST['codigo']);
        
        // Buscar denuncia
        $stmt = $pdo->prepare("SELECT * FROM denuncias WHERE codigo_seguimiento = ?");
        $stmt->execute([$codigo]);
        $denuncia = $stmt->fetch();
        
        if ($denuncia) {
            // Obtener actualizaciones
            $stmt_act = $pdo->prepare("SELECT * FROM denuncia_actualizaciones WHERE denuncia_id = ? ORDER BY fecha DESC");
            $stmt_act->execute([$denuncia['id']]);
            $actualizaciones = $stmt_act->fetchAll();
            
            // Obtener fotos
            $stmt_fotos = $pdo->prepare("SELECT * FROM denuncia_fotos WHERE denuncia_id = ? ORDER BY fecha_subida");
            $stmt_fotos->execute([$denuncia['id']]);
            $fotos = $stmt_fotos->fetchAll();
        } else {
            $error = "No se encontró ninguna denuncia con ese código de seguimiento.";
        }
    } catch (Exception $e) {
        $error = "Error del sistema al procesar la consulta. Por favor, intente nuevamente.";
    }
}

// Función para obtener el color del estado
function getEstadoColor($estado) {
    switch (strtolower($estado)) {
        case 'pendiente': return 'warning';
        case 'en_proceso': return 'info';
        case 'resuelto': return 'success';
        case 'cerrado': return 'secondary';
        default: return 'primary';
    }
}

// Función para obtener el ícono del estado
function getEstadoIcono($estado) {
    switch (strtolower($estado)) {
        case 'pendiente': return 'clock';
        case 'en_proceso': return 'gear';
        case 'resuelto': return 'check-circle';
        case 'cerrado': return 'times-circle';
        default: return 'info-circle';
    }
}

// Función para formatear tipo de denuncia
function formatearTipo($tipo) {
    $tipos = [
        'corrupcion' => 'Corrupción',
        'maltrato' => 'Maltrato Animal',
        'ambiental' => 'Daño Ambiental',
        'vial' => 'Infraestructura Vial',
        'servicios' => 'Servicios Públicos',
        'otro' => 'Otro'
    ];
    return $tipos[$tipo] ?? ucfirst($tipo);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Denuncia - CodeChoco Denuncias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --codechoco-primary: #1a472a;    /* Verde oscuro del Chocó */
            --codechoco-secondary: #2d5016;  /* Verde bosque */
            --codechoco-accent: #f4a024;     /* Dorado (oro del Chocó) */
            --codechoco-light: #4a7c59;      /* Verde claro */
            --codechoco-earth: #8b4513;      /* Tierra del Chocó */
            --codechoco-water: #1e6091;      /* Azul río */
            --codechoco-gradient: linear-gradient(135deg, var(--codechoco-primary) 0%, var(--codechoco-secondary) 50%, var(--codechoco-light) 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to bottom, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }

        /* Navegación */
        .navbar {
            background: var(--codechoco-gradient) !important;
            box-shadow: 0 2px 20px rgba(26, 71, 42, 0.3);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
        }

        .navbar-brand i {
            color: var(--codechoco-accent);
            margin-right: 0.5rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 0 0.5rem;
            border-radius: 5px;
            padding: 0.5rem 1rem !important;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--codechoco-accent) !important;
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        /* Hero Section */
        .hero-section {
            background: var(--codechoco-gradient);
            color: white;
            padding: 4rem 0;
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
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="1" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="1" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: white;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .choco-badge {
            background: var(--codechoco-accent);
            color: var(--codechoco-primary);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            display: inline-block;
        }

        /* Formulario de búsqueda */
        .search-form {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }

        .search-input {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 1rem 1.5rem;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: var(--codechoco-primary);
            box-shadow: 0 0 0 0.2rem rgba(26, 71, 42, 0.25);
        }

        .btn-search {
            background: var(--codechoco-accent);
            border: none;
            color: var(--codechoco-primary);
            font-weight: 600;
            padding: 1rem 2rem;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-search:hover {
            background: #e6941f;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(244, 160, 36, 0.4);
        }

        /* Cards */
        .denuncia-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }

        .denuncia-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
        }

        .card-header-custom {
            background: var(--codechoco-gradient);
            color: white;
            padding: 1.5rem;
            border: none;
        }

        .card-header-custom h4 {
            margin: 0;
            font-weight: 600;
        }

        .card-body-custom {
            padding: 2rem;
        }

        /* Estados */
        .estado-badge {
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .estado-pendiente {
            background: #fff3cd;
            color: #856404;
            border: 2px solid #ffeaa7;
        }

        .estado-proceso {
            background: #d1ecf1;
            color: #0c5460;
            border: 2px solid #7dd3fc;
        }

        .estado-resuelto {
            background: #d4edda;
            color: #155724;
            border: 2px solid #86efac;
        }

        .estado-cerrado {
            background: #f8d7da;
            color: #721c24;
            border: 2px solid #fca5a5;
        }

        /* Timeline */
        .timeline {
            position: relative;
            padding-left: 2rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 1rem;
            top: 0;
            height: 100%;
            width: 3px;
            background: linear-gradient(to bottom, var(--codechoco-primary), var(--codechoco-light));
            border-radius: 3px;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -2.25rem;
            top: 1rem;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
            background: var(--codechoco-accent);
            border: 3px solid white;
            box-shadow: 0 0 0 3px var(--codechoco-primary);
            z-index: 1;
        }

        .timeline-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            border-left: 4px solid var(--codechoco-accent);
        }

        /* Fotos */
        .foto-thumbnail {
            width: 120px;
            height: 120px;
            object-fit: cover;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 10px;
            border: 3px solid transparent;
        }

        .foto-thumbnail:hover {
            transform: scale(1.05);
            border-color: var(--codechoco-accent);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }

        /* Info boxes */
        .info-box {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            border-left: 5px solid var(--codechoco-accent);
            margin-bottom: 1rem;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.05);
        }

        .info-label {
            color: #6c757d;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            color: var(--codechoco-primary);
            font-size: 1.1rem;
            font-weight: 600;
        }

        /* Alerts */
        .alert-custom {
            border: none;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .alert-error {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border-left: 5px solid #ef4444;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #6c757d;
        }

        .empty-state i {
            color: var(--codechoco-light);
            margin-bottom: 2rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2rem;
            }
            
            .search-form {
                padding: 1.5rem;
                margin-top: 1rem;
            }
            
            .foto-thumbnail {
                width: 80px;
                height: 80px;
            }
            
            .timeline {
                padding-left: 1.5rem;
            }
        }

        /* Animaciones */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Código de seguimiento destacado */
        .codigo-seguimiento {
            background: var(--codechoco-gradient);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 1.2rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-align: center;
            margin: 1rem 0;
            position: relative;
            overflow: hidden;
        }

        .codigo-seguimiento::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            animation: shine 2s infinite;
        }

        @keyframes shine {
            0% { left: -100%; }
            50% { left: 100%; }
            100% { left: 100%; }
        }
    </style>
</head>
<body>
    <!-- Navegación -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-shield-alt"></i>CodeChoco Denuncias
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="fas fa-home me-1"></i>Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="nueva-denuncia.php">
                            <i class="fas fa-plus me-1"></i>Nueva Denuncia
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="consultar.php">
                            <i class="fas fa-search me-1"></i>Consultar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            <i class="fas fa-user-shield me-1"></i>Administración
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center hero-content">
                    <div class="choco-badge">
                        <i class="fas fa-map-marker-alt me-2"></i>Quibdó - Chocó
                    </div>
                    <h1 class="hero-title">Consulta el Estado de tu Denuncia</h1>
                    <p class="hero-subtitle">
                        Transparencia y seguimiento en tiempo real para las denuncias ciudadanas del Chocó
                    </p>
                    
                    <div class="search-form fade-in-up">
                        <form method="POST" class="row g-3 align-items-center">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-barcode text-muted"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control search-input border-start-0" 
                                           name="codigo" 
                                           placeholder="Ingresa tu código de seguimiento (Ej: CC-2024-1234)" 
                                           value="<?php echo isset($_POST['codigo']) ? htmlspecialchars($_POST['codigo']) : ''; ?>"
                                           required>
                                </div>
                                <small class="text-muted mt-2 d-block">
                                    <i class="fas fa-info-circle me-1"></i>
                                    El código te fue enviado cuando registraste tu denuncia
                                </small>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-search w-100" type="submit">
                                    <i class="fas fa-search me-2"></i>Consultar Estado
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container py-5">
        <?php if ($error): ?>
            <!-- Mensaje de Error -->
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="alert alert-custom alert-error text-center fade-in-up">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                        <h4 class="mb-3">Denuncia No Encontrada</h4>
                        <p class="mb-3"><?php echo $error; ?></p>
                        <div class="alert alert-info mt-3">
                            <strong>Consejos de búsqueda:</strong>
                            <ul class="list-unstyled mt-2 mb-0">
                                <li><i class="fas fa-check text-success me-2"></i>Verifica que el código esté completo (Ej: CC-2024-1234)</li>
                                <li><i class="fas fa-check text-success me-2"></i>Asegúrate de incluir los guiones (-)</li>
                                <li><i class="fas fa-check text-success me-2"></i>El código distingue entre mayúsculas y minúsculas</li>
                            </ul>
                        </div>
                        <div class="mt-4">
                            <a href="nueva-denuncia.php" class="btn btn-primary me-2">
                                <i class="fas fa-plus me-2"></i>Crear Nueva Denuncia
                            </a>
                            <button onclick="location.reload()" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-2"></i>Intentar de Nuevo
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
        <?php elseif ($denuncia): ?>
            <!-- Información de la Denuncia -->
            <div class="row fade-in-up">
                <div class="col-lg-8">
                    <!-- Card Principal -->
                    <div class="denuncia-card">
                        <div class="card-header-custom">
                            <h4>
                                <i class="fas fa-file-contract me-2"></i>
                                Información de la Denuncia
                            </h4>
                        </div>
                        <div class="card-body-custom">
                            <!-- Código y Estado -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <div class="info-label">Código de Seguimiento</div>
                                        <div class="codigo-seguimiento">
                                            <?php echo $denuncia['codigo_seguimiento']; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <div class="info-label">Estado Actual</div>
                                        <div class="mt-2">
                                            <span class="estado-badge estado-<?php echo $denuncia['estado']; ?>">
                                                <i class="fas fa-<?php echo getEstadoIcono($denuncia['estado']); ?>"></i>
                                                <?php echo ucfirst(str_replace('_', ' ', $denuncia['estado'])); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Detalles -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <div class="info-label">
                                            <i class="fas fa-tag me-1"></i>Tipo de Denuncia
                                        </div>
                                        <div class="info-value"><?php echo formatearTipo($denuncia['tipo']); ?></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <div class="info-label">
                                            <i class="fas fa-calendar me-1"></i>Fecha del Incidente
                                        </div>
                                        <div class="info-value"><?php echo date('d/m/Y', strtotime($denuncia['fecha'])); ?></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Descripción -->
                            <div class="info-box mb-4">
                                <div class="info-label">
                                    <i class="fas fa-align-left me-1"></i>Descripción del Incidente
                                </div>
                                <div class="mt-2" style="line-height: 1.6;">
                                    <?php echo nl2br(htmlspecialchars($denuncia['descripcion'])); ?>
                                </div>
                            </div>
                            
                            <?php if ($denuncia['latitud'] && $denuncia['longitud']): ?>
                            <!-- Ubicación -->
                            <div class="info-box mb-4">
                                <div class="info-label">
                                    <i class="fas fa-map-marker-alt me-1"></i>Ubicación Geográfica
                                </div>
                                <div class="mt-2">
                                    <span class="badge bg-success me-2">
                                        <i class="fas fa-globe me-1"></i>
                                        Lat: <?php echo number_format($denuncia['latitud'], 6); ?>
                                    </span>
                                    <span class="badge bg-info">
                                        <i class="fas fa-compass me-1"></i>
                                        Lng: <?php echo number_format($denuncia['longitud'], 6); ?>
                                    </span>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($fotos)): ?>
                            <!-- Evidencias -->
                            <div class="info-box">
                                <div class="info-label">
                                    <i class="fas fa-camera me-1"></i>
                                    Evidencias Adjuntas (<?php echo count($fotos); ?> archivo<?php echo count($fotos) != 1 ? 's' : ''; ?>)
                                </div>
                                <div class="row g-3 mt-2">
                                    <?php foreach ($fotos as $index => $foto): ?>
                                    <div class="col-6 col-md-4 col-lg-3">
                                        <?php
                                        $extension = pathinfo($foto['foto_path'], PATHINFO_EXTENSION);
                                        if (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif'])):
                                        ?>
                                            <img src="<?php echo $foto['foto_path']; ?>" 
                                                 class="foto-thumbnail w-100" 
                                                 data-bs-toggle="modal" 
                                                 data-bs-target="#fotoModal"
                                                 data-src="<?php echo $foto['foto_path']; ?>"
                                                 data-index="<?php echo $index + 1; ?>"
                                                 alt="Evidencia <?php echo $index + 1; ?>">
                                        <?php else: ?>
                                            <div class="d-flex align-items-center justify-content-center bg-light border rounded foto-thumbnail w-100">
                                                <div class="text-center">
                                                    <i class="fas fa-file fa-2x text-secondary mb-2"></i>
                                                    <small class="text-muted d-block"><?php echo strtoupper($extension); ?></small>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Información adicional -->
                    <div class="denuncia-card">
                        <div class="card-header-custom">
                            <h5>
                                <i class="fas fa-info-circle me-2"></i>
                                Información del Proceso
                            </h5>
                        </div>
                        <div class="card-body-custom">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <div class="info-label">Fecha de Registro</div>
                                        <div class="info-value">
                                            <?php echo date('d/m/Y H:i', strtotime($denuncia['fecha'])); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <div class="info-label">Última Actualización</div>
                                        <div class="info-value">
                                            <?php 
                                            $ultima_fecha = !empty($actualizaciones) ? 
                                                date('d/m/Y H:i', strtotime($actualizaciones[0]['fecha'])) : 
                                                date('d/m/Y H:i', strtotime($denuncia['fecha_creacion']));
                                            echo $ultima_fecha;
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Timeline de Actualizaciones -->
                <div class="col-lg-4">
                    <div class="denuncia-card">
                        <div class="card-header-custom">
                            <h5>
                                <i class="fas fa-history me-2"></i>
                                Seguimiento y Actualizaciones
                            </h5>
                        </div>
                        <div class="card-body-custom">
                            <?php if (!empty($actualizaciones)): ?>
                                <div class="timeline">
                                    <?php foreach ($actualizaciones as $actualizacion): ?>
                                    <div class="timeline-item">
                                        <div class="timeline-card">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0" style="color: var(--codechoco-primary);">
                                                    <i class="fas fa-user-tie me-2"></i>
                                                    <?php echo htmlspecialchars($actualizacion['responsable']); ?>
                                                </h6>
                                                <span class="badge bg-light text-dark">
                                                    <?php echo date('d/m/Y', strtotime($actualizacion['fecha'])); ?>
                                                </span>
                                            </div>
                                            <p class="mb-2" style="line-height: 1.5;">
                                                <?php echo nl2br(htmlspecialchars($actualizacion['descripcion'])); ?>
                                            </p>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                <?php echo date('H:i', strtotime($actualizacion['fecha'])); ?> hrs
                                            </small>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                                    <h6 class="text-muted">Sin Actualizaciones</h6>
                                    <p class="text-muted small mb-0">
                                        Tu denuncia ha sido registrada exitosamente.<br>
                                        Las actualizaciones aparecerán aquí cuando estén disponibles.
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Card de Contacto -->
                    <div class="denuncia-card">
                        <div class="card-header-custom">
                            <h6>
                                <i class="fas fa-phone-alt me-2"></i>
                                ¿Necesitas Ayuda?
                            </h6>
                        </div>
                        <div class="card-body-custom">
                            <div class="text-center">
                                <p class="small mb-3">
                                    Si tienes dudas sobre tu denuncia, puedes contactarnos:
                                </p>
                                <div class="d-grid gap-2">
                                    <a href="tel:+5746701234" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-phone me-2"></i>
                                        (4) 670-1234
                                    </a>
                                    <a href="mailto:denuncias@codechoco.gov.co" class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-envelope me-2"></i>
                                        Correo Electrónico
                                    </a>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    Horario: Lunes a Viernes 8:00 AM - 5:00 PM
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        <?php else: ?>
            <!-- Estado inicial sin búsqueda -->
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="empty-state fade-in-up">
                        <i class="fas fa-search fa-5x"></i>
                        <h3>Ingresa tu Código de Seguimiento</h3>
                        <p class="lead mb-4">
                            Para consultar el estado de tu denuncia, necesitas el código que recibiste al momento del registro.
                        </p>
                        
                        <!-- Información sobre códigos -->
                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="info-box text-start">
                                    <h5 class="mb-3">
                                        <i class="fas fa-info-circle me-2" style="color: var(--codechoco-accent);"></i>
                                        Sobre los Códigos de Seguimiento
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success me-2"></i>
                                                    Formato: <code>CC-YYYY-XXXX</code>
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success me-2"></i>
                                                    Ejemplo: <code>CC-2024-1234</code>
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success me-2"></i>
                                                    Único para cada denuncia
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <ul class="list-unstyled">
                                                <li class="mb-2">
                                                    <i class="fas fa-shield-alt text-primary me-2"></i>
                                                    Completamente confidencial
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-clock text-info me-2"></i>
                                                    Válido permanentemente
                                                </li>
                                                <li class="mb-2">
                                                    <i class="fas fa-eye text-warning me-2"></i>
                                                    Solo tú puedes consultarlo
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <a href="nueva-denuncia.php" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-plus me-2"></i>Crear Nueva Denuncia
                            </a>
                            <a href="#" onclick="document.querySelector('input[name=codigo]').focus()" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-arrow-up me-2"></i>Buscar Arriba
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal para ver fotos -->
    <div class="modal fade" id="fotoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-header" style="background: var(--codechoco-gradient); color: white; border: none;">
                    <h5 class="modal-title">
                        <i class="fas fa-image me-2"></i>
                        Evidencia <span id="evidenciaNumero"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <img id="modalImg" src="" class="img-fluid w-100" style="max-height: 70vh; object-fit: contain;">
                </div>
                <div class="modal-footer border-0 bg-light">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Haz clic fuera de la imagen para cerrar
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-5" style="background: var(--codechoco-gradient); color: white; padding: 3rem 0 2rem 0;">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-3">
                        <i class="fas fa-shield-alt me-2" style="color: var(--codechoco-accent);"></i>
                        CodeChoco Denuncias
                    </h5>
                    <p class="mb-3">
                        Sistema de denuncias ciudadanas para el Departamento del Chocó.
                        Transparencia, eficiencia y compromiso con nuestra comunidad.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white">
                            <i class="fab fa-facebook fa-lg"></i>
                        </a>
                        <a href="#" class="text-white">
                            <i class="fab fa-twitter fa-lg"></i>
                        </a>
                        <a href="#" class="text-white">
                            <i class="fab fa-instagram fa-lg"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-3">
                    <h6 class="mb-3">Enlaces Rápidos</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="index.php" class="text-white-50 text-decoration-none">
                                <i class="fas fa-home me-2"></i>Inicio
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="nueva-denuncia.php" class="text-white-50 text-decoration-none">
                                <i class="fas fa-plus me-2"></i>Nueva Denuncia
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="consultar.php" class="text-white-50 text-decoration-none">
                                <i class="fas fa-search me-2"></i>Consultar
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="mb-3">Contacto</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-map-marker-alt me-2" style="color: var(--codechoco-accent);"></i>
                            Quibdó, Chocó
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-phone me-2" style="color: var(--codechoco-accent);"></i>
                            (4) 670-1234
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-envelope me-2" style="color: var(--codechoco-accent);"></i>
                            denuncias@codechoco.gov.co
                        </li>
                    </ul>
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(255,255,255,0.2);">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <small>&copy; 2024 CodeChoco. Todos los derechos reservados.</small>
                </div>
                <div class="col-md-6 text-md-end">
                    <small>
                        Desarrollado con <i class="fas fa-heart" style="color: var(--codechoco-accent);"></i> 
                        para el Chocó
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Modal para ver fotos
        document.querySelectorAll('.foto-thumbnail').forEach((img, index) => {
            img.addEventListener('click', function() {
                const modalImg = document.getElementById('modalImg');
                const evidenciaNumero = document.getElementById('evidenciaNumero');
                
                modalImg.src = this.dataset.src;
                evidenciaNumero.textContent = `#${this.dataset.index || index + 1}`;
            });
        });
        
        // Auto-focus en el campo de búsqueda
        document.addEventListener('DOMContentLoaded', function() {
            const codigoInput = document.querySelector('input[name="codigo"]');
            if (codigoInput && !codigoInput.value) {
                // Delay para que la página se cargue completamente
                setTimeout(() => {
                    codigoInput.focus();
                }, 500);
            }
        });

        // Validación del formato del código
        document.querySelector('input[name="codigo"]').addEventListener('input', function(e) {
            let value = e.target.value.toUpperCase();
            // Remover caracteres no permitidos
            value = value.replace(/[^A-Z0-9-]/g, '');
            
            // Formatear automáticamente
            if (value.length >= 2 && !value.includes('-')) {
                value = value.substring(0, 2) + '-' + value.substring(2);
            }
            if (value.length >= 7 && value.split('-').length === 2) {
                const parts = value.split('-');
                if (parts[1].length >= 4) {
                    value = parts[0] + '-' + parts[1].substring(0, 4) + '-' + parts[1].substring(4);
                }
            }
            
            e.target.value = value;
        });

        // Animación de carga para el botón de búsqueda
        document.querySelector('form').addEventListener('submit', function() {
            const btn = document.querySelector('.btn-search');
            const originalText = btn.innerHTML;
            
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Consultando...';
            btn.disabled = true;
            
            // Restaurar después de 3 segundos si no hay respuesta
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }, 3000);
        });

        // Efectos de hover mejorados
        document.querySelectorAll('.denuncia-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Copiar código de seguimiento al hacer clic
        document.querySelectorAll('.codigo-seguimiento').forEach(codigo => {
            codigo.addEventListener('click', function() {
                const text = this.textContent.trim();
                navigator.clipboard.writeText(text).then(() => {
                    // Mostrar tooltip temporal
                    const tooltip = document.createElement('div');
                    tooltip.textContent = '¡Código copiado!';
                    tooltip.style.cssText = `
                        position: fixed;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        background: var(--codechoco-accent);
                        color: var(--codechoco-primary);
                        padding: 0.5rem 1rem;
                        border-radius: 20px;
                        font-weight: 600;
                        z-index: 9999;
                        animation: fadeInOut 2s ease-in-out;
                    `;
                    
                    document.body.appendChild(tooltip);
                    setTimeout(() => tooltip.remove(), 2000);
                });
                
                // Agregar animación de clic
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 150);
            });
            
            // Agregar cursor pointer y tooltip
            codigo.style.cursor = 'pointer';
            codigo.title = 'Haz clic para copiar el código';
        });

        // Agregar animación CSS para el tooltip
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeInOut {
                0% { opacity: 0; transform: translate(-50%, -50%) scale(0.8); }
                20% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
                80% { opacity: 1; transform: translate(-50%, -50%) scale(1); }
                100% { opacity: 0; transform: translate(-50%, -50%) scale(0.8); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>