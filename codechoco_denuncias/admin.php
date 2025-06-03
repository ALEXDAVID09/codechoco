<?php
require_once 'config.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header('Location: login.php');
    exit();
}

// Conectar a la base de datos usando la función de config.php
$pdo = conectarDB();

// DIAGNÓSTICO: Primero veamos qué estados realmente existen en la BD
$debug_estados_query = "SELECT estado, COUNT(*) as cantidad FROM denuncias GROUP BY estado";
$debug_estados_result = $pdo->query($debug_estados_query);
$estados_reales = $debug_estados_result->fetchAll(PDO::FETCH_ASSOC);

// Obtener estadísticas - CORREGIDA para ser más flexible
$stats_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN TRIM(LOWER(estado)) = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
    SUM(CASE WHEN TRIM(LOWER(estado)) = 'en_proceso' OR TRIM(LOWER(estado)) = 'en proceso' THEN 1 ELSE 0 END) as en_proceso,
    SUM(CASE WHEN TRIM(LOWER(estado)) = 'resuelto' OR TRIM(LOWER(estado)) = 'resueltas' THEN 1 ELSE 0 END) as resueltas
    FROM denuncias";
$stats_result = $pdo->query($stats_query);
$stats = $stats_result->fetch(PDO::FETCH_ASSOC);

// Obtener denuncias recientes
$denuncias_query = "SELECT d.*, 
    (SELECT COUNT(*) FROM denuncia_fotos WHERE denuncia_id = d.id) as fotos_count,
    (SELECT COUNT(*) FROM denuncia_actualizaciones WHERE denuncia_id = d.id) as actualizaciones_count
    FROM denuncias d 
    ORDER BY d.fecha DESC 
    LIMIT 20";
$denuncias_result = $pdo->query($denuncias_query);

// Debug mejorado: Verificar que los datos se están obteniendo correctamente
if (isset($_GET['debug'])) {
    echo "<div class='container mt-4'>";
    echo "<div class='alert alert-info'>";
    echo "<h5>Información de Debug</h5>";
    echo "<h6>Estados reales en la base de datos:</h6>";
    echo "<pre>";
    foreach ($estados_reales as $estado_info) {
        echo "Estado: '" . $estado_info['estado'] . "' - Cantidad: " . $estado_info['cantidad'] . "\n";
        echo "Longitud: " . strlen($estado_info['estado']) . " caracteres\n";
        echo "Hex: " . bin2hex($estado_info['estado']) . "\n";
        echo "---\n";
    }
    echo "</pre>";
    
    echo "<h6>Estadísticas calculadas:</h6>";
    echo "<pre>";
    print_r($stats);
    echo "</pre>";
    
    echo "<h6>Primera denuncia:</h6>";
    $debug_denuncia = $denuncias_result->fetch(PDO::FETCH_ASSOC);
    if ($debug_denuncia) {
        echo "<pre>";
        echo "ID: " . $debug_denuncia['id'] . "\n";
        echo "Estado: '" . $debug_denuncia['estado'] . "'\n";
        echo "Longitud estado: " . strlen($debug_denuncia['estado']) . "\n";
        echo "Hex estado: " . bin2hex($debug_denuncia['estado']) . "\n";
        echo "</pre>";
        // Reejecutar la consulta para el resto del código
        $denuncias_result = $pdo->query($denuncias_query);
    }
    echo "</div>";
    echo "</div>";
}

