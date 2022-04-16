<?php
session_start();
include("../helpers/dbConnection.php");


if(!isset($_SESSION['userId'])) {
    header('location: ./../login');
};



$error = false;
$message = "";
$getNewValue = false;
$result = [];
$connection = dbConnection();
$avg = 0;

//Delete the selected grade in the db
if(isset($_POST['delete'])) {
    
    global $connection;
    $id = $_POST["delete"];
    //Call deleteGrade-Procedure in SQL-Script
    $stmt = $connection->query("DELETE FROM Noten WHERE id=$id");
}

//Open the update window
if(isset($_POST['update']) && !isset($_POST["updatedGrade"])) {
    try{
    $_SESSION["gradeIndex"] = $_POST['update'];
    ?>
    <div class="updateGrade">
        <form target="_self" method="post" name="newGrade">
            <div>
                <input placeholder="Neue Note" type="number" step="any" name="updatedGrade" id="updatedGrade" class="field2">
            </div>
            <div>
               <button type="submit" class="button2">Aktualisieren</button>
            </div>
        </form>
    </div>
<?php
    } catch (Exception $e) {
        $error = true;
        $message = "Überprüfen Sie Ihre Eingaben";
    }
}

//Update the given new grade in the database
if(isset($_POST["updatedGrade"])) {
    Try {
        global $connection, $result;
        $res = $_SESSION['allGrades'];
        $req = $res[$_SESSION['gradeIndex']];
        //Call addGrade-Procedure in SQL-Script
        $sql = "EXEC addGrade @note=:note, @fach=:fach, @userId=:userId, @credits=:credits";
        $build = $connection->prepare($sql);
        $build->execute(array(
                ':note' => $_POST["updatedGrade"],
                ':fach' => $req['fach'],
                ':userId' => $req['nutzer'],
                ':credits' => $req['credits']
        ));
    }catch (Exception $e) {
        $error = true;
        $message = "Überprüfen Sie Ihre neu eingegebene Note";
    }
}

//Get all grades from a user
function getGrades() {
    try {
        global $result, $connection, $error, $message;
        //Call getGrades-Procedure in SQL-Script
        $sql = "SELECT * FROM Noten n INNER JOIN Nutzer r ON n.nutzer=r.userId";
        $build = $connection->prepare($sql);
        $build->execute(array(':userId' => intval($_SESSION["userId"])));
        while ($grades = $build->fetch()) {
            array_push($result, [
                'note' => $grades["note"],
                'id' => $grades["id"],
                'durchgefallen' => $grades["durchgefallen"],
                'credits' => $grades["username"],
                'fach' => $grades["fach"],
                'versuch' => $grades["versuch"],
                'nutzer' => $grades['nutzer']
            ]);
        }
        $_SESSION['allGrades'] = $result;
    } catch (Exception $e) {
        $error = true;
        $message = "Server-Error";
    }
}

//Calculate the avg grade and save it in the db
function getAvg() {
    try {
        global $connection, $avg, $error, $message;
        //Call getAvg-Procedure in SQL-Script
        $sql = "EXECUTE getAvg @userId=:userId";
        $build = $connection->prepare($sql);
        $build->execute(array(':userId' => intval($_SESSION["userId"])));
        $res = $build->fetch();
        $avg = round($res["durchschnitt"], 2);
    }catch (Exception $e) {
        $error = true;
        $message = "Server-Error";
    }
}

//Add a new grade and save it in the db
if(isset($_POST['fach']) && isset($_POST['note']) && isset($_POST['credits'])) {
    try {
        $note = $_POST["note"];
        $fach = $_POST["fach"];
        $nutzer = $_SESSION["userId"];
    
        //Call addGrade-Procedure in SQL-Script
        $sql = "INSERT INTO Noten (note, fach, nutzer) VALUES ($note, '$fach', $nutzer) "; //"EXEC addGrade @note=:note, @fach=:fach, @userId=:userId, @credits=:credits";
        $build = $connection->prepare($sql);
        $build->execute($data);
        $getNewValue = true;
    } catch (Exception $e) {
        $error = "true";
        $message = "Überprüfen Sie Ihre Eingaben bei der Notenvergabe";
    }
}

//Logout to the login window
if (isset($_POST["logOut"])) {
    header("location: ../index.php");
}

include("../helpers/includes.php");
?>
<body>
    <div class="durch">
        <strong style="color: white";>Durchschnitt</strong>
    </div>
    <div class="logOut">
        <form target="_self" method="post">
            <button id="logOut" name="logOut" class="logOutButton">LogOut</button>
        </form>
    </div>
    <div class="logOut">
    </div>
    <div class="durch">
        <strong style="color: white;"> <?php
        //Calculate the avg grade after every action and display it
        getAvg();
        echo($avg)
        ?></strong>
    </div>
    <div class="display2">
        <form target="_self" method="post">
            <div class="flexChild">
                <input id="fach" name="fach" type="text" placeholder="fach" class="field">
            </div>
            <div class="flexChild">
                <input id="note" name="note" type="number" step="any" placeholder="note" class="field">
            </div>
            <div class="flexChild">
                <input id="credits" name="credits" type="number" placeholder="credits" class="field">
            </div>
            <div class="flexChild">
                <button type="submit" class="button"><strong>Note hinzufügen</strong></button>
            </div>
        </form>
    </div>
    <div class="display">
        <div class="divProp">
            <strong>FACH</strong>
        </div>
        <div class="divProp">
            <strong>Username</strong>
        </div>
        <div class="divProp">
            <strong>Versuch</strong>
        </div>
        <div class="divProp">
            <strong>Note</strong>
        </div>
    </div>
    <?php
    //Get the newest grades and display them
    getGrades();
    foreach ($result as $key => $item) { ?>
    <div class="display">
        <div class="divProp">
            <?php echo($item["fach"]) ?>
        </div>
        <div class="divProp">
            <?php echo($item["credits"]) ?>
        </div>
        <div class="divProp">
            <?php echo($item["versuch"]) ?>
        </div>
        <div class="<?php if($item["durchgefallen"] == 1) echo "divFail"; if($item["durchgefallen"] == 0) echo "divProp" ?>">
             <?php echo($item["note"]) ?>
        </div>
        <form target="./delete.php" method="get">
            <div class="configFlex">
                <div class="config">
                    <button type="submit" class="button3" name="update" value=<?php echo $key; ?>>Update</button>
                </div>
                <div class="config">
                    <?php if( $_SESSION['admin'] == true) { ?>
                        <button type="submit" class="button4" style="background-color: #b76666" value="<?php echo $item["id"]; ?>" name="delete">Delete</button>
                    <?php } ?>
                </div>
            </div>
        </form>
    </div>
    <?php }
    //Include the errorHandlr for all possible errors
    include("../helpers/errorHandler.php");
    handler($error, $message, 2);
    ?>
</body>