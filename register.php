<?php
// Include config file
require_once "config.php";
 
$_SESSION['message'] ='';
// Define variables and initialize with empty values

 
// Processing form data when form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['register'])) {
        // make sure that the two passwords are the same
        if ($_POST['password'] == $_POST['confirmPassword']) {
            $username = $mysqli -> real_escape_string($_POST['username']);
            $email = $mysqli -> real_escape_string($_POST['email']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $image_path = $mysqli -> real_escape_string('images/'.$_FILES['userImage']['name']);
            
            if (preg_match("!image!", $_FILES['userImage']['type'])) {
                // copy image to images folder
                if (copy($_FILES['userImage']['tmp_name'], $image_path)) {
                    $_SESSION['username'] = $username;
                    $_SESSION['userImage'] = $image_path;

                    $sql = "INSERT INTO users (username, email, password, image)"
                            . "VALUES  ('$username', '$email', '$password', '$image_path')";
                    if ($mysqli->query($sql) === true) {
                        $_SESSION['message'] = "Registration successful! Added $username to the database!";
                        header("location: login.php");
                    } else {
                        $_SESSION['message'] = "User could not be added to the database!";
                    }

                } else {
                    $_SESSION['message'] = "File upload failed";
                }
            } else {
                $_SESSION['message'] = "Please only upload GIF, JPG, PNG images!";
            }
        } else {
            $_SESSION['message'] = "Two passwords do not match!";
        }
        $_SESSION['checkUser'] = false;
    }
    

}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link href="//db.onlinewebfonts.com/c/a4e256ed67403c6ad5d43937ed48a77b?family=Core+Sans+N+W01+35+Light" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="styles.css" type="text/css">
</head>
<body>
    <div class="wrapper">
        <div class="module">
            <h1>Create an account</h1>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" autocomplete="off">
            <div class="alert alert-error"><?= $_SESSION['message'] ?></div>
            <input type="text" placeholder="User Name" name="username" required />
            <input type="email" placeholder="Email" name="email" required />
            <input type="password" placeholder="Password" name="password" autocomplete="new-password" required />
            <input type="password" placeholder="Confirm Password" name="confirmPassword" autocomplete="new-password" required />
            <div class="avatar"><label>Select your profile image: </label><input type="file" name="userImage" accept="image/*" required /></div>
            <input type="submit" value="Register" name="register" class="btn btn-block btn-primary" />
            </form>
            <p>Already have an account? <a style="color: wheat" href="login.php">Login here</a>.</p>
        </div>
    </div>    
</body>
</html>