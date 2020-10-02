<?php

include_once './creds.php';
$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
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
    if (isset($_GET['page'])) {
        $page = $con->real_escape_string(strip_tags($_GET['page']));
    } else {
        $page = 1;
    }

    $email = $_SESSION['email'];
    $admin = $con->query("SELECT * FROM `admins` WHERE `email` = '$email'")->fetch_assoc();
    $user_count = $con->query("SELECT count(`name`) AS `count` FROM `users`")->fetch_assoc()['count'];
    $product_count = $con->query("SELECT COUNT(`id`) AS `count` FROM `products`")->fetch_assoc()['count'];
    $com_count = $con->query("SELECT count(`name`) AS `count` FROM `companies`")->fetch_assoc()['count'];
    $categories = $con->query("SELECT * FROM `product-category`")->fetch_all(MYSQLI_ASSOC);

    $limit = 6;
    $start = ($page - 1) * $limit;

    if (isset($_GET['search'])) {
        $search = $con->real_escape_string(strip_tags($_GET['search']));
        $result = $con->query("SELECT * FROM `products` WHERE `name` LIKE '%$search%' OR `company` LIKE '%$search%' ORDER BY `category` LIMIT $start, $limit");
        $count = $con->query("SELECT count(`id`) AS `id` FROM `products` WHERE `name` LIKE '$search' OR `company` LIKE '%$search%'");
    } else {
        $result = $con->query("SELECT * FROM `products` ORDER BY `category` LIMIT $start, $limit");
        $count = $con->query("SELECT count(`id`) AS `id` FROM `products`");
    }

    $result = $result->fetch_all(MYSQLI_ASSOC);

    $count = $count->fetch_all(MYSQLI_ASSOC);
    $count = $count[0]['id'];

    $pages = ceil($count / $limit);

    $prev = $page > 1 ? $page - 1 : 1;
    $next = $pages > ($page + 1) ? ($page + 1) : ($page == 1 ? 1 : $pages);

    if (isset($_POST['update-product'])) {
        $id = $con->real_escape_string(strip_tags($_POST['id']));
        $name = $con->real_escape_string(strip_tags($_POST['name']));
        $name = ucfirst($name);
        $category = $con->real_escape_string(strip_tags($_POST['category']));
        $description = $con->real_escape_string(strip_tags($_POST['description']));
        $price = $con->real_escape_string(strip_tags($_POST['price']));
        $available = $con->real_escape_string(strip_tags($_POST['available']));

        if ($con->query("UPDATE `products` SET `name`='$name',`category`='$category',`description`='$description',`price`='$price',`available`='$available' WHERE `id` = '$id'")) {
            echo "<script>alert(`Product Updated!`); window.location.href=`./products.php`</script>";
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
    <title>Newtork X Products</title>
</head>

<body>
    <?php include_once './navbar.php' ?>

    <main>
        <h1 class="text-center">Welcome <?= $admin['name'] ?></h1>
        <div class="col-12">
            <div class="row">
                <div class="col-md-3 alert alert-info">
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
        <?= isset($_GET['search']) ? '<h1 class="text-center">Showing results for' . $_GET['search'] . '</h1>'  : '<h1 class="text-center">All Products</h1>' ?>

            <form action="./products.php" method="GET" class="form-group">
                <div class="col-12">
                    <div class="row">
                        <div class="col-9">
                            <input type="text" name="search" class="form-control" id="search" placeholder="Enter Product Name or Company Name to search*" required>
                        </div>
                        <div class="col-3">
                            <button class="btn btn-primary">Search</button> 
                            &nbsp; &nbsp;
                            <a href="./products.php" type="button" class="btn btn-danger" id="reset">Reset</a>
                        </div>
                    </div>
                </div>
            </form>
            <div class="col-12">
                <div class="row my-products">
                    <?php foreach ($result as $product) : ?>
                        <div class="col-md-4 col-6">
                            <img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
                            <form action="./products.php" method="post" class="was-validated" style="<?= $product['available'] == 0 ? 'border: 4px solid firebrick;' : 'border: 4px solid teal;' ?>">
                                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                <div class="form-group">
                                    <input type="text" name="name" value="<?= $product['name'] ?>" required placeholder="Enter Product Name*" class="form-control">
                                    <div class="invalid-feedback">Please fill in this field</div>
                                </div>

                                <div class="form-group">
                                    <select name="category" required class="form-control">
                                        <option value="" disabled selected>Select category*</option>
                                        <?php foreach ($categories as $category) : ?>
                                            <option value="<?= $category['name'] ?>" <?= $product['category'] === $category['name'] ? 'selected' : '' ?>><?= $category['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Please select product category</div>
                                </div>

                                <div class="form-group">
                                    <textarea name="description" rows="3" placeholder="Enter Product Description*" required class="form-control"><?= $product['description'] ?></textarea>
                                    <div class="invalid-feedback">Please Enter Description</div>
                                </div>

                                <div class="form-group">
                                    <input type="number" name="price" value="<?= $product['price'] ?>" required placeholder="Enter Product Price*" min="1" class="form-control">
                                    <div class="invalid-feedback">Please Enter price in rupees</div>
                                </div>

                                <div class="form-group">
                                    <select name="available" required class="form-control">
                                        <option value="" disabled selected>Select Availability*</option>
                                        <option value="0" <?= $product['available'] == 0 ? 'selected' : '' ?>>Unavailable</option>
                                        <option value="1" <?= $product['available'] == 1 ? 'selected' : '' ?>>Available</option>
                                    </select>
                                    <div class="invalid-feedback">Please select product category</div>
                                </div>

                                <input type="submit" value="Update" name="update-product" class="btn btn-primary">
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="container">
                <nav aria-label="Page navigation example" class="table-responsive mb-2">
                    <ul class="pagination">
                        <li class="page-item">
                            <a class="page-link" href="<?= $actual_link; ?><?= isset($_GET['search']) ? '' : '?';  ?>&page=<?= $prev; ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php for ($i = $page == 1 ? 1 : ($pages >= 10 ? $page - 1 : 1), $j = $i + 10; $i <= $pages && $i < $j; $i++) : ?>
                            <li class="page-item <?= $page == $i ? 'active' : ''; ?>"><a class="page-link" href="<?= $actual_link; ?><?= isset($_GET['search']) ? '' : '?';  ?>&page=<?= $i; ?>"><?= $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= $actual_link; ?><?= isset($_GET['search']) ? '' : '?';  ?>&page=<?= $next; ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </main>
</body>

</html>