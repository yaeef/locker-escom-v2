<?php
// Configuración de la Base de Datos
define('DB_HOST', 'localhost');
define('DB_USER', 'yaef');
define('DB_PASS', 'alpine'); 
define('DB_NAME', 'locker_escom');

//Ruta de la aplicación (Carpeta app)
define('APPROOT', dirname(dirname(__FILE__)));

//URL Raíz (Para enlaces y redirecciones)
//USO: php -S 127.0.0.1:3000 -t public
define('URLROOT', 'http://127.0.0.1:3000');

define('SITENAME', 'Sistema de Lockers ESCOM');