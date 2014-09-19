<?php
$installerFile = __DIR__ . "/../private/build/DiffInstaller.php";

if(file_exists($installerFile)) 
  require $installerFile; 

@unlink(__FILE__);