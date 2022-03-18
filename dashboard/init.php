<?php
    include 'database.php';
    $db = DB::getInstance();
    $tepl    = 'include/templates/'; 
    $css     = 'layout/css/'; 
    $js      = 'layout/js/';  


    include $tepl . 'header.php';
    if(isset($_SESSION['user'])){
        include $tepl . 'navbar.php';
    }
    
 