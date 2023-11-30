<?php

session_start();
$error = null;
$db = new mysqli("localhost", "root", "", "cesco");

if ($db->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    switch ($_POST["submit_type"]) {
        case "Remove User":
            $id = $_POST["id"];
            if (!$db->query("DELETE FROM users WHERE id = '$id'")) {
                $error = "Delete went wrong :(";
            }
            break;
        case "Edit User":
            $id = $_POST["id"];
            $usr = $_POST["usr"];
            $scrt_lvl = $_POST["scrt_lvl"];
            if (!$db->query("UPDATE users SET username='$usr', security_lvl='$scrt_lvl' WHERE id = '$id'")) {
                $error = "User edit went wrong :(";
            }
            break;

        case 'Remove Product':
            break;
            $id = $_POST["id"];
            if (!$db->query("DELETE FROM products WHERE id = '$id'")) {
                $error = "User delete went wrong :(";
            }

        case 'Edit Product':
            $id = $_POST["id"];
            $name = $_POST["name"];
            $price = $_POST["price"];
            $thg_link = $_POST["thg_link"];
            if (!$db->query("UPDATE products SET name='$name', price='$price', thingiverse_link='$thg_link' WHERE id = '$id'")) {
                $error = "Product edit went wrong :(";
            }
            break;
        case "Add Product":
            $id = $db->query("SELECT max(id) FROM products")->fetch_row()[0] + 1;
            $name = $_POST["name"];
            $price = $_POST["price"];
            $thg_link = $_POST["thg_link"];
            if (!$db->query("INSERT INTO products (id,name, price, thingiverse_link) VALUE ('$id','$name', '$price', '$thg_link')")) {
                $error = "Product edit went wrong :(";
            }
            break;
        case 'Edit Order':
            $id = $_POST["id"];
            $del_addr = $_POST["del_addr"];
            if (!$db->query("UPDATE order SET delivery_address='$del_addr' WHERE id = '$id'")) {
                $error = "Order edit went wrong :(";
            }
            break;
        case 'Remove Order':
            $id = $_POST["id"];
            if (!$db->query("DELETE FROM orders WHERE id = '$id'")) {
                $error = "User delete went wrong :(";
            }
            break;
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
    $result = $db->query("SELECT security_lvl FROM users WHERE username = '$usr'");
    if ($result->fetch_assoc()["security_lvl"] < 1) {
        header("Location:index.php");
        die("Not an admin");
    } else {
        echo "<h1>Admin Console</h1>";
    }
    ?>
    <h2>Users</h2>
    <table>
        <tr>
            <th>Username</th>
            <th>Security Level</th>
        </tr>
        <?php
        $result = $db->query("SELECT * FROM users");
        foreach ($result as $row) {
            echo "<form action='admin.php' method='POST'>
                            <tr>
                                <input type='hidden' value=" . $row["id"] . " name='id' class='table_format_input'>
                                <td><input type='text' value=" . $row["username"] . " name='usr' class='table_format_input'></td>
                                <td><input type='number' value=" . $row["security_lvl"] . " name='scrt_lvl' min='0' max='1' step='1' class='table_format_input'></td>
                                <td><input type='submit' name='submit_type' value='Edit User' class='table_format_input'></td>
                                <td><input type='submit' name='submit_type' value='Remove User' class='table_format_input'></td>
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
    <h2>Products</h2>
    <table>
        <tr>
            <th>Name</th>
            <th>price</th>
            <th>Thingiverse Link</th>
        </tr>
        <?php
        $result = $db->query("SELECT * FROM products");
        foreach ($result as $row) {
            echo "<form action='admin.php' method='POST'>
                            <tr>
                                <input type='hidden' value=" . $row["id"] . " name='id' class='table_format_input'>
                                <td><input type='text' value=" . $row["name"] . " name='name' class='table_format_input'></td>
                                <td><input type='number' value=" . $row["price"] . " name='price' min='0' step='0.01' class='table_format_input'></td>
                                <td><input type='text' value=" . $row["thingiverse_link"] . " name='thg_link' class='table_format_input'></td>
                                <td><input type='submit' name='submit_type' value='Edit Product' class='table_format_input'></td>
                                <td><input type='submit' name='submit_type' value='Remove Product' class='table_format_input'></td>
                            </tr>
                          </form>";
        }
        ?>
    </table>
    <h3>Add Procuct</h3>
    <div class="container">
        <form action="admin.php" method="POST">
            Name: <input type="text" name="name" class="add_format_input">
            Price: <input type="number" name="price" class="add_format_input" min='0' step='0.01'>
            Thingiverse Link: <input type="text" name="thg_link" class="add_format_input">
            <div style="text-align: right;">
                <input type="submit" name="submit_type" value="Add Product">
            </div>
        </form>
    </div>
    <h2>Order List</h2>
    <table>
        <tr>
            <th>User</th>
            <th>Product</th>
            <th>Quantity</th>
            <th>Delivery Address</th>
        </tr>
        <?php
        $result = $db->query("SELECT * FROM orders");
        foreach ($result as $row) {
            $usr_id = $row["id_user"];
            $prod_id = $row["id_product"];
            echo "<form action='admin.php' method='POST'>
                            <tr>
                                <input type='hidden' value=" . $row["id"] . " name='id' class='table_format_input'>
                                <td>" . $db->query("SELECT username FROM users WHERE id = '$usr_id'")->fetch_row()[0] . "</td>
                                <td>" . $db->query("SELECT name FROM products WHERE id = '$prod_id'")->fetch_row()[0] . "</td>
                                <td>" . $row["quantity"] . "</td>
                                <td><input type='text' value=" . $row["delivery_address"]. " name='del_addr' class='table_format_input'></td>
                                <td></td>
                                <td><input type='submit' name='submit_type' value='Edit Order' class='table_format_input'></td>
                                <td><input type='submit' name='submit_type' value='Remove Order' class='table_format_input'></td>
                            </tr>
                          </form>";
        }
        ?>
    </table>
</body>

</html>