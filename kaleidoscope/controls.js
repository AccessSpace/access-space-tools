
var formResponce = null;


	var bChanged = false;

var Comet = new Class({
  timestamp: 0,
  url: './backend.php',
  noerror: true,
	ajax:null,
	sender:null,
  initialize: function() {
		this.ajax = new Request.JSON({
			url: this.url,
      method: 'get',
      onSuccess: function(response) {
				//console.log('ajax sucess this',this,'response', response);
        
				if($defined(response['timestamp']))
				{
					// handle the server response
					this.timestamp = response['timestamp'];
					this.handleResponse(response);
					this.noerror = true;
				}
				else
				{
					this.noerror = false;
				}
      }.bind(this),
      onComplete: function(transport) {
        // send a new ajax request when this request is finished
        if (!this.noerror)
				{
					//console.log('error retrying');
          // if a connection problem occurs, try to reconnect each 5 seconds
          this.connect().delay(5000); 
        }
				else
        {
					this.connect();
        }
				this.noerror = false;
      }.bind(this)
		});

		this.sender = new Request({
			url: this.url,
			method: 'get',
		});
	},
	
	connect: function()
	{
		//console.log('connect this', this);
		this.ajax.send('timestamp='+this.timestamp);
	},
	
  disconnect: function()
  {
  },

  handleResponse: function(response)
  {
		formResponse = new Hash(JSON.decode(response.msg));
		//console.log('formResponse', formResponse);
		bChanged = true;
		

		var oForm = $("controller");//el.getParent('form');
		if(bControls)
		{
			bChanged = false;
		
			oForm.getElements('input,select,textarea').each(function(item){
					var sElementName = item.get('name');
					//console.log('item',item,'key',key);
					if($defined(sElementName) && $defined(formResponse[sElementName]))
					{
						if(formResponse[sElementName] == item.get('value'))
						{
							//console.log(sElementName, 'unchanged');
						}
						else
						{
							bChanged = true;
							item.set('value', formResponse[sElementName]);
							item.oSlider.set(formResponse[sElementName]);
						}
					}
				});
		}
		if (bChanged && bViewer)
		{
			rebuildSVG(formResponse);
			//rebuildSVG();
		}
    //oForm.getElements('input,select,textarea');
		    
    //$('content').innerHTML += '<div>' + response['msg'] + '</div>';
  },
	

	
  doRequest: function(request)
  {
		this.sender.send(request);
  }
});


var rebuildSVG = function(State)
{
	//var oForm = $("controller");//el.getParent('form');
	//var sQuery = oForm.toQueryString();
	var oState = new Hash(State);
	var sQuery = oState.toQueryString();
	
	var eOutputframe = $('outputframe');
	eOutputframe.set('src', 'k.php?'+sQuery);
	eOutputframe.set('width', oState.width);
	eOutputframe.set('height', oState.height);
};	

var oFormChangeTimer = null;
      var formChange = function(el)
      {
        var oForm = el.getParent('form');
        var sQuery = oForm.toQueryString();
				comet.doRequest('msg='+escape(JSON.encode(oForm.getFormValues()))+'');
				if(bViewer)
				{
					rebuildSVG(oForm.getFormValues());
				}
      }.create({delay:500});

			
			
Element.implement({
		getFormValues: function(){
			var aValues = this.getElements('input,select,textarea').get('value');
			var aNames = this.getElements('input,select,textarea').get('name');
			var oValues = aValues.associate(aNames);
			return oValues;
		},
		
  mooslider: function(options) {
    var input = this;
		if (input.get("tag") == "input")
    {
			options = $extend({}, options);
      var iMin = input.getAttribute('min').toInt();
      var iMax = input.getAttribute('max').toInt();
      input.options = options;
      
      input.slider_control = new Element('div');
      input.slider_control.addClass('slider_control');
      
      input.knob = new Element('div');
      input.knob.addClass('knob');
      input.slider_control.adopt(input.knob);
      
      input.slider_control.inject(input, 'after');
      
      input.set('type', 'hidden');
      
      var oSlider = new Slider(input.slider_control, input.knob, {
        range: [iMin, iMax],	// Minimum value is 8
        onChange: function(value)
        {
          // Everytime the value changes, we change the font of an element
          input.set('value', value);
          input.knob.set('html', value);
          if(oFormChangeTimer)
          {
            $clear(oFormChangeTimer);
          }
          oFormChangeTimer = formChange(input);
        }
      }).set(input.get('value').toInt());
			input.oSlider = oSlider;
    }
    return input;
  }
});
	
var comet;

// AUTOLOAD CODE BLOCK (MAY BE CHANGED OR REMOVED)
window.addEvent("domready", function() {
	
	comet = new Comet();
	comet.connect();
	if(bControls)
	{
		$$("input").filter(function(input) { return input.hasClass("slider"); }).mooslider({});
		$('controller').getElements('input').addEvent('change', function(event){/*console.log("event =", event);*/});
  }
});