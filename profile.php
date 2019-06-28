<?php
session_start();
// require_once('PHPMailer/PHPMailerAutoload.php');

// $mail = new PHPMailer();
// $mail -> isSMTP();
// $mail -> SMTPAuth = true;
// $mail -> SMTPSecure = 'ssl';
// $mail -> Host = 'smtp@gmail.com';
// $mail -> Port = '456';
// $mail -> isHTML();
// $mail -> Username = 'ykroshdy@gmail.com';
// $mail -> Username = 'ykroshdy@gmail.com';
// $mail -> Password = "it'sasecret123";
// $mail -> SetForm('no-reply@howcode.org');
// $mail -> Subject = "Registration message";
// $mail -> Body = "Thank you for registering in our service> ";
// $mail -> AddAddress('uossefkhaled99@gmail.com');
// $mail -> Send();
if($_SESSION['checkUser'])
{
    echo "ahafadjlglf;jg";
    // not logged in
    header('Location: form.php');
    $_SESSION['message'] = "You are no logged in!";
    exit();
}

$_SESSION['message-2'] ='';
$mysqli = new mysqli('localhost', 'root', '12345678', 'accounts');
$hamada = $_SESSION['username'];
echo "Hello ".$hamada." again.";

if (isset($_POST['update'])) {
    foreach($_POST as $key =>$value){
        if ($value != "" && $key == "password") {
            $password = md5($_POST['password']); // hash the password for security
        }
        else if ($value != "" && $key == "image") {
            $image_path = $mysqli -> real_escape_string('images/'.$_FILES['userImage']['name']);
        }
        else if ($value != "" && $key == "username") {
            $username = $mysqli -> real_escape_string($_POST['username']);
            echo $username;
        }
        else if ($value != "" && $key == "email") {
            $email = $mysqli -> real_escape_string($_POST['email']);
        }
    }
    if (preg_match("!image!", $_FILES['userImage']['type'])) {
        // copy image to images folder
        if (copy($_FILES['userImage']['tmp_name'], $image_path)) {
            $_SESSION['username'] = $username;
            $_SESSION['userImage'] = $image_path;

            $sql = "UPDATE users SET username='$username', email='$email', password='$password', image='$image_path'  WHERE username= '$hamada' ";

            if ($mysqli->query($sql) === TRUE) {
                echo "Record updated successfully";
            } else {
                echo "Error updating record: " . $mysqli->error;
            }

        } else {
            $_SESSION['message'] = "File upload failed";
        }
    } else {
        $_SESSION['message'] = "Please only upload GIF, JPG, PNG images!";
    }
        


$mysqli->close();
}

// if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//     if (isset($_POST['register'])) {
//         // make sure that the two passwords are the same
//         if ($_POST['password'] == $_POST['confirmPassword']) {
//            
//             $username = $mysqli -> real_escape_string($_POST['username']);
//             $email = $mysqli -> real_escape_string($_POST['email']);
//             $password = md5($_POST['username']); // hash the password for security
//             $image_path = $mysqli -> real_escape_string('images/'.$_FILES['userImage']['name']);
            
//             if (preg_match("!image!", $_FILES['userImage']['type'])) {
//                 // copy image to images folder
//                 if (copy($_FILES['userImage']['tmp_name'], $image_path)) {
//                     $_SESSION['username'] = $username;
//                     $_SESSION['userImage'] = $image_path;
                    
                    

//                 } else {
//                     $_SESSION['message-2'] = "File upload failed";
//                 }
//             } else {
//                 $_SESSION['message-2'] = "Please only upload GIF, JPG, PNG images!";
//             }
//         } else {
//             $_SESSION['message-2'] = "Two passwords do not match!";
//         }
//     }
    

// }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="styles.css" type="text/css">
    <title>Document</title>
</head>
<body>
    <h2><a href = "logout.php">Sign Out</a></h2>
    <div class="body content">
        <div class="profile">
            <div class="alert alert-success"><?= $_SESSION['message'] ?></div>
            <span><img class='userImage' src="<?= $_SESSION['userImage'] ?>" alt=""></span> <br>
            Welcome <span class="user"><?= $_SESSION['username'] ?></span>
        </div>
        <div class="module">
            <h1>Update your account</h1>
            <form class="form" action="profile.php" method="post" enctype="multipart/form-data" autocomplete="off">
            <div class="alert alert-error"><?= $_SESSION['message-2'] ?></div>
            <input type="text" placeholder="User Name" name="username"  />
            <input type="email" placeholder="Email" name="email"  />
            <input type="password" placeholder="Password" name="password" autocomplete="new-password"  />
            <div class="avatar"><label>Select your profile image: </label><input type="file" name="userImage" accept="image/*"  /></div>
            <input type="submit" value="Update" name="update" class="btn btn-block btn-primary" />
            </form>
        </div>
    </div>
</body>
</html>