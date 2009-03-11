 var oFormChangeTimer = null;
      var formChange = function(el)
      {
        var oForm = el.getParent('form');
        var sQuery = oForm.toQueryString();
        var eOutputframe = $('outputframe');
        eOutputframe.set('src', 'k.php?'+sQuery);
        eOutputframe.set('width', oForm.width.value);
        eOutputframe.set('height', oForm.height.value);
        
      }.create({delay:5000});

Element.implement({
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
      
      
    }
    return input;
  }
});
	

// AUTOLOAD CODE BLOCK (MAY BE CHANGED OR REMOVED)
window.addEvent("domready", function() {
  $$("input").filter(function(input) { return input.hasClass("slider"); }).mooslider({});
  $('controller').getElements('input').addEvent('change', function(event){console.log("event =", event);});
  
});