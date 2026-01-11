<?php
//Cargar la configuración primero (Define APPROOT)
require_once 'Config/config.php';

//Autoload 
spl_autoload_register(function($className) {
    // Definimos todas las carpetas donde pueden vivir nuestras clases
    $paths = [
        'Core',
        'Controllers',
        'Models',
        'Services'
    ];

    foreach ($paths as $path) {
        $file = APPROOT . '/' . $path . '/' . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return; 
        }
    }
});