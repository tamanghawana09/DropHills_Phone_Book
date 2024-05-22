<?php
    session_start();
    include 'connect.php';

    $email = $password = "";
    $emailErr = $passwordErr = "";


    if($_SERVER["REQUEST_METHOD"]=="POST"){
        if(isset($_POST["email"])){
            if(empty($_POST["email"])){
                $emailErr = "Email is required";
            }else{
                $email = test_input($_POST["email"]);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $emailErr = "Invalid email format";
                }
            }
        }
       if(isset($_POST["password"])){
            if(empty($_POST["password"])){
                $passwordErr = "Password is required";
            }else{
                $password = test_input($_POST["password"]);
            }
       }
    }

    //form validation
    function test_input($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $selectsql = "SELECT email,password FROM users WHERE email = ?";
        $stmt = $conn->prepare($selectsql);
        $stmt->bind_param('s',$email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows > 0){
            $rows = $result->fetch_assoc();
            if($rows['password'] === $password){
                    $_SESSION["email"] = $email;
                    $_SESSION["loggedin"] = true;
                    header("Location: dashboard.php");
                }else{
                    $passwordErr = "Wrong password provided";
            }   
        }else{
            $emailErr = "Email not found";
        }
    }
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Book</title>
    <link rel="stylesheet" href="/css/login.css">
</head>
<body>
    <div class="container">
        <h2>LOGIN</h2>
        <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">
            <input type="email" name="email" placeholder="Enter email">
            <span class="error">* <?php echo $emailErr;?></span>
            <input type="password" name="password" placeholder="Enter password">
            <span class="error">* <?php echo $passwordErr;?></span>
            <input type="submit" value="Submit" class="btn">
        </form>
        <div class="register-container">
            Don't have an account yet ? <a href="register.php">Register here.</a>
        </div>
    </div>
</body>
</html>