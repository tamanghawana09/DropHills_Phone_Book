<?php
    session_start();
    include 'connect.php';

    $fname = $lname = $mname = $email = $number = "";
    $fnameErr = $lnameErr = $mnameErr = $emailErr = $numberErr = $passwordErr = "";

    if (!isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
        header("Location: login.php");
        exit();
    }

    $selectsql = "SELECT * FROM contacts";
    $select_result = $conn->query($selectsql);
    $rows = mysqli_fetch_assoc($select_result);

    
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        if(empty($_POST["fname"])){
            $fnameErr = "First name is required";
        }else{
            $fname = test_input($_POST["fname"]);
            if (!preg_match("/^[a-zA-Z-' ]*$/",$fname)) {
                $fnameErr = "Only letters and white space allowed";
              }
        }
       
        $mname = test_input($_POST["mname"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/",$mname)) {
            $mnameErr = "Only letters and white space allowed";
          }
        if(empty($_POST["lname"])){
            $lnameErr = "Last name is required";
        }else{
            $lname = test_input($_POST["lname"]);
            if (!preg_match("/^[a-zA-Z-' ]*$/",$lname)) {
                $lnameErr = "Only letters and white space allowed";
            }
        }
        if(empty($_POST["email"])){
            $emailErr = "Email is required";
        }else{
            $email = test_input($_POST["email"]);
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $emailErr = "Invalid email format";
            }
        }
        if(empty($_POST['number'])){
            $numberErr = "Phone number is required";
        }else{
            $number = test_input($_POST['number']);
            if (!preg_match("/^[0-9 ]*$/",$number)) {
                $numberErr = "Only numbers allowed";
            }
        }
    }

    function test_input($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

   

    if(isset($_POST['update'])){
        $id = $_POST['update_id'];
        $fname = $_POST['fname'];
        $mname = $_POST['mname'];
        $lname = $_POST['lname'];
        $email = $_POST['email'];
        $number = $_POST['number'];

        $updatesql = "UPDATE contacts SET first_name = '$fname', middle_name ='$mname', last_name ='$lname', email = '$email', phone_number = '$number' WHERE id = $id";
        $result = $conn->query($updatesql);
        if($result === true){
            header("Location: dashboard.php");
        }
    }

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Book</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body style="background-color: #D9CBA4;">
    <div class="card m-2">
        <div class="card-body">
            <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <h2>Update the record</h2>
                <div class="mb-3">
                    <label for="id">Id:"<?php echo $rows['id']?>"</label>
                </div>
                <div class="mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <span class="error" style="color:red;">* <?php echo $fnameErr;?></span>
                    <input type="txt" class="form-control" name="fname" value="<?php echo $rows['first_name']; ?>"> 
                </div>
                <div class="mb-3">
                    <label for="middle_name" class="form-label">Middle Name</label>
                    <span class="error" style="color:red;">* <?php echo $mnameErr;?></span>
                    <input type="txt" class="form-control" name="mname" value="<?php echo $rows['middle_name']; ?>">
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <span class="error" style="color:red;">* <?php echo $lnameErr;?></span>
                    <input type="txt" class="form-control" name="lname" value="<?php echo $rows['last_name']; ?>">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <span class="error" style="color:red;">* <?php echo $emailErr;?></span>
                    <input type="email" class="form-control" name="email" value="<?php echo $rows['email']; ?>">
                </div>
                <div class="mb-3">
                    <label for="number" class="form-label">Phone Number</label>
                    <span class="error" style="color:red;">* <?php echo $numberErr;?></span>
                    <input type="txt" class="form-control" name="number" value="<?php echo $rows['phone_number']; ?>">
                </div>
                <input type="hidden" value="<?php echo $rows['id']?>" name="update_id">
                <button type="submit" class="btn btn-success" name="update">Submit</button>
            </form>
        </div>
    </div>
</body>

</html>