<?php

session_start();

if(!isset($_SESSION['usuario_id'])){
    header("Location: login.php");
    exit();
}

?>

<a href="logout.php">
    Sair
</a>