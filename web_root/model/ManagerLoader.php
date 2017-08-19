<?php
require_once 'DatabaseManager.php';
require_once 'MongoDBManager.php';

$currentFilename = basename(__FILE__);

foreach (scandir(dirname(__FILE__)) as $filename) {
    $path = dirname(__FILE__) . '/' . $filename;

    if($filename == $currentFilename)
    	continue;

    if($filename == "config.php")
    	continue;

    if($filename == "FormSubmitManager.php")
    	continue;

    if($filename == "DatabaseManager.php")
        continue;

    if($filename == "MongoDBManager.php")
        continue;

    if(strpos($filename, "Manager") < 0)
    	continue;

    if (is_file($path)) {
        // echo $path."<br>";
        require_once $path;
    }
}

?>