<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Bug Reporting</title>
  <link id="css1" href="layout.css" type="text/css" rel="stylesheet">
</head>

<body>

<div id="page">
 <div id="leftside">
  <div id="header">
   <h1><img src="images/access-space.png" alt="" /></h1>
  </div>
  <div id="instructions">
   <h2>Bug Reporting</h2>
<p>Please use the boxes to the right to tell us :-</p>
 <ul>
<li>what has gone wrong?</li>
<li>
 can we make the network better for you and your project?</li>
<ul>

  </div>
  </div>
<div id="rightside">
 <div id="bugreport">
<?php

@define('CONST_BUGREPORT_EMAIL_ADDR', 'martyn@access-space.org');

function spamcheck($field)
  {
  //filter_var() sanitizes the e-mail
  //address using FILTER_SANITIZE_EMAIL
  $field=filter_var($field, FILTER_SANITIZE_EMAIL);

  //filter_var() validates the e-mail
  //address using FILTER_VALIDATE_EMAIL
  if(filter_var($field, FILTER_VALIDATE_EMAIL))
    {
    return TRUE;
    }
  else
    {
    return FALSE;
    }
  }

if (isset($_REQUEST['email']))
  {//if "email" is filled out, proceed

  //check if the email address is invalid
  $mailcheck = spamcheck($_REQUEST['email']);
  if ($mailcheck==FALSE)
    {
    echo "Invalid input";
    }
  else
    {//send email
    $email = $_REQUEST['email'] ;
    $subject = 'Bug report from '.$_SERVER['REMOTE_ADDR'] ;
    
    $message = var_export($_REQUEST, true);//['message'] ;
    mail(CONST_BUGREPORT_EMAIL_ADDR, "Subject: $subject",
    $message, "From: $email" );
    echo "Thank you for using our mail form";
    }
  }
else
  {//if "email" is not filled out, display the form
 ?> 
 <form method='post' action='bugreport.php'>
  <input name="user" value="<?=$_REQUEST['user']; ?>" type="hidden" />
  Your Email: <input name='email' type='text' size="50"/><br />
  What are you trying to use?:<br />
  <textarea name='task' rows='5' cols='60'></textarea><br />
  What should it have done?:<br />
  <textarea name='expected' rows='5' cols='60'></textarea><br />
  What happened?:<br />
  <textarea name='actual' rows='5' cols='60'></textarea><br />
  
  Which machines is it affecting:
  <select name="scope">
   <option>At Least This Machine</option>
   <option>This Machine Only</option>
   <option>All Machines</option>
  </select>
  <br />
  <input type='submit' />
  </form>

<?php
  }
?>
 </div>
</div>
</div>
</body>
</html>


