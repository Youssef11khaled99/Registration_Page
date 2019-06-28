<?php
// Include config file
require_once "config.php";
 
$_SESSION['message'] ='';
// Define variables and initialize with empty values

 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $_SESSION['message'] = "Please enter a username.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // store result
                $stmt->store_result();
                
                if($stmt->num_rows == 1){
                    $_SESSION['message'] = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        $stmt->close();
    }
    
    // Validate email
    if(empty(trim($_POST["email"]))){
        $_SESSION['message'] = "Please enter your email.";     
    } elseif(!preg_match("/^[a-zA-Z ]*$/",$name)){
        $_SESSION['message'] = "Please enter write email";
    } else{
        $email = trim($_POST["email"]);
    }

    // Validate Image
    if (preg_match("!image!", $_FILES['userImage']['type'])) {
        // copy image to images folder
        if (copy($_FILES['userImage']['tmp_name'], $image_path)) {
            $_SESSION['userImage'] = $image_path;
        }
    }

    // Validate password
    if(empty(trim($_POST["password"]))){
        $_SESSION['message'] = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $_SESSION['message'] = "Password must have at least 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $_SESSION['message'] = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $_SESSION['message'] = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($username) && empty($password) && empty($confirm_password) && empty($email) && empty($image_path)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, email, password, image) VALUES (?, ?, ?, $image_path)";
         
        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ssss", $param_username, $param_password, $email, $image_path);
            
            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Redirect to login page
                header("location: login.php");
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        $stmt->close();
    }
    
    // Close connection
    $mysqli->close();
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