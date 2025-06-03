<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Denuncia - CodeChoco</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2E7D32;        /* Verde Chocó */
            --secondary-color: #FFB300;       /* Oro/Amarillo */
            --accent-color: #1565C0;          /* Azul Atrato */
            --success-color: #388E3C;
            --warning-color: #F57C00;
            --danger-color: #D32F2F;
            --dark-color: #2C3E50;
            --light-bg: #F8FBF8;
            --card-shadow: 0 8px 32px rgba(46, 125, 50, 0.1);
            --gradient-bg: linear-gradient(135deg, #2E7D32 0%, #388E3C 50%, #4CAF50 100%);
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #E8F5E8 0%, #F1F8E9 100%);
            min-height: 100vh;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            background: var(--gradient-bg);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .navbar {
            background: linear-gradient(90deg, var(--dark-color) 0%, #34495E 100%) !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
        }

        .hero-section {
            background: var(--gradient-bg);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
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
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .form-container {
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .form-section {
            background: linear-gradient(145deg, #ffffff 0%, #fafbfa 100%);
            border: none;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 20px rgba(46, 125, 50, 0.05);
            border-left: 4px solid var(--primary-color);
            transition: all 0.3s ease;
        }

        .form-section:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(46, 125, 50, 0.1);
        }

        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #E8F5E8;
        }

        .section-icon {
            width: 48px;
            height: 48px;
            background: var(--gradient-bg);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 1rem;
            font-size: 1.2rem;
        }

        .form-control, .form-select {
            border: 2px solid #E0E7E0;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(46, 125, 50, 0.15);
            background: white;
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .required {
            color: var(--danger-color);
            font-weight: bold;
        }

        .btn-primary {
            background: var(--gradient-bg);
            border: none;
            border-radius: 12px;
            padding: 0.875rem 2rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(46, 125, 50, 0.4);
            background: linear-gradient(135deg, #1B5E20 0%, #2E7D32 50%, #388E3C 100%);
        }

        .btn-outline-success {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-success:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            transform: translateY(-1px);
        }

        .btn-outline-secondary {
            border: 2px solid #6C757D;
            color: #6C757D;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
        }

        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            font-weight: 500;
        }

        .alert-success {
            background: linear-gradient(135deg, #E8F5E8 0%, #F1F8E9 100%);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .progress-indicator {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 4px 15px rgba(46, 125, 50, 0.1);
            margin-bottom: 2rem;
            position: sticky;
            top: 20px;
            z-index: 100;
        }

        .progress-step {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .step-circle {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #E0E7E0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 600;
            color: #6C757D;
            transition: all 0.3s ease;
        }

        .step-circle.active {
            background: var(--primary-color);
            color: white;
            transform: scale(1.1);
        }

        .step-circle.completed {
            background: var(--success-color);
            color: white;
        }

        .floating-help {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }

        .help-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--gradient-bg);
            border: none;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 4px 20px rgba(46, 125, 50, 0.3);
            transition: all 0.3s ease;
        }

        .help-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 30px rgba(46, 125, 50, 0.4);
        }

        .file-upload-area {
            border: 3px dashed #E0E7E0;
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            background: linear-gradient(145deg, #fafbfa 0%, #f5f7f5 100%);
        }

        .file-upload-area:hover {
            border-color: var(--primary-color);
            background: linear-gradient(145deg, #E8F5E8 0%, #F1F8E9 100%);
        }

        .file-upload-area.dragover {
            border-color: var(--primary-color);
            background: linear-gradient(145deg, #E8F5E8 0%, #F1F8E9 100%);
            transform: scale(1.02);
        }

        @media (max-width: 768px) {
            .form-section {
                padding: 1.5rem;
                margin-bottom: 1rem;
            }
            
            .hero-section {
                padding: 2rem 0;
            }
            
            .section-icon {
                width: 40px;
                height: 40px;
                font-size: 1rem;
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-code me-2"></i>CodeChoco
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
                        <a class="nav-link active" href="nueva-denuncia.php">
                            <i class="fas fa-plus-circle me-1"></i>Nueva Denuncia
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="consultar.php">
                            <i class="fas fa-search me-1"></i>Consultar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            <i class="fas fa-user-shield me-1"></i>Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content text-center">
                <h1 class="display-4 fw-bold mb-3">
                    <i class="fas fa-shield-alt me-3"></i>Nueva Denuncia
                </h1>
                <p class="lead mb-0">
                    Tu voz importa. Reporta incidentes de manera segura y confidencial
                </p>
                <div class="mt-4">
                    <span class="badge bg-light text-dark fs-6 px-3 py-2">
                        <i class="fas fa-lock me-2"></i>100% Confidencial
                    </span>
                    <span class="badge bg-light text-dark fs-6 px-3 py-2 ms-2">
                        <i class="fas fa-eye-slash me-2"></i>Anónimo Opcional
                    </span>
                </div>
            </div>
        </div>
    </section>

    <div class="container py-4">
        <div class="row">
            <!-- Indicador de Progreso -->
            <div class="col-lg-3">
                <div class="progress-indicator d-none d-lg-block">
                    <h6 class="fw-bold mb-3 text-center">Progreso del Formulario</h6>
                    <div class="progress-step">
                        <span class="step-text">Información del Incidente</span>
                        <div class="step-circle" id="step1">1</div>
                    </div>
                    <div class="progress-step">
                        <span class="step-text">Ubicación</span>
                        <div class="step-circle" id="step2">2</div>
                    </div>
                    <div class="progress-step">
                        <span class="step-text">Evidencias</span>
                        <div class="step-circle" id="step3">3</div>
                    </div>
                    <div class="progress-step">
                        <span class="step-text">Información de Contacto</span>
                        <div class="step-circle" id="step4">4</div>
                    </div>
                    <div class="progress-step">
                        <span class="step-text">Confirmación</span>
                        <div class="step-circle" id="step5">5</div>
                    </div>
                </div>
            </div>

            <!-- Formulario Principal -->
            <div class="col-lg-9">
                <div class="form-container animate-fade-in">
                    <form action="procesar-denuncia.php" method="POST" enctype="multipart/form-data" id="denunciaForm">
                        <!-- Información del Incidente -->
                        <div class="form-section" data-step="1">
                            <div class="section-header">
                                <div class="section-icon">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div>
                                    <h4 class="mb-1 fw-bold">Información del Incidente</h4>
                                    <p class="text-muted mb-0">Describe qué sucedió y cuándo ocurrió</p>
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="tipo" class="form-label">
                                        Tipo de Denuncia <span class="required">*</span>
                                    </label>
                                    <select class="form-select" id="tipo" name="tipo" required>
                                        <option value="">Selecciona el tipo de incidente</option>
                                        <option value="acoso">🚫 Acoso o Intimidación</option>
                                        <option value="seguridad">🛡️ Problema de Seguridad</option>
                                        <option value="etico">⚖️ Problema Ético</option>
                                        <option value="discriminacion">🤝 Discriminación</option>
                                        <option value="corrupcion">💼 Corrupción</option>
                                        <option value="laboral">👔 Problema Laboral</option>
                                        <option value="ambiental">🌿 Problema Ambiental</option>
                                        <option value="otro">📋 Otro</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="fecha" class="form-label">Fecha del Incidente</label>
                                    <input type="date" class="form-control" id="fecha" name="fecha" 
                                           max="<?php echo date('Y-m-d'); ?>">
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>Fecha aproximada si no recuerdas exactamente
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="descripcion" class="form-label">
                                    Descripción Detallada del Incidente <span class="required">*</span>
                                </label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="6" 
                                          placeholder="Describe detalladamente lo que ocurrió... ¿Qué pasó? ¿Quién estuvo involucrado? ¿Dónde sucedió? ¿Cuándo ocurrió? ¿Hay testigos?" 
                                          required></textarea>
                                <div class="form-text">
                                    <div class="d-flex justify-content-between">
                                        <span><i class="fas fa-edit me-1"></i>Mínimo 20 caracteres. Sé específico y detallado.</span>
                                        <span id="charCount" class="text-muted">0 caracteres</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="urgencia" class="form-label">Nivel de Urgencia</label>
                                    <select class="form-select" id="urgencia" name="urgencia">
                                        <option value="baja">🟢 Baja - No requiere acción inmediata</option>
                                        <option value="media" selected>🟡 Media - Requiere atención</option>
                                        <option value="alta">🔴 Alta - Requiere acción urgente</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="categoria_afectada" class="form-label">Área Afectada</label>
                                    <select class="form-select" id="categoria_afectada" name="categoria_afectada">
                                        <option value="">Selecciona el área</option>
                                        <option value="personal">👥 Personal/Recursos Humanos</option>
                                        <option value="operaciones">⚙️ Operaciones</option>
                                        <option value="financiero">💰 Financiero</option>
                                        <option value="tecnologia">💻 Tecnología</option>
                                        <option value="legal">⚖️ Legal/Cumplimiento</option>
                                        <option value="seguridad">🛡️ Seguridad</option>
                                        <option value="otro">📋 Otro</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Ubicación -->
                        <div class="form-section" data-step="2">
                            <div class="section-header">
                                <div class="section-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <h4 class="mb-1 fw-bold">Ubicación del Incidente</h4>
                                    <p class="text-muted mb-0">Ayúdanos a ubicar dónde ocurrió el incidente</p>
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="direccion" class="form-label">Dirección o Lugar</label>
                                    <input type="text" class="form-control" id="direccion" name="direccion" 
                                           placeholder="Ej: Calle 25 #12-34, Oficina, etc.">
                                </div>
                                <div class="col-md-6">
                                    <label for="municipio" class="form-label">Municipio</label>
                                    <select class="form-select" id="municipio" name="municipio">
                                        <option value="quibdo" selected>Quibdó</option>
                                        <option value="istmina">Istmina</option>
                                        <option value="condoto">Condoto</option>
                                        <option value="nuqui">Nuquí</option>
                                        <option value="otro_choco">Otro municipio del Chocó</option>
                                        <option value="otro">Otro departamento</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="latitud" class="form-label">
                                        <i class="fas fa-globe me-1"></i>Latitud
                                    </label>
                                    <input type="number" step="any" class="form-control" id="latitud" name="latitud" 
                                           placeholder="5.6918" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="longitud" class="form-label">
                                        <i class="fas fa-globe me-1"></i>Longitud
                                    </label>
                                    <input type="number" step="any" class="form-control" id="longitud" name="longitud" 
                                           placeholder="-76.6669" readonly>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="button" class="btn btn-outline-success btn-lg" id="getLocationBtn">
                                    <i class="fas fa-crosshairs me-2"></i>Obtener Ubicación Actual
                                </button>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        Tu ubicación se mantendrá confidencial y solo se usará para la investigación
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Evidencias -->
                        <div class="form-section" data-step="3">
                            <div class="section-header">
                                <div class="section-icon">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <div>
                                    <h4 class="mb-1 fw-bold">Evidencias</h4>
                                    <p class="text-muted mb-0">Adjunta documentos, fotos o archivos que respalden tu denuncia</p>
                                </div>
                            </div>

                            <div class="file-upload-area" id="fileUploadArea">
                                <div class="mb-3">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                    <h5>Arrastra archivos aquí o haz clic para seleccionar</h5>
                                    <p class="text-muted mb-3">
                                        Imágenes, PDFs, documentos de Word. Máximo 5 archivos de 10MB cada uno.
                                    </p>
                                    <input type="file" class="form-control d-none" id="evidencias" name="evidencias[]" 
                                           multiple accept="image/*,.pdf,.doc,.docx,.txt">
                                    <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('evidencias').click()">
                                        <i class="fas fa-plus me-2"></i>Seleccionar Archivos
                                    </button>
                                </div>
                            </div>

                            <div id="file-preview" class="row mt-4"></div>
                        </div>

                        <!-- Información de Contacto -->
                        <div class="form-section" data-step="4">
                            <div class="section-header">
                                <div class="section-icon">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div>
                                    <h4 class="mb-1 fw-bold">Información de Contacto</h4>
                                    <p class="text-muted mb-0">Opcional y completamente confidencial</p>
                                </div>
                            </div>

                            <div class="alert alert-info mb-4">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>¿Por qué pedimos esta información?</strong><br>
                                Solo para contactarte si necesitamos aclarar detalles. Tu identidad permanecerá protegida.
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="nombre" class="form-label">
                                        <i class="fas fa-user me-1"></i>Nombre Completo
                                    </label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                           placeholder="Tu nombre completo (opcional)">
                                </div>
                                <div class="col-md-6">
                                    <label for="contacto" class="form-label">
                                        <i class="fas fa-phone me-1"></i>Teléfono
                                    </label>
                                    <input type="tel" class="form-control" id="contacto" name="contacto" 
                                           placeholder="+57 300 123 4567 (opcional)">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>Correo Electrónico
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="tu@email.com (opcional)">
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="anonimo" name="anonimo">
                                <label class="form-check-label" for="anonimo">
                                    <i class="fas fa-user-secret me-2"></i>Prefiero mantener mi denuncia completamente anónima
                                </label>
                            </div>
                        </div>

                        <!-- Confirmación y Envío -->
                        <div class="form-section" data-step="5">
                            <div class="section-header">
                                <div class="section-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div>
                                    <h4 class="mb-1 fw-bold">Confirmación</h4>
                                    <p class="text-muted mb-0">Revisa y confirma tu denuncia</p>
                                </div>
                            </div>

                            <div class="alert alert-warning mb-4">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Importante:</strong> Una vez enviada, recibirás un código de seguimiento para consultar el estado de tu denuncia.
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="terminos" name="terminos" required>
                                <label class="form-check-label" for="terminos">
                                    <strong>Declaro que:</strong><br>
                                    • La información proporcionada es veraz y completa<br>
                                    • Autorizo el procesamiento de mis datos para la investigación<br>
                                    • Entiendo que proporcionar información falsa puede tener consecuencias legales
                                    <span class="required">*</span>
                                </label>
                            </div>

                            <div class="d-grid gap-3 d-md-flex justify-content-md-center">
                                <a href="index.php" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-arrow-left me-2"></i>Volver al Inicio
                                </a>
                                <button type="button" class="btn btn-outline-primary btn-lg" id="previewBtn">
                                    <i class="fas fa-eye me-2"></i>Vista Previa
                                </button>
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <i class="fas fa-paper-plane me-2"></i>Enviar Denuncia
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Vista Previa -->
    <div class="modal fade" id="previewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-eye me-2"></i>Vista Previa de la Denuncia
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="previewContent">
                    <!-- Contenido generado dinámicamente -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-edit me-2"></i>Editar
                    </button>
                    <button type="button" class="btn btn-primary" id="confirmSubmit">
                        <i class="fas fa-check me-2"></i>Confirmar y Enviar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Botón de Ayuda Flotante -->
    <div class="floating-help">
        <button class="help-btn" data-bs-toggle="modal" data-bs-target="#helpModal" title="¿Necesitas ayuda?">
            <i class="fas fa-question"></i>
        </button>
    </div>

    <!-- Modal de Ayuda -->
    <div class="modal fade" id="helpModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-life-ring me-2"></i>¿Necesitas Ayuda?
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <h6><i class="fas fa-phone text-success me-2"></i>Líneas de Ayuda</h6>
                        <p class="mb-2"><strong>Emergencias:</strong> 123</p>
                        <p class="mb-2"><strong>Policía Nacional:</strong> 112</p>
                        <p class="mb-4"><strong>Línea Antiextorsión:</strong> 165</p>
                    </div>
                    
                    <div class="mb-4">
                        <h6><i class="fas fa-shield-alt text-primary me-2"></i>Tu Seguridad es Importante</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i>Tus datos están protegidos</li>
                            <li><i class="fas fa-check text-success me-2"></i>Puedes reportar de forma anónima</li>
                            <li><i class="fas fa-check text-success me-2"></i>No se compartirá tu identidad sin tu consentimiento</li>
                        </ul>
                    </div>

                    <div>
                        <h6><i class="fas fa-envelope text-info me-2"></i>Contacto Directo</h6>
                        <p>Email: denuncias@codechoco.com<br>
                        WhatsApp: +57 314 123 4567</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                        <i class="fas fa-check me-2"></i>Entendido
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Variables globales
        let currentStep = 1;
        const totalSteps = 5;

        // Inicialización
        document.addEventListener('DOMContentLoaded', function() {
            updateProgressIndicator();
            setupFormValidation();
            setupFileUpload();
            setupLocationServices();
            setupCharacterCounter();
        });

        // Actualizar indicador de progreso
        function updateProgressIndicator() {
            for (let i = 1; i <= totalSteps; i++) {
                const circle = document.getElementById(`step${i}`);
                if (i < currentStep) {
                    circle.className = 'step-circle completed';
                    circle.innerHTML = '<i class="fas fa-check"></i>';
                } else if (i === currentStep) {
                    circle.className = 'step-circle active';
                    circle.innerHTML = i;
                } else {
                    circle.className = 'step-circle';
                    circle.innerHTML = i;
                }
            }
        }

        // Configurar validación del formulario
        function setupFormValidation() {
            const form = document.getElementById('denunciaForm');
            const sections = document.querySelectorAll('.form-section');

            // Observador de scroll para actualizar progreso
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const step = parseInt(entry.target.dataset.step);
                        if (step && step !== currentStep) {
                            currentStep = step;
                            updateProgressIndicator();
                        }
                    }
                });
            }, { threshold: 0.5 });

            sections.forEach(section => observer.observe(section));

            // Validación en tiempo real
            form.addEventListener('input', function(e) {
                validateField(e.target);
            });

            // Prevenir envío si hay errores
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (validateForm()) {
                    showPreview();
                }
            });
        }

        // Validar campo individual
        function validateField(field) {
            const fieldContainer = field.closest('.mb-3, .mb-4');
            let isValid = true;
            let message = '';

            // Remover validaciones previas
            const existingFeedback = fieldContainer.querySelector('.invalid-feedback');
            if (existingFeedback) existingFeedback.remove();
            field.classList.remove('is-invalid', 'is-valid');

            // Validaciones específicas
            switch (field.id) {
                case 'descripcion':
                    if (field.value.length < 20) {
                        isValid = false;
                        message = 'La descripción debe tener al menos 20 caracteres.';
                    }
                    break;
                case 'email':
                    if (field.value && !isValidEmail(field.value)) {
                        isValid = false;
                        message = 'Ingresa un email válido.';
                    }
                    break;
                case 'contacto':
                    if (field.value && !isValidPhone(field.value)) {
                        isValid = false;
                        message = 'Ingresa un número de teléfono válido.';
                    }
                    break;
            }

            // Validaciones requeridas
            if (field.required && !field.value.trim()) {
                isValid = false;
                message = 'Este campo es obligatorio.';
            }

            // Aplicar estilos de validación
            if (field.value.trim()) {
                field.classList.add(isValid ? 'is-valid' : 'is-invalid');
                
                if (!isValid) {
                    const feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.textContent = message;
                    fieldContainer.appendChild(feedback);
                }
            }

            return isValid;
        }

        // Validar formulario completo
        function validateForm() {
            const requiredFields = document.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!validateField(field)) {
                    isValid = false;
                }
            });

            if (!isValid) {
                showNotification('Por favor, corrige los errores antes de continuar.', 'error');
                // Scroll al primer error
                const firstError = document.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            }

            return isValid;
        }

        // Configurar carga de archivos
        function setupFileUpload() {
            const fileInput = document.getElementById('evidencias');
            const uploadArea = document.getElementById('fileUploadArea');
            const preview = document.getElementById('file-preview');

            // Drag and drop
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            });

            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
            });

            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
                const files = e.dataTransfer.files;
                handleFiles(files);
                fileInput.files = files;
            });

            // Cambio de archivos
            fileInput.addEventListener('change', function(e) {
                handleFiles(e.target.files);
            });

            function handleFiles(files) {
                preview.innerHTML = '';
                const filesArray = Array.from(files);

                if (filesArray.length > 5) {
                    showNotification('Máximo 5 archivos permitidos.', 'warning');
                    return;
                }

                filesArray.forEach((file, index) => {
                    if (file.size > 10 * 1024 * 1024) {
                        showNotification(`El archivo "${file.name}" excede el tamaño máximo de 10MB.`, 'warning');
                        return;
                    }

                    const div = document.createElement('div');
                    div.className = 'col-md-6 col-lg-4 mb-3';
                    
                    let content = '';
                    if (file.type.startsWith('image/')) {
                        const url = URL.createObjectURL(file);
                        content = `<img src="${url}" class="img-fluid rounded" style="height: 120px; object-fit: cover; width: 100%;">`;
                    } else {
                        const icon = getFileIcon(file.type);
                        content = `<div class="p-4 bg-light border rounded text-center">
                            <i class="${icon} fa-3x text-secondary mb-2"></i>
                        </div>`;
                    }
                    
                    div.innerHTML = `
                        <div class="card h-100">
                            <div class="card-body p-2">
                                ${content}
                                <h6 class="card-title mt-2 text-truncate" title="${file.name}">${file.name}</h6>
                                <small class="text-muted">${formatFileSize(file.size)}</small>
                                <button type="button" class="btn btn-sm btn-outline-danger mt-2 w-100" onclick="removeFile(${index})">
                                    <i class="fas fa-trash me-1"></i>Eliminar
                                </button>
                            </div>
                        </div>
                    `;
                    
                    preview.appendChild(div);
                });

                // Actualizar mensaje del área de carga
                if (filesArray.length > 0) {
                    uploadArea.querySelector('h5').textContent = `${filesArray.length} archivo(s) seleccionado(s)`;
                    uploadArea.querySelector('p').textContent = 'Arrastra más archivos o haz clic para agregar más';
                }
            }
        }

        // Configurar servicios de ubicación
        function setupLocationServices() {
            const getLocationBtn = document.getElementById('getLocationBtn');
            
            getLocationBtn.addEventListener('click', function() {
                if (!navigator.geolocation) {
                    showNotification('Tu navegador no soporta geolocalización.', 'error');
                    return;
                }

                this.innerHTML = '<i class="fas fa-spinner spinner me-2"></i>Obteniendo ubicación...';
                this.disabled = true;
                
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        document.getElementById('latitud').value = position.coords.latitude.toFixed(6);
                        document.getElementById('longitud').value = position.coords.longitude.toFixed(6);
                        
                        this.innerHTML = '<i class="fas fa-check me-2"></i>Ubicación Obtenida';
                        this.className = 'btn btn-success btn-lg';
                        
                        showNotification('Ubicación obtenida correctamente.', 'success');
                        
                        // Resetear botón después de 3 segundos
                        setTimeout(() => {
                            this.innerHTML = '<i class="fas fa-crosshairs me-2"></i>Actualizar Ubicación';
                            this.className = 'btn btn-outline-success btn-lg';
                            this.disabled = false;
                        }, 3000);
                    },
                    (error) => {
                        this.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Error al Obtener Ubicación';
                        this.className = 'btn btn-danger btn-lg';
                        this.disabled = false;
                        
                        let errorMessage = 'Error desconocido al obtener la ubicación.';
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage = 'Permiso denegado para acceder a la ubicación.';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage = 'Información de ubicación no disponible.';
                                break;
                            case error.TIMEOUT:
                                errorMessage = 'Tiempo de espera agotado al obtener la ubicación.';
                                break;
                        }
                        
                        showNotification(errorMessage, 'error');
                        
                        setTimeout(() => {
                            this.innerHTML = '<i class="fas fa-crosshairs me-2"></i>Reintentar Ubicación';
                            this.className = 'btn btn-outline-success btn-lg';
                        }, 3000);
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 60000
                    }
                );
            });
        }

        // Configurar contador de caracteres
        function setupCharacterCounter() {
            const descripcion = document.getElementById('descripcion');
            const charCount = document.getElementById('charCount');

            descripcion.addEventListener('input', function() {
                const count = this.value.length;
                charCount.textContent = `${count} caracteres`;
                
                if (count < 20) {
                    charCount.className = 'text-danger';
                } else if (count < 50) {
                    charCount.className = 'text-warning';
                } else {
                    charCount.className = 'text-success';
                }
            });
        }

        // Mostrar vista previa
        function showPreview() {
            const formData = new FormData(document.getElementById('denunciaForm'));
            const previewContent = document.getElementById('previewContent');
            
            let html = '<div class="row">';
            
            // Información del incidente
            html += `
                <div class="col-12 mb-4">
                    <h6 class="text-primary"><i class="fas fa-exclamation-triangle me-2"></i>Información del Incidente</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Tipo:</strong></td><td>${getSelectText('tipo')}</td></tr>
                        <tr><td><strong>Fecha:</strong></td><td>${formData.get('fecha') || 'No especificada'}</td></tr>
                        <tr><td><strong>Urgencia:</strong></td><td>${getSelectText('urgencia')}</td></tr>
                        <tr><td><strong>Área:</strong></td><td>${getSelectText('categoria_afectada') || 'No especificada'}</td></tr>
                    </table>
                    <p><strong>Descripción:</strong><br>${formData.get('descripcion')}</p>
                </div>
            `;
            
            // Ubicación
            html += `
                <div class="col-md-6 mb-4">
                    <h6 class="text-success"><i class="fas fa-map-marker-alt me-2"></i>Ubicación</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Dirección:</strong></td><td>${formData.get('direccion') || 'No especificada'}</td></tr>
                        <tr><td><strong>Municipio:</strong></td><td>${getSelectText('municipio')}</td></tr>
                        <tr><td><strong>Coordenadas:</strong></td><td>${formData.get('latitud') || 'N/A'}, ${formData.get('longitud') || 'N/A'}</td></tr>
                    </table>
                </div>
            `;
            
            // Contacto
            html += `
                <div class="col-md-6 mb-4">
                    <h6 class="text-info"><i class="fas fa-user me-2"></i>Contacto</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Nombre:</strong></td><td>${formData.get('nombre') || 'Anónimo'}</td></tr>
                        <tr><td><strong>Email:</strong></td><td>${formData.get('email') || 'No proporcionado'}</td></tr>
                        <tr><td><strong>Teléfono:</strong></td><td>${formData.get('contacto') || 'No proporcionado'}</td></tr>
                    </table>
                </div>
            `;
            
            html += '</div>';
            
            previewContent.innerHTML = html;
            
            const modal = new bootstrap.Modal(document.getElementById('previewModal'));
            modal.show();
        }

        // Funciones auxiliares
        function getSelectText(selectId) {
            const select = document.getElementById(selectId);
            return select.options[select.selectedIndex].text;
        }

        function getFileIcon(mimeType) {
            if (mimeType.includes('pdf')) return 'fas fa-file-pdf';
            if (mimeType.includes('word')) return 'fas fa-file-word';
            if (mimeType.includes('image')) return 'fas fa-file-image';
            return 'fas fa-file';
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        function isValidPhone(phone) {
            return /^[\+]?[\d\s\-\(\)]{7,}$/.test(phone);
        }

        function removeFile(index) {
            // Implementar lógica para remover archivo específico
            showNotification('Funcionalidad de eliminar archivo individual pendiente de implementar.', 'info');
        }

        function showNotification(message, type = 'info') {
            const alertClass = {
                'success': 'alert-success',
                'error': 'alert-danger',
                'warning': 'alert-warning',
                'info': 'alert-info'
            };

            const iconClass = {
                'success': 'fas fa-check-circle',
                'error': 'fas fa-exclamation-circle',
                'warning': 'fas fa-exclamation-triangle',
                'info': 'fas fa-info-circle'
            };

            const alert = document.createElement('div');
            alert.className = `alert ${alertClass[type]} alert-dismissible fade show position-fixed`;
            alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alert.innerHTML = `
                <i class="${iconClass[type]} me-2"></i>${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(alert);

            setTimeout(() => {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 5000);
        }

        // Confirmar envío desde modal
        document.getElementById('confirmSubmit').addEventListener('click', function() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('previewModal'));
            modal.hide();
            
            // Mostrar loading
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.innerHTML = '<i class="fas fa-spinner spinner me-2"></i>Enviando...';
            submitBtn.disabled = true;
            
            // Enviar formulario
            document.getElementById('denunciaForm').submit();
        });

        // Vista previa desde botón
        document.getElementById('previewBtn').addEventListener('click', function() {
            if (validateForm()) {
                showPreview();
            }
        });

        // Checkbox anónimo
        document.getElementById('anonimo').addEventListener('change', function() {
            const contactFields = ['nombre', 'email', 'contacto'];
            contactFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (this.checked) {
                    field.value = '';
                    field.disabled = true;
                    field.placeholder = 'Deshabilitado - Denuncia anónima';
                } else {
                    field.disabled = false;
                    field.placeholder = field.getAttribute('placeholder').replace('Deshabilitado - Denuncia anónima', '').trim();
                }
            });
        });
    </script>
</body>
</html>