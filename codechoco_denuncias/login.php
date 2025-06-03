<?php
session_start();

// Si ya está logueado, redirigir al panel
if (isset($_SESSION['admin_loggedin']) && $_SESSION['admin_loggedin'] === true) {
    header('Location: admin.php');
    exit();
}

$error_message = '';

// Procesar login
if ($_POST && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Credenciales de administrador (en producción usar BD con hash)
    // Usuario: admin, Contraseña: codechoco2024
    if ($username === 'admin' && $password === 'codechoco2024') {
        $_SESSION['admin_loggedin'] = true;
        $_SESSION['admin_username'] = $username;
        $_SESSION['login_time'] = time();
        header('Location: admin.php');
        exit();
    } else {
        $error_message = 'Credenciales incorrectas. Verifique su usuario y contraseña.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración - CodeChoco Quibdó</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --choco-primary: #2E5C3E;      /* Verde esmeralda del Chocó */
            --choco-secondary: #D4AF37;     /* Oro representativo */
            --choco-accent: #1B4D3E;        /* Verde más oscuro */
            --choco-light: #E8F5E8;         /* Verde muy claro */
            --choco-warning: #FF6B35;       /* Naranja cálido */
            --choco-dark: #1A1A1A;          /* Negro suave */
            --choco-gray: #6C757D;          /* Gris neutral */
            --choco-nature: #8B4513;        /* Marrón tierra del Chocó */
            --choco-river: #4682B4;         /* Azul de los ríos chocoanos */
            --choco-forest: #228B22;        /* Verde del bosque tropical */
            --gradient-primary: linear-gradient(135deg, var(--choco-forest) 0%, var(--choco-primary) 50%, var(--choco-accent) 100%);
            --gradient-secondary: linear-gradient(135deg, var(--choco-secondary) 0%, #B8941F 100%);
            --gradient-nature: linear-gradient(135deg, var(--choco-nature) 0%, #A0522D 100%);
            --gradient-river: linear-gradient(135deg, var(--choco-river) 0%, #5F9EA0 100%);
            --shadow-soft: 0 10px 40px rgba(46, 92, 62, 0.1);
            --shadow-medium: 0 15px 50px rgba(46, 92, 62, 0.15);
            --shadow-strong: 0 20px 60px rgba(46, 92, 62, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            /* Fondo que representa la naturaleza del Chocó */
            background: linear-gradient(135deg, 
                var(--choco-forest) 0%, 
                var(--choco-primary) 25%, 
                var(--choco-river) 50%, 
                var(--choco-nature) 75%, 
                var(--choco-accent) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Fondo animado que representa la biodiversidad del Chocó */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(212, 175, 55, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(70, 130, 180, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(34, 139, 34, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 60% 70%, rgba(139, 69, 19, 0.08) 0%, transparent 50%);
            animation: backgroundFloat 25s ease-in-out infinite;
            z-index: -1;
        }

        @keyframes backgroundFloat {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            25% { transform: translateY(-5px) rotate(0.5deg); }
            50% { transform: translateY(-10px) rotate(1deg); }
            75% { transform: translateY(-5px) rotate(0.5deg); }
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: var(--shadow-strong);
            overflow: hidden;
            max-width: 420px;
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
        }

        .login-header {
            background: var(--gradient-primary);
            color: white;
            padding: 40px 30px 30px;
            text-align: center;
            position: relative;
        }

        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: headerGlow 8s ease-in-out infinite;
        }

        @keyframes headerGlow {
            0%, 100% { transform: rotate(0deg); }
            50% { transform: rotate(180deg); }
        }

        .admin-logo {
            width: 90px;
            height: 90px;
            background: var(--gradient-secondary);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.3);
            position: relative;
            z-index: 2;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 3px solid rgba(255, 255, 255, 0.2);
        }

        .admin-logo::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: var(--gradient-primary);
            border-radius: 22px;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .admin-logo:hover {
            transform: scale(1.08) rotate(2deg);
            box-shadow: 0 15px 40px rgba(212, 175, 55, 0.4);
        }

        .admin-logo:hover::before {
            opacity: 1;
        }

        .admin-logo i {
            font-size: 2.5rem;
            color: white;
            text-shadow: 0 3px 15px rgba(0,0,0,0.3);
            position: relative;
        }

        .admin-logo i::after {
            content: '';
            position: absolute;
            top: -8px;
            right: -8px;
            width: 18px;
            height: 18px;
            background: #28a745;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.4);
        }

        .login-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 8px;
            position: relative;
            z-index: 2;
        }

        .login-subtitle {
            font-size: 0.95rem;
            opacity: 0.9;
            font-weight: 400;
            position: relative;
            z-index: 2;
        }

        .login-body {
            padding: 40px 30px;
        }

        .form-floating {
            margin-bottom: 24px;
            position: relative;
        }

        .form-control {
            border: 2px solid #E9ECEF;
            border-radius: 16px;
            padding: 20px 60px;  /* Padding centrado igual en ambos lados */
            font-size: 16px;
            font-weight: 500;
            background: #FAFBFC;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: 65px;
            text-align: center;  /* Centrar el texto */
        }

        .form-control:focus {
            border-color: var(--choco-primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(46, 92, 62, 0.1);
            transform: translateY(-2px);
        }

        .form-control:not(:placeholder-shown) {
            background: white;
        }

        .input-icon {
            position: absolute;
            left: 50%;  /* Centrar horizontalmente */
            top: 50%;
            transform: translate(-50%, -50%);  /* Centrar perfectamente */
            color: var(--choco-gray);
            font-size: 1.2rem;
            z-index: 3;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            width: 32px;  /* Aumentar tamaño para mejor centrado */
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;  /* Evitar interferencia con el input */
        }

        .input-icon.user-icon {
            background: var(--gradient-nature);  /* Colores del Chocó */
            border-radius: 50%;
            color: white;
            font-size: 0.95rem;
            box-shadow: 0 4px 12px rgba(139, 69, 19, 0.3);
        }

        .input-icon.password-icon {
            background: var(--gradient-river);  /* Azul de los ríos chocoanos */
            border-radius: 8px;
            color: white;
            font-size: 0.9rem;
            box-shadow: 0 4px 12px rgba(70, 130, 180, 0.3);
        }

        /* Mover el icono cuando el input está enfocado o tiene contenido */
        .form-control:focus ~ .input-icon,
        .form-control:not(:placeholder-shown) ~ .input-icon {
            left: 20px;  /* Mover a la izquierda */
            transform: translateY(-50%) scale(1.15);
        }

        .form-control:focus ~ .input-icon.user-icon {
            box-shadow: 0 6px 18px rgba(139, 69, 19, 0.5);
            background: linear-gradient(135deg, #A0522D, #8B4513);
        }

        .form-control:focus ~ .input-icon.password-icon {
            box-shadow: 0 6px 18px rgba(70, 130, 180, 0.5);
            background: linear-gradient(135deg, #5F9EA0, #4682B4);
        }

        /* Ajustar el texto del input cuando el icono se mueve */
        .form-control:focus,
        .form-control:not(:placeholder-shown) {
            text-align: left;  /* Alinear texto a la izquierda cuando hay contenido */
            padding-left: 60px;  /* Espacio para el icono */
            padding-right: 20px;
        }

        .form-label {
            font-weight: 600;
            color: var(--choco-dark);
            margin-bottom: 12px;
            font-size: 0.95rem;
        }

        .btn-login {
            background: var(--gradient-primary);
            border: none;
            border-radius: 16px;
            padding: 18px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-soft);
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-medium);
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:active {
            transform: translateY(-1px);
        }

        .alert {
            border-radius: 16px;
            border: none;
            padding: 16px 20px;
            margin-bottom: 24px;
            font-weight: 500;
            background: rgba(255, 107, 53, 0.1);
            color: var(--choco-warning);
            border-left: 4px solid var(--choco-warning);
        }

        .back-link {
            color: var(--choco-gray);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-link:hover {
            color: var(--choco-primary);
            transform: translateX(-3px);
        }

        .credentials-info {
            background: var(--choco-light);
            border-radius: 16px;
            padding: 20px;
            margin-top: 24px;
            border: 1px solid rgba(46, 92, 62, 0.1);
        }

        .credentials-info .badge {
            background: var(--choco-primary);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
        }

        /* Responsive Design */
        @media (max-width: 576px) {
            body {
                padding: 15px;
            }
            
            .login-container {
                max-width: 100%;
                border-radius: 20px;
            }
            
            .login-header {
                padding: 30px 20px 25px;
            }
            
            .login-body {
                padding: 30px 20px;
            }
            
            .admin-logo {
                width: 80px;
                height: 80px;
                margin-bottom: 15px;
            }
            
            .admin-logo i {
                font-size: 2rem;
            }
            
            .login-title {
                font-size: 1.3rem;
            }
            
            .form-control {
                padding: 18px 55px;
                height: 60px;
            }
            
            .form-control:focus,
            .form-control:not(:placeholder-shown) {
                padding-left: 55px;
                padding-right: 18px;
            }
            
            .input-icon {
                width: 28px;
                height: 28px;
                font-size: 1rem;
            }

            .form-control:focus ~ .input-icon,
            .form-control:not(:placeholder-shown) ~ .input-icon {
                left: 18px;
            }
        }

        @media (max-width: 375px) {
            .login-header {
                padding: 25px 15px 20px;
            }
            
            .login-body {
                padding: 25px 15px;
            }
            
            .form-control {
                font-size: 15px;
                padding: 16px 50px;
            }

            .form-control:focus,
            .form-control:not(:placeholder-shown) {
                padding-left: 50px;
                padding-right: 16px;
            }

            .input-icon {
                width: 26px;
                height: 26px;
            }

            .form-control:focus ~ .input-icon,
            .form-control:not(:placeholder-shown) ~ .input-icon {
                left: 16px;
            }
        }

        /* Animaciones de entrada */
        .login-container {
            animation: slideInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Estados de carga */
        .btn-login.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .btn-login.loading i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Mejoras de accesibilidad */
        .form-control:focus {
            outline: none;
        }

        .btn-login:focus {
            outline: 3px solid rgba(46, 92, 62, 0.3);
            outline-offset: 2px;
        }

        /* Efectos hover para móvil */
        @media (hover: none) {
            .btn-login:hover {
                transform: none;
                box-shadow: var(--shadow-soft);
            }
            
            .admin-logo:hover {
                transform: none;
            }
        }

        /* Animación de placeholder personalizada */
        @keyframes placeholderPulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        .form-control:placeholder-shown {
            animation: placeholderPulse 2s ease-in-out infinite;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="admin-logo">
                <i class="fas fa-user-cog"></i>
            </div>
            <h1 class="login-title">CodeChoco</h1>
            <p class="login-subtitle">Panel de Administración • Quibdó</p>
        </div>
        
        <div class="login-body">
            <?php if ($error_message): ?>
                <div class="alert d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <span><?= htmlspecialchars($error_message) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="" id="loginForm" novalidate>
                <div class="form-floating">
                    <label for="username" class="form-label">Usuario Administrativo</label>
                    <input type="text" 
                           class="form-control" 
                           id="username" 
                           name="username" 
                           placeholder="Ingrese su usuario"
                           value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
                           autocomplete="username"
                           required>
                    <i class="fas fa-user-tie input-icon user-icon"></i>
                </div>

                <div class="form-floating">
                    <label for="password" class="form-label">Contraseña de Acceso</label>
                    <input type="password" 
                           class="form-control" 
                           id="password" 
                           name="password" 
                           placeholder="Ingrese su contraseña"
                           autocomplete="current-password"
                           required>
                    <i class="fas fa-key input-icon password-icon"></i>
                </div>

                <button type="submit" name="login" class="btn btn-login" id="loginBtn">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    <span>Acceder al Panel</span>
                </button>
            </form>

            <div class="text-center mt-4">
                <a href="index.php" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                    <span>Volver al sitio principal</span>
                </a>
            </div>

            <!-- Información de credenciales (solo para desarrollo) -->
            <div class="credentials-info">
                <div class="d-flex align-items-center mb-2">
                    <span class="badge me-2">DESARROLLO</span>
                    <small class="text-muted fw-bold">Credenciales de Prueba</small>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <small class="text-muted d-block">Usuario:</small>
                        <code class="text-dark fw-bold">admin</code>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">Contraseña:</small>
                        <code class="text-dark fw-bold">codechoco2024</code>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Inicialización del formulario
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');

            // Validación en tiempo real
            function validateForm() {
                const username = usernameInput.value.trim();
                const password = passwordInput.value;
                
                if (username.length >= 3 && password.length >= 6) {
                    loginBtn.disabled = false;
                    loginBtn.style.opacity = '1';
                } else {
                    loginBtn.disabled = true;
                    loginBtn.style.opacity = '0.6';
                }
            }

            // Event listeners para validación
            usernameInput.addEventListener('input', validateForm);
            passwordInput.addEventListener('input', validateForm);

            // Validación inicial
            validateForm();

            // Efectos de focus mejorados
            const formControls = document.querySelectorAll('.form-control');
            formControls.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });

                // Efecto de escritura
                input.addEventListener('input', function() {
                    if (this.value) {
                        this.style.fontWeight = '600';
                    } else {
                        this.style.fontWeight = '500';
                    }
                });
            });

            // Manejo del envío del formulario
            form.addEventListener('submit', function(e) {
                const username = usernameInput.value.trim();
                const password = passwordInput.value;

                if (!username || !password) {
                    e.preventDefault();
                    showAlert('Por favor complete todos los campos requeridos.', 'warning');
                    return;
                }

                // Animación de carga
                loginBtn.classList.add('loading');
                loginBtn.innerHTML = '<i class="fas fa-spinner me-2"></i><span>Verificando acceso...</span>';
                
                // Simular delay para UX
                setTimeout(() => {
                    // El formulario se enviará normalmente
                }, 100);
            });

            // Función para mostrar alertas
            function showAlert(message, type = 'danger') {
                const existingAlert = document.querySelector('.alert');
                if (existingAlert) {
                    existingAlert.remove();
                }

                const alert = document.createElement('div');
                alert.className = `alert alert-${type} d-flex align-items-center`;
                alert.innerHTML = `
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <span>${message}</span>
                `;

                form.insertBefore(alert, form.firstChild);

                // Auto-remover después de 5 segundos
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 5000);
            }

            // Efecto de animación al cargar
            setTimeout(() => {
                document.querySelector('.login-container').style.transform = 'scale(1)';
            }, 100);

            // Atajos de teclado
            document.addEventListener('keydown', function(e) {
                // Enter para enviar formulario
                if (e.key === 'Enter' && (usernameInput === document.activeElement || passwordInput === document.activeElement)) {
                    if (!loginBtn.disabled) {
                        form.submit();
                    }
                }
                
                // Escape para limpiar campos
                if (e.key === 'Escape') {
                    usernameInput.value = '';
                    passwordInput.value = '';
                    usernameInput.focus();
                    validateForm();
                }
            });

            // Prevenir envío múltiple
            let submitting = false;
            form.addEventListener('submit', function(e) {
                if (submitting) {
                    e.preventDefault();
                    return false;
                }
                submitting = true;
            });
        });

        // Efecto de partículas sutil representando la biodiversidad del Chocó
        function createFloatingElement() {
            const colors = [
                'rgba(212, 175, 55, 0.3)',  // Oro
                'rgba(34, 139, 34, 0.3)',   // Verde bosque
                'rgba(70, 130, 180, 0.3)',  // Azul río
                'rgba(139, 69, 19, 0.3)'    // Marrón tierra
            ];
            
            const element = document.createElement('div');
            element.style.cssText = `
                position: absolute;
                width: 4px;
                height: 4px;
                background: ${colors[Math.floor(Math.random() * colors.length)]};
                border-radius: 50%;
                pointer-events: none;
                z-index: -1;
                animation: float-particle 10s linear infinite;
            `;
            
            element.style.left = Math.random() * 100 + '%';
            element.style.animationDelay = Math.random() * 10 + 's';
            
            document.body.appendChild(element);
            
            setTimeout(() => {
                if (element.parentNode) {
                    element.remove();
                }
            }, 10000);
        }

        // Crear partículas ocasionalmente
        setInterval(createFloatingElement, 4000);

        // CSS para partículas
        const style = document.createElement('style');
        style.textContent = `
            @keyframes float-particle {
                0% {
                    transform: translateY(100vh) rotate(0deg);
                    opacity: 0;
                }
                10% {
                    opacity: 1;
                }
                90% {
                    opacity: 1;
                }
                100% {
                    transform: translateY(-100px) rotate(360deg);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>