<?php

$sJSON = '{"width":400,"height":400,"actors":[{"id":"testk","type":"kaleidoscope"}]}';

define('CONST_CMA',		dirname(__FILE__)."/");
define('CONST_CMA_LIB', CONST_CMA."lib/");
define('CONST_CMA_MODULES', CONST_CMA."modules/");


require_once(CONST_CMA_LIB."actor.php");
require_once(CONST_CMA_LIB."jsontemplate.php");
      
  //echo "\n<br><pre>\nsJSON  =" .$sJSON ."</pre>";
  
  $aParameters = json_decode($sJSON, true);
  //echo "\n<br><pre>\naParameters  =" .var_export($aParameters , TRUE)."</pre>";
  
  $aScene = array();

  foreach($aParameters['actors'] as $aActor)
  {
    $oActor =& getActor($aActor);
    $aOut[] = array('sDef'=>$oActor->getDefs(), 'sBody'=>$oActor->getBody()) ;
    $aActorParams[] = $oActor;//json_decode(json_encode($oActor), true);
  }
  
  $aOut = array('aBodies' => $aOut,
                'aDefs'   => $aOut,
                'actors'  => $aActorParams,
                'iOutputWidth'   => $aParameters['width'],
                'iOutputHeight'  => $aParameters['height']
                );
  
  $sTemplate = file_get_contents(dirname(__FILE__).'/templates/output.svg');
  $oTemplate = new JsonTemplate($sTemplate);
  $sOut = $oTemplate->expand($aOut);
  header("Content-Type: image/svg+xml");
  echo $sOut;
  
  
  /**
   * @param array $aActor
   * @return actor
   */
  function getActor($aActor)
  {
    require_once('modules/'.$aActor['type'].'/module.php');
    $sClassName = 'actor_'.$aActor['type'];
    $oActor = new $sClassName($aActor);
    return $oActor;
  }
