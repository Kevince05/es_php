<?php

session_start();

$db = new mysqli("localhost", "root", "","cesco");

if ($db->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(array_key_exists("REQUEST_TYPE", $_SERVER)){
    if($_SERVER["REQUEST_TYPE"] === "POST"){

        $usr = $_POST["usr"];
        $scrt_lvl = $_POST["scrt_lvl"];
    
        if($_POST["submit_type"] == "Remove"){
            if(!$db->query("DELETE FROM users WHERE username = '$usr'")){
                echo "Something went wrong :(";
                die("Something went wrong :(");
            }
        }else{
            //TODO: add id to db, use id to delete and edit
            $db->query("UPDATE table_name SET WHERE condition;");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Admin</title>
    </head>
    <body>
        <?php
            $usr = $_SESSION["usr"];
            $result = $db->query("SELECT  security_lvl FROM users WHERE username = '$usr'");
            if($result->fetch_assoc()["security_lvl"] < 1){
                header("Location:index.php");
                die("Not an admin");
            }else{
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
                foreach($result as $row){
                    echo "<form action='admin.php' method='POST'>
                            <tr>
                                <td><input type='text' value=".$row["username"]." name='usr'></td>
                                <td><input type='number' value=".$row["security_lvl"]." name='scrt_lvl' min='0' max='1' step='1'></td>
                                <td><input type='submit' name='submit_type' value='Edit'></td>
                                <td><input type='submit' name='submit_type' value='Remove'></td>
                            </tr>
                          </form>";
                }
            ?>
            
        </table>
    </body>
</html>