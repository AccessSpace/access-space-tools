<h2>News and Events</h2>

<? foreach($aStories as $aStory): ?>
 <h3><a target="_blank" href="<?=$aStory['sLink'];?>"><?=$aStory['sTitle'];?></a></h3>
 <p>
  <?=nl2br($aStory['sDescription']);?>
 </p>
<? endforeach; ?> 
