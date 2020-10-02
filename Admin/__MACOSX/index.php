<?php

include_once './creds.php';
session_start();

function compressImage($source, $destination, $quality){
    $imgInfo = getimagesize($source);
    $mime = $imgInfo['mime'];

    switch ($mime) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($source);
            break;
        default:
            $image = imagecreatefromjpeg($source);
    }

    imagejpeg($image, $destination, $quality);

    return $destination;
}
$uploadPath = "uploads/";
$allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'tif');

if (!isset($_SESSION['email'])) {
    header("Location: ./login.php");
} else {
    $email = $_SESSION['email'];

    $admin = $con->query("SELECT * FROM `admins` WHERE `email` = '$email'")->fetch_assoc();
    $user_count = $con->query("SELECT count(`name`) AS `count` FROM `users`")->fetch_assoc()['count'];
    $product_count = $con->query("SELECT COUNT(`id`) AS `count` FROM `products`")->fetch_assoc()['count'];
    $com_count = $con->query("SELECT count(`name`) AS `count` FROM `companies`")->fetch_assoc()['count'];
    $all_companies = $con->query("SELECT * FROM `companies` ORDER BY `name`")->fetch_all(MYSQLI_ASSOC);

    $all_admins = $con->query("SELECT * FROM `admins`")->fetch_all(MYSQLI_ASSOC);

    if (isset($_POST['add-admin'])) {
        $email = $con->real_escape_string(strip_tags($_POST['email']));
        $password = $con->real_escape_string(strip_tags($_POST['password']));
        $name = $con->real_escape_string(strip_tags($_POST['name']));

        $password = password_hash($password, PASSWORD_BCRYPT);

        if ($con->query("INSERT INTO `admins`(`name`, `email`, `password`) VALUES ('$name', '$email', '$password')")) {
            echo "<script>alert(`Admin Added!`); window.location.href=`./index.php`</script>";
        } else {
            echo "<script>alert(`Email already exists!`); window.location.href=`./index.php`</script>";
        }
    } elseif (isset($_GET['remove-admin'])) {
        $id = $con->real_escape_string(strip_tags($_GET['remove-admin']));

        if ($con->query("DELETE FROM `admins` WHERE `id` = '$id'")) {
            echo "<script>alert(`Admin Removed!`); window.location.href=`./index.php`</script>";
        } else {
            echo "<script>alert(`Admin doesn't exist!`); window.location.href=`./index.php`</script>";
        }
    } elseif (isset($_POST['add-company'])) {
        $name = $con->real_escape_string(strip_tags($_POST['name']));

        if (!empty($_FILES['file']['name'])) {
            $fileName = rand(1000, 10000) . '-' . basename($_FILES['file']['name']);
            $imageUploadpath = $uploadPath . $fileName;
            $fileType = pathinfo($imageUploadpath, PATHINFO_EXTENSION);
            if (in_array(strtolower($fileType), $allowTypes)) {
                $imageTemp = $_FILES['file']['tmp_name'];
                $compressedImage = compressImage($imageTemp, $imageUploadpath, 50);
                if ($con->query("INSERT INTO `companies`(`name`, `image`) VALUES ('$name', '$compressedImage')")) {
                    echo "<script>alert(`Company Added!`); window.location.href=`./index.php`</script>";
                }
            } else {
                echo "<script>alert(`Upload Valid Image!`); window.location.href=`./index.php`</script>";
            }
        } else {
            echo "<script>alert(`Upload Image!`); window.location.href=`./index.php`</script>";
        }
    } elseif (isset($_GET['download-members'])) {
        function array2csv(array &$array)
        {
            if (count($array) == 0) {
                return null;
            }
            ob_start();
            $df = fopen("php://output", 'w');
            fputcsv($df, array_keys(reset($array)));
            foreach ($array as $row) {
                fputcsv($df, $row);
            }
            fclose($df);
            return ob_get_clean();
        }

        function download_send_headers($filename)
        {
            // disable caching
            $now = gmdate("D, d M Y H:i:s");
            header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
            header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
            header("Last-Modified: {$now} GMT");

            // force download  
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");

            // disposition / encoding on response body
            header("Content-Disposition: attachment;filename={$filename}");
            header("Content-Transfer-Encoding: binary");
        }
        $res = $con->query("SELECT `name`,`dob`,`email`,`aadhaar`,`mobile`,`my_ref`,`super_ref`,`company` FROM `users` ORDER BY `name`");
        $array = array();
        while ($row = mysqli_fetch_assoc($res)) {
            $array[] = $row;
        }
        download_send_headers("data_export_All_Members" . date("Y-m-d") . ".csv");
        echo array2csv($array);
        die();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once './meta.php' ?>
    <title>Network X Admin</title>
</head>

<body>
    <?php include_once './navbar.php' ?>

    <main>
        <h1 class="text-center">Welcome <?= $admin['name'] ?></h1>
        <div class="col-12">
            <div class="row">
                <div class="col-md-3 alert alert-info" onclick="downloadUsers()">
                    Total Users: <?= $user_count ?>
                </div>
                <div class="col-md-3 alert alert-info">
                    Total Companies: <?= $com_count ?>
                </div>
                <div class="col-md-3 alert alert-info">
                    Total Products: <?= $product_count ?>
                </div>
            </div>
        </div>

        <div class="jumbotron">
            <div class="col-12">
                <div class="row">

                    <div class="col-md-5">
                        <form action="./index.php" method="POST" class="was-validated">
                            <h2>Add Admin</h2>
                            <div class="form-group">
                                <input type="email" class="form-control" placeholder="Enter Email*" name="email" required>
                                <div class="invalid-feedback">Enter Valid Email</div>
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" placeholder="Enter Password*" name="password" minlength="8" required>
                                <div class="invalid-feedback">Enter a password with min 8 characters.</div>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" placeholder="Enter Name*" name="name" required>
                                <div class="invalid-feedback">Please fill this field</div>
                            </div>
                            <input type="submit" value="Add" name="add-admin" class="btn btn-primary">
                        </form>
                    </div>

                    <div class="col-md-5">
                        <ul class="list-group">
                            <h2>Remove Admin</h2>
                            <?php foreach ($all_admins as $adm) : ?>
                                <li class="list-group-item">
                                    <p><?= $adm['email'] ?></p>
                                    <?= $adm['email'] != $email ? '<a href="./index.php?remove-admin=' . $adm['id'] . '" class="btn btn-danger">Remove</a>' : '' ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="col-md-5">
                        <form action="./index.php" method="post" class="was-validated" enctype="multipart/form-data">
                            <h2>Add Company</h2>
                            <div class="form-group">
                                <input type="text" name="name" required placeholder="Enter Name*" class="form-control">
                                <p class="invalid-feedback">Enter a valid Name</p>
                            </div>
                            <input type="file" name="file" required accept="image/*" class="form-control">
                            <br>
                            <input type="submit" value="Add" name="add-company" class="btn btn-primary">
                        </form>
                    </div>

                    <div class="col-md-5">
                        <ul class="list-group">
                            <h2>All Companies</h2>
                            <?php foreach ($all_companies as $com) : ?>
                                <li class="list-group-item">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-4">
                                                <img src="<?= $com['image'] ?>" width="100%" style="width: 5rem; height: 5rem" alt="" class="img-thumbnail">
                                            </div>
                                            <div class="col-8">
                                                <?= $com['name'] ?>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </main>
</body>
<script>
    const downloadUsers = async () => {
        window.location.href = './index.php?download-members=true'
    }
</script>

</html>