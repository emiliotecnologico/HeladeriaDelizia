<?php
session_start();
error_reporting(E_PARSE);

// HEADERS PARA PREVENIR CACHE
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Si el usuario ya está autenticado, redirigir al index inmediatamente
if (!empty($_SESSION['nombreAdmin']) || !empty($_SESSION['nombreUser'])) {
    header("Location: index.php");
    exit();
}

// Procesar el modo invitado
if (isset($_GET['invitado']) && $_GET['invitado'] == '1') {
    $_SESSION['esInvitado'] = true;
    header("Location: index.php");
    exit();
}

// Manejar errores de login
$errorMsg = "";
if (isset($_GET['error'])) {
    if ($_GET['error'] == 1) {
        $errorMsg = "Nombre de usuario o contraseña incorrectos";
    } elseif ($_GET['error'] == 2) {
        $errorMsg = "Error, no se permiten campos vacíos";
    }
}

// FORZAR REGENERACIÓN DE ID DE SESIÓN
session_regenerate_id(true);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Iniciar Sesión</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <!-- Bootstrap CSS Local -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    
    <!-- Font Awesome Local -->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    
    <style>
        body {
            background-color: #ecf0f5;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-image: url('assets/img/font-index.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            position: relative;
            height: 100vh;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        
        /* Overlay para mejorar legibilidad */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.92);
            z-index: -1;
        }
        
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
        }
        
        .login-box {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 6px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 500px;
            padding: 30px;
            border-top: 5px solid #3c8dbc;
            margin: 0 auto;
        }
        
        .login-logo {
            text-align: center;
            margin-bottom: 25px;
        }
        
        .login-logo img {
            height: 90px;
            margin-bottom: 10px;
            transition: transform 0.3s ease;
        }
        
        .login-logo img:hover {
            transform: scale(1.05);
        }
        
        .login-title {
            text-align: center;
            color: #3c8dbc;
            margin-bottom: 25px;
            font-weight: bold;
            font-size: 28px;
        }
        
        .login-subtitle {
            text-align: center;
            color: #666;
            font-size: 18px;
            margin-bottom: 25px;
            font-weight: bold;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 25px;
            color: #666;
            font-size: 14px;
        }
        
        /* Estilos para las dos columnas */
        .login-content {
            display: flex;
            gap: 25px;
            align-items: flex-start;
        }
        
        .form-column {
            flex: 1.5;
            min-width: 0;
        }
        
        .buttons-column {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 15px;
            justify-content: center;
            min-width: 0;
        }
        
        /* BOTONES - AUMENTADOS */
        .login-btn {
            border: none;
            color: white;
            width: 100%;
            font-weight: bold;
            padding: 16px 12px;
            font-size: 18px;
            border-radius: 6px;
            transition: all 0.3s ease;
            box-shadow: 0 3px 6px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            white-space: nowrap;
            min-width: 0;
            flex-shrink: 0;
            height: 55px;
            box-sizing: border-box;
            cursor: pointer;
        }
        
        .btn-ingresar {
            background: linear-gradient(135deg, #3c8dbc, #367fa9);
        }
        
        .btn-ingresar:hover {
            background: linear-gradient(135deg, #367fa9, #2c6d9c);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 12px rgba(0,0,0,0.25);
        }

        .btn-invitado {
            background: linear-gradient(135deg, #28a745, #218838);
        }
        
        .btn-invitado:hover {
            background: linear-gradient(135deg, #218838, #1e7e34);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 12px rgba(0,0,0,0.25);
        }
        
        /* CAMPOS DE FORMULARIO - AUMENTADOS */
        .form-control {
            border-radius: 6px;
            padding: 16px 18px;
            font-size: 18px;
            height: auto;
            border: 2px solid #ddd;
            transition: all 0.3s ease;
            width: 100%;
            box-sizing: border-box;
            background-color: white;
        }
        
        .form-control:focus {
            border-color: #3c8dbc;
            box-shadow: 0 0 0 3px rgba(60, 141, 188, 0.25);
            outline: none;
        }
        
        .form-group {
            margin-bottom: 35px;
            position: relative;
            width: 100%;
        }
        
        .help-block {
            color: #a94442;
            font-size: 14px;
        }
        
        .login-footer a {
            color: #3c8dbc;
            font-weight: bold;
            text-decoration: none;
            transition: color 0.3s ease;
            font-size: 14px;
        }
        
        .login-footer a:hover {
            color: #367fa9;
            text-decoration: underline;
        }
        
        /* SEPARACIÓN AUMENTADA */
        .label-floating {
            position: relative;
            width: 100%;
        }
        
        .label-floating label {
            font-size: 18px;
            color: #555;
            font-weight: 600;
            position: absolute;
            top: -28px;
            left: 0;
            background: transparent;
            padding: 0;
            z-index: 2;
            transition: all 0.2s ease;
            pointer-events: none;
            margin-bottom: 10px;
            display: block;
            width: 100%;
        }
        
        .label-floating.is-focused label {
            color: #3c8dbc;
            font-weight: bold;
        }
        
        /* Espacio adicional entre el label y el campo de entrada */
        .label-floating .form-control {
            margin-top: 10px;
        }
        
        /* CORRECCIÓN ESPECÍFICA PARA EL CAMPO DE CONTRASEÑA */
        .input-group {
            position: relative;
            margin-top: 10px;
        }
        
        .input-group .form-control {
            padding-right: 55px;
            margin-top: 0;
        }
        
        .input-group-addon {
            background-color: transparent;
            border: none;
            cursor: pointer;
            padding: 12px 15px;
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            z-index: 3;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .input-group-addon .glyphicon {
            color: #666;
            font-size: 18px;
        }
        
        .input-group-addon:hover .glyphicon {
            color: #333;
        }

        /* Estilos de respaldo */
        .alert {
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid transparent;
            border-radius: 6px;
            font-size: 16px;
        }
        
        .alert-danger {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
        
        .fa {
            display: inline-block;
            font: normal normal normal 16px/1 FontAwesome;
            font-size: inherit;
            text-rendering: auto;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            margin-right: 8px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 480px) {
            .login-box {
                padding: 25px 20px;
                margin: 15px;
            }
            
            .login-content {
                flex-direction: column;
                gap: 20px;
            }
            
            .buttons-column {
                flex-direction: row;
                flex-wrap: wrap;
                width: 100%;
            }
            
            .buttons-column .login-btn {
                flex: 1;
                min-width: 140px;
                margin: 0 8px;
                font-size: 16px;
                padding: 14px 8px;
                height: 50px;
            }
            
            .login-logo img {
                height: 75px;
            }
            
            .login-title {
                font-size: 24px;
            }
            
            .login-subtitle {
                font-size: 16px;
            }
            
            /* Ajustes responsive para la separación aumentada */
            .label-floating label {
                top: -24px;
                font-size: 16px;
            }
            
            .form-group {
                margin-bottom: 30px;
            }
            
            .form-control {
                padding: 14px 16px;
                font-size: 16px;
            }
        }
        
        @media (max-width: 360px) {
            .login-box {
                padding: 20px 15px;
            }
            
            .buttons-column .login-btn {
                font-size: 14px;
                padding: 12px 6px;
                height: 48px;
            }
            
            .login-title {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-logo">
                <img src="assets/img/logo_delizia-1.png" alt="Logo Delizia">
                <div class="login-subtitle">Tienda Oruro-Central</div>
            </div>
            
            <h3 class="login-title">Iniciar Sesión</h3>
            
            <!-- Mostrar mensaje de error si existe -->
            <?php if (!empty($errorMsg)): ?>
            <div class="alert alert-danger" style="margin-bottom: 25px;">
                <i class="fa fa-exclamation-triangle"></i> <?php echo $errorMsg; ?>
            </div>
            <?php endif; ?>
            
            <div class="login-content">
                <div class="form-column">
                    <!-- FORMULARIO TRADICIONAL - SIN AJAX -->
                    <form action="process/procesar_login.php" method="post" role="form" class="FormCatElec" id="loginForm">
                        <div class="form-group label-floating">
                            <label class="control-label"><span class="glyphicon glyphicon-user"></span>&nbsp;Usuario</label>
                            <input type="text" class="form-control" name="nombre-login" required autocomplete="username">
                            <span class="help-block"></span>
                        </div>
                        
                        <div class="form-group label-floating">
                            <label class="control-label"><span class="glyphicon glyphicon-lock"></span>&nbsp;Contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="clave-login" id="password-input" required autocomplete="current-password">
                                <span class="input-group-addon" id="password-toggle">
                                    <span class="glyphicon glyphicon-eye-open"></span>
                                </span>
                            </div>
                            <span class="help-block"></span>
                        </div>
                        
                        <!-- BOTÓN DE ENVÍO DENTRO DEL FORMULARIO -->
                        <button type="submit" class="login-btn btn-ingresar" style="display: none;" id="submitBtn">
                            <i class="fa fa-sign-in"></i> Ingresar
                        </button>
                    </form>
                </div>

                <div class="buttons-column">
                    <!-- BOTÓN QUE DISPARA EL ENVÍO DEL FORMULARIO -->
                    <button type="button" class="login-btn btn-ingresar" onclick="document.getElementById('submitBtn').click()">
                        <i class="fa fa-sign-in"></i> Ingresar
                    </button>
                    
                    <a href="login.php?invitado=1" class="login-btn btn-invitado">
                        <i class="fa fa-user-secret"></i> Invitado
                    </a>
                </div>
            </div>
            
            <div class="login-footer">
                <p style="margin-top: 20px; font-size: 13px; color: #999;">&copy; <?php echo date('Y'); ?> Delizia Oruro</p>
            </div>
        </div>
    </div>

    <!-- jQuery Local -->
    <script src="js/jquery.min.js"></script>
    
    <!-- Bootstrap JS Local -->
    <script src="js/bootstrap.min.js"></script>
    
    <script>
    // SCRIPT SIMPLIFICADO - SOLO FUNCIONALIDADES BÁSICAS
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🔐 Login cargado - Formulario tradicional');
        
        // 1. Focus effects manuales
        var formControls = document.querySelectorAll('.form-control');
        formControls.forEach(function(control) {
            control.addEventListener('focus', function() {
                this.parentElement.parentElement.classList.add('is-focused');
            });
            
            control.addEventListener('blur', function() {
                if (this.value === '') {
                    this.parentElement.parentElement.classList.remove('is-focused');
                }
            });
            
            // Estado inicial
            if (control.value !== '') {
                control.parentElement.parentElement.classList.add('is-focused');
            }
        });
        
        // 2. Toggle password visibility
        var passwordToggle = document.getElementById('password-toggle');
        if (passwordToggle) {
            passwordToggle.addEventListener('click', function() {
                var passwordInput = document.getElementById('password-input');
                var icon = this.querySelector('.glyphicon');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('glyphicon-eye-open');
                    icon.classList.add('glyphicon-eye-close');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('glyphicon-eye-close');
                    icon.classList.add('glyphicon-eye-open');
                }
            });
        }
        
        // 3. Animación simple
        var loginBox = document.querySelector('.login-box');
        if (loginBox) {
            loginBox.style.opacity = '0';
            loginBox.style.transition = 'opacity 0.6s ease';
            setTimeout(function() {
                loginBox.style.opacity = '1';
            }, 100);
        }
    });

    // PREVENIR CUALQUIER INICIALIZACIÓN DE MATERIAL DESIGN EN LOGIN
    console.log('🚫 Material Design no cargado en login - Previniendo conflictos');
    </script>
</body>
</html>