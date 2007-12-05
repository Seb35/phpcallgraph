<?php
function __autoload($classname) {
    require_once "classes/$classname.php";
}

$timer = new Timer();
sleep(1);
echo "Elapsed Time: $timer\n";
?>
