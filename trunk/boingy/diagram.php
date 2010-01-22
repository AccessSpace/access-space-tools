<?php 
  
  //Get all the bits of dokuwiki we need
  if(!defined('DOKU_INC')) define('DOKU_INC',dirname(dirname(__FILE__)).'/');
  require_once(DOKU_INC.'conf/dokuwiki.php');
  require_once(DOKU_INC.'inc/utf8.php');
  require_once(DOKU_INC.'inc/pageutils.php');
  require_once(DOKU_INC.'inc/plugin.php');
  require_once(DOKU_INC.'lib/plugins/linkmap/helper.php');
  
  //A default dokuwiki page incase we are not passed one
  $sPage = 'skill:gimp';
  if($_GET['page'])
  {
    //we can't pass ':' reliably in urls so we are using '-' instead
    $sPage = str_replace('-',':',$_GET['page']);
  }
  
  //converts 'user:Mike Howe' to 'user:mike_howe' 
  $sSearch = cleanID($sPage);
  //splits the search into an array which we need later
  $aSearch = split(':', trim($sSearch));
  
  //Get the dokuwiki link mapping object and get it to do its magic 
  if ($oMap =& new helper_plugin_linkmap())
  {
    $oMap->createMap('activity', 'date');
    $oMap->getFullMap();
  }
  
  //what namesspaces are we allowing to be linked to then turn it into an array   
  $sAllowedNS = 'user,project,skill';
  $aAllowedNS = array_combine(split(',',$sAllowedNS),split(',',$sAllowedNS));
  
  //This fetchs the references to the lines that are mentioned by and under the namespace or page mentioned in the search
  $aKeys = treeGet($aSearch, $oMap->aMap['aNameSpaces'], 'aSubs', 'aData', 'flatten');
	
  //Setup blank arrays for the nodes and edges
  $aNodes = array();
  $aEdges = array();
  
  //Put the subject of the search as the first / central node 
  $aNodes[$sSearch] = new_node($sSearch);
  
  //Loop around the referenced lines
  foreach($aKeys as $iKey)
  {
    //get the line data
    $aLine = $oMap->aMap['aIndex'][$iKey];
    //Loop around all the pages referenced
    foreach($aLine['aPages'] as $sFrom)
    {
      //Loop around all the pages referenced again so all nodes can point to each other
      foreach($aLine['aPages'] as $sTo)
      {
        //If we are not going to link to our selves
        if($sFrom != $sTo)
        {
          //if we don't have a node for the from node and it is an allowed namespace
          if(!isset($aNodes[$sFrom]) && isset($aAllowedNS[getNodeNameSpace($sFrom)]))
          {
            //create the new node and place it in the node list
            $aNodes[$sFrom] = new_node($sFrom);
          }
          
          //up the count of times this node has been seen 
          if($aNodes[$sFrom])
          {
            $aNodes[$sFrom]['count'] ++;
          }
          
          //if we don't have a node for the to node and it is an allowed namespace
          if(!isset($aNodes[$sTo]) && isset($aAllowedNS[getNodeNameSpace($sTo)]))
          {
            //create the new node and place it in the node list
            $aNodes[$sTo] = new_node($sTo);
          }
          
          //up the count of times this node has been seen 
          if($aNodes[$sTo])
          {
            $aNodes[$sTo]['count'] ++;
          }
          
          //if there both nodes of this edge exist
          if($aNodes[$sTo] && $aNodes[$sFrom])
          {
            //get a sensible id for this edge
            $sEdgeID = getEdgeID($sFrom, $sTo);
            //if this edge does not already exist
            if(!isset($aEdges[$sEdgeID]))
            {
              //create this edge and add it to the list
              $aEdges[$sEdgeID] = new_edge($sFrom, $sTo);
            }
            //up the count for this edge
            $aEdges[$sEdgeID]['count'] ++;
          }
        }
      }
    }
    //$aData  = array_merge_recursive($aData, $aLine['aInfo']);
  }
  //swap ':' for '-' as javascript just doesn't like it
  function safeID($sID)
  {
    return str_replace(':','-',$sID);
  }
  
  //create a sensible id for an edge
  function getEdgeID($sFrom, $sTo)
  {
    return safeID($sFrom.'_'.$sTo);
  }
  
  //create a new edge
  function new_edge($sFrom, $sTo)
  {
    $aEdge = array(
              'count'   => 0,
              'target'  => safeID($sTo),
              'source'  => safeID($sFrom),
              'id'      => getEdgeID($sFrom, $sTo),
              'label'   => ''
              );
    return $aEdge;
  }
  
  //get the bottom namesspace from and ID
  function getNodeNameSpace($sID)
  {
    $aParts = split(':', $sID);
    $sNS = $aParts[0];
    return $sNS;
  }
  
  //create a new node
  function new_node($sID)
  {
    //what
    $aTypes = array(  'user' => 'CircleNode',
                      'skill' => 'CircleNode',
                      'project' => 'SquareNode',
                      'type' => 'SquareNode'
                      );
    $aParts = split(':', $sID);
    $sNS = $aParts[0];
    $sLeaf = $aParts[count($aParts) -1];
    $aNode = array(
              'count'  => 0,
              'id'     => safeID($sID),
              'link'    => 'index.php?page='.$sID,
              'type'    => $aTypes[$sNS],
              'text'   => ucwords(str_replace('_',' ',$sLeaf)),
              'ns'      => $sNS,
              );
    return $aNode;
  }
  
  //get a color for a node at the mo it works by baseing everything on a mid gray and changeing the Red for users, green for Projects and Blue for skills depending on how many times a node has been used
  function getColor($aNode)
  {
    //get R G and B
    $sR = 'cc';
    if($aNode['ns'] == 'user')
    {
      $sR = dechex(255 - ($aNode['count'] % 255));
    }
    
    $sG = 'cc';
    if($aNode['ns'] == 'project')
    {
      $sG = dechex(255 - ($aNode['count'] % 255));
    }
    
    
    $sB = 'cc';
    if($aNode['ns'] == 'skill')
    {
      $sB = dechex(255 - ($aNode['count'] % 255));
    }
    
    return $sR.$sG.$sB;
  }
      
  //print out the xml version line has to be done like this cos php throws a fit if you do it in the main body of the XML
	echo '<?xml version="1.0"?>';
	
	
	/*
	* A few example lines from the orginal xml to show what to aim at
	*<node id="n1" text="Creative Synthesis" image="example1/co.png" link="http://www.creativesynthesis.net" scale="120" color="0000ff" textcolor="0000ff"/>
	*<edge sourceNode="n1" targetNode="n3" label="" textcolor="555555"/>
	*/
	//
	//The main xmak starts here with a line of setting for the while diagram
?>

<graph title="<?=$aNodes[$sSearch]['text'];?>" bgcolor="ffffff" linecolor="cccccc" viewmode="display" width="500" height="400" bounce="1" springforce="1" repelforce="1" resistance="1" segmentlength="3">
  <?php
  //looop around the nodes creating a line of xml for each
  foreach($aNodes as $aNode)
  {
    ?>
      <node id="<?=$aNode['id'];?>" text="<?=$aNode['text'];?>" link="<?=htmlentities($aNode['link']);?>" type="<?=$aNode['type'];?>" color="<?=getColor($aNode);?>" textcolor="000000" />
    <?php
  }?>
  
  <?php
  //looop around the edges creating a line of xml for each
  foreach($aEdges as $aEdge)
  {
    ?>
      <edge sourceNode="<?=$aEdge['source'];?>" targetNode="<?=$aEdge['target'];?>" label="<?=$aEdge['label'];?>" textcolor="555555"/>
    <?php
  }?>
</graph>