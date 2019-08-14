var NotimeShippingForm = Class.create();
NotimeShippingForm.prototype = {
    initialize: function (rate_id,formElementId) {
        this.rate_id = rate_id;
        this.formElement = $(formElementId);
        this._addEventListeners();
        this._addValidation();
        this._toggleForm();
    },
    _addEventListeners: function () {
        var self = this;
        $$('input[name="shipping_method"]').each(
            function(sel){

                if (window.addEventListener) {
                    // Check for addEventListener first, since IE9/10 have both,
                    // but you should use the standard over the deprecated IE-specific one
                    sel.addEventListener('click', function() {
                        self._toggleForm();
                    });
                } else if (window.attachEvent) {
                    sel.attachEvent('onclick', function() {
                        self._toggleForm();
                    });
                } else {
                    Event.observe(sel, 'click', function() {
                        self._toggleForm();
                    });
                }
            }
        );
    },
	_isNotimeShippingSelected: function(){
        var selectedShippingMethod = $$('input[name="shipping_method"]:checked');
        return this.rate_id == selectedShippingMethod.pluck('value');
    },
    _toggleForm: function(){
        if(this._isNotimeShippingSelected()){
            this.formElement.show();
        }else{
            this.formElement.hide();
        }
    },
    _addValidation: function(){
        var self = this;

        Validation.add('required-notime-shipment',Translator.translate('Click here and choose your delivery time-slot'),function(value){
			if(self._isNotimeShippingSelected() !== true){
				return true;
			}
			var $notimeWidgetNotSupportedPostcodeContainer = $('notimeWidgetNotSupportedPostcodeContainer');
			if((value != '' && value != '-') || $notimeWidgetNotSupportedPostcodeContainer.visible())
			{	
				return true;
			}
			return false;
        });


        Validation.add('required-notime-shipment-notsupported',Translator.translate('Please choose another postcode or another delivery method'),function(value){
			if(self._isNotimeShippingSelected() !== true){
				return true;
			}
			
            var $notimeWidgetNotSupportedPostcodeContainer = $('notimeWidgetNotSupportedPostcodeContainer');
            if((value == '' || value == '-') && $notimeWidgetNotSupportedPostcodeContainer.visible())
            {
                return false;
            }
            return true;
        });

    },
    _validate: function(){

    }
};