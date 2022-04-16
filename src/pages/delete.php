<?php

include("../helpers/dbConnection.php");

$connection = dbConnection();

//Delete the selected grade in the db
if(isset($_GET['delete'])) {
    global $connection;
    $id = $_GET["delete"];
    //Call deleteGrade-Procedure in SQL-Script
    $stmt = $connection->query("DELETE FROM Noten WHERE id=$id");
    header("location: ./dashBoard.php");
}



?>