// Procesar eliminación de denuncias resueltas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_denuncia'])) {
    try {
        $denuncia_id = (int)$_POST['denuncia_id'];
        
        // Verificar que la denuncia existe y está resuelta
        $check_query = "SELECT id, estado FROM denuncias WHERE id = ? AND (TRIM(LOWER(estado)) = 'resuelto' OR TRIM(LOWER(estado)) = 'resueltas')";
        $check_stmt = $pdo->prepare($check_query);
        $check_stmt->execute([$denuncia_id]);
        
        if ($check_stmt->rowCount() > 0) {
            // Comenzar transacción para eliminar de forma segura
            $pdo->beginTransaction();
            
            try {
                // Eliminar fotos relacionadas
                $delete_fotos = "DELETE FROM denuncia_fotos WHERE denuncia_id = ?";
                $stmt_fotos = $pdo->prepare($delete_fotos);
                $stmt_fotos->execute([$denuncia_id]);
                
                // Eliminar actualizaciones relacionadas
                $delete_updates = "DELETE FROM denuncia_actualizaciones WHERE denuncia_id = ?";
                $stmt_updates = $pdo->prepare($delete_updates);
                $stmt_updates->execute([$denuncia_id]);
                
                // Eliminar la denuncia
                $delete_denuncia = "DELETE FROM denuncias WHERE id = ?";
                $stmt_denuncia = $pdo->prepare($delete_denuncia);
                $stmt_denuncia->execute([$denuncia_id]);
                
                $pdo->commit();
                header('Location: admin.php?deleted=1');
                exit();
                
            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }
        } else {
            $error = "Solo se pueden eliminar denuncias resueltas";
        }
    } catch (Exception $e) {
        $error = "Error al eliminar la denuncia: " . $e->getMessage();
    }
}

