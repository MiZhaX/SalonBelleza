<?php 
    namespace Controllers;

    class ErrorController{
        public static function show_Error404():string{
            return "<p>La página que buscas no existe</p>";
        }
    }
?>