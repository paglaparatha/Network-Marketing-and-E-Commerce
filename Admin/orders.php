<?php

include_once './creds.php';
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ./login.php");
} else {

    if (isset($_GET['page'])) {
        $page = $con->real_escape_string(strip_tags($_GET['page']));
    } else {
        $page = 1;
    }

    $limit = 25;
    $start = ($page - 1) * $limit;

    $email = $_SESSION['email'];
    $admin = $con->query("SELECT * FROM `admins` WHERE `email` = '$email'")->fetch_assoc();

    if (isset($_GET['search'])) {
        $search = $con->real_escape_string(strip_tags($_GET['search']));
        $result = $con->query("SELECT o.*, CONCAT(a.`house`,'<br/>', a.`area`, '<br/>', a.`town`, '<br/>Landmark: ', a.`landmark`, '<br/>', a.`state`,', ',a.`pin`) AS `address`, u.`name`, u.`company`, u.`email`, p.`name` AS `product`, p.`price` AS `price` FROM `orders` o INNER JOIN `users` u ON o.`user_id` = u.`id` INNER JOIN `addresses` a ON a.`id` = o.`address_id` INNER JOIN `products` p ON o.`product_id` = p.`id` WHERE o.`order_id` = '$search' AND o.`delivered` = '0' ORDER BY o.`date` LIMIT $start, $limit");
        $count = $con->query("SELECT COUNT(o.`id`) AS `id` FROM `orders` o INNER JOIN `users` u ON o.`user_id` = u.`id` INNER JOIN `addresses` a ON a.`id` = o.`address_id` INNER JOIN `products` p ON o.`product_id` = p.`id` WHERE o.`order_id` = '$search'");
    } else {
        $result = $con->query("SELECT o.*, CONCAT(a.`house`,'<br/>', a.`area`, '<br/>', a.`town`, '<br/>Landmark: ', a.`landmark`, '<br/>', a.`state`,', ',a.`pin`) AS `address`, u.`name`, u.`company`, u.`email`, p.`name` AS `product`, p.`price` AS `price` FROM `orders` o INNER JOIN `users` u ON o.`user_id` = u.`id` INNER JOIN `addresses` a ON a.`id` = o.`address_id` INNER JOIN `products` p ON o.`product_id` = p.`id` WHERE o.`delivered` = '0' ORDER BY o.`date` LIMIT $start, $limit");
        $count = $con->query("SELECT COUNT(o.`id`) AS `id` FROM `orders` o INNER JOIN `users` u ON o.`user_id` = u.`id` INNER JOIN `addresses` a ON a.`id` = o.`address_id` INNER JOIN `products` p ON o.`product_id` = p.`id`");
    }

    $result = $result->fetch_all(MYSQLI_ASSOC);

    $count = $count->fetch_all(MYSQLI_ASSOC);
    $count = $count[0]['id'];

    $pages = ceil($count / $limit);

    $prev = $page > 1 ? $page - 1 : 1;
    $next = $pages > ($page + 1) ? ($page + 1) : ($page == 1 ? 1 : $pages);

    if (isset($_POST['deliver'])) {
        
        if (isset($_POST['order_id'])) {
            $subject = "Product Delivered!";
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            
            foreach ($_POST['order_id'] as $order) {
                $email = $con->query("SELECT u.`email` FROM `users` u INNER JOIN `orders` o ON o.`user_id` = u.`id`")->fetch_assoc()['email'];
                $product = $con->query("SELECT p.*, o.`order_id` FROM `products` p INNER JOIN `orders` o ON o.`product_id` = p.`id` WHERE o.`id` = '$order'")->fetch_assoc();
                
                $message = "
                <html>
                    <head>
                        <title>
                            Product Delivered!
                        </title>
                    </head>
                    <body>
                        <h2>
                            Hey, ".$email.",<br />
                            Your order, <b>".$product['name']."</b> by ".$product['company']." has been delivered today.
                            <br />
                            Order Id: ".$product['order_id']."<br />
                            For more details or queries contact ".$product['company'].".<br />
                            Regards,<br />
                            Network X
                        </h2>
                    </body>
                </html>
                ";
                mail($email, $subject, $message, $headers);
                
                $con->query("UPDATE `orders` SET `delivered` = 1 WHERE `id` = '$order'");
            }
        }
        
        echo "<script>alert(`Products marked as delivered!`); window.location.href=`./orders.php`;</script>";
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
    <style>
        table {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            min-width: 100%;
        }

        table td,
        table th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tr:hover {
            background-color: #ddd;
        }

        table th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #4CAF50;
            color: white;
        }
    </style>
</head>

<body>
    <?php include_once './navbar.php' ?>

    <div class="modal" id="myModal">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title" id="modalTitle">Modal Heading</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body" id="modalBody">
                    Modal body..
                </div>

            </div>
        </div>
    </div>

    <main>
        <div class="jumbotron">
            <?= isset($_GET['search']) ? '<h1 class="text-center">Showing results for Id:' . $_GET['search'] . '</h1>'  : '<h1 class="text-center">All Orders</h1>' ?>

            <form action="./orders.php" method="GET" class="form-group">
                <div class="col-12">
                    <div class="row">
                        <div class="col-9">
                            <input type="text" name="search" class="form-control" id="search" placeholder="Enter Order Id to search*" required>
                        </div>
                        <div class="col-3">
                            <button class="btn btn-primary">Search</button>
                            &nbsp; &nbsp;
                            <a href="./orders.php" type="button" class="btn btn-danger" id="reset">Reset</a>
                        </div>
                    </div>
                </div>
            </form>

            <form action="./orders.php" method="post">
                <div style="overflow-x: scroll;">
                    <table>
                        <thead>
                            <tr>
                                <th></th>
                                <th>Order Id</th>
                                <th>Company</th>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>User</th>
                                <th>Date</th>
                                <th>Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($result as $order) : ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="order_id[]" value="<?= $order['id'] ?>" class="check">
                                    </td>
                                    <td>
                                        <?= $order['order_id'] ?>
                                    </td>
                                    <td>
                                        <?= $order['company'] ?>
                                    </td>
                                    <td style="color: dodgerblue; cursor: pointer" onclick="showProduct(<?= $order['product_id'] ?>)" data-toggle="modal" data-target="#myModal">
                                        <?= $order['product'] ?>
                                    </td>
                                    <td>
                                        <?= $order['quantity'] ?>
                                    </td>
                                    <td>
                                        <?= $order['price'] * $order['quantity'] ?>
                                    </td>
                                    <td style="color: dodgerblue; cursor: pointer" onclick="showUser('<?= $order['email'] ?>')" data-toggle="modal" data-target="#myModal">
                                        <?= $order['name'] ?>
                                    </td>
                                    <td>
                                        <?= $order['date'] ?>
                                    </td>
                                    <td style="height: fit-content;">
                                        <p>
                                            <?= $order['address'] ?>
                                        </p>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <br>
                <input type="submit" value="Mark as delivered" name="deliver" class="btn btn-primary">
            </form>

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
    </main>
</body>
<script>
    const key = "$2y$10$sqahb9LsEFFn/Ig/xsZXkOdax8xoqMSMDP6ehcON8pdSueZNxCxcS"
    const base = "./api.php"

    const modalTitle = document.querySelector('#modalTitle')
    const modalBody = document.querySelector('#modalBody')

    const showProduct = (id) => {
        modalTitle.textContent = 'Loading..'
        modalBody.textContent = 'Loading..'

        fetch(`${base}?api-key=${key}&id=${id}&get-product=true`).then(res => {
            res.json().then(res => {
                modalTitle.textContent = res.name + ' by ' + res.company
                modalBody.innerHTML = `
                <div class="container">
                    <img src="./${res.image}" style="display: block; margin: auto; max-height: 15rem; max-width: 100%;" />
                    <br />
                    <h2>${res.name} (₹${res.price})</h2>
                    <h4>Category: ${res.category}</h4>
                    <p>${res.description}</p>
                    ${res.available == 1 ? '<h4 style="color: teal;">Currently Available</h4>' : '<h4 style="color: firebrick;">Currently Unavailable</h4>'}
                </div>
                `;
            })
        })
    }

    const showUser = (email) => {
        modalTitle.textContent = 'Loading..'
        modalBody.textContent = 'Loading..'

        fetch(`${base}?api-key=${key}&get-user=${email}`).then(res => {
            res.json().then(res => {
                modalTitle.textContent = res.name + ' of ' + res.company
                modalBody.innerHTML = `
                <div class="container">
                    <img src="./${res.image}" style="display: block; margin: auto; max-height: 15rem; max-width: 100%;" />
                    <br />
                    <h2>${res.name} (${res.my_ref})</h2>
                    <h4>
                        <a href="tel:${res.mobile}" target="_blank">${res.mobile}</a>
                        <br />
                        <a href="mailto:${res.email}" target="_blank">${res.email}</a>
                    </h4>
                    
                </div>
                `;
            })
        })
    }
</script>

</html>