<?php
function dbConnection() {
        $serverName = "mssqlserver";
        $databaseName = "company";
        $uid = "root";
        $pwd = "root";
        $b_port=3306;
        return new PDO("mysql:dbname=$databaseName;host=$serverName;port=$b_port", $uid, $pwd);
    }

    