<?php
    session_start(); // Inicia la sesión

    session_destroy(); // Destruye la sesión actual y elimina todas las variables de sesión

    header('location: ../'); // Redirige al usuario a la página principal del sitio web
