<?php
session_start();
if(isset($_SESSION['user'])){
    include "init.php"
?>


    <div class="container mt-5">
        <div class='alert alert-info'>  Home Page Empty</div>
    </div>

<?php

}else{
    header("location: login.php");
}

?>