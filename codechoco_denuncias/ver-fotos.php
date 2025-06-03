<?php
require_once 'config.php';

// Verificar que se proporcione el ID de la denuncia
if (!isset($_GET['denuncia_id']) || !is_numeric($_GET['denuncia_id'])) {
    die('ID de denuncia no válido');
}

$denuncia_id = (int)$_GET['denuncia_id'];

// Obtener información de la denuncia
$denuncia_query = "SELECT codigo_seguimiento, tipo, descripcion, nombre_denunciante FROM denuncias WHERE id = ?";
$stmt = $conn->prepare($denuncia_query);
$stmt->bind_param("i", $denuncia_id);
$stmt->execute();
$denuncia_result = $stmt->get_result();

if ($denuncia_result->num_rows === 0) {
    die('Denuncia no encontrada');
}

$denuncia = $denuncia_result->fetch_assoc();

// Obtener las fotos de la denuncia
$fotos_query = "SELECT id, foto_path, fecha_subida FROM denuncia_fotos WHERE denuncia_id = ? ORDER BY fecha_subida ASC";
$stmt = $conn->prepare($fotos_query);
$stmt->bind_param("i", $denuncia_id);
$stmt->execute();
$fotos_result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fotos - Denuncia #<?= htmlspecialchars($denuncia['codigo_seguimiento']) ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand {
            color: #d4692b !important;
            font-weight: bold;
        }
        .foto-thumbnail {
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 10px;
            overflow: hidden;
        }
        .foto-thumbnail:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        .foto-thumbnail img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .modal-img {
            max-width: 100%;
            max-height: 80vh;
            object-fit: contain;
        }
        .foto-info {
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #8B4513;">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-shield-alt me-2"></i>CodeChoco
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="javascript:history.back()">
                    <i class="fas fa-arrow-left me-1"></i>Volver
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Información de la denuncia -->
        <div class="card mb-4">
            <div class="card-header" style="background: linear-gradient(135deg, #d4692b, #8B4513); color: white;">
                <h5 class="mb-0">
                    <i class="fas fa-camera me-2"></i>
                    Evidencias Fotográficas - Denuncia #<?= htmlspecialchars($denuncia['codigo_seguimiento']) ?>
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <p><strong>Tipo:</strong> <?= htmlspecialchars($denuncia['tipo']) ?></p>
                        <p><strong>Denunciante:</strong> <?= htmlspecialchars($denuncia['nombre_denunciante']) ?></p>
                        <p><strong>Descripción:</strong> <?= htmlspecialchars(substr($denuncia['descripcion'], 0, 200)) ?>...</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="badge bg-info fs-6">
                            <i class="fas fa-images me-1"></i>
                            <?= $fotos_result->num_rows ?> foto(s)
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Galería de fotos -->
        <?php if ($fotos_result->num_rows > 0): ?>
            <div class="row">
                <?php while ($foto = $fotos_result->fetch_assoc()): ?>
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="foto-thumbnail" data-bs-toggle="modal" data-bs-target="#fotoModal<?= $foto['id'] ?>">
                            <div class="position-relative">
                                <?php if (file_exists($foto['foto_path'])): ?>
                                    <img src="<?= htmlspecialchars($foto['foto_path']) ?>" 
                                         alt="Evidencia fotográfica" 
                                         class="img-fluid">
                                <?php else: ?>
                                    <div class="d-flex align-items-center justify-content-center bg-secondary text-white" style="height: 200px;">
                                        <div class="text-center">
                                            <i class="fas fa-image fa-3x mb-2"></i>
                                            <p class="mb-0">Archivo no encontrado</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="position-absolute bottom-0 start-0 foto-info">
                                    <i class="fas fa-calendar me-1"></i>
                                    <?= date('d/m/Y H:i', strtotime($foto['fecha_subida'])) ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal para cada foto -->
                    <div class="modal fade" id="fotoModal<?= $foto['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        Evidencia - <?= date('d/m/Y H:i', strtotime($foto['fecha_subida'])) ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <?php if (file_exists($foto['foto_path'])): ?>
                                        <img src="<?= htmlspecialchars($foto['foto_path']) ?>" 
                                             alt="Evidencia fotográfica" 
                                             class="modal-img">
                                        <div class="mt-3">
                                            <a href="<?= htmlspecialchars($foto['foto_path']) ?>" 
                                               download 
                                               class="btn btn-primary">
                                                <i class="fas fa-download me-1"></i>Descargar
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            El archivo no se encuentra disponible
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <h5>No hay evidencias fotográficas</h5>
                <p class="mb-0">Esta denuncia no tiene fotos adjuntas.</p>
            </div>
        <?php endif; ?>

        <!-- Navegación adicional -->
        <div class="text-center mt-4">
            <a href="consultar.php" class="btn btn-outline-secondary me-2">
                <i class="fas fa-search me-1"></i>Consultar otra denuncia
            </a>
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-home me-1"></i>Inicio
            </a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Previsualización mejorada con zoom
        document.querySelectorAll('.foto-thumbnail').forEach(thumbnail => {
            thumbnail.addEventListener('mouseenter', function() {
                this.style.zIndex = '10';
            });
            
            thumbnail.addEventListener('mouseleave', function() {
                this.style.zIndex = '1';
            });
        });

        // Navegación con teclado en modales
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const openModal = document.querySelector('.modal.show');
                if (openModal) {
                    bootstrap.Modal.getInstance(openModal).hide();
                }
            }
        });
    </script>
</body>
</html>