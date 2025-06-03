<?php
require_once 'config.php';

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: nueva-denuncia.php');
    exit;
}

try {
    // Conectar a la base de datos
    $pdo = conectarDB();
    
    // Validar y limpiar datos requeridos
    $tipo = limpiarInput($_POST['tipo']);
    $descripcion = limpiarInput($_POST['descripcion']);
    
    if (empty($tipo) || empty($descripcion)) {
        throw new Exception('Los campos tipo y descripción son requeridos.');
    }
    
    if (strlen($descripcion) < 20) {
        throw new Exception('La descripción debe tener al menos 20 caracteres.');
    }
    
    // Datos opcionales
    $latitud = !empty($_POST['latitud']) ? floatval($_POST['latitud']) : null;
    $longitud = !empty($_POST['longitud']) ? floatval($_POST['longitud']) : null;
    $fecha = !empty($_POST['fecha']) ? $_POST['fecha'] : date('Y-m-d');
    $nombre = !empty($_POST['nombre']) ? limpiarInput($_POST['nombre']) : null;
    $contacto = !empty($_POST['contacto']) ? limpiarInput($_POST['contacto']) : null;
    $email = !empty($_POST['email']) ? limpiarInput($_POST['email']) : null;
    
    // Validar email si se proporciona
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('El formato del email no es válido.');
    }
    
    // Generar código de seguimiento único
    do {
        $codigo_seguimiento = generarCodigoSeguimiento();
        $stmt = $pdo->prepare("SELECT id FROM denuncias WHERE codigo_seguimiento = ?");
        $stmt->execute([$codigo_seguimiento]);
    } while ($stmt->fetch()); // Asegurar que el código sea único
    
    // Insertar denuncia en la base de datos
    $sql = "INSERT INTO denuncias (tipo, descripcion, latitud, longitud, fecha, estado, codigo_seguimiento, nombre_denunciante, contacto_denunciante, email_denunciante) 
            VALUES (?, ?, ?, ?, ?, 'pendiente', ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $tipo,
        $descripcion,
        $latitud,
        $longitud,
        $fecha,
        $codigo_seguimiento,
        $nombre,
        $contacto,
        $email
    ]);
    
    $denuncia_id = $pdo->lastInsertId();
    
    // Crear directorio de uploads si no existe
    $upload_dir = UPLOADS_DIR;
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Procesar archivos de evidencia
    $archivos_subidos = [];
    if (isset($_FILES['evidencias']) && !empty($_FILES['evidencias']['name'][0])) {
        $files = $_FILES['evidencias'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $max_size = 10 * 1024 * 1024; // 10MB
        
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                // Validar tipo de archivo
                if (!in_array($files['type'][$i], $allowed_types)) {
                    continue; // Saltar archivo no permitido
                }
                
                // Validar tamaño
                if ($files['size'][$i] > $max_size) {
                    continue; // Saltar archivo muy grande
                }
                
                // Generar nombre único para el archivo
                $extension = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                $filename = 'evidencia_' . $denuncia_id . '_' . $i . '_' . time() . '.' . $extension;
                $filepath = $upload_dir . $filename;
                
                // Mover archivo
                if (move_uploaded_file($files['tmp_name'][$i], $filepath)) {
                    // Guardar en base de datos
                    $stmt_foto = $pdo->prepare("INSERT INTO denuncia_fotos (denuncia_id, foto_path, fecha_subida) VALUES (?, ?, NOW())");
                    $stmt_foto->execute([$denuncia_id, $filepath]);
                    $archivos_subidos[] = $files['name'][$i];
                }
            }
        }
    }
    
    // Crear primera actualización
    $stmt_actualizacion = $pdo->prepare("INSERT INTO denuncia_actualizaciones (denuncia_id, descripcion, fecha, responsable) VALUES (?, ?, NOW(), 'Sistema')");
    $stmt_actualizacion->execute([$denuncia_id, 'Denuncia recibida y registrada en el sistema.']);
    
    $success = true;
    $mensaje = "Denuncia registrada exitosamente.";
    
} catch (Exception $e) {
    $success = false;
    $mensaje = "Error: " . $e->getMessage();
    $codigo_seguimiento = null;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $success ? 'Denuncia Registrada' : 'Error'; ?> - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .result-card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .codigo-seguimiento {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-shield-alt me-2"></i>CodeChoco Denuncias
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">Inicio</a>
                <a class="nav-link" href="consultar.php">Consultar Denuncia</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if ($success): ?>
                    <!-- Resultado Exitoso -->
                    <div class="card result-card border-0">
                        <div class="card-body p-5 text-center">
                            <div class="mb-4">
                                <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                            </div>
                            <h1 class="display-6 fw-bold text-success mb-4">¡Denuncia Registrada!</h1>
                            <p class="lead text-muted mb-4">
                                Tu denuncia ha sido recibida y registrada en nuestro sistema. 
                                Guarda el siguiente código para dar seguimiento a tu caso.
                            </p>
                            
                            <div class="codigo-seguimiento">
                                <div class="mb-2">
                                    <i class="fas fa-qrcode me-2"></i>Código de Seguimiento
                                </div>
                                <div class="fs-3" id="codigoSeguimiento"><?php echo $codigo_seguimiento; ?></div>
                                <button class="btn btn-light btn-sm mt-2" onclick="copiarCodigo()">
                                    <i class="fas fa-copy me-1"></i>Copiar Código
                                </button>
                            </div>
                            
                            <div class="row text-start mt-4">
                                <div class="col-md-6">
                                    <h5><i class="fas fa-info-circle text-primary me-2"></i>Información del Registro</h5>
                                    <ul class="list-unstyled">
                                        <li><strong>Tipo:</strong> <?php echo ucfirst($tipo); ?></li>
                                        <li><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($fecha)); ?></li>
                                        <li><strong>Estado:</strong> <span class="badge bg-warning">Pendiente</span></li>
                                        <?php if ($latitud && $longitud): ?>
                                        <li><strong>Ubicación:</strong> Registrada</li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h5><i class="fas fa-clock text-info me-2"></i>Próximos Pasos</h5>
                                    <ul class="list-unstyled">
                                        <li>• Tu denuncia será revisada</li>
                                        <li>• Recibirás actualizaciones del proceso</li>
                                        <li>• Puedes consultar el estado cuando quieras</li>
                                        <?php if (!empty($archivos_subidos)): ?>
                                        <li>• Evidencias subidas: <?php echo count($archivos_subidos); ?></li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                            
                            <?php if (!empty($archivos_subidos)): ?>
                            <div class="alert alert-success mt-4">
                                <i class="fas fa-paperclip me-2"></i>
                                <strong>Archivos subidos exitosamente:</strong><br>
                                <?php echo implode(', ', $archivos_subidos); ?>
                            </div>
                            <?php endif; ?>
                            
                            <div class="mt-4">
                                <a href="consultar.php" class="btn btn-primary btn-lg me-3">
                                    <i class="fas fa-search me-2"></i>Consultar Estado
                                </a>
                                <a href="nueva-denuncia.php" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-plus me-2"></i>Nueva Denuncia
                                </a>
                            </div>
                        </div>
                    </div>
                    
                <?php else: ?>
                    <!-- Resultado de Error -->
                    <div class="card result-card border-0">
                        <div class="card-body p-5 text-center">
                            <div class="mb-4">
                                <i class="fas fa-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
                            </div>
                            <h1 class="display-6 fw-bold text-danger mb-4">Error al Procesar</h1>
                            <p class="lead text-muted mb-4"><?php echo $mensaje; ?></p>
                            
                            <div class="mt-4">
                                <a href="nueva-denuncia.php" class="btn btn-primary btn-lg me-3">
                                    <i class="fas fa-arrow-left me-2"></i>Volver al Formulario
                                </a>
                                <a href="index.php" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-home me-2"></i>Ir al Inicio
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copiarCodigo() {
            const codigo = document.getElementById('codigoSeguimiento').textContent;
            navigator.clipboard.writeText(codigo).then(function() {
                // Mostrar mensaje de éxito
                const btn = event.target.closest('button');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check me-1"></i>¡Copiado!';
                btn.className = 'btn btn-success btn-sm mt-2';
                
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.className = 'btn btn-light btn-sm mt-2';
                }, 2000);
            });
        }
        
        // Auto-seleccionar código al hacer clic
        document.getElementById('codigoSeguimiento').addEventListener('click', function() {
            const range = document.createRange();
            range.selectNodeContents(this);
            const selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);
        });
    </script>
</body>
</html>