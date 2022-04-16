<?php
session_start();
include("./helpers/dbConnection.php");

$error = false;
$message = "";
$connection = dbConnection();
$myfile = fopen("./pages/log.txt", "a") or die("File not reachable");

//Handle the login
if(isset($_POST["username"]) && isset($_POST["password"])) {
    try {
        if ($_POST["username"] != "" && $_POST["password"] != "") {
            $username = $_POST["username"];
            $password = $_POST["password"];
            $logtext = date("d.m.Y:H:i:s").": Logging attempt: with username: $username and password: $password\n";
            fwrite($myfile, $logtext);
            $stmt = $connection->query("SELECT * FROM Nutzer WHERE username='$username' AND passwort='$password'");
            $result = $stmt->fetch();
            if ($username == "admin") $_SESSION['admin'] = true;
            //Check if logIn is valid
            if (sizeof($result) >= 1) {
                $_SESSION['username'] = $result['username'];
                $_SESSION['userId'] = $result['userId'];
                header('location: ./pages/dashBoard.php');
            } else {
                
                $error = true;
                $message = "Der angegebene Username oder das Passwort ist Inkorrekt";
            }
        } else {
            $error = true;
            $message = "Überprüfen Sie Ihre Eingaben";
        }
    } catch (Exception $e) {
        $error = true;
        $message = "Server-Error";
    }
}

include("./helpers/errorHandler.php");
include("./helpers/includes.php");
?>
<body>
<div>
    <div style="text-align: center; color: white; padding-top: 2em; padding-bottom: 2em">
        <strong>Anmelden</strong>
    </div>
    <form target="signUp.php" method="post">
        <div class="signUpField">
            <input id="username" name="username" type="text" class="field" placeholder="Username">
        </div>
        <div class="signUpField">
            <input id="password" name="password" type="password" class="field" placeholder="Passwort">
        </div>
        <div class="signUpField">
            <button type="submit" class="button">Anmelden</button>
        </div>
        <div class="lol" style="padding-top: 1em;">
            <a href="../pages/signUp.php" style="color: white; ">Noch keinen Account? Registrieren Sie sich hier!</a>
        </div>
    </form>
</div>
<?php
//Call errorHandler for all possible errors
handler($error, $message, 1);
?>
</body>