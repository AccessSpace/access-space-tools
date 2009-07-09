window.addEvent('domready', function(){	
//Variables
  var sCurrURL = '';
//REQUEST OBJECTS
  

  var oPageFetchRequest = new Request.HTML({
    method:'get',
	onComplete: function(html) {
        $('content').empty();
        $('content').adopt(html);
			},
			
		onFailure: function(){
			alert( 'The "Page Fetch" request failed.');
		}
	});
  
  var oPageInfoRequest = new Request.JSON({
    method: 'get',
    url: "pageinfo.php",
    onComplete: function(jsonObj) {
      iCycleStarted = jsonObj.iCycleStarted;
      if (jsonObj.sURL !== sCurrURL)
      {
	if(jsonObj.sURL.match(/(\.htm)l?/i))	
	{
       	  var d = new Date();
          var t = d.getTime();
          oPageFetchRequest.send({'url':jsonObj.sURL,'data':{'cb':t}});
          sCurrURL = jsonObj.sURL;
	}
        else
	{
	 $('content').empty();
	var eImage = new Element('img', {width:'100%', src:jsonObj.sURL});
		//console.log(eImage);
         $('content').adopt(eImage); 
	}
      }
    },
    onFailure: function(){
      alert( 'The "Page Info" request failed.');
    }
  });
  
  var getNextPage = function()
  {
    oPageInfoRequest.send({'data':{'iCycleStarted':iCycleStarted,'sCurrURL':sCurrURL}});
  };
  
//Doing
  getNextPage.periodical(1000);
});

