<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITENAME; ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <style>
        :root {
            --ipn-maroon: #621132;
            --escom-blue: #003366;
            --soft-gray: #f4f7f6;
            --text-dark: #2d3436;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--soft-gray);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navbar Estilo Clean */
        .navbar-clean {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 12px 0;
            z-index: 1050;
        }

        .brand-logo {
            font-weight: 700;
            font-size: 1.4rem;
            color: var(--escom-blue) !important;
            letter-spacing: -0.5px;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        /* Lógica del cursor para el logo */
        .brand-logo[href="#"] {
            cursor: default;
        }

        .brand-dot {
            color: var(--ipn-maroon);
        }

        .nav-link {
            color: #636e72 !important;
            font-weight: 500;
            padding: 0.5rem 1.2rem !important;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: var(--escom-blue) !important;
            background-color: rgba(0, 51, 102, 0.05);
        }

        .btn-ipn {
            background-color: var(--ipn-maroon);
            color: white !important;
            border-radius: 50px;
            padding: 8px 24px;
            font-weight: 600;
            border: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-ipn:hover {
            background-color: #4a0d26;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(98, 17, 50, 0.2);
        }

        /* Animación para el badge de notificaciones del sidebar */
        .badge-notify {
            font-size: 0.7rem;
            padding: 0.4em 0.6em;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-clean sticky-top">
    <div class="container">
        <?php 
            // El logo ahora siempre puede llevar al Inicio público o al index según prefieras
            $linkLogo = URLROOT . '/paginas/inicio'; 
            if (isset($_SESSION['user_id'])) {
                if ($_SESSION['rol'] == 'admin') {
                    $linkLogo = URLROOT . '/admin/index';
                }
            }
        ?>
        
        <a class="navbar-brand brand-logo" href="<?= $linkLogo; ?>">
            <i class="fas fa-box-open me-2"></i>Locker<span class="brand-dot">.</span>ESCOM
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <i class="fas fa-bars"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <a class="nav-link" href="<?= URLROOT; ?>/paginas/inicio">
                        <i class="fas fa-home me-1"></i>Inicio
                    </a>
                </li>

                <?php if(isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= ($_SESSION['rol'] == 'admin') ? URLROOT.'/admin/index' : URLROOT.'/alumno/index'; ?>">
                            <i class="fas fa-columns me-1"></i>Panel Control
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn-ipn text-decoration-none shadow-sm" href="<?= URLROOT; ?>/auth/logout">
                            <i class="fas fa-sign-out-alt me-2"></i>Salir
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= URLROOT; ?>/auth/login">Ingresar</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn-ipn text-decoration-none shadow-sm" href="<?= URLROOT; ?>/auth/registro">
                            Comenzar Registro
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="pt-4"></div>
