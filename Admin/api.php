<?php

include_once './creds.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: X-Requested-With');

date_default_timezone_set('Asia/Kolkata');

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



$con = new mysqli('localhost', 'beardcod_barney', 'Pata nahi', 'beardcod_networkx');
//$2y$10$sqahb9LsEFFn/Ig/xsZXkOdax8xoqMSMDP6ehcON8pdSueZNxCxcS

//Default Password: hello admin
$final_res = array();
$total_income = 0;
function getUnderLings($referral)
{
    global $con;
    global $final_res;

    $user = $con->query("SELECT * FROM `users` WHERE `my_ref` = '$referral'")->fetch_assoc();
    $myCom = $user['company'];

    $my_chain = $con->query("SELECT * FROM `users` WHERE `super_ref` = '$referral' AND `company` = '$myCom'");

    $rows = $my_chain->num_rows;
    $my_chain = $my_chain->fetch_all(MYSQLI_ASSOC);

    if ($rows == 0) {
        return;
    } else {
        foreach ($my_chain as $chain) {
            $final_res[] = $chain;
            getUnderLings($chain['my_ref']);
        }
    }
}

function calculateIncome($referral)
{
    global $con;
    global $total_income;

    $user = $con->query("SELECT * FROM `users` WHERE `my_ref` = '$referral'")->fetch_assoc();
    $myCom = $user['company'];

    $my_chain = $con->query("SELECT * FROM `users` WHERE `super_ref` = '$referral' AND `company` = '$myCom'");

    $rows = $my_chain->num_rows;

    $amount = (int)($rows / 3) * 1500; //Change if necessary!
    $total_income += $amount;

    if ($rows == 0) {
        return;
    } else {
        foreach ($my_chain as $chain) {
            calculateIncome($chain['my_ref']);
        }
    }
}

