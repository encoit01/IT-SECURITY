<?php
session_start();
include("../helpers/dbConnection.php");

$error = false;
$message = "";

if (isset($_POST["username"]) && isset($_POST["password"])) {

        if($_POST["password"] != "" && $_POST["username"] != ""){
            try {
                //Check is username already given
                $connection = dbConnection();
                $data = [
                    $username = $_POST["username"],
                ];
                $sql = "SELECT COUNT(*) AS counter FROM Nutzer WHERE username=:username";
                $build = $connection->prepare($sql);
                $build->execute($data);
                $result = $build->fetch(PDO::FETCH_ASSOC);

                if($result['counter'] == 0) {
                    //Add with addUser-Procedure of SQL-Script
                    $sql = "EXEC addUser @username=:username, @password=:password";
                    $build = $connection->prepare($sql);
                    $build->execute(array(':username' => $_POST["username"], ':password' =>  password_hash($_POST["password"], PASSWORD_BCRYPT)));
                    if($build) {
                        //Get user with getUser-Procedure of SQL-Script
                        $sql = "EXEC getUser @username=:username";
                        $build = $connection->prepare($sql);
                        $build->execute(array(':username' => $_POST["username"]));
                        $result = $build->fetchAll();
                        //Set session
                        $_SESSION['username'] = $result[0]['username'];
                        $_SESSION['userId'] = $result[0]['userId'];
                        header('location: dashBoard.php');
                    } else {
                        $error = true;
                        $message = "Server-Error";
                    }

                } else {
                    $error = true;
                    $message = "Username ist bereits in Benutzung";
                }
            } catch (Exception $e) {
                $error = true;
                $message = "Server-Error";
            }
        }else{
            $error = true;
            $message = "Überprüfen Sie Ihre Eingaben";
        }
}

include("../helpers/includes.php");
include ("../helpers/errorHandler.php")
?>
<body>
    <div>
        <div style="text-align: center; color: white; padding-top: 2em; padding-bottom: 2em">
            <strong>Registrieren</strong>
        </div>
        <form target="_self" method="post">
            <div class="signUpField">
                <input id="username" name="username" type="text" class="field" placeholder="Username">
            </div>
            <div class="signUpField">
                <input id="password" name="password" type="password" class="field" placeholder="Passwort">
            </div>
            <div class="signUpField">
                <button type="submit" class="button">Registrieren</button>
            </div>
        </form>
    </div>
    <?php
    //Call errorHandler for all possible errors
    handler($error, $message, 2);
    ?>
</body>
