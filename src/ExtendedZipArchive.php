<?php 
/**
 * @author Vojtech Studenka | www.appsdevteam.com 
 */
class ExtendedZipArchive extends ZipArchive { 

  private $directories = Array();

  protected $outputFilename;

  public $movedFoldersCount = 0;

  public $movedFilesCount = 0;

  protected $moved = Array();

  private function move($filename, $name) {
    if(!file_exists($filename))
      return;

    if(isset($this->moved[$filename]))
      return;

    $this->movedFilesCount++;
    $this->moved[$filename] = true;  
    $this->addFromString($name, file_get_contents($filename));
  }

  public function addFile($filename, $localname = NULL, $start = 0, $length = 0) {
    $this->movedFilesCount++;
    return parent::addFile($filename, $localname, $start, $length);
  }

  public function open($filename, $flags = null) {
    $this->outputFilename = $filename;
    return parent::open($filename, $flags);
  }

  public function addDir($location, $name) { 
    if(isset($this->directories[$name]))
      return;

    // echo "Moving directory: $name\n\r";

    $this->movedFoldersCount++;
    $this->directories[$name] = true;

    return $this->addDirFiles($location, $name); 
  }

  public function cleanDir($name) {
    // echo "Cleaning directory: $name \n\r";
    $this->deleteName($name);
    $this->addEmptyDir($name);
  }

  public function close() {    
    // echo "\nMoved {$this->movedFilesCount} files and {$this->movedFoldersCount} folders. ";
    return parent::close();
  }

  private function addDirFiles($location, $name) {       
      $location = realpath($location);
      $location = str_replace('\\', '/', $location);
      
      $recursiveDirectoryIterator = new RecursiveDirectoryIterator($location, 
        RecursiveDirectoryIterator::SKIP_DOTS);

      $files = new RecursiveIteratorIterator($recursiveDirectoryIterator, 
        RecursiveIteratorIterator::SELF_FIRST);
      
      foreach($files as $file) {
        
        $filePath = realpath($file);
        $filePath = str_replace('\\', '/', $filePath);        

        $parts = explode($location, $filePath);
                
        if(!isset($parts[1]) || empty($parts[1]))
         continue;

        
        $relativePath = $name . $parts[1];

        if(is_dir($filePath)) {                             
          $this->addEmptyDir($relativePath);
          $this->addDir($filePath, $relativePath);          
        } else {          
          $filename = $relativePath;  
          $this->move($filePath, $filename);          
        }
      }
      
  }
} 
