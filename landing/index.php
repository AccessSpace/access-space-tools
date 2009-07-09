<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 

<html xmlns="http://www.w3.org/1999/xhtml">

<head>




<title>Access Space</title>


<link id="css1" href="layout.css" type="text/css" rel="stylesheet"> 




</head>

<body>

<div id="page">
<div id="leftside">
<div id="header">
<h1><img src="images/access-space.png" alt="" /></h1>
</div>
<div id="links">
<h2>Useful Links</h2>
<dl>
<dt><a href="http://knowhow.access-space.org">The Knowhow Page</a></dt>
<dd>Helpful knowledge that you will find useful at Access Space.</dd>

<!--
<dt><a href="#">Book help for your project</a></dt>
<dd>Arrange a session with a volunteer or staff member.</dd>
-->
<dt><a href="http://spacers.lowtech.org">Spacers</a></dt>
<dd>Wiki of projects by Access Space participants.</dd>

<dt><a href="http://www.access-space.org">Main Site</a></dt>
<dd>The main site for Access Space.</dd>

<dt><a href="bugreport.php?user=<?=$_REQUEST['user']; ?>">
Report a bug on this machine</a></dt>
<dd>If there is something wrong with this machine please use this link to send us an email and report it so we can fix the problem.</dd>
</dl>
</div>
<!--
<div id="forms">
<h2>Your Contact Details</h2>
<form>
First name:
<input type="text" name="firstname" />
<br />
Last name:
<input type="text" name="lastname" />
<br />
Email:
<input type="text" name="email" />
<br />
<input type="submit" value="Submit" />

</form> 
</div>
-->
</div>
<div id="rightside">
<div id="news">
<h2>News and Events</h2>

<?php
$sFeed = file_get_contents('http://access-space.org/wiki/feed.php');


  $xmlDoc = new DOMDocument();
  $bResult = $xmlDoc->load('http://access-space.org/wiki/feed.php');
  if(!($bResult))
  {
    echo "sXML not set";
    header('HTTP/1.1 404 Not Found', true, 404);
    exit;
  }
  
  $aItems = $xmlDoc->getElementsByTagName('item');

  foreach ($aItems as $domElement){

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

	if(trim($sDescription))
{ 
?>
<h3><a target="_blank" href="<?=$sLink;?>"><?=$sTitle;?></a></h3>
<p>
<?=nl2br($sDescription);?>
</p>
<?php
}

}
?>

</div>
</div>
</div>

</body>
</html>
