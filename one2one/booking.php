<?php
  require_once('template.php');
  $sTemplateFolderPath = './templates/';
  
  $oTpl = & new Template($sTemplateFolderPath, 'layout.tpl.php'); // this is the outer template
  $oTpl->set('sTitle', '1:1 Project Planning Booking');
  
  $oBody = & new Template($sTemplateFolderPath, 'makebooking.tpl.php'); // This is the inner template
  
  /*
   * The get_user_list() function simply runs get data about users 
   * nothing fancy or complex going on here.
   */
  $oBody->set('aUserList', null);
  
  $oTpl->set('sContent', $oBody);
  
  echo $oTpl->fetch();
