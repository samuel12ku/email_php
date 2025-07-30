<?php
session_start();
session_unset();
session_destroy();
header("Location: ../../login.html"); // O ajusta la ruta si es diferente en tu proyecto
exit;
