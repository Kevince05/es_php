<?php

session_start();
$error = null;
$db = new mysqli("localhost", "root", "", "cesco");

if ($db->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id = $_POST["id"];
    $usr = $_POST["usr"];
    $scrt_lvl = $_POST["scrt_lvl"];
    if ($_POST["submit_type"] == "Remove") {
        if (!$db->query("DELETE FROM users WHERE id = '$id'")) {
            $error = "Delete went wrong :(";
        }
    } else {
        if (!$db->query("UPDATE users SET username='$usr', security_lvl='$scrt_lvl' WHERE id = '$id'")) {
            $error = "Edit went wrong :(";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="style/style_admin.css">
    <title>Admin</title>
</head>

<body>
    <?php
    $usr = $_SESSION["usr"];
    $result = $db->query("SELECT  security_lvl FROM users WHERE username = '$usr'");
    if ($result->fetch_assoc()["security_lvl"] < 1) {
        header("Location:index.php");
        die("Not an admin");
    } else {
        echo "<h1>Admin Console</h1>";
    }
    ?>
    <table>
        <tr>
            <th>Username</th>
            <th>security_lvl</th>
        </tr>
        <?php
        $result = $db->query("SELECT * FROM users");
        foreach ($result as $row) {
            echo "<form action='admin.php' method='POST'>
                            <tr>
                                <input type='hidden' value=" . $row["id"] . " name='id'>
                                <td><input type='text' value=" . $row["username"] . " name='usr'></td>
                                <td><input type='number' value=" . $row["security_lvl"] . " name='scrt_lvl' min='0' max='1' step='1'></td>
                                <td><input type='submit' name='submit_type' value='Edit'></td>
                                <td><input type='submit' name='submit_type' value='Remove'></td>
                            </tr>
                          </form>";
        }
        ?>
        <?php
        if (isset($error)) {
            echo '<div class="error-container" style="display: flex;">' . $error . '</div>';
        }
        ?>
    </table>
</body>

</html>