// Procesar actualizaciones de estado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_estado'])) {
    try {
        $denuncia_id = (int)$_POST['denuncia_id'];
        $nuevo_estado = trim(limpiarInput($_POST['nuevo_estado']));
        $descripcion_actualizacion = limpiarInput($_POST['descripcion_actualizacion']);
        
        // Verificar que la denuncia existe
        $check_query = "SELECT id FROM denuncias WHERE id = ?";
        $check_stmt = $pdo->prepare($check_query);
        $check_stmt->execute([$denuncia_id]);
        
        if ($check_stmt->rowCount() > 0) {
            // Actualizar estado de la denuncia
            $update_query = "UPDATE denuncias SET estado = ? WHERE id = ?";
            $stmt = $pdo->prepare($update_query);
            $resultado = $stmt->execute([$nuevo_estado, $denuncia_id]);
            
            if ($resultado) {
                // Agregar actualización
                $insert_update = "INSERT INTO denuncia_actualizaciones (denuncia_id, descripcion, fecha, responsable) VALUES (?, ?, NOW(), ?)";
                $stmt2 = $pdo->prepare($insert_update);
                $responsable = "Administrador";
                $stmt2->execute([$denuncia_id, $descripcion_actualizacion, $responsable]);
                
                header('Location: admin.php?updated=1&id=' . $denuncia_id);
                exit();
            } else {
                $error = "Error al actualizar la denuncia";
            }
        } else {
            $error = "Denuncia no encontrada";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - CodeChoco Denuncias</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Colores del Chocó - Paleta inspirada en la naturaleza chocoana */
            --primary-green: #2E8B57; /* Verde selva del Chocó */
            --secondary-green: #228B22; /* Verde profundo */
            --accent-gold: #DAA520; /* Dorado (oro del Chocó) */
            --earth-brown: #8B4513; /* Marrón tierra */
            --river-blue: #4682B4; /* Azul río Atrato */
            --light-green: #90EE90; /* Verde claro */
            --dark-green: #1C5F3C; /* Verde oscuro */
            --warm-orange: #FF8C00; /* Naranja cálido */
            --bg-light: #F5F7FA;
            --text-dark: #2C3E50;
            --shadow: rgba(46, 139, 87, 0.15);
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--bg-light) 0%, #E8F4F0 100%);
            color: var(--text-dark);
            min-height: 100vh;
        }

        /* Navbar mejorada */
        .navbar-custom {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);
            box-shadow: 0 4px 20px var(--shadow);
            padding: 1rem 0;
        }

        .navbar-brand {
            color: white !important;
            font-weight: 700;
            font-size: 1.4rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .navbar-brand:hover {
            color: var(--accent-gold) !important;
            transform: scale(1.05);
            transition: all 0.3s ease;
        }

        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: var(--accent-gold) !important;
            background: rgba(255,255,255,0.1);
            transform: translateY(-2px);
        }

        /* Tarjetas de estadísticas mejoradas */
        .stats-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px var(--shadow);
            transition: all 0.3s ease;
            border: none;
            overflow: hidden;
            position: relative;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(46, 139, 87, 0.25);
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-green), var(--accent-gold));
        }

        .stats-card .card-body {
            padding: 2rem 1.5rem;
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0.5rem 0;
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .icon-total { background: linear-gradient(135deg, var(--river-blue), #5DADE2); }
        .icon-pending { background: linear-gradient(135deg, var(--warm-orange), #F39C12); }
        .icon-process { background: linear-gradient(135deg, var(--river-blue), #3498DB); }
        .icon-resolved { background: linear-gradient(135deg, var(--primary-green), var(--secondary-green)); }

        /* Tarjeta principal mejorada */
        .main-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px var(--shadow);
            border: none;
            overflow: hidden;
        }

        .main-card .card-header {
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            color: white;
            padding: 1.5rem 2rem;
            border: none;
            font-weight: 600;
            font-size: 1.1rem;
        }

        /* Tabla mejorada */
        .table-custom {
            margin: 0;
        }

        .table-custom thead th {
            background: linear-gradient(135deg, #F8F9FA, #E9ECEF);
            color: var(--text-dark);
            font-weight: 600;
            border: none;
            padding: 1rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table-custom tbody tr {
            transition: all 0.3s ease;
            border: none;
        }

        .table-custom tbody tr:hover {
            background: linear-gradient(135deg, #F0F8F5, #E8F4F0);
            transform: scale(1.01);
        }

        .table-custom td {
            padding: 1rem;
            vertical-align: middle;
            border: none;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        /* Estados mejorados */
        .estado-badge {
            padding: 8px 16px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .estado-pendiente { 
            background: linear-gradient(135deg, #FFF3CD, #FFECB5);
            color: #856404;
            border: 2px solid #F0D86C;
        }

        .estado-en_proceso { 
            background: linear-gradient(135deg, #CCE5FF, #B3D9FF);
            color: #004085;
            border: 2px solid #7DB8E8;
        }

        .estado-resuelto { 
            background: linear-gradient(135deg, #D1E7DD, #BFE3C7);
            color: #0F5132;
            border: 2px solid #A3D9A5;
        }

        /* Botones mejorados */
        .btn-custom {
            border-radius: 10px;
            font-weight: 500;
            padding: 8px 16px;
            transition: all 0.3s ease;
            border: none;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            color: white;
        }

        .btn-primary-custom:hover {
            background: linear-gradient(135deg, var(--secondary-green), var(--dark-green));
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(46, 139, 87, 0.4);
        }

        .btn-danger-custom {
            background: linear-gradient(135deg, #DC3545, #C82333);
            color: white;
        }

        .btn-danger-custom:hover {
            background: linear-gradient(135deg, #C82333, #BD2130);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }

        .btn-outline-custom {
            border: 2px solid var(--primary-green);
            color: var(--primary-green);
            background: transparent;
        }

        .btn-outline-custom:hover {
            background: var(--primary-green);
            color: white;
            transform: translateY(-2px);
        }

        /* Modal mejorado */
        .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 1.5rem 2rem;
            border: none;
        }

        .modal-title {
            font-weight: 600;
            font-size: 1.2rem;
        }

        .modal-body {
            padding: 2rem;
        }

        /* Alertas mejoradas */
        .alert-custom {
            border-radius: 15px;
            border: none;
            padding: 1rem 1.5rem;
            font-weight: 500;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .alert-success-custom {
            background: linear-gradient(135deg, #D1E7DD, #BFE3C7);
            color: #0F5132;
        }

        .alert-danger-custom {
            background: linear-gradient(135deg, #F8D7DA, #F1B4B9);
            color: #721C24;
        }

        /* Código de seguimiento destacado */
        .codigo-seguimiento {
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            color: white;
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* Badge de tipo mejorado */
        .tipo-badge {
            background: linear-gradient(135deg, var(--accent-gold), #B8860B);
            color: white;
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.8rem;
        }

        /* Información de archivos */
        .archivos-info {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .archivo-count {
            background: rgba(46, 139, 87, 0.1);
            color: var(--primary-green);
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stats-card {
                margin-bottom: 1rem;
            }
            
            .table-responsive {
                font-size: 0.9rem;
            }
            
            .modal-dialog {
                margin: 1rem;
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

        /* Header con gradiente */
        .page-header {
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 30px var(--shadow);
        }

        .page-header h1 {
            font-weight: 700;
            margin: 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .page-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-shield-alt me-2"></i>CodeChoco Admin
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php" target="_blank">
                    <i class="fas fa-external-link-alt me-1"></i>Ver Sitio
                </a>
                <a class="nav-link" href="?debug=1">
                    <i class="fas fa-bug me-1"></i>Debug
                </a>
                <a class="nav-link" href="?logout=1">
                    <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Header de página -->
        <div class="page-header fade-in-up">
            <h1><i class="fas fa-tachometer-alt me-2"></i>Panel de Administración</h1>
            <p>Gestión de Denuncias - Quibdó, Chocó</p>
        </div>

        <!-- Alertas -->
        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success-custom alert-custom alert-dismissible fade show fade-in-up">
                <i class="fas fa-check-circle me-2"></i>Denuncia actualizada correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-success-custom alert-custom alert-dismissible fade show fade-in-up">
                <i class="fas fa-trash-alt me-2"></i>Denuncia eliminada correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger-custom alert-custom alert-dismissible fade show fade-in-up">
                <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stats-card card fade-in-up">
                    <div class="card-body text-center">
                        <div class="stats-icon icon-total">
                            <i class="fas fa-exclamation-triangle fa-lg text-white"></i>
                        </div>
                        <div class="stats-number text-primary"><?= isset($stats['total']) ? $stats['total'] : 0 ?></div>
                        <p class="text-muted mb-0 fw-500">Total Denuncias</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stats-card card fade-in-up">
                    <div class="card-body text-center">
                        <div class="stats-icon icon-pending">
                            <i class="fas fa-clock fa-lg text-white"></i>
                        </div>
                        <div class="stats-number" style="color: var(--warm-orange);"><?= isset($stats['pendientes']) ? $stats['pendientes'] : 0 ?></div>
                        <p class="text-muted mb-0 fw-500">Pendientes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stats-card card fade-in-up">
                    <div class="card-body text-center">
                        <div class="stats-icon icon-process">
                            <i class="fas fa-cogs fa-lg text-white"></i>
                        </div>
                        <div class="stats-number" style="color: var(--river-blue);"><?= isset($stats['en_proceso']) ? $stats['en_proceso'] : 0 ?></div>
                        <p class="text-muted mb-0 fw-500">En Proceso</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stats-card card fade-in-up">
                    <div class="card-body text-center">
                        <div class="stats-icon icon-resolved">
                            <i class="fas fa-check-circle fa-lg text-white"></i>
                        </div>
                        <div class="stats-number" style="color: var(--primary-green);"><?= isset($stats['resueltas']) ? $stats['resueltas'] : 0 ?></div>
                        <p class="text-muted mb-0 fw-500">Resueltas</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Denuncias -->
        <div class="main-card card fade-in-up">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-list me-2"></i>Gestión de Denuncias</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th><i class="fas fa-barcode me-1"></i>Código</th>
                                <th><i class="fas fa-tag me-1"></i>Tipo</th>
                                <th><i class="fas fa-user me-1"></i>Denunciante</th>
                                <th><i class="fas fa-calendar me-1"></i>Fecha</th>
                                <th><i class="fas fa-flag me-1"></i>Estado</th>
                                <th><i class="fas fa-paperclip me-1"></i>Archivos</th>
                                <th><i class="fas fa-cog me-1"></i>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($denuncia = $denuncias_result->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td>
                                    <span class="codigo-seguimiento"><?= htmlspecialchars($denuncia['codigo_seguimiento'] ?? '') ?></span>
                                </td>
                                <td>
                                    <span class="tipo-badge"><?= htmlspecialchars($denuncia['tipo'] ?? '') ?></span>
                                </td>
                                <td>
                                    <div>
                                        <strong><?= htmlspecialchars($denuncia['nombre_denunciante'] ?? '') ?></strong>
                                        <br><small class="text-muted"><?= htmlspecialchars($denuncia['email_denunciante'] ?? '') ?></small>
                                    </div>
                                </td>
                                <td>
                                    <i class="fas fa-calendar-alt text-muted me-1"></i>
                                    <?= isset($denuncia['fecha']) ? date('d/m/Y', strtotime($denuncia['fecha'])) : 'N/A' ?>
                                    <br><small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        <?= isset($denuncia['fecha']) ? date('H:i', strtotime($denuncia['fecha'])) : '' ?>
                                    </small>
                                </td>
                                <td>
                                    <?php 
                                    $estado = trim($denuncia['estado'] ?? 'pendiente');
                                    $estado_texto = ucfirst(str_replace('_', ' ', $estado));
                                    ?>
                                    <span class="estado-badge estado-<?= $estado ?>">
                                        <?= $estado_texto ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="archivos-info">
                                        <span class="archivo-count">
                                            <i class="fas fa-camera me-1"></i><?= $denuncia['fotos_count'] ?? 0 ?>
                                        </span>
                                        <span class="archivo-count">
                                            <i class="fas fa-comments me-1"></i><?= $denuncia['actualizaciones_count'] ?? 0 ?>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-outline-custom btn-custom btn-sm" data-bs-toggle="modal" data-bs-target="#modal<?= $denuncia['id'] ?>" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php if (trim(strtolower($denuncia['estado'] ?? '')) == 'resuelto' || trim(strtolower($denuncia['estado'] ?? '')) == 'resueltas'): ?>
                                        <button class="btn btn-danger-custom btn-custom btn-sm" onclick="confirmarEliminacion(<?= $denuncia['id'] ?>, '<?= htmlspecialchars($denuncia['codigo_seguimiento'] ?? '') ?>')" title="Eliminar denuncia resuelta">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal para cada denuncia -->
                            <div class="modal fade" id="modal<?= $denuncia['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="fas fa-file-alt me-2"></i>
                                                Denuncia #<?= htmlspecialchars($denuncia['codigo_seguimiento'] ?? '') ?>
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="card mb-3" style="border: 1px solid rgba(46, 139, 87, 0.2);">
                                                        <div class="card-header" style="background: linear-gradient(135deg, #F0F8F5, #E8F4F0); color: var(--text-dark); font-weight: 600;">
                                                            <i class="fas fa-info-circle me-2"></i>Información de la Denuncia
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="mb-3">
                                                                <label class="fw-bold text-muted">Tipo:</label>
                                                                <div><span class="tipo-badge"><?= htmlspecialchars($denuncia['tipo'] ?? '') ?></span></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="fw-bold text-muted">Estado Actual:</label>
                                                                <div>
                                                                    <span class="estado-badge estado-<?= trim($denuncia['estado'] ?? 'pendiente') ?>">
                                                                        <?= ucfirst(str_replace('_', ' ', trim($denuncia['estado'] ?? 'pendiente'))) ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="fw-bold text-muted">Descripción:</label>
                                                                <div class="p-3 rounded" style="background: linear-gradient(135deg, #F8F9FA, #E9ECEF);">
                                                                    <?= nl2br(htmlspecialchars($denuncia['descripcion'] ?? '')) ?>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <label class="fw-bold text-muted">Fecha:</label>
                                                                    <div>
                                                                        <i class="fas fa-calendar-alt text-muted me-1"></i>
                                                                        <?= isset($denuncia['fecha']) ? date('d/m/Y H:i', strtotime($denuncia['fecha'])) : 'N/A' ?>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <label class="fw-bold text-muted">Ubicación:</label>
                                                                    <div>
                                                                        <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                                                        <?= ($denuncia['latitud'] ?? 'N/A') . ', ' . ($denuncia['longitud'] ?? 'N/A') ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="card mb-3" style="border: 1px solid rgba(46, 139, 87, 0.2);">
                                                        <div class="card-header" style="background: linear-gradient(135deg, #F0F8F5, #E8F4F0); color: var(--text-dark); font-weight: 600;">
                                                            <i class="fas fa-user-circle me-2"></i>Datos del Denunciante
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="mb-3">
                                                                <label class="fw-bold text-muted">Nombre:</label>
                                                                <div><i class="fas fa-user text-muted me-2"></i><?= htmlspecialchars($denuncia['nombre_denunciante'] ?? '') ?></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="fw-bold text-muted">Email:</label>
                                                                <div><i class="fas fa-envelope text-muted me-2"></i><?= htmlspecialchars($denuncia['email_denunciante'] ?? '') ?></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="fw-bold text-muted">Contacto:</label>
                                                                <div><i class="fas fa-phone text-muted me-2"></i><?= htmlspecialchars($denuncia['contacto_denunciante'] ?? 'No proporcionado') ?></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="fw-bold text-muted">Archivos adjuntos:</label>
                                                                <div class="archivos-info">
                                                                    <span class="archivo-count">
                                                                        <i class="fas fa-camera me-1"></i><?= $denuncia['fotos_count'] ?? 0 ?> fotos
                                                                    </span>
                                                                    <span class="archivo-count">
                                                                        <i class="fas fa-comments me-1"></i><?= $denuncia['actualizaciones_count'] ?? 0 ?> actualizaciones
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <hr style="border-color: rgba(46, 139, 87, 0.2);">

                                            <!-- Actualizar Estado -->
                                            <div class="card" style="border: 1px solid rgba(46, 139, 87, 0.2);">
                                                <div class="card-header" style="background: linear-gradient(135deg, var(--primary-green), var(--secondary-green)); color: white;">
                                                    <h6 class="mb-0"><i class="fas fa-edit me-2"></i>Actualizar Estado</h6>
                                                </div>
                                                <div class="card-body">
                                                    <form method="POST" onsubmit="return confirm('¿Está seguro de actualizar el estado de esta denuncia?');">
                                                        <input type="hidden" name="denuncia_id" value="<?= $denuncia['id'] ?>">
                                                        <div class="row">
                                                            <div class="col-md-4 mb-3">
                                                                <label class="form-label fw-bold">Nuevo Estado:</label>
                                                                <select name="nuevo_estado" class="form-select" required style="border: 2px solid rgba(46, 139, 87, 0.2);">
                                                                    <option value="">Seleccionar estado</option>
                                                                    <option value="pendiente" <?= trim($denuncia['estado'] ?? '') == 'pendiente' ? 'selected' : '' ?>>⏳ Pendiente</option>
                                                                    <option value="en_proceso" <?= trim($denuncia['estado'] ?? '') == 'en_proceso' ? 'selected' : '' ?>>🔄 En Proceso</option>
                                                                    <option value="resuelto" <?= trim($denuncia['estado'] ?? '') == 'resuelto' ? 'selected' : '' ?>>✅ Resuelto</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-8 mb-3">
                                                                <label class="form-label fw-bold">Descripción de la actualización:</label>
                                                                <input type="text" name="descripcion_actualizacion" class="form-control" placeholder="Describe los cambios realizados..." required maxlength="255" style="border: 2px solid rgba(46, 139, 87, 0.2);">
                                                            </div>
                                                        </div>
                                                        <div class="d-flex gap-2 justify-content-end">
                                                            <button type="submit" name="actualizar_estado" class="btn btn-primary-custom btn-custom">
                                                                <i class="fas fa-save me-2"></i>Actualizar Estado
                                                            </button>
                                                            <?php if (trim(strtolower($denuncia['estado'] ?? '')) == 'resuelto' || trim(strtolower($denuncia['estado'] ?? '')) == 'resueltas'): ?>
                                                            <button type="button" class="btn btn-danger-custom btn-custom" onclick="confirmarEliminacion(<?= $denuncia['id'] ?>, '<?= htmlspecialchars($denuncia['codigo_seguimiento'] ?? '') ?>')">
                                                                <i class="fas fa-trash-alt me-2"></i>Eliminar Denuncia
                                                            </button>
                                                            <?php endif; ?>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Footer informativo -->
        <div class="mt-5 text-center">
            <div class="card main-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-2" style="color: var(--primary-green);">
                                <i class="fas fa-leaf me-2"></i>CodeChoco - Sistema de Denuncias Ambientales
                            </h5>
                            <p class="text-muted mb-0">
                                Protegiendo nuestro patrimonio natural en Quibdó, Chocó. 
                                Juntos construimos un futuro más verde y sostenible.
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex justify-content-end gap-3">
                                <div class="text-center">
                                    <i class="fas fa-tree fa-2x" style="color: var(--primary-green);"></i>
                                    <div class="small text-muted">Conservación</div>
                                </div>
                                <div class="text-center">
                                    <i class="fas fa-water fa-2x" style="color: var(--river-blue);"></i>
                                    <div class="small text-muted">Protección</div>
                                </div>
                                <div class="text-center">
                                    <i class="fas fa-hands-helping fa-2x" style="color: var(--accent-gold);"></i>
                                    <div class="small text-muted">Comunidad</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación para eliminar -->
    <div class="modal fade" id="modalEliminar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #DC3545, #C82333); color: white;">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                        <h6>¿Está seguro de eliminar esta denuncia?</h6>
                        <p class="text-muted">Esta acción no se puede deshacer. La denuncia <strong id="codigoEliminar"></strong> será eliminada permanentemente junto con todos sus archivos y actualizaciones.</p>
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Solo se pueden eliminar denuncias resueltas</strong>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-custom btn-custom" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <form method="POST" style="display: inline;" id="formEliminar">
                        <input type="hidden" name="denuncia_id" id="denunciaIdEliminar">
                        <button type="submit" name="eliminar_denuncia" class="btn btn-danger-custom btn-custom">
                            <i class="fas fa-trash-alt me-2"></i>Eliminar Permanentemente
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función para confirmar eliminación
        function confirmarEliminacion(id, codigo) {
            document.getElementById('denunciaIdEliminar').value = id;
            document.getElementById('codigoEliminar').textContent = '#' + codigo;
            
            var modal = new bootstrap.Modal(document.getElementById('modalEliminar'));
            modal.show();
        }

        // Animaciones al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            // Agregar clase de animación a elementos con retraso
            const elements = document.querySelectorAll('.fade-in-up');
            elements.forEach((el, index) => {
                el.style.animationDelay = (index * 0.1) + 's';
            });

            // Auto-cerrar alertas después de 5 segundos
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });

        // Efectos hover mejorados
        document.querySelectorAll('.stats-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Confirmación mejorada para actualización de estado
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form[onsubmit*="confirm"]');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const select = this.querySelector('select[name="nuevo_estado"]');
                    const descripcion = this.querySelector('input[name="descripcion_actualizacion"]');
                    
                    if (select.value && descripcion.value.trim()) {
                        const estadoTexto = select.options[select.selectedIndex].text;
                        const confirmMsg = `¿Confirma cambiar el estado a "${estadoTexto}"?\n\nDescripción: ${descripcion.value}`;
                        
                        if (!confirm(confirmMsg)) {
                            e.preventDefault();
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>

<?php

// Cerrar sesión
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}
?>