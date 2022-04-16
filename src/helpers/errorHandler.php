<?php
function handler($error, $msg, $zahl) {
    if($zahl == 1) {
        include("./helpers/includes.php");
    }else{
        include("../helpers/includes.php");
    }
    if($error == true) {
        ?>
        <div class="errorBox">
            <?php
            //Display the given error
            echo($msg) ?>
        </div>
        <?php
    }
}
?>