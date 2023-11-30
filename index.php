<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    if ($_POST["submit_type"] == "Logout") {
        $_SESSION["usr"] = null;
        $_SESSION["md5_pwd"] = null;
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
    </header>

    <section id="main-content">
        <?php
        $result = $db->query("SELECT * IN product")
        foreach(){

        }
        $content=file_get_contents($url);
        if (preg_match("/<img.*src=\"(.*)\".*class=\".*pinit\".*>/", $content, $matches)) 
        {
        echo $matches[0];
        }
        ?>
    </section>

    <footer>
        <p>&copy;</p>
    </footer>
</body>

</html>