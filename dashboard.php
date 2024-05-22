<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != true) {
    header("Location: /");
    exit();
}

if (isset($_POST['delete'])) {
    $id = $_POST['id'];

    $deletesql = "DELETE FROM contacts where id = $id";
    $delete_result = $conn->query($deletesql);
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
    <h1>Phone Book Management System</h1>
    <button type="button" class="btn btn-primary m-2"><a href="insert.php" style="color: white; text-decoration:none;" name="insert">Add Record</a></button>

    <div class="card p-2 m-2">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">First Name</th>
                        <th scope="col">Middle Name</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Phone Number</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <?php

                $selectsql = "SELECT * FROM contacts";
                $select_result = $conn->query($selectsql);
                if ($select_result->num_rows > 0) {
                    while ($rows = $select_result->fetch_assoc()) {
                        echo "<tbody>";
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($rows['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($rows['first_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($rows['middle_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($rows['last_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($rows['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($rows['phone_number']) . "</td>";
                        echo "<td>";
                        echo "<form method='POST' action='update.php' style='display: inline;'>";
                        echo "<input type='hidden' name='id' value='" . $rows['id'] . "'>";
                        echo "<td>" . "<button type='submit' class='btn btn-info' name='update'>Update</button>" . "</td>";
                        echo "</form>";
                        echo "</td>";
                        echo "<td>";
                        echo "<form method='POST' action='' style='display: inline;'>";
                        echo "<input type='hidden' name='id' value='" . $rows['id'] . "'>";
                        echo "<button type='submit' class='btn btn-danger' name='delete'>Delete</button>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                        echo "</tbody>";
                    }
                }
                $conn->close();
                ?>
            </table>
                <button type="button" class="btn btn-danger" name="logout"><a href="logout.php" style="color:white; text-decoration:none;">Logout</a></button>
        </div>
    </div>
</body>

</html>