if (isset($_GET['api-key'])) {
    $api_key = $_GET['api-key'];
    if (password_verify('legendary', $api_key)) {
        //Main Logic Start
        if (isset($_GET['get-companies'])) {
            $res = $con->query("SELECT * FROM `companies`")->fetch_all(MYSQLI_ASSOC);
            echo json_encode($res, JSON_PRETTY_PRINT);
        } elseif (isset($_POST['signup'])) {
            $name = $con->real_escape_string(strip_tags($_POST['name']));
            $dob = $con->real_escape_string(strip_tags($_POST['dob']));
            $email = $con->real_escape_string(strip_tags($_POST['email']));
            $password = $con->real_escape_string(strip_tags($_POST['password']));
            $aadhaar = $con->real_escape_string(strip_tags($_POST['aadhaar']));
            $super_ref = $con->real_escape_string(strip_tags($_POST['super_ref']));
            $company = $con->real_escape_string(strip_tags($_POST['company']));
            $mobile = $con->real_escape_string(strip_tags($_POST['mobile']));

            $name = ucfirst($name);
            $password = password_hash($password, PASSWORD_BCRYPT);
            $my_ref = strtoupper(substr($name, 0, 3) . '-' . substr(md5(uniqid()), 0, 5));

            $flag = true;

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(array('status' => 'error', 'description' => 'Invalid Email!'), JSON_PRETTY_PRINT);
                $flag = false;
            } elseif (!($con->query("SELECT `id` FROM `users` WHERE `my_ref` = '$super_ref'")->num_rows == 1 || $super_ref == 'null')) {
                echo json_encode(array('status' => 'error', 'description' => 'Invalid Referal Code!'), JSON_PRETTY_PRINT);
                $flag = false;
            } elseif ($flag && $con->query("INSERT INTO `users`(`name`, `dob`, `email`, `password`, `aadhaar`, `my_ref`, `super_ref`, `company`, `mobile`) VALUES ('$name','$dob','$email','$password','$aadhaar','$my_ref','$super_ref','$company','$mobile')")) {
                echo json_encode(array('status' => 'success', 'description' => ''), JSON_PRETTY_PRINT);
            } else {
                echo json_encode(array('status' => 'error', 'description' => 'Email Already Exists!'), JSON_PRETTY_PRINT);
            }
        } elseif (isset($_POST['login'])) {
            $res;
            $email = $con->real_escape_string(strip_tags($_POST['email']));
            $password = $con->real_escape_string(strip_tags($_POST['password']));

            $user = $con->query("SELECT * FROM `users` WHERE `email` = '$email'");
            if ($user->num_rows == 1) {
                $user = $user->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    $res = array('status' => 'success', 'description' => $email);
                } else {
                    $res = array('status' => 'error', 'description' => 'Invalid Password');
                }
            } else {
                $res = array('status' => 'error', 'description' => 'Invalid Email');
            }

            echo json_encode($res, JSON_PRETTY_PRINT);
        } elseif (isset($_GET['get-user'])) {
            $email = $con->real_escape_string(strip_tags($_GET['get-user']));

            $res = $con->query("SELECT u.*, c.`image` AS `company_img` FROM `users` u INNER JOIN `companies` c ON u.`company` = c.`name` WHERE u.`email` = '$email'")->fetch_assoc();
            $myRef = $res['my_ref'];

            $myCom = $res['company'];

            $immediate_underlings = $con->query("SELECT * FROM `users` WHERE `super_ref` = '$myRef' AND `company` = '$myCom'");

            $immediate_underlings_count = $immediate_underlings->num_rows;

            $immediate_underlings = $immediate_underlings->fetch_all(MYSQLI_ASSOC);

            $final_res = array();
            $total_income = 0;

            getUnderLings($myRef);
            calculateIncome($myRef);

            $response = $res + array('all_underlings' => $final_res) + array('immediate_underlings' => $immediate_underlings) + array('immediate_underlings_count' => $immediate_underlings_count) + array('income' => $total_income);

            echo json_encode($response, JSON_PRETTY_PRINT);
        } elseif (isset($_POST['edit-profile'])) {
            $id = $con->real_escape_string(strip_tags($_POST['id']));
            $name = $con->real_escape_string(strip_tags($_POST['name']));
            $email = $con->real_escape_string(strip_tags($_POST['email']));
            $aadhaar = $con->real_escape_string(strip_tags($_POST['aadhaar']));
            $mobile = $con->real_escape_string(strip_tags($_POST['mobile']));

            $name = ucfirst($name);

            $flag = true;

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(array('status' => 'error', 'description' => 'Invalid Email!'), JSON_PRETTY_PRINT);
                $flag = false;
            } elseif ($flag && $con->query("UPDATE `users` SET `name`='$name',`email`='$email',`aadhaar`='$aadhaar',`mobile`='$mobile' WHERE `id`='$id'")) {
                echo json_encode(array('status' => 'success', 'description' => $email), JSON_PRETTY_PRINT);
            } else {
                echo json_encode(array('status' => 'error', 'description' => 'Could not update data!'), JSON_PRETTY_PRINT);
            }
        } elseif (isset($_POST['edit-image'])) {
            $id = $con->real_escape_string(strip_tags($_POST['id']));
            $img = $con->query("SELECT `image` FROM `users` WHERE `id` = '$id'")->fetch_assoc()['image'];
            if (!empty($_FILES['file']['name'])) {
                if ($img != 'uploads/default.png') {
                    unlink("./" . $img);
                }
                $fileName = rand(1000, 10000) . '-' . basename($_FILES['file']['name']);
                $imageUploadpath = $uploadPath . $fileName;
                $fileType = pathinfo($imageUploadpath, PATHINFO_EXTENSION);
                if (in_array(strtolower($fileType), $allowTypes)) {
                    $imageTemp = $_FILES['file']['tmp_name'];
                    $compressedImage = compressImage($imageTemp, $imageUploadpath, 50);
                    if ($con->query("UPDATE `users` SET `image` = '$compressedImage' WHERE `id` = '$id'")) {
                        echo json_encode(array('status' => 'success', 'description' => ''), JSON_PRETTY_PRINT);
                    }
                } else {
                    echo json_encode(array('status' => 'error', 'description' => 'Invalid Extension'), JSON_PRETTY_PRINT);
                }
            } else {
                echo json_encode(array('status' => 'success', 'description' => 'No File Found!'), JSON_PRETTY_PRINT);
            }
        } elseif (isset($_POST['change-password'])) {
            $cpass = $con->real_escape_string(strip_tags($_POST['cpass']));
            $npass1 = $con->real_escape_string(strip_tags($_POST['npass1']));
            $npass2 = $con->real_escape_string(strip_tags($_POST['npass2']));
            $id = $con->real_escape_string(strip_tags($_POST['id']));

            $password = $con->query("SELECT `password` FROM `users` WHERE `id` = '$id'")->fetch_assoc()['password'];
            if (password_verify($cpass, $password) && $npass1 === $npass2) {
                $password = password_hash($npass1, PASSWORD_BCRYPT);
                if ($con->query("UPDATE `users` SET `password` = '$password' WHERE `id` = '$id'")) {
                    echo json_encode(array('status' => 'success', 'description' => ''), JSON_PRETTY_PRINT);
                } else {
                    echo json_encode(array('status' => 'error', 'description' => 'Could not update Password!'), JSON_PRETTY_PRINT);
                }
            } else {
                echo json_encode(array('status' => 'error', 'description' => 'Passwords do not match!'), JSON_PRETTY_PRINT);
            }
        } elseif (isset($_GET['get-sliders'])) {
            $sliders = $con->query("SELECT * FROM `shop-slider`")->fetch_all(MYSQLI_ASSOC);

            echo json_encode($sliders, JSON_PRETTY_PRINT);
        } elseif (isset($_GET['get-categories'])) {
            $categories = $con->query("SELECT * FROM `product-category`")->fetch_all(MYSQLI_ASSOC);

            echo json_encode($categories, JSON_PRETTY_PRINT);
        } elseif (isset($_GET['view-demo-products'])) {
            $company = $con->real_escape_string(strip_tags($_GET['company']));
            $res = $con->query("SELECT * FROM `products` WHERE `company` = '$company' ORDER BY `name` LIMIT 5")->fetch_all(MYSQLI_ASSOC);

            echo json_encode($res, JSON_PRETTY_PRINT);
        } elseif (isset($_GET['get-product-by-category'])) {
            $company = $con->real_escape_string(strip_tags($_GET['company']));
            $page = $con->real_escape_string(strip_tags($_GET['page']));
            $category = $con->real_escape_string(strip_tags($_GET['category']));
            $limit = 6;
            $start = ($page - 1) * $limit;

            if ($category != 'all') {
                $result = $con->query("SELECT * FROM `products` WHERE `category` = '$category' AND `company` = '$company' LIMIT $start, $limit");
                $count = $con->query("SELECT count(`id`) AS `id` FROM `products` WHERE `category` = '$category' AND `company` = '$company'");
            } else {
                $result = $con->query("SELECT * FROM `products` WHERE `company` = '$company' LIMIT $start, $limit");
                $count = $con->query("SELECT count(`id`) AS `id` FROM `products` WHERE `company` = '$company'");
            }

            $result = $result->fetch_all(MYSQLI_ASSOC);

            $count = $count->fetch_all(MYSQLI_ASSOC);
            $count = $count[0]['id'];

            $pages = ceil($count / $limit);

            echo json_encode(array('Products' => $result, 'Pages' => $pages), JSON_PRETTY_PRINT);
        } elseif (isset($_GET['get-product'])) {
            $id = $con->real_escape_string(strip_tags($_GET['id']));

            $res = $con->query("SELECT * FROM `products` WHERE `id` = '$id'")->fetch_assoc();

            echo json_encode($res, JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT);
        } elseif (isset($_GET['get-similiar-products'])) {
            $category = $con->real_escape_string(strip_tags($_GET['category']));
            $id = $con->real_escape_string(strip_tags($_GET['id']));
            $company = $con->real_escape_string(strip_tags($_GET['company']));

            $res = $con->query("SELECT * FROM `products` WHERE `id` != '$id' AND `category` = '$category' AND `company` = '$company' LIMIT 5")->fetch_all(MYSQLI_ASSOC);

            echo json_encode($res, JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT);
        } elseif (isset($_GET['get-addresses'])) {
            $id = $con->real_escape_string(strip_tags($_GET['id']));

            $res = $con->query("SELECT * FROM `addresses` WHERE `user_id` = '$id'")->fetch_all(MYSQLI_ASSOC);

            echo json_encode($res, JSON_PRETTY_PRINT);
        } elseif (isset($_POST['add-address'])) {
            $user_id = $con->real_escape_string(strip_tags($_POST['user_id']));
            $state = $con->real_escape_string(strip_tags($_POST['state']));
            $pin = $con->real_escape_string(strip_tags($_POST['pin']));
            $house = $con->real_escape_string(strip_tags($_POST['house']));
            $area = $con->real_escape_string(strip_tags($_POST['area']));
            $landmark = $con->real_escape_string(strip_tags($_POST['landmark']));
            $town = $con->real_escape_string(strip_tags($_POST['town']));

            if ($con->query("INSERT INTO `addresses`(`user_id`, `state`, `pin`, `house`, `area`, `landmark`, `town`) VALUES ('$user_id','$state','$pin','$house','$area','$landmark','$town')")) {
                echo json_encode(array('status' => 'success', 'description' => ''), JSON_PRETTY_PRINT);
            } else {
                echo json_encode(array('status' => 'error', 'description' => 'Could not add Address, try again later!'), JSON_PRETTY_PRINT);
            }
        } elseif (isset($_POST['add-order'])) {
            $order_id = strtoupper('Nx-' . substr(md5(uniqid()), 0, 5));
            $address_id = $con->real_escape_string(strip_tags($_POST['address_id']));
            $user_id = $con->real_escape_string(strip_tags($_POST['user_id']));
            $count = $con->real_escape_string(strip_tags($_POST['count']));

            $con->autocommit(FALSE);
            for ($i = 0; $i < (int)$count; $i++) {
                $product_id = $con->real_escape_string(strip_tags($_POST['product_id'][$i]));
                $quantity = $con->real_escape_string(strip_tags($_POST['quantity'][$i]));

                $con->query("INSERT INTO `orders`(`address_id`, `user_id`, `order_id`, `product_id`, `quantity`) VALUES ('$address_id','$user_id','$order_id','$product_id','$quantity')");
            }

            if (!$con->commit()){
                echo json_encode(array('status' => 'error', 'description' => 'Could not place order, try again later!'), JSON_PRETTY_PRINT);
            } else {
                echo json_encode(array('status' => 'success', 'description' => ''), JSON_PRETTY_PRINT);
            }

            $con->autocommit(TRUE);
        } elseif (isset($_GET['get-my-orders'])) {
            $id = $con->real_escape_string(strip_tags($_GET['id']));

            $res = $con->query("SELECT o.*, a.*, p.* FROM `orders` o INNER JOIN `addresses` a ON o.`address_id` = a.`id` INNER JOIN `products` p ON o.`product_id` = p.`id` WHERE o.`user_id` = '$id' ")->fetch_all(MYSQLI_ASSOC);

            echo json_encode($res, JSON_PRETTY_PRINT);
        } elseif (isset($_GET['ask-reset'])) {
            
            $subject = "Reset Password";
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            
            $email = $con->real_escape_string(strip_tags($_GET['email']));
            
            $check = (int)$con->query("SELECT COUNT(`id`) AS `count` FROM `users` WHERE `email` = '$email'")->fetch_assoc()['count'];
            
            if ($check == 1) {
                $otp = strtoupper(substr(md5(uniqid()), 0, 6));
                if ($con->query("DELETE FROM `reset` WHERE `email` = '$email'")) {
                    $con->query("INSERT INTO `reset` (`email`,`otp`) VALUES ('$email','$otp')");
                    $message = "
                    <html>
                        <head>
                            <title>
                                Reset Password
                            </title>
                        </head>
                        <body>
                            <h2>
                                Hey, ".$email.",<br />
                                Here's your One Time Password to reset your Network X account password.<br />
                                <span style='color: dodgerblue;'>".$otp."</span><br />
                                Please make sure to enter it correctly!<br />
                                Regards,<br />
                                Network X
                            </h2>
                        </body>
                    </html>
                    ";
                    mail($email, $subject, $message, $headers);
                    
                }
                
                echo json_encode(array('status' => 'success', 'description' => ''), JSON_PRETTY_PRINT);
            } else {
                echo json_encode(array('status' => 'error', 'description' => 'Invalid email!'), JSON_PRETTY_PRINT);
            }
        } elseif (isset($_POST['verify-otp'])) {
            $email = $con->real_escape_string(strip_tags($_POST['email']));
            $otp = $con->real_escape_string(strip_tags($_POST['otp']));
            
            $rows = (int)$con->query("SELECT COUNT(`id`) AS `count` FROM `reset` WHERE `email` = '$email' AND `otp` = '$otp'")->fetch_assoc()['count'];
            
            if ($rows == 1) {
                echo json_encode(array('status' => 'success', 'description' => ''), JSON_PRETTY_PRINT);
            } else {
                echo json_encode(array('status' => 'error', 'description' => 'Invalid OTP entered!'), JSON_PRETTY_PRINT);
            }
        } elseif (isset($_POST['reset-password'])) {
            $email = $con->real_escape_string(strip_tags($_POST['email']));
            $password = $con->real_escape_string(strip_tags($_POST['password']));
            $password = password_hash($password, PASSWORD_BCRYPT);
            
            if ($con->query("UPDATE `users` SET `password` = '$password' WHERE `email` = '$email'")) {
                echo json_encode(array('status' => 'success', 'description' => ''), JSON_PRETTY_PRINT);
            } else {
                echo json_encode(array('status' => 'error', 'description' => 'Could not reset Password!'), JSON_PRETTY_PRINT);
            }
        }

        //Cron Jobs



        //Main Logic End
    } else {
        echo json_encode(array('status' => 'error', 'description' => 'Invalid Api Key Found!'), JSON_PRETTY_PRINT);
    }
} else {
    echo json_encode(array('status' => 'error', 'description' => 'No Api Key Found!'), JSON_PRETTY_PRINT);
}

$con->close();
