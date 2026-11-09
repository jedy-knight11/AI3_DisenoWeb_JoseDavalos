<?php
// Copiar como config/config.php y ajustar a tu entorno local (XAMPP, WAMP, Laragon).
// config/config.php está en .gitignore para que las credenciales no se publiquen.
// Con Docker no hace falta: los datos llegan como variables de entorno.
return [
    'host'     => '127.0.0.1',
    'puerto'   => '3306',
    'base'     => 'db_sistema_web',
    'usuario'  => 'root',
    'clave'    => '',
];
