<?php
/**
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Esther Brunner <wikidesign@gmail.com>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC.'lib/plugins/');

class helper_plugin_linkmap extends DokuWiki_Plugin {

  
	  var $iMostRecent = 0;
	  var $aNameSpaces = array();
	  var $iCacheFormat = 0;
	  var $bUseCache = false;
	  var $sBasePath = '';
  
    /**
     * Constructor
     */
    function helper_plugin_linkmap() {
        global $conf;

        // load sort key from settings
        $this->sort = $this->getConf('sortkey');
    }

    function getInfo() {
        return array(
                'author' => 'Martyn Eggleton for access-space.org',
                'email'  => 'martyn@access-space.org',
                'date'   => @file_get_contents(DOKU_PLUGIN . 'linkmap/VERSION'),
                'name'   => 'Linkmap Plugin (helper component)',
                'desc'   => 'Produces maps of where links used within wiki',
                'url'    => 'http://dokuwiki.org/plugin:linkmap',
                );
    }

    function getMethods() {
        $result = array();
        $result[] = array(
                'name'   => 'createMap',
                'desc'   => 'returns linkmap object',
                'params' => array(
                    'namespace' => 'string',
                    'dataheadings'=> 'string',
                    'loadnow (optional)' => 'boolean'),
                'return' => array(),
                );
        return $result;
    }

    
	  function createMap($sNameSpace = null, $sDataHeadings = null, $bLoadNow = true)
	  {
	    $this->sDataHeadings = $sDataHeadings; 
      $this->aHeadings = split(',', $sDataHeadings);
      
      $this->aLevelHeadings = array_combine(range(1,count($this->aHeadings)), $this->aHeadings);
      //echo "this->aLevelHeadings  =" .var_export($this->aLevelHeadings , TRUE)."\n";
      $this->addNameSpace($sNameSpace, $bLoadNow);
	  }
	  
	  function addNameSpace($sNameSpace, $bLoadNow = false)
	  {
	    if(!isset($this->aNameSpaces[$sNameSpace]))
	    {
	      $this->aNameSpaces[$sNameSpace] = $sNameSpace;
	      if($bLoadNow)
        {
          $this->getFilesNames($sNameSpace);
        }
	    }
	  }
	  
	  function getFilesNames($sNameSpace = null)
	  {
	    if($sNameSpace)
	    {
	      $aNameSpaces = array($sNameSpace);
	    }
	    else
	    {
	      $aNameSpaces = $this->aNameSpaces;
	    }
	    
	    foreach($aNameSpaces as $sNameSpace)
      {
        $sNameSpaceDir = $this->sBasePath.'data/pages/'.$sNameSpace.'/';
        
        $sVisiblePattern = '/\/.*[^\~]$/';
        
        $aResults = array();
        $d = new RecDir($sNameSpaceDir, false);
      
        while (false !== ($entry = $d->read())) {
          if(strpos($entry, '.svn') === false && strpos($entry, '_cache') === false  && strpos($entry, '_template.txt') === false)
          {
            if(preg_match($sVisiblePattern, $entry))
            {
             $iCurrent = filemtime($entry);
             if($iCurrent && $iCurrent > $this->iMostRecent)
             {
               $this->iMostRecent = $iCurrent;
             }
             $id = str_replace(array('.txt',$this->sBasePath.'data/pages/','/'), array('','',':'), $entry);
             $this->aFilesToConvert[$id] = $entry;
            }
          }
        }
      }
      $d->close();
      return $this->aFilesToConvert;
    }
    
    function getCacheFileName()
    {
      $sName = 'data/cache/'.$this->iMostRecent."_".$this->iCacheFormat."_".md5(var_export($this->aFilesToConvert, true));
      return $sName;
    }
    
    
    
    function getFullMap()
	  {
	    
	    $sName = $this->getCacheFileName();
	    //echo "sName  =" .$sName ."\n";
	    /*if($this->bUseCache && file_exists($sName))
	    {
	      $sContents = file_get_contents($sName);
	      $this->aMap = json_decode($sContents);
	    }
	    else
	    {*/
	      $this->_getFullMap();
	     /* if($this->bUseCache)
	      {
          $sContents = json_encode($this->aMap);
          //echo "sContents  =" .$sContents ."\n";
          file_put_contents($sName, $sContents);
        }
	    }*/
	  }
	   
	  
    function _getFullMap()
	  {
	    
	    $this->aMap = array(  'aIndex'   => array(),
	                          'aLevels' => array(),
	                          'aNameSpaces' => array(),
	                    );
	    
	    
      //echo "aFilesToConvert =" .var_export($aFilesToConvert, TRUE)."\n";
      foreach($this->aFilesToConvert as $isID => $sFileName)
      {
        
        $aLevels = array();
        
        $aPage = file($sFileName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        //echo "aPage  =" .var_export($aPage , TRUE)."\n";
        
        $iLevel = $iOldLevel= 0;
        
        foreach($aPage as $sLine)
        {
          $iEquals = substr_count($sLine, '=');
          
          if($iEquals)
          {
            $iLevel = 7 - ($iEquals/2);
            $sData = trim($sLine, "= ");
            $sDataType = $aLevelHeadings[$iLevel];
            //echo "aLevelHeadings =" .var_export($aLevelHeadings, TRUE)."\n";
            //echo "sDataType  =" .$sDataType ."\n";
            $aLevels[$sDataType] = $sData;
            //echo "aLevels =" .var_export($aLevels, TRUE)."\n";
            if ($iLevel < $iOldLevel)
            {
              $aLevels = array_splice($aLevels, $iLevel);
              //echo "Dropping level current data = aLevels  =" .var_export($aLevels , TRUE)."\n";
            }
            $iOldLevel = $iLevel;
          }
          else
          {
            $iPos = count($this->aMap['aIndex']);
            $aLine = $this->parseLine($sLine, $iPos);
            $aLine['aLevels'] = $aLevels;
            $aLine['ID'] = $isID;
            $this->aMap['aIndex'][] = $aLine;
            treeSet($aLevels, $this->aMap['aLevels'], $iPos);
            //echo "this->aMap =" .var_export($this->aMap, TRUE)."\n";
          }
        }
      }
      
	    if($this->aLevelHeadings[1] = 'date')
      {
        uksort($this->aMap['aLevels'], "date_cmp");
      // todo : sort by uksort if the levels[0] is date using a date sort.
      }
      else
      {
        ksort($this->aMap['aLevels']);
      }
      
	  }
	  
	  function parseLine($sLine, $iPos)
	  {
	    $aLine = array('sLine'=>$sLine, 'iPos' => $iPos, 'aPages' => array(), 'aInfo' => array());
	    
	    //echo "sLine =" .$sLine."\n";
	    $aMatches = null;
	    preg_match_all('/\[\[([^\]\|]*)(\|([^\]]*))?\]\]/', $sLine, $aMatches);
	    //echo "aMatches =" .var_export($aMatches, TRUE)."\n";
	    
	    foreach($aMatches[1] as $sLink)
	    {
	      $sLink = cleanID($sLink);
	      $aLine['aPages'][] = $sLink;
	      $aKey = split(':', $sLink);
	      treeSet($aKey, $aLine['aInfo'], $iPos, 'aSubs', 'aData');
	      treeSet($aKey, $this->aMap['aNameSpaces'], $iPos, 'aSubs', 'aData');
	      
	    //$this->aMap['aNameSpaces']['project']['artist']['aSub']['online portfolio']
	    //$this->aMap['aNameSpaces']['project']['artist']['aIndexs']
	    }
	    
	    return $aLine;
	  }
    
	  
	  function findLeveled($sSearch)
	  {
	    $aSearch = split(':', trim($sSearch));
	    //echo "\n<br><pre>\naSearch  =" .var_export($aSearch , TRUE)."</pre>";
	    //echo "aSearch  =" .var_export($aSearch , TRUE)."\n";
	    //echo "this->aMap =" .var_export(array_keys($this->aMap), TRUE)."\n";
	    //echo "this =" .var_export($this, TRUE)."\n";
	    $aKeys = treeGet($aSearch, $this->aMap['aNameSpaces'], 'aSubs', 'aData', 'flatten');
	    
	    $aLeveled = array();
	    $aPages = array();
	    if(is_array($aKeys))
	    {
        foreach($aKeys as $iKey)
        {
          $aLine = $this->aMap['aIndex'][$iKey];
          //echo "aLine  =" .var_export($aLine , TRUE)."\n";
          treeSet($aLine['aLevels'], $aLeveled, $aLine['sLine']);
          $aPages[$aLine['ID']] = $aLine['ID'];
        }
	    
	    
        if($this->aLevelHeadings[1] = 'date')
        {
          uksort($aLeveled, "date_cmp");
        // todo : sort by uksort if the levels[0] is date using a date sort.
        }
        else
        {
          ksort($aLeveled);
        }
      }
	    return array('aLeveled' => $aLeveled, 'aPages' => $aPages);
	  }
	  
	  
	  
	  function render_list($sSearch, $sFormat = 'dokuwiki')
	  { 
	    $aSearch = split(':', trim($sSearch));
	    //echo "\n<br><pre>\naSearch  =" .var_export($aSearch , TRUE)."</pre>";
	    //echo "\n<br><pre>\nthis->aMap['aNameSpaces'] =" .var_export($this->aMap['aNameSpaces'], TRUE)."</pre>";
	    
	    $aData = treeGet($aSearch, $this->aMap['aNameSpaces'], 'aSubs', 'aData', 'all');
	    //echo "\n<br><pre>\naData  =" .var_export($aData , TRUE)."</pre>";
	    
	    $sFunctionName = "_render_list_".$sFormat;
	    if(method_exists($this, $sFunctionName))
	    {
	      $sContents = $this->${sFunctionName}($aData, $sSearch);
	      return array('sContents' => $sContents);
	    }
	    return false;
	  }
	  
	  
	  function _render_list_dokuwiki($aData, $sSearch, $iLevel = 1)
	  {
      $aIDs = array();
      $sOutput = '';
      if(is_array($aData['aSubs']))
      {
        ksort($aData['aSubs']);
        foreach($aData['aSubs'] as $sKey => $aSection)
        {
          $sOutput .= str_repeat('  ',$iLevel)."* [[$sSearch:$sKey]] \n";
          $sOutput .= $this->_render_list_dokuwiki($aSection, "$sSearch:$sKey", $iLevel+1);
          //$sOutput .= "\n";
        }
      }
      return $sOutput;
    }
	  
    
    
    
	  
	  function render_related($sSearch, $sFormat = 'dokuwiki')
	  { 
	    $aSearch = split(':', trim($sSearch));
	    //echo "\n<br><pre>\naSearch  =" .var_export($aSearch , TRUE)."</pre>";
	    //echo "\n<br><pre>\nthis->aMap['aNameSpaces'] =" .var_export($this->aMap['aNameSpaces'], TRUE)."</pre>";
	    
	    $aKeys = treeGet($aSearch, $this->aMap['aNameSpaces'], 'aSubs', 'aData', 'flatten');
	    
	    $aData = array();
	    
	    foreach($aKeys as $iKey)
	    {
	      $aLine = $this->aMap['aIndex'][$iKey];
        //echo "aLine  =" .var_export($aLine , TRUE)."\n";
	      $aData  = array_merge_recursive($aData, $aLine['aInfo']);
	    }
	    //echo "\n<br><pre>\naData  =" .var_export($aData , TRUE)."</pre>";
	    
	    $sFunctionName = "_render_related_".$sFormat;
	    if(method_exists($this, $sFunctionName))
	    {
	      $sContents = $this->${sFunctionName}($aData, '');
	      return array('sContents' => $sContents);
	    }
	    return false;
	  }
	  
	  
	  function _render_related_dokuwiki($aData, $sSearch, $iLevel = 1)
	  {
      $aIDs = array();
      $sOutput = '';
      if(is_array($aData['aSubs']))
      {
        ksort($aData['aSubs']);
        foreach($aData['aSubs'] as $sKey => $aSection)
        {
          //echo "\n<br><pre>\nsKey  =" .$sKey ."</pre>";
        
          $iCount = 0;
          if(is_array($aSection['aData']))
          {
            $iCount = count(array_unique($aSection['aData']));
            //echo "\n<br><pre>\naSection['aData'] =" .var_export($aSection['aData'], TRUE)."</pre>";
            //echo "\n<br><pre>\niCount  =" .var_export($iCount , TRUE)."</pre>";
            
          }
          
          $aSubs = treeGet(array($sKey), $aData, 'aSubs', 'aData', 'flatten');
          //echo "\n<br><pre>\naSubs  =" .var_export($aSubs , TRUE)."</pre>";
          $iSub = count(array_unique($aSubs));
          //$iSub = count(($aSubs));
          //echo "\n<br><pre>\niSub  =" .var_export($iSub , TRUE)."</pre>";
          
          
          //echo "\n<br><pre>\niCount $sKey  =" .$iCount ."</pre>";
          
          $sOutput .= str_repeat('  ',$iLevel)."* [[$sSearch:$sKey|".ucwords(str_replace("_"," ",$sKey))."]] ".($iCount?(' '.$iCount.''):'').(($iSub && ($iCount != $iSub))?(' ('.$iSub.')'):'')."\n";
          $sOutput .= $this->_render_related_dokuwiki($aSection, "$sSearch:$sKey", $iLevel+1);
          //$sOutput .= "\n";
        }
      }
      return $sOutput;
    }
	  
    
	  function render_graph($sSearch, $sFormat = 'dokuwiki')
	  { 
	    if(trim($sSearch))
	    {
	      $aSearch = split(':', trim($sSearch));
	      $aKeys = array_unique(treeGet($aSearch, $this->aMap['aNameSpaces'], 'aSubs', 'aData', 'flatten'));
	      
	    }
	    else
	    {
	      $aKeys = array_keys($this->aMap['aIndex']);
	    }
	    
	    
	    $sFunctionName = "_render_graph_".$sFormat;
	    if(method_exists($this, $sFunctionName))
	    {
	      $sContents = $this->${sFunctionName}($aKeys);
	      return array('sContents' => $sContents);
	    }
	    return false;
	  }
	  
	  
	  function _render_graph_dokuwiki($aKeys)
	  {
	    $sOutput = 'turned off';
	    /*//Todo loop round fetching the aIndex item and using the ID and pages array
      if(is_array($aKeys))
      {
        foreach($aKeys as $iKey)
        {
          $aCurrent = $this->aMap['aIndex'][$iKey];
          $aOuterNSs = array_keys($aCurrent['aInfo']);
          $sTop = $aOuterNSs[0];
          //echo "\n<br><pre>\nsTop  =" .$sTop ."</pre>";
          //echo "\n<br><pre>\naCurrent['aInfo']  =" .var_export($aCurrent['aInfo'] , TRUE)."</pre>";
          foreach($aCurrent['aInfo'][$sTop] as $sOuterKey => $xJunk)
          {
            foreach($aOuterNSs as $sNS)
            {
              $aPaths = treeGet(array($sNS), $aCurrent['aInfo'], null, null, 'keypathleaves');
              //echo "\n<br><pre>\naPaths  =" .var_export($aPaths , TRUE)."</pre>";
              foreach($aPaths as $sPath)
              {
                $sOutput .= $sTop.':'.$sOuterKey.' -> '. $sPath.' [dummy="test"];'."\n";
                $aNodes[$sPath] = "$sPath [label=\"$sPath\" url=\"".wl($sPath)."\"];";
              }
            }
            //echo "\n<br><pre>\nsOuterKey =" .$sOuterKey."</pre>";
          }
          
            
          //echo "\n<br><pre>\naCurrent  =" .var_export($aCurrent , TRUE)."</pre>";
        }
        $sOutput .= "\n\n".join("\n", $aNodes);
      }
      */
      return $sOutput;
    }
	  
	  function render_leveled($sSearch, $sFormat = 'dokuwiki')
	  {
	    $aData = $this->findLeveled($sSearch);
	    $sFunctionName = "_render_leveled_".$sFormat;
	    if(method_exists($this, $sFunctionName))
	    {
	      $sContents = $this->${sFunctionName}($aData['aLeveled']);
	      return array('sContents' => $sContents, 'aPages' => $aData['aPages']);
	    }
	    return false;
	  }
	  	  
    function _render_leveled_dokuwiki($aData, $iLevel = 1)
    {
      $aIDs = array();
      $sOutput = '';
      if(is_array($aData))
      {
        if(isset($aData[0]))
        {
          return join("\n\n", $aData);
        }
        
        foreach($aData as $sKey => $aSection)
        {
          $sOutput .= str_repeat('=', 5 - $iLevel)." ".$sKey.str_repeat('=', 5 - $iLevel)." \n";
          $sOutput .= $this->_render_leveled_dokuwiki($aSection, $iLevel+1);
          $sOutput .= "\n\n";
        }
      }
      return $sOutput;
    }
}


  
  
  
  function treeSet($aKey, &$aTree, $xData, $sSubName = null, $sDataName = null)
  {
    $aCurrent = & $aTree;
    if ($sSubName && $sDataName)
    {
      foreach($aKey as $sKey => $sValue)
      {
        if(!isset($aCurrent[$sSubName]))
        {
          $aCurrent[$sSubName] = array();
        }
        if(!isset($aCurrent[$sSubName][$sValue]))
        {
          $aCurrent[$sSubName][$sValue] = array();//$sSubName => array(), $sDataName => array());
        }
        $aCurrent = & $aCurrent[$sSubName][$sValue];
      }
      if(!isset($aCurrent[$sDataName]))
      {
        $aCurrent[$sDataName] = array();
      }
      if(!in_array($xData, $aCurrent[$sDataName]))
      {
        $aCurrent[$sDataName][] = $xData;
      }
    }
    else
    {
      foreach($aKey as $sKey => $sValue)
      {
        if(!isset($aCurrent[$sValue]))
        {
          $aCurrent[$sValue] = array();
        }
        $aCurrent = & $aCurrent[$sValue];
      }
       
      if(!in_array($xData, $aCurrent))
      {
        $aCurrent[] = $xData;
      }
    }
  }
  
  
  
  function treeGet($aKey, $aTree, $sSubName = null, $sDataName = null, $sGetType = 'data')  //value data, all, flatten
  {
    $aCurrent = & $aTree;
    if ($sSubName && $sDataName)
    {
      foreach($aKey as $sKey => $sValue)
      {
        if(isset($aCurrent[$sSubName][$sValue]))
        {
          $aCurrent = & $aCurrent[$sSubName][$sValue];
        }
        else
        {
          return null;
        }
      
      }
      
      switch($sGetType)
      {
        case 'flatten':
          return flattenSubTree( $aCurrent, $sSubName,  $sDataName);
        case 'keypath':
          return keypathSubTree( $aCurrent, $sSubName,  $sDataName, $aKey, ':');
        case 'keypathleaves':
          return keypathSubTree( $aCurrent, $sSubName,  $sDataName, $aKey, ':', false);
        case 'all':
          return $aCurrent;
        default:
          return $aCurrent[$sDataName];
      }
    }
    else
    {
      foreach($aKey as $sKey => $sValue)
      {
        if(isset($aCurrent[$sValue]))
        {
          $aCurrent = & $aCurrent[$sValue];
        }
        else
        {
          return null;
        }
      }
      switch($sGetType)
      {
        case 'flatten':
          return flattenSubTree( $aCurrent, $sSubName,  $sDataName);
        case 'keypath':
          return keypathSubTree( $aCurrent, $sSubName,  $sDataName, $aKey, ':');
        case 'keypathleaves':
          return keypathSubTree( $aCurrent, $sSubName,  $sDataName, $aKey, ':', false);
        default:
          return $aCurrent;
      }
    }
  }
  
  function keypath($aKey, $sDelimiter = ':')
  {
    //echo "\n<br><pre>\naKey =" .var_export($aKey, TRUE)."</pre>";
    //echo "\n<br><pre>\nsDelimiter  =" .$sDelimiter ."</pre>";
    return join($sDelimiter, $aKey);
  }
  
  function keypathSubTree( $aTree, $sSubName, $sDataName, $aKey, $sDelimiter =':', $bBranches = true)
  {
    
    $aData = array();
    
    if ($sSubName && $sDataName)
    {
      $aTree = $aTree[$sSubName];
    }
    if(is_array($aTree) and !isset($aTree[0]))
    {
      foreach($aTree as $sKey => $xValue)
      {
        $aNextKey = array_merge($aKey, array($sKey));
        if($bBranches || isset($xValue[0]))
        {
          $aData[] = keypath($aNextKey, $sDelimiter);
        }
        $aData = array_merge($aData, keypathSubTree( $xValue, $sSubName,  $sDataName, $aNextKey, $sDelimiter, $bBranches));
      }
    }
    
    return $aData;
  }
  
  function flattenSubTree( $aTree, $sSubName = null, $sDataName = null)
  {
    $aData = array();
    
    if(is_array($aTree[$sDataName]))
    {
      $aData = $aTree[$sDataName];
    }
    
    if(is_array($aTree[$sSubName]))
    {
      foreach($aTree[$sSubName] as $sKey => $xValue)
      {
        $aData = array_merge($aData, flattenSubTree($xValue, $sSubName , $sDataName));
      }
    }
    return $aData;
  }
  
  class RecDir
  {
     protected $currentPath;
     protected $slash;
     protected $rootPath;
     protected $recursiveTree;
     
     function __construct($rootPath, $win=false)
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
              $junk = array_pop($this->recursiveTree);
              $junk->close();
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
       $oLast = end($this->recursiveTree);
       return $oLast->rewind();
     }
  }
  
  

function date_cmp($a, $b)
{
    $sFormat = '%d/%m/%Y';
    $aA = strptime($a, $sFormat);
    
    $iA = mktime(0, 0, 0, $aA['tm_mon']+1, $aA['tm_mday'], 1900+$aA['tm_year'], 0);
    
    $aB = strptime($b, $sFormat);
    $iB = mktime(0, 0, 0, $aB['tm_mon']+1, $aB['tm_mday'], 1900+$aB['tm_year'], 0);
    
    return $iA < $iB ? (-1):($iA == $iB?0:1);
}




// vim:ts=4:sw=4:et:enc=utf-8:
