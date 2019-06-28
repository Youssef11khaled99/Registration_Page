<?php
// Initialize the session
session_start();

$_SESSION['message'] ='';
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: welcome.php");
    exit;
}
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Store result
                $stmt->store_result();
                
                // Check if username exists, if yes then verify password
                if($stmt->num_rows == 1){                    
                    // Bind result variables
                    $stmt->bind_result($id, $username, $hashed_password);
                    if($stmt->fetch()){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username; 
                            // Remember me  
                            if(!empty($_POST["remember"]))   
                            {  
                                setcookie ("member_login",$username,time()+ (10 * 365 * 24 * 60 * 60));  
                                setcookie ("member_password",$password,time()+ (10 * 365 * 24 * 60 * 60));
                                $_SESSION["admin_name"] = $name;
                            }  
                            else  
                            {  
                                if(isset($_COOKIE["member_login"]))   
                                {  
                                setcookie ("member_login","");  
                                }  
                                if(isset($_COOKIE["member_password"]))   
                                {  
                                setcookie ("member_password","");  
                                }  
                            }                         
                            
                            // Redirect user to welcome page
                            header("location: welcome.php");
                        } else{
                            // Display an error message if password is not valid
                            $_SESSION['message'] = "The password you entered was not valid.";
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $_SESSION['message'] = "No account found with that username.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
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
    <title>Login</title>
    <link href="//db.onlinewebfonts.com/c/a4e256ed67403c6ad5d43937ed48a77b?family=Core+Sans+N+W01+35+Light" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="styles.css" type="text/css">
</head>
<body>
    <div class="module">
            <h1>Login</h1>
            <h4>Please fill in your credentials to login.</h4>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" autocomplete="off">
            <div class="alert alert-error"><?= $_SESSION['message'] ?></div>
            <input 
                type="text" 
                placeholder="User Name" 
                name="username" 
                value="<?php if(isset($_COOKIE["member_login"])) { echo $_COOKIE["member_login"]; } ?>" 
                required 
                />
            <input 
                type="password"
                placeholder="Password"
                name="password"
                autocomplete="new-password" 
                value="<?php if(isset($_COOKIE["member_password"])) { echo $_COOKIE["member_password"]; } ?>" 
                required 
                />
            <input type="checkbox" name="remember" <?php if(isset($_COOKIE["member_login"])) { ?> checked <?php } ?>>
            <label for="remember">Remember me</label>
            <input type="submit" value="Login" name="login" class="btn btn-block btn-primary" />
            </form>
            <p>Don't have an account? <a style="color: wheat" href="register.php">Sign up now</a>.</p>
        </div> 
</body>
</html>