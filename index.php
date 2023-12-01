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
            <input class="header-buttons" type="submit" name="submit_type" value="Logout">
            <?php
                if($db->query("SELECT security_lvl FROM users WHERE username='$_SESSION[usr]'")->fetch_assoc()["security_lvl"] > 0){
                    echo "<input class='header-buttons' type='submit' name='submit_type' value='Admin'>";
                }
            ?>
        </form>
    </header>

    <section id="main-content">
        <?php
        $result = $db->query("SELECT * FROM products");
        foreach ($result as $row) {
            $api_url = "https://api.thingiverse.com/things/" . str_split($row["thingiverse_link"], 34)[1] . "?access_token=bb7e3c709f50c76f50dd9b3579effce6";
            $curl = curl_init($api_url);
            curl_setopt($curl, CURLOPT_URL, $api_url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $resp = curl_exec($curl);
            if($resp){
                echo "<div class='product_container'>
                          <div class='product_img_container'>
                              <img class='product_img' src= " . json_decode($resp, true)["thumbnail"] . " alt='thumb''>
                          </div>
                      </div>";
            }
        }
        curl_close($curl);
        ?>

    </section>
</body>

</html>