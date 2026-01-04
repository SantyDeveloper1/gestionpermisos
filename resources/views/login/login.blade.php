<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN - EPIIS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%), no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
            overflow: auto;
        }


        /* Contenedor de partículas */
        #particles-js {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 50px 60px;
            width: 100%;
            max-width: 480px;
            position: relative;
            z-index: 1;
            /* encima de partículas */
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo h1 {
            color: #0c4a6e;
            font-size: 48px;
            font-weight: bold;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }

        .logo-underline {
            display: flex;
            justify-content: center;
            gap: 3px;
            margin-top: 5px;
        }

        .logo-underline span {
            height: 4px;
            width: 40px;
        }

        .color-1 {
            background: #0944E5;
        }

        .color-2 {
            background: #f59e0b;
        }

        .color-3 {
            background: #b91010;
        }

        .color-4 {
            background: #ef4444;
        }

        .color-5 {
            background: #6b7280;
        }

        .title-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .title-section .icon-hands {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .title-section h2 {
            color: #0c4a6e;
            font-size: 22px;
            font-weight: 400;
            margin: 5px 0;
        }

        .title-section .beneficiario {
            color: #ec4899;
            font-weight: bold;
            font-size: 26px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group i.lock-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #0891b2;
            font-size: 18px;
            z-index: 1;
        }


        .form-group input {
            width: 100%;
            padding: 15px 45px 15px 45px;
            border: none;
            border-bottom: 2px solid #cbd5e1;
            font-size: 16px;
            outline: none;
            background: transparent;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            border-bottom-color: #0891b2;
        }

        .form-group input::placeholder {
            color: #64748b;
        }

        .password-field {
            position: relative;
        }

        .password-field input {
            padding-left: 45px;
            padding-right: 45px;
        }

        .password-field i.lock-icon {
            left: 15px;
        }

        .password-field i.toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #0891b2;
            font-size: 18px;
            z-index: 2;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
        }

        .btn-login {
            background: #0891b2;
            color: white;
            border: none;
            padding: 12px 35px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.3s;
        }

        .btn-login:hover {
            background: #0e7490;
        }

        .forgot-password {
            color: #0891b2;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }

        .forgot-password:hover {
            color: #0e7490;
        }

        .color-bar {
            display: flex;
            gap: 3px;
            margin-top: 30px;
            justify-content: center;
        }

        .color-bar span {
            height: 5px;
            flex: 1;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
        }

        .contact-info {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .contact-info a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-icons a {
            width: 45px;
            height: 45px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0891b2;
            font-size: 20px;
            transition: transform 0.3s, background 0.3s;
        }

        .social-icons a:hover {
            transform: translateY(-3px);
            background: #f0f9ff;
        }

        @media (max-width:600px) {
            .login-container {
                padding: 40px 30px;
            }

            .logo h1 {
                font-size: 36px;
            }

            .form-actions {
                flex-direction: column;
                gap: 15px;
            }

            .contact-info {
                flex-direction: column;
                gap: 10px;
            }
        }

        .footer-line {
            border: none;
            height: 2px;
            background: #ffffff;
            width: 150%;
            max-width: none;
            position: relative;
            left: 50%;
            transform: translateX(-50%);
            margin: 20px 0;
        }

        /* Contenedor general */
        .title-section {
            text-align: center;
            margin-bottom: 40px;
        }

        /* Contenedor horizontal: imagen a la izquierda, texto a la derecha */
        .title-inline {
            display: flex;
            align-items: center;
            /* centra verticalmente ambos */
            justify-content: center;
            gap: 20px;
            /* espacio entre imagen y texto */
        }

        /* Imagen del logo */
        .title-logo {
            width: 80px;
            /* 🔹 un poco más grande */
            height: auto;
            object-fit: contain;
            image-rendering: -webkit-optimize-contrast;
            transition: transform 0.3s ease;
        }

        /* Efecto suave al pasar el mouse */
        .title-logo:hover {
            transform: scale(1.05);
        }

        /* Texto del título */
        .title-text h2 {
            color: #0c4a6e;
            font-size: 22px;
            font-weight: 400;
            margin: 0;
            line-height: 1.1;
        }

        /* Segunda línea (LECTIVA) más resaltada */
        .title-text .beneficiario {
            color: #ec4899;
            font-weight: bold;
            font-size: 26px;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PNotify CSS -->
    <link rel="stylesheet" href="{{ asset('plugins/pnotify/pnotify.custom.min.css') }}">

</head>

<body>

    <!-- Contenedor de partículas -->
    <div id="particles-js"></div>

    <div class="login-container">
        <div class="logo">
            <h1>EPIIS</h1>
            <div class="logo-underline">
                <span class="color-1"></span>
                <span class="color-2"></span>
                <span class="color-3"></span>
                <span class="color-4"></span>
                <span class="color-5"></span>
            </div>
        </div>

        <div class="title-section">
            <div class="title-inline">
                <img src="{{asset('plugins/login/image/epiis.png')}}" alt="Logo EPIIS" class="title-logo" />
                <div class="title-text">
                    <h2>GESTION DE</h2>
                    <h2 class="beneficiario">PERMISOS</h2>
                </div>
            </div>
        </div>


        <form id="loginForm">
            <div class="form-group">
                <i class="fas fa-user lock-icon"></i>
                <input type="text" id="email" name="email" placeholder="Usuario">
            </div>

            <div class="form-group password-field">
                <i class="fas fa-lock lock-icon"></i>
                <input type="password" id="password" name="password" placeholder="Contraseña">
                <i class="fas fa-eye toggle-password" id="togglePassword"></i>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Ingresar
                </button>
                <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
            </div>

            <div class="color-bar">
                <span class="color-1"></span>
                <span class="color-2"></span>
                <span class="color-3"></span>
                <span class="color-4"></span>
                <span class="color-5"></span>
                <span style="background: #6b7280;"></span>
            </div>
        </form>
    </div>

    <div class="footer">
        <div class="contact-info">
            <a href="tel:08000018"><i class="fas fa-phone"></i> 0800-00018</a>
            <a href="tel:016128230"><i class="fas fa-phone"></i> (01) 612-8230</a>
            <a href="https://wa.me/914121106"><i class="fab fa-whatsapp"></i> 914 121 106</a>
        </div>

        <hr class="footer-line">

        <div class="social-icons">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-linkedin-in"></i></a>
            <a href="#"><i class="fab fa-youtube"></i></a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <script src="{{ asset('plugins/adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('plugins/adminlte/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script>
        $.widget.bridge('uibutton', $.ui.button)

        // Definir URLs globales para usar en JavaScript
        const LOGIN_URL = "{{ url('/login') }}";
        const HOME_URL = "{{ url('/') }}";
    </script>
    <script src="{{ asset('plugins/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('plugins/formvalidation/formValidation.min.js') }}"></script>
    <script src="{{ asset('plugins/formvalidation/bootstrap.validation.min.js') }}"></script>
    <script src="{{ asset('plugins/pnotify/pnotify.custom.min.js') }}"></script>
    <script src="{{ asset('plugins/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('viewresources/login/login.js?v=24122025b') }}"></script>
    <script>
        // Inicializar partículas
        particlesJS('particles-js', {
            particles: {
                number: { value: 80, density: { enable: true, value_area: 800 } },
                color: { value: ['#ffffff', '#3498db', '#1abc9c', '#2a5298'] },
                shape: { type: 'circle', stroke: { width: 0, color: '#000000' } },
                opacity: { value: 0.5, random: true, anim: { enable: true, speed: 1, opacity_min: 0.1, sync: false } },
                size: { value: 5, random: true, anim: { enable: true, speed: 2, size_min: 0.1, sync: false } },
                line_linked: { enable: true, distance: 150, color: '#ffffff', opacity: 0.2, width: 1 },
                move: { enable: true, speed: 1, direction: 'none', random: true, straight: false, out_mode: 'out', bounce: false }
            },
            interactivity: {
                detect_on: 'canvas',
                events: { onhover: { enable: true, mode: 'grab' }, onclick: { enable: true, mode: 'push' }, resize: true },
                modes: { grab: { distance: 140, line_linked: { opacity: 0.5 } }, push: { particles_nb: 4 } }
            },
            retina_detect: true
        });

        // Ojito funcional
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

    </script>
</body>
</html>