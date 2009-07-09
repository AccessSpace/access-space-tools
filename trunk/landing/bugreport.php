<html>
<body>
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
    $subject = 'Bug report from '.$_REQUEST['REMOTE_ADDR'] ;
    
    $message = $_REQUEST['message'] ;
    mail("someone@example.com", "Subject: $subject",
    $message, "From: $email" );
    echo "Thank you for using our mail form";
    }
  }
else
  {//if "email" is not filled out, display the form
  echo "<form method='post' action='bugreport.php'>
  Your Email: <input name='email' type='text' /><br />
  What are you trying to do?:<br />
  <textarea name='task' rows='10' cols='40'></textarea><br />
  What should happen?:<br />
  <textarea name='task' rows='10' cols='40'></textarea><br />
  What is happening?:<br />
  <textarea name='task' rows='10' cols='40'></textarea><br />
  
  Which machines is it affecting:
  <select></select>
  <br />
  <input type='submit' />
  </form>";
  }
?>

</body>
</html>


