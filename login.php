

<!DOCTYPE html>
<html>
    <head>
        <title>Login</title>
    </head>
    <body>
        <form action="login.php" method="POST">
            Username: <input type="text" name="usr"><br>
            Password: <input type="password" name="pwd"><br>
            Admin:  <input type="checkbox" name="scrt_lvl"><br>
            <input type="submit" name="submit_type" value="Login">
            <input type="submit" name="submit_type" value="Register">
        </form>   
        <?php
            session_start();

            $db = new mysqli("localhost", "root", "","cesco");
            
            if ($db->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $usr = $_POST["usr"];
                $pwd = md5($_POST["pwd"]);
                
                if($_POST["submit_type"] === "Login"){
                    $result = $db->query("SELECT username, md5_password FROM users WHERE username='$usr' AND md5_password='$pwd'"); 
                    if($result->num_rows == 1){
                        $_SESSION["usr"] = $usr;
                        $_SESSION["md5_pwd"] = $pwd;
                        header("Location:index.php");
                    }else{
                        echo "Wrong user or password";
                    }
                }else{
                    $scrt_lvl = $_POST["scrt_lvl"]?1:0;
                    $result = $db->query("INSERT INTO users (username, md5_password, security_lvl) VALUE ('$usr', '$pwd', '$scrt_lvl')");
                    if($result === TRUE){
                        $_SESSION["usr"] = $usr;
                        $_SESSION["md5_pwd"] = $pwd;
                        $_SESSION["scrt_lvl"] = $scrt_lvl;
                        header("Location:index.php");
                    }else{
                        echo "Error:" . $db -> error;
                    }                     
                }         
            }
        ?>
    </body>
</html>