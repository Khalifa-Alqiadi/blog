<?php
session_start();
include "init.php";
if(isset($_SESSION['user'])){
    header("location: index.php");
}
$messageError = '';
if($_SERVER['REQUEST_METHOD']== 'POST'){
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    $hashPassword = sha1($password);

    $user = $db->table("users")->select("FullName", "Password")
                ->where("FullName", $username)
                ->where("Password", $hashPassword)->get();
    if($user){
        $_SESSION['user'] = $username;
        header("location: index.php");
    }else{
        $messageError = "Sorry, logged on, confirm the user name or password";
    }
}
?>

<div class="container d-flex flex-column align-items-center justify-content-center vh-100">
    <div class="col-sm-4 border p-3 bg-white">
        <h1 class="text-title mt-0 text-center">Login</h1>
        <?php
            if(!empty($messageError)){
                echo "<div class='alert alert-danger'>" . $messageError . "</div>";
            }
        ?>
        <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST">
            <div class="form-login-page mb-4">
                <input
                    title="Username Must Be 4 Characters"
                    class="form-control p-2"
                    type="text" 
                    name="username"
                    autocomplete="off"
                    required
                    placeholder="Your Full Name" />
            </div>
            <div class="form-login-page mb-4">
                <input 
                    class="form-control p-2" 
                    type="password"
                    name="password"
                    autocomplete="new-password"
                    required
                    placeholder="Your Password" />
            </div>
            <input class="btn btn-primary btn-block" type="submit" name="login" value="Login">
        </form>
    </div>
</div>