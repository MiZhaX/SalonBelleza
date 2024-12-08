<?php 
    namespace Controllers;

    use Lib\Pages;
    use Services\ServicioService;

    class ServicioController
    {
        private ServicioService $servicioService;
        private Pages $pages;

        function __construct() {
            $this->servicioService = new ServicioService();
            $this->pages = new Pages();
        }

        public function mostrarTodos(){
            $servicios = $this->servicioService->obtenerTodos();

            $this->pages->render('Servicio/mostrarServicios', ['servicios' => $servicios]);
        }
    }

?>