<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<?php
  $sPage = 'skill:gimp';
  if($_GET['page'])
  {
    $sPage = $_GET['page'];
  }
  
  $sXMLFile = (("diagram.php?page=".str_replace(':','-',$sPage)));
  
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Graph Gear Demo</title>
<!-- SWFObject embed by Geoff Stearns geoff@deconcept.com http://blog.deconcept.com/swfobject/ -->
<link rel="stylesheet" type="text/css" href="boingy.css" />
<script type="text/javascript" src="swfobject/swfobject.js"></script>

<script language="JavaScript">
  //the javascript functions that came with the demo but we might use the nodeNotify and something modified from jsSwitchXML to change the xml every few minutes
	var flashMovie;
	function init() {
		if (document.getElementById) {
		   flashMovie = document.getElementById("graphgear");
		}
	}
	window.onload = init;

	function jsSwitchXML() {
		var xml = document.xmlChooser.xmlChoice.value;
		flashMovie.switchXML(xml);
	}
	
	function jsLiveXML() {
		var xml = document.liveXml.liveXmlArea.value;
		flashMovie.liveXML(xml);
	}
	//is run when a node is clicked we could get extra info and display it?
	function nodeNotify(str) {
		//document.getElementById("nodify").innerHTML = "<strong>Javascript Events:</strong> Selected Node: " + str;
	}
	function addNode(idN, scaleN, contentN, linkN, textcolorN, imageN, colorN, targetN, labelN, labelColorN) {
		flashMovie.jsAddNode(idN, scaleN, contentN, linkN, textcolorN, imageN, colorN, targetN, labelN, labelColorN);
	}
	function addRandomNode() {
		flashMovie.addRandomNode();
	}
</script>
</head>

<body>
<div id="wrap_junk">
		<div id="gearspace">
			<strong>You need to upgrade your Flash Player</strong>
		</div>
		<div id="nodify">&nbsp;</div>
		
		
		
		<script type="text/javascript">
			// <![CDATA[
		
			var so = new SWFObject("GraphGear.swf", "graphgear", "1270", "600", "8");
			so.addVariable("graphXMLFile", "<?=$sXMLFile;?>"); // rename to your xml file
			so.addParam("allowScriptAccess", "always");
			so.addParam("scale", "noborder");
			so.addParam("salign", "cc");
			so.addParam("base", ""); 
			so.write("gearspace");
		
			// ]]>
		</script>

</div>
</body>
</html>
