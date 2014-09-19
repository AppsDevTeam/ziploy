<?php
class DiffInstaller {

  function install($diff) {
    
    if(!file_exists($diff))
      return;

    gc_enable();

    $root = __DIR__ . "/../../";

    $archive = new ZipArchive();
    $archive->open($diff);
    $bower = $archive->locateName("bower.json");

    $webDir = $root . "web";
    if(!file_exists($webDir))
      mkdir($webDir);

    $privateDir = $root . "private";
    if(!file_exists($privateDir))
      mkdir($privateDir);

    if($bower) {
      $bowerDir = $root . "web/vendor";
      if(file_exists($bowerDir))
        $this->rrmdir($bowerDir);
      mkdir($bowerDir);
    }

    $composer = $archive->locateName("composer.json");  
    if($composer) {
      $composerDir = $root . "private/vendor";
      if(file_exists($composerDir))
        $this->rrmdir($composerDir);
      mkdir($composerDir);
    }
        
    $tempDir = $root . "private/temp";
    $this->rrmdir($tempDir);
    mkdir($tempDir);

    $archive->extractTo($root);
    @unlink($diff);
    unset($archive);

    gc_collect_cycles();
    gc_disable();
  }

  function rrmdir($dir) { 
   if (is_dir($dir)) { 
     $objects = scandir($dir); 
     foreach ($objects as $object) { 
       if ($object != "." && $object != "..") { 
         if (filetype($dir."/".$object) == "dir") $this->rrmdir($dir."/".$object); else unlink($dir."/".$object); 
       } 
     } 
     reset($objects); 
     rmdir($dir); 
   } 
 }
}

$diffFile = __DIR__ . "/../diff.zip";
$installer = new DiffInstaller();
$installer->install($diffFile);