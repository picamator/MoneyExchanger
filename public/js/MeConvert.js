/**
 * Money Exchange Convert: convert one currency to other that is based on Yahoo! Finance
 * Request to sent to MoneyExchenger that is like a Proxy fro Yahoo! service.
 */
(function($) {
	
    var methods = {	
        
        /**
         * Hide Css Class
         * 
         * @property {String}
         */
        hideClass: 'hide',
        
        /**
         * Currency course
         * It is used only when option.cache = true
         * 
         * @todo implement cache option
         * @property {Object}
         */
        course: { from: '', to: '', course: 0 },
                
        /**
         * Initiate plugin with options 
         * 
         * @todo implement cache option
         * @param {Object}  options
         * @param {Object}  options.form                 QueryDom
         * @param {Object}  options.success              Success reponce options 
         * @param {String}  options.success.target       Result target 
         * @param {String}  options.failed.target        Failed target  
         * @param {String}  options.failed.msgErr        General failed message
         * @param {String}  options.data.target          Target to currency amount field  
         * @param {String}  options.data.msgErr          Validation error message
         * @param {String}  options.targetLoader         Target to wite loader
         * @param {Boolean} options.cache                Turn on/off currency course cache
         * @return {Object}
         */
        init: function(options) {       
            var _this 	= methods;      
            var options = $.extend({
                form:           null,
                success:        { target: ''},
                failed:         { target: '', msgErr: '' },
                data:           { target: '', msgErr: '' },
                targetLoader:   '.loader',
                cache:          false
            }, options);

            _this.options = options;
            _this.initSubmit();
            
            return _this; 
        },
        
        /**
         * Add listeners to form submit event
         * Privents original action behaviour
         */
        initSubmit: function() {
            var _this = methods; 
            
            _this.options.form.submit(function() {              
               // validate and send request 
               if (_this.isValid() === true) {
                   _this.ajax();
               } else {
                   _this.showMsg(_this.options.failed.target, _this.options.data.msgErr);
               }
                
               return false; 
            });
        },
        
        /**
         * Check is data valid
         * 
         * @return {Boolean}
         */
        isValid: function() {
            var _this   = methods,
                data    = $(_this.options.data.target).val().trim();
            
           if ($.isNumeric(data) === false || data <= 0) {
               return false;
           } 
            
           return true;
        },
        
        /**
         * Sent request and handler response from Server
         */        
        ajax: function() {
             var _this  = methods,
                submitBtn = $('input[type=submit]', _this.options.form);
        
             $.ajax({
	            type:       "POST",
	            url:        _this.options.form.attr('action'), 
                dataType:   "json",
	            data:       _this.options.form.serialize(),
	            
	            success: function(resp) {
	            	if (typeof(resp.success) !== undefined &&  resp.success === true) {
                        _this.showResult(resp.course);
                    } else {
                        _this.showMsg(_this.options.failed.target, resp.msg);
                    }
	            },
	            
	            error: function (jqXHR, textStatus, errorThrown) {	  	       	
                     var resp = jQuery.parseJSON(jqXHR.responseText);
                     if (typeof(resp.msg) !== undefined) {
                        _this.showMsg(_this.options.failed.target, resp.msg);
                     }
	            },
                        
                beforeSend: function (jqXHR, settings) {
                     // hide previous result
                    $(_this.options.success.target).addClass(_this.hideClass);
                    // show loader
                    $(_this.options.targetLoader).removeClass(_this.hideClass);
                    // desible submit btn
                    submitBtn.attr("disabled", "disabled");
                },
                        
                complete: function (jqXHR, textStatus) {
                    // hide loader
                    $(_this.options.targetLoader).addClass(_this.hideClass);
                    // enable submit button
                    submitBtn.removeAttr("disabled");
                }        
            });
        },        
                
       /**
        * Show Messages in the Target container
        * 
        * @param {String} target
        * @param {String} msg
        */ 
       showMsg: function (target, msg) {
           var _this 	= methods; 
           
           target = $(target);
           target.html(msg);
           target.removeClass(_this.hideClass);
       },
                      
       /**
        * Show result of curency convertation
        * 
        * @param {Object} data
        * @param {String} data.from     Converable currency name
        * @param {String} data.to       Convert currency name
        * @param {Float}  data.value    Currency course
        */
       showResult: function (data) {
           //<span class="result-from"></span> RUB = <strong><span class="result-to"></span> PLN</strong>
           var _this    = methods,
               fromVal  = $(_this.options.data.target).val(),    
               result   = (fromVal*data.value).toFixed(2);
       
            _this.showMsg(
                _this.options.success.target,
                fromVal + " " + data.from + " = <strong>" + result + " " + data.to + "</strong>"
            ); 
       }        
    };

    $.fn.MeConvert = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } 
        else if (typeof method === 'object' || ! method) { 
            return methods.init.apply(this, arguments);
        } 
        else {
            $.error('Method ' +  method + ' does not exist on jQuery.MeConvert');
        } 
    };
    
})((jQuery))