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
<script type="text/javascript" src="swfobject/swfobject.js"></script>
<link rel="stylesheet" type="text/css" href="examples/example-style.css" />

<script language="JavaScript">
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
	<div id="wrap">
		<div id="gearspace">
			<strong>You need to upgrade your Flash Player</strong>
		</div>
		<div id="nodify">&nbsp;</div>
		
		
		
		<script type="text/javascript">
			// <![CDATA[
		
			var so = new SWFObject("GraphGear.swf", "graphgear", "725", "400", "8");
			//so.addVariable("graphXMLFile", "diagram.php?page=<?=urlencode($sPage);?>"); // rename to your xml file
			
			so.addVariable("graphXMLFile", "<?=$sXMLFile;?>"); // rename to your xml file
			
			
			//so.addVariable("graphXMLFile", "diagram.php?page=<?=$sPage;?>"); // rename to your xml file
			//so.addVariable("graphXMLFile", "text.xml"); // rename to your xml file
			
			//so.addVariable("graphXMLFile", "diagram.php"); // rename to your xml file
			
			//so.addVariable("graphXMLFile", "example1/example1.xml"); // rename to your xml file
			//so.addVariable("graphXMLFile", "examples/example1/example1.xml"); // rename to your xml file
			so.addParam("allowScriptAccess", "always");
			so.addParam("scale", "noborder");
			so.addParam("salign", "tl");
			so.addParam("base", "");
			//so.addParam("base", "examples");
			
				      
			so.write("gearspace");
		
			// ]]>
		</script>

</div>
</body>
</html>
