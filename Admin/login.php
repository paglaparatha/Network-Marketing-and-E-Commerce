<?php

include_once './creds.php';
session_start();

if (isset($_POST['login'])) {
    $email = $con->real_escape_string(strip_tags($_POST['email']));
    $password = $con->real_escape_string(strip_tags($_POST['password']));

    $user = $con->query("SELECT * FROM `admins` WHERE `email` = '$email'");
    if ($user->num_rows == 1) {
        $user = $user->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['email'] = $email;
            header("Location: ./index.php");
        } else {
            echo "<script>alert(`Invalid Password!`)</script>";
        }
    } else {
        echo "<script>alert(`Invalid Email!`)</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once './meta.php' ?>
    <title>Login</title>
</head>

<body>
    <main>
        <div class="container container-fluid">
            <div class="row">
                <div class="col-md-6 login-screen" style="margin: auto;">
                    <h2 class="text-center">Login</h2>
                    <form action="./login.php" method="POST" class="form-group">
                        <input type="email" name="email" required placeholder="Enter Email*" class="form-control">
                        <br>
                        <input type="password" name="password" required placeholder="Enter Password*" class="form-control">
                        <br>
                        <input type="submit" value="Login" name="login" class="btn btn-primary">
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>

</html>