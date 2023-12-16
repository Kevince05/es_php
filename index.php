<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    switch ($_POST["submit_type"]) {
        case 'Logout':
            $_SESSION["usr"] = null;
            $_SESSION["md5_pwd"] = null;
            break;
        case 'Admin':
            header("Location:admin.php");
            break;
    }
}

$db = new mysqli("localhost", "root", "", "cesco");

if ($db->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_SESSION["usr"]) && isset($_SESSION["md5_pwd"])) {
    $usr = $_SESSION["usr"];
    $pwd = $_SESSION["md5_pwd"];
    $result = $db->query("SELECT username, md5_password FROM users WHERE username='$usr' AND md5_password='$pwd'");
    if ($result->num_rows == 0) {
        header("Location:login.php");
    }
} else {
    header("Location:login.php");
}

function parse_description($desc)
{

    while (preg_match('/\\[[^\\]]*\\]\\([^)]*\\)/i', $desc, $repl)) {
        $link = substr($repl[0], strpos($repl[0], "(") + 1, strpos($repl[0], ")") - 1);
        $text = substr($repl[0], strpos($repl[0], "[") + 1, strpos($repl[0], "]") - 1);
        $desc = str_replace($repl[0], "<a href=" . $link . ">" . $text . "</a>", $desc);
        $desc = str_replace("\r\n", "<br>", $desc);
    }

    return $desc;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="style/style_index.css">
</head>

<body>
    <header>
        <h1>Thingiverse Maker</h1>
        <form action="index.php" method="post">
            <input class="header_buttons_logout" type="submit" name="submit_type" value="Logout">
            <?php
            $usr = $_SESSION["usr"];
            $result = $db->query("SELECT security_lvl FROM users WHERE username='$usr'");
            if ($result->fetch_assoc()["security_lvl"] > 0) {
                echo "<input class='header_buttons_admin' type='submit' name='submit_type' value='Admin'>";
            }
            ?>
        </form>
    </header>
    <?php
    $result = $db->query("SELECT * FROM products");
    foreach ($result as $row) {
        $api_url = "https://api.thingiverse.com/things/" . str_split($row["thingiverse_link"], 34)[1] . "?access_token=bb7e3c709f50c76f50dd9b3579effce6";
        $curl = curl_init($api_url);
        curl_setopt($curl, CURLOPT_URL, $api_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($curl);
        $json_data = json_decode($resp, true);
        //debug//file_put_contents("../debug/api_data_" . $row["name"] . ".json", json_encode($json_data));
        if ($resp) {
            echo "<div class='product_container'>
                    <div class='product_img_container'>
                            <img src= " . $json_data["thumbnail"] . " alt='thumb''>
                    </div>
                    <h2>" . $row["name"] . "</h2>
                    <div class='description_container'>
                        <p>" . parse_description(substr($json_data["description"], 0, 500)) . "</p>
                        <a href=" . $row["thingiverse_link"] . ">Learn more</a>
                    </div>                            
                  </div>
                  <div class='buy_container'> 
                    <form action='order.php' method='post'>
                        " . $row["price"] . "&euro; x 
                        <input type='hidden' name='thumbnail_link' value=" . $json_data["thumbnail"] . ">
                        <input type='hidden' name='prod_id' value=" . $row["id"] . ">
                        <input type='number' name='quantity' min='1' step='1' max='1000' value='1'>
                        <input class='buy_button' type='submit' name='submit_type' value='Buy'>
                    </form>
                  </div>";
        }
    }
    curl_close($curl);
    ?>
</body>

</html>