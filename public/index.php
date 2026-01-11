<?php
    //Iniciar sesión al principio
    session_start();

    //SOPORTE PARA ARCHIVOS ESTÁTICOS (Linux/PHP Server) 
    // Si la petición apunta a un archivo que existe en /public, servirlo directamente
    $url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file(__DIR__ . $url_path)) {
        return false; // Detiene el script y PHP entrega el archivo (img, css, js)
    }


    //Cargar la configuración
    require_once __DIR__ . '/../app/Config/config.php';

    if (!isset($_GET['url'])) {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $_GET['url'] = ltrim($uri, '/');
    }

    //Autoload de clases
    spl_autoload_register(function($className) {
        $folders = ['Core', 'Controllers', 'Models'];
        foreach($folders as $folder) {
            $file = APPROOT . '/' . $folder . '/' . $className . '.php';
            if(file_exists($file)) {
                require_once $file;
                return;
            }
        }
    });

    //Inicializar el Router
    $init = new Router();