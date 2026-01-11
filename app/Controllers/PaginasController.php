<?php

class PaginasController {
    
    public function __construct() {

    }

    public function index() {
        $data = [
            'titulo' => 'Inicio - Lockers ESCOM'
        ];
        
        //Carga Manual de Header y Footer
        require_once APPROOT . '/Views/layout/header.php';
        require_once APPROOT . '/Views/paginas/inicio.php';
        require_once APPROOT . '/Views/layout/footer.php';
    }

    //Por si el router llama a /paginas/inicio
    public function inicio() {
        $this->index();
    }
}