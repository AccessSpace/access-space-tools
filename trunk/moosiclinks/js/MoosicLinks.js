/*MoosicLinks by Martyn Eggleton (martyn[dot]eggleton[at]gmail[dot]com) 04/03/2009 
example and instuctions @
  http://stretch.deedah.org/MoosicLinks/
License LGPL @
 http://www.gnu.org/licenses/lgpl.html

Adds in line mp3 listening to links in a mootools way. compatible with milkbox
(i.e. if you add an mp3 attribute that point at the mp3 file it will get played 
when milkbox opens the associated link. and stops when it closes)
Mostly a very simple linker between MooSound and milkbox

Usage
Put the following at the top

<script src="js/mootools.js" type="text/javascript"></script>
<script src="js/MooSound.js" type="text/javascript"></script>
<script src="js/milkbox.js" type="text/javascript"></script>
<script type="text/javascript" src="js/musiclinks.js"></script>

and add either an mp3 attribute to the link 
<a mp3="full file path to mp3 file" href="whatever.html">junk</a>

or when the link pointed to the mp3
<a rel="moosiclinks" href="junk.mp3">junk</a>

*/

window.addEvent('domready', function() {
  if(!Playlist)
  {
    return true;
  }
  var mySound = null;
  var aLinks = document.getElements('a');
  var aMp3Links = [];
  aLinks.each(function(link, index){
      var mp3 = getMp3FileName(link);
      if (mp3)
      {
        aMp3Links[aMp3Links.length] = mp3;
        if(typeof milkbox === 'undefined')
        {
          link.addEvent('click', function(event)
          {
            event.preventDefault();
            if (mySound)
            {
              mySound.stop();
            }
            var sSoundName = getMp3FileName(event.target.getParent('a'));
            if (sSoundName)
            {
              mySound = Playlist.getSound(sSoundName);
              if(mySound)
              {
                mySound.start();
              }
            }
            return false;
          });
        }
      }
  });
  
  Playlist.loadSounds(aMp3Links, {});
  
  if(typeof milkbox !== 'undefined' && milkbox)
  {
    milkbox.changeOptions({'onClosed':function(){if(mySound){mySound.stop();}}});
    milkbox.addEvent('fileReady', function(event){
        if (mySound)
        {
          mySound.stop();
        }
        var eLink = this.currentGallery[this.currentIndex];
        if(eLink)
        {
          var sSoundName = eLink.getAttribute('mp3');
          if(sSoundName)
          {
            mySound = Playlist.getSound(sSoundName);
            if(mySound)
            {
              mySound.start();
            }
          }
        }
    });
  }
  function getMp3FileName(eLink)
  {
    var mp3 = eLink.getAttribute('mp3');
    if (!mp3)
    {
      var rel = eLink.getAttribute('rel');
      if (rel == 'moosiclinks')
      {
        mp3 = eLink.getAttribute('href');
      }
    }
    return mp3;
  }
  
});

