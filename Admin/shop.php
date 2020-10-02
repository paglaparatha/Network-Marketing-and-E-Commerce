<?php

include_once './creds.php';
session_start();

function compressImage($source, $destination, $quality)
{
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

    $sliders = $con->query("SELECT * FROM `shop-slider`")->fetch_all(MYSQLI_ASSOC);
    $categories = $con->query("SELECT * FROM `product-category`")->fetch_all(MYSQLI_ASSOC);
    $companies = $con->query("SELECT `name` FROM `companies`")->fetch_all(MYSQLI_ASSOC);

    if (isset($_POST['add-slider'])) {
        if (!empty($_FILES['file']['name'])) {
            $fileName = rand(1000, 10000) . '-' . basename($_FILES['file']['name']);
            $imageUploadpath = $uploadPath . $fileName;
            $fileType = pathinfo($imageUploadpath, PATHINFO_EXTENSION);
            if (in_array(strtolower($fileType), $allowTypes)) {
                $imageTemp = $_FILES['file']['tmp_name'];
                $compressedImage = compressImage($imageTemp, $imageUploadpath, 50);
                if ($con->query("INSERT INTO `shop-slider`(`image`) VALUES ('$compressedImage')")) {
                    echo "<script>alert(`Slider Image Added!`); window.location.href=`./shop.php`</script>";
                }
            } else {
                echo "<script>alert(`Upload Valid Image!`); window.location.href=`./shop.php`</script>";
            }
        } else {
            echo "<script>alert(`Upload Image!`); window.location.href=`./shop.php`</script>";
        }
    } elseif (isset($_GET['remove-slider'])) {
        $id = $con->real_escape_string(strip_tags($_GET['remove-slider']));

        $img = $con->query("SELECT `image` FROM `shop-slider` WHERE `id` = '$id'")->fetch_assoc()['image'];

        unlink('./' . $img);

        if ($con->query("DELETE FROM `shop-slider` WHERE `id` = '$id'")) {
            echo "<script>alert(`Slider Image Removed!`); window.location.href=`./shop.php`</script>";
        } else {
            echo "<script>alert(`Slider Image Not Found!`); window.location.href=`./shop.php`</script>";
        }
    } elseif (isset($_POST['add-category'])) {
        $name = $con->real_escape_string(strip_tags($_POST['name']));
        $name = ucfirst($name);

        if (!empty($_FILES['file']['name'])) {
            $fileName = rand(1000, 10000) . '-' . basename($_FILES['file']['name']);
            $imageUploadpath = $uploadPath . $fileName;
            $fileType = pathinfo($imageUploadpath, PATHINFO_EXTENSION);
            if (in_array(strtolower($fileType), $allowTypes)) {
                $imageTemp = $_FILES['file']['tmp_name'];
                $compressedImage = compressImage($imageTemp, $imageUploadpath, 50);
                if ($con->query("INSERT INTO `product-category`(`name`, `image`) VALUES ('$name', '$compressedImage')")) {
                    echo "<script>alert(`Category Added!`); window.location.href=`./shop.php`</script>";
                }
            } else {
                echo "<script>alert(`Upload Valid Image!`); window.location.href=`./shop.php`</script>";
            }
        } else {
            echo "<script>alert(`Upload Image!`); window.location.href=`./shop.php`</script>";
        }
    } elseif (isset($_POST['add-product'])) {
        $name = $con->real_escape_string(strip_tags($_POST['name']));
        $name = ucfirst($name);
        $company = $con->real_escape_string(strip_tags($_POST['company']));
        $category = $con->real_escape_string(strip_tags($_POST['category']));
        $description = $con->real_escape_string(strip_tags($_POST['description']));
        $price = $con->real_escape_string(strip_tags($_POST['price']));

        if (!empty($_FILES['file']['name'])) {
            $fileName = rand(1000, 10000) . '-' . basename($_FILES['file']['name']);
            $imageUploadpath = $uploadPath . $fileName;
            $fileType = pathinfo($imageUploadpath, PATHINFO_EXTENSION);
            if (in_array(strtolower($fileType), $allowTypes)) {
                $imageTemp = $_FILES['file']['tmp_name'];
                $compressedImage = compressImage($imageTemp, $imageUploadpath, 50);
                if ($con->query("INSERT INTO `products`(`name`, `company`, `category`, `description`, `price`, `image`) VALUES ('$name','$company','$category','$description','$price','$compressedImage')")) {
                    echo "<script>alert(`Product Added!`); window.location.href=`./shop.php`</script>";
                }
            } else {
                echo "<script>alert(`Upload Valid Image!`); window.location.href=`./shop.php`</script>";
            }
        } else {
            echo "<script>alert(`Upload Image!`); window.location.href=`./shop.php`</script>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include_once './meta.php' ?>
    <title>Network X Shop</title>
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
                        <form action="./shop.php" method="POST" class="was-validated" enctype="multipart/form-data">
                            <h2>Add Slider</h2>
                            <div class="form-group">
                                <input type="file" class="form-control" name="file" accept="image/*" required>
                                <div class="invalid-feedback">Please Upload an Image</div>
                            </div>
                            <input type="submit" value="Add" name="add-slider" class="btn btn-primary">
                        </form>
                    </div>

                    <div class="col-md-5">
                        <ul class="list-group">
                            <h2>Slider Images</h2>
                            <?php foreach ($sliders as $slider) : ?>
                                <li class="list-group-item">
                                    <img src="<?= $slider['image'] ?>" alt="" style="height: 7rem;">
                                    <br>
                                    <a href="./shop.php?remove-slider=<?= $slider['id'] ?>" class="btn btn-danger">Delete</a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="col-md-5">
                        <form action="./shop.php" method="post" class="was-validated" enctype="multipart/form-data">
                            <h2>Add Category</h2>
                            <div class="form-group">
                                <input type="text" name="name" required placeholder="Enter Name*" class="form-control">
                                <div class="invalid-feedback">Please fill this field</div>
                            </div>
                            <div class="form-group">
                                <input type="file" class="form-control" name="file" accept="image/*" required>
                                <div class="invalid-feedback">Please Upload an Image</div>
                            </div>
                            <input type="submit" value="Add" name="add-category" class="btn btn-primary">
                            <hr>
                            <ul>
                            <h2>All Categories</h2>
                            <?php foreach ($categories as $category) : ?>
                                <li class="list-group-item">
                                    <img src="<?= $category['image'] ?>" alt="" style="height: 7rem; width: 12rem">
                                    <br>
                                    <strong><?= $category['name'] ?></strong>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        </form>
                    </div>

                    <div class="col-md-5">
                        <form action="./shop.php" method="post" class="was-validated" enctype="multipart/form-data">
                            <h2>Add Product</h2>
                            <div class="form-group">
                                <input type="text" name="name" required placeholder="Enter Product Name*" class="form-control">
                                <div class="invalid-feedback">Please fill in this field</div>
                            </div>

                            <div class="form-group">
                                <select name="company" required class="form-control">
                                    <option value="" disabled selected>Select company*</option>
                                    <?php foreach ($companies as $company) : ?>
                                        <option value="<?= $company['name'] ?>"><?= $company['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Please select company</div>
                            </div>

                            <div class="form-group">
                                <select name="category" required class="form-control">
                                    <option value="" disabled selected>Select category*</option>
                                    <?php foreach ($categories as $category) : ?>
                                        <option value="<?= $category['name'] ?>"><?= $category['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Please select product category</div>
                            </div>

                            <div class="form-group">
                                <textarea name="description" rows="3" placeholder="Enter Product Description*" required class="form-control"></textarea>
                                <div class="invalid-feedback">Please Enter Description</div>
                            </div>

                            <div class="form-group">
                                <input type="number" name="price" required placeholder="Enter Product Price*" min="1" class="form-control">
                                <div class="invalid-feedback">Please Enter price in rupees</div>
                            </div>

                            <div class="form-group">
                                <input type="file" class="form-control" name="file" accept="image/*" required>
                                <div class="invalid-feedback">Please Upload an Image</div>
                            </div>
                            <input type="submit" value="Add" name="add-product" class="btn btn-primary">
                        </form>
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