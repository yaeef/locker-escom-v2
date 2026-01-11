<?php
    class Router {
        protected $currentController = 'Paginas'; // Controlador por defecto
        protected $currentMethod = 'index';             // Método por defecto
        protected $params = [];

        public function __construct() {
            $url = $this->getUrl();

            //Buscar el controlador en la carpeta Controllers
            if (isset($url[0])) {
                $controllerName = ucwords($url[0]) . 'Controller';
                if (file_exists(APPROOT . '/Controllers/' . $controllerName . '.php')) {
                    $this->currentController = $controllerName;
                    unset($url[0]);
                }
            }

            //Instanciar el controlador
            $this->currentController = new $this->currentController;

            //Buscar el método dentro del controlador
            if (isset($url[1])) {
                if (method_exists($this->currentController, $url[1])) {
                    $this->currentMethod = $url[1];
                    unset($url[1]);
                }
            }

            //Obtener los parámetros restantes
            $this->params = $url ? array_values($url) : [];

            //Llamar al método con los parámetros
            call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
        }

        public function getUrl() {
            if (isset($_GET['url'])) {
                $url = rtrim($_GET['url'], '/');
                $url = filter_var($url, FILTER_SANITIZE_URL);
                $url = explode('/', $url);
                return $url;
            }
            return [];
        }
    }


?>