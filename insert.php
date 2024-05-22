<?php
session_start();
include 'connect.php';


if (!isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
    header("Location: /");
    exit();
}

$fname = $lname = $mname = $email = $number = "";
$fnameErr = $lnameErr = $mnameErr = $emailErr = $numberErr = $passwordErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["fname"])) {
        $fnameErr = "First name is required";
    } else {
        $fname = test_input($_POST["fname"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $fname)) {
            $fnameErr = "Only letters and white space allowed";
        }
    }
    $mname = test_input($_POST["mname"]);
    if (empty($_POST["lname"])) {
        $lnameErr = "Last name is required";
    } else {
        $lname = test_input($_POST["lname"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $lname)) {
            $lnameErr = "Only letters and white space allowed";
        }
    }
    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
    } else {
        $email = test_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Invalid email format";
        }
    }
    if (empty($_POST['number'])) {
        $numberErr = "Phone number is required";
    } else {
        $number = test_input($_POST['number']);
        if (!preg_match("/^[0-9 ]*$/", $number)) {
            $numberErr = "Only numbers allowed";
        }
    }
}

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $selectsql = "SELECT phone_number from contacts";
    $select_result = $conn->query($selectsql);
    if ($select_result->num_rows > 0) {
        while ($rows = $select_result->fetch_assoc()) {
            if ($rows['phone_number'] == $number) {
                $numberErr = "Phone number already exists";
            }
        }
    }

    $selectsql = "SELECT email from contacts";
    $select_result = $conn->query($selectsql);
    if ($select_result->num_rows > 0) {
        while ($rows = $select_result->fetch_assoc()) {
            if ($rows['email'] == $email) {
                $emailErr = "Email already exists";
            }
        }
    }

    if (empty($emailErr) && empty($numberErr)) {
        $insertsql = "INSERT INTO contacts (first_name,middle_name,last_name,email,phone_number) VALUES ('$fname','$mname','$lname','$email','$number')";
        $result = $conn->query($insertsql);
        if ($result) {
            header("Location: dashboard.php");
            exit();
        }
    }
}

$conn->close();
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
            <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
                <h2>Insert a record </h2>
                <div class="mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <span class="error" style="color:red;">* <?php echo $fnameErr; ?></span>
                    <input type="txt" class="form-control" name="fname">
                </div>
                <div class="mb-3">
                    <label for="middle_name" class="form-label">Middle Name</label>
                    <span class="error" style="color:red;">* <?php echo $mnameErr; ?></span>
                    <input type="txt" class="form-control" name="mname">
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <span class="error" style="color:red;">* <?php echo $lnameErr; ?></span>
                    <input type="txt" class="form-control" name="lname">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <span class="error" style="color:red;">* <?php echo $emailErr; ?></span>
                    <input type="email" class="form-control" name="email">
                </div>
                <div class="mb-3">
                    <label for="number" class="form-label">Phone Number</label>
                    <span class="error" style="color:red;">* <?php echo $numberErr; ?></span>
                    <input type="txt" class="form-control" name="number">
                </div>
                <button type="submit" class="btn btn-success">Submit</button>
            </form>
        </div>
    </div>
</body>

</html>