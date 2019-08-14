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
    _toggleForm: function(){
        var selectedShippingMethodCode = $$('input[name="shipping_method"]:checked');

        //alert(selectedShippingMethodCode.pluck('value'));

        if(this.rate_id == selectedShippingMethodCode.pluck('value')){
            this.formElement.show();
        }else{
            this.formElement.hide();
        }
    },
    _addValidation: function(){
        var self = this;
        Validation.add('required-notime-shipment',Translator.translate('Click here and choose your delivery time-slot'),function(value){
            if(value != '' && value != '-')
            {
                return true;
            }
            return false;
        });
    },
    _validate: function(){

    }
};