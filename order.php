<?php
session_start();
$error = null;
$db = new mysqli("localhost", "root", "", "cesco");

if ($db->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($_POST["submit_type"]) {

        case 'Buy':
            $thumb_link = $_SESSION["thumb_link"] = $_POST["thumbnail_link"];
            $prod_id = $_SESSION["id_prod"] = $_POST["prod_id"];
            $quantity = $_SESSION["quantity"] = $_POST["quantity"];
            break;

        case 'BUY':
            $id = $db->query("SELECT max(id) FROM orders")->fetch_row()[0] + 1;
            $usr = $_SESSION["usr"];
            $id_usr = $db->query("SELECT id FROM users WHERE username = '$usr'")->fetch_row()[0];
            $id_prod = $_SESSION["id_prod"];
            $quantity = $_SESSION["quantity"];
            $addr = $_POST["addr"];
            $payment_id = sha1($_POST["card_name"] . $_POST["card_name"] . $_POST["card_n"]. $_POST["card_date"] . $_POST["card_cvv"]);
            //facciamo finta di fare una richiesta a visa o mastercard e facciamo finta mi torni una key che rappresenta la transazione
            $status = 0;
            $result = $db->query("INSERT INTO orders (id,id_user,id_product,quantity,delivery_address,payment_request_id,status) VALUE ('$id','$id_usr','$id_prod','$quantity','$addr','$payment_id','$status')");	
            
            if($result){
                header("Location:index.php");
            }else{
                $error = "Something went wrong :(";
            }

            break;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="style/style_order.css">
    <title>Order</title>
</head>

<body>
    <header>
        <h1>Send order</h1>
        <form class="" action="admin.php" method="post">
            <input class="header_buttons" type="submit" name="submit_type" value="Home">
        </form>
    </header>
    <div>
        <?php
        $row = $db->query("SELECT * FROM products WHERE id='$prod_id'")->fetch_assoc();
        if ($row) {
            echo "<div class='product_container'>
                    <div class='product_img_container'>
                        <img src= " . $thumb_link . " alt='thumb''>
                    </div>
                    <p>
                    <h2>" . $row["name"] . "</h2>
                    " . $row["price"] . "&euro; x " . $quantity . " = " . $row["price"] * $quantity . "&euro;
                    </p>
                  </div>";
        }

        if (isset($error)) {
            echo '<div class="error-container" style="display: flex;">' . $error . '</div>';
        }
        ?>

        <form class="form_container" action="order.php" method="post">
            <input class="buy_button" type="submit" name='submit_type' value="BUY">
            <div style="flex: 1;">
                <label for="addr">Address</label>
                <input type="text" name="addr" id="addr" required><br>
                <label for="card_name">Card holder</label>
                <input type="text" name="card_name" id="card_name" required><br>
                <label for="ard_n">Card numeber</label>
                <input type="text" name="card_n" id="ard_n" required><br>
                <label for="card_date">Expiration date</label>
                <input type="date" name="card_date" id="card_date" required><br>
                <label for="card_cvv">CVV</label>
                <input type="number" name="card_cvv" id="card_cvv" required><br>
            </div>
        </form>
    </div>
</body>

</html>