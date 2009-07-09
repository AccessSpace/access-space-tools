<?php

  $iSecondsPerTick = 5;
  $iSecondsPerPage = 10;
  
  $iCycleStarted = $_REQUEST['iCycleStarted'];
  if(!$iCycleStarted)
  {
    $iCycleStarted = time();
  }
  
  $iLastMod = 0;
  
  //$d = new RecDir($sData = '/home/public/infopages/', false);
  $d = new RecDir($sData = 'pages/', false);
  $aPossPagesURLs = array();
  while (false !== ($entry = $d->read()))
  {
    $aPossPagesURLs[] = $entry;
    //echo "\n<br><pre>\nentry =" .var_export($entry, TRUE)."</pre>";
    $iCurrMod = filemtime($entry);
    //echo "\n<br><pre>\niCurrMod  =" .var_export($iCurrMod , TRUE)."</pre>";
    if ($iLastMod < $iCurrMod)
    {
      $iLastMod = $iCurrMod;
    }
  }
  $d->close();
  
  
  sort($aPossPagesURLs);
  
  if ($iCycleStarted > $iLastMod)
  {
    include('urlcache.php');
  }
  
  $iTicksPerPage = floor($iSecondsPerPage / $iSecondsPerTick);
  
  if (!isset($aURLs))
  {
    foreach( $aPossPagesURLs as $sPossURL)
    {
      for( $i = 0 ; $i < $iTicksPerPage; $i++)
      {
        $aURLs[] = $sPossURL;
      }
    }
    $sURLs = var_export($aURLs, true);
    
    $sPHP = "<?php\n\$aURLs = $sURLs;";
    file_put_contents('urlcache.php', $sPHP);
    $iCycleStarted = time();
  }
  $iCount = count($aURLs);
  $iCurrPos = ((time() - $iCycleStarted)/$iSecondsPerTick) % count($aURLs);
  $sURL = $aURLs[$iCurrPos];
  
  echo json_encode(array('sURL' => $sURL, 'iCycleStarted'=>$iCycleStarted));
  
  exit;
  
  
  
  
  
class RecDir
{
   protected $currentPath;
   protected $slash;
   protected $rootPath;
   protected $recursiveTree;  

   function __construct($rootPath,$win=false)
   {
      switch($win)
      {
         case true:
            $this->slash = '\\';
            break;
         default:
            $this->slash = '/';
      }
      $this->rootPath = $rootPath;
      $this->currentPath = $rootPath;
      $this->recursiveTree = array(dir($this->rootPath));
      $this->rewind();
   }

   function __destruct()
   {
      $this->close();
   }

   public function close()
   {
      while(true === ($d = array_pop($this->recursiveTree)))
      {
         $d->close();
      }
   }

   public function closeChildren()
   {
      while(count($this->recursiveTree)>1 && false !== ($d = array_pop($this->recursiveTree)))
      {
         $d->close();
         return true;
      }
      return false;
   }

   public function getRootPath()
   {
      if(isset($this->rootPath))
      {
         return $this->rootPath;
      }
      return false;
   }

   public function getCurrentPath()
   {
      if(isset($this->currentPath))
      {
         return $this->currentPath;
      }
      return false;
   }
  
   public function read()
   {
      while(count($this->recursiveTree)>0)
      {
         $d = end($this->recursiveTree);
         if((false !== ($entry = $d->read())))
         {
            if($entry!='.' && $entry!='..')
            {
               $path = $d->path.$entry;
              
               if(is_file($path))
               {
                  return $path;
               }
               elseif(is_dir($path.$this->slash))
               {
                  $this->currentPath = $path.$this->slash;
                  if($child = @dir($path.$this->slash))
                  {
                     $this->recursiveTree[] = $child;
                  }
               }
            }
         }
         else
         {
            array_pop($this->recursiveTree)->close();
         }
      }
      return false;
   }

   public function rewind()
   {
      $this->closeChildren();
      $this->rewindCurrent();
   }

   public function rewindCurrent()
   {
      return end($this->recursiveTree)->rewind();
   }
}
