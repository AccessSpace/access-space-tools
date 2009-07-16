<?php
  require_once('template.php');
  $sTemplateFolderPath = './templates/';
  
  $oTpl = & new Template($sTemplateFolderPath, 'layout.tpl.php'); // this is the outer template
  $oTpl->set('sTitle', 'Access Space Landing Page');
  
  $oBody = & new Template($sTemplateFolderPath, 'landing.tpl.php'); // This is the inner template
  
  $oNewsBody = & new Template($sTemplateFolderPath, 'news.tpl.php'); // This is the inner template


  $sFeed = 'http://access-space.org/wiki/feed.php';
  $aStories = array();

  $xmlDoc = new DOMDocument();
  $bResult = $xmlDoc->load($sFeed);
  if($bResult)
  {
   $aItems = $xmlDoc->getElementsByTagName('item'); 
 
   foreach ($aItems as $domElement)
   { 

    $sLink =  str_replace("&do=diff", "", $domElement->getElementsByTagName('link')->item(0)->nodeValue);
    $sTitle =  $domElement->getElementsByTagName('title')->item(0)->nodeValue; 

    $iStart = strpos($sTitle, ':'); 
    $sTitle = ucwords(trim(str_replace('_', ' ', substr($sTitle, $iStart+1))));
    $iEnd = strpos($sTitle, '-');
    if($iEnd)
    {
     $sTitle = substr($sTitle, 0, $iEnd - strlen($sTitle));
    }
    $sDescription =  $domElement->getElementsByTagName('description')->item(0)->nodeValue;
    $sDescription = trim($sDescription);
    if($sDescription)
    {
     $aStories[] = array(
			'sLink' => $sLink,
			'sTitle' => $sTitle,
			'sDescription' => $sDescription,
    			);

    }
   }
  }
 
  $oNewsBody->set('aStories', $aStories);
  $oBody->set('sNewsContent', $oNewsBody);
  $oTpl->set('sContent', $oBody);
  
  echo $oTpl->fetch();


