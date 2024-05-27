<?php
session_start();
include 'connect.php';

$fname = $lname = $mname = $email = $number = "";
$fnameErr = $lnameErr = $mnameErr = $emailErr = $numberErr = $passwordErr = "";

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
    header("Location: /");
    exit();
}

$id = isset($_POST['id']) ? $_POST['id'] : '';
if ($id) {
    $selectsql = "SELECT * FROM contacts WHERE id=?";
    $stmt = $conn->prepare($selectsql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $select_result = $stmt->get_result();
    $rows = $select_result->fetch_assoc();
    $stmt->close();

    // Initialize form variables with fetched data
    if ($rows) {
        $fname = $rows['first_name'];
        $mname = $rows['middle_name'];
        $lname = $rows['last_name'];
        $email = $rows['email'];
        $number = $rows['phone_number'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_new'])) {
    if (empty($_POST["fname"])) {
        $fnameErr = "First name is required";
    } else {
        $fname = test_input($_POST["fname"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $fname)) {
            $fnameErr = "Only letters and white space allowed";
        }
    }

    if (!empty($_POST["mname"])) {
        $mname = test_input($_POST["mname"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/", $mname)) {
            $mnameErr = "Only letters and white space allowed";
        }
    }

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
        } else {
            $selectsql = "SELECT phone_number from contacts";
            $select_result = $conn->query($selectsql);
            if ($select_result->num_rows > 0) {
                while ($rows = $select_result->fetch_assoc()) {
                    if ($rows['phone_number'] == $number) {
                        $numberErr = "Phone number already exists";
                    }
                }
            }
        }
    }

    if (empty($fnameErr) && empty($lnameErr) && empty($mnameErr) && empty($emailErr) && empty($numberErr)) {
        $id = $_POST['update_id'];
        $updatesql = "UPDATE contacts SET first_name=?, middle_name=?, last_name=?, email=?, phone_number=? WHERE id=?";
        $stmt = $conn->prepare($updatesql);
        $stmt->bind_param("sssssi", $fname, $mname, $lname, $email, $number, $id);
        if ($stmt->execute()) {
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
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
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <h2>Update the record</h2>
                <div class="mb-3">
                    <label for="id">Id: "<?php echo isset($rows['id']) ? $rows['id'] : ''; ?>"</label>
                </div>
                <div class="mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <span class="error" style="color:red;">* <?php echo $fnameErr; ?></span>
                    <input type="text" class="form-control" name="fname" value="<?php echo htmlspecialchars($fname); ?>">
                </div>
                <div class="mb-3">
                    <label for="middle_name" class="form-label">Middle Name</label>
                    <span class="error" style="color:red;"> <?php echo $mnameErr; ?></span>
                    <input type="text" class="form-control" name="mname" value="<?php echo htmlspecialchars($mname); ?>">
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <span class="error" style="color:red;">* <?php echo $lnameErr; ?></span>
                    <input type="text" class="form-control" name="lname" value="<?php echo htmlspecialchars($lname); ?>">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <span class="error" style="color:red;">* <?php echo $emailErr; ?></span>
                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($email); ?>">
                </div>
                <div class="mb-3">
                    <label for="number" class="form-label">Phone Number</label>
                    <span class="error" style="color:red;">* <?php echo $numberErr; ?></span>
                    <input type="text" class="form-control" name="number" value="<?php echo htmlspecialchars($number); ?>">
                </div>
                <input type="hidden" value="<?php echo isset($rows['id']) ? $rows['id'] : ''; ?>" name="update_id">
                <button type="submit" class="btn btn-success" name="update_new">Submit</button>
            </form>
        </div>
    </div>
</body>

</html>