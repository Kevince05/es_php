<?php
    session_start();

    if($_SERVER["REQUEST_METHOD"] === 'POST'){
        if($_POST["submit_type"] == "Logout"){
            $_SESSION["usr"] = null;
            $_SESSION["md5_pwd"] = null;
        }
    }

    $db = new mysqli("localhost", "root", "","cesco");

    if ($db->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if(isset($_SESSION["usr"]) && isset($_SESSION["md5_pwd"])){
        $usr = $_SESSION["usr"];
        $pwd = $_SESSION["md5_pwd"];

        $result = $db->query("SELECT username, md5_password FROM users WHERE username='$usr' AND md5_password='$pwd'");                    
        if($result->num_rows == 0){
            header("Location:login.php");
        }
    }else{
        header("Location:login.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Index</title>
    </head>
    <body>
        <?php
            echo "Ciao ". $_SESSION["usr"] ."(md5: ". $_SESSION["md5_pwd"]. ")";
        ?>
        <form action="index.php" method="post">
            <input type="submit" name="submit_type" value="Logout">
        </form>
    </body>
</html>