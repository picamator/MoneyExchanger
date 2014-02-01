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
         * @property {Object}
         */
        courseCache: undefined,
        
        /**
         * Converable input field
         * 
         * @propery {Object}
         */
        dataDom: undefined, 
        
        /**
         * Success DOM element
         * 
         * @propery {Object}
         */
        successDom: undefined,
        
        /**
         * Failed DOM element
         * 
         * @propery {Object}
         */
        failedDom: undefined,
        
        /**
         * Initiate plugin with options 
         * 
         * @todo implement cache option
         * @param {Object}  options
         * @param {Object}  options.success              Success reponce options 
         * @param {String}  options.success.target       Result target 
         * @param {String}  options.failed.target        Failed target  
         * @param {String}  options.failed.msgErr        General failed message
         * @param {String}  options.data.target          Target to currency amount field  
         * @param {String}  options.data.msgErr          Validation error message
         * @param {String}  options.targetLoader         Target to waite loader
         * @param {Boolean} options.cache                Turn on/off currency course cache
         * @return {Object}
         */
        init: function(options) {       
            var _this 	= methods;      
            var options = $.extend({
                success:        { target: ''},
                failed:         { target: '', msgErr: '' },
                data:           { target: '', msgErr: '' },
                targetLoader:   '.loader',
                cache:          false
            }, options);

            _this.options       = options;
            _this.options.form  = this;
            
            // add listener to submit event
            _this.initSubmit();
            
            // save convertable DOM 
            _this.dataDom = $(_this.options.data.target);
            _this.dataDom.focus();
            
            // save success DOM
            _this.successDom = $(_this.options.success.target);
            
            // save failed DOM
            _this.failedDom = $(_this.options.failed.target);
            
            return _this; 
        },
        
        /**
         * Add listeners to form submit event
         * Privents original action behaviour
         */
        initSubmit: function() {
            var _this = methods; 
            
            _this.options.form.submit(function(event) {              
               // validate and send request 
               if (_this.isValid() === true) {
                   _this.ajax();
               } else {
                   _this.showError(_this.options.data.msgErr);
               }
                
               event.preventDefault(); 
            });
        },
        
        /**
         * Check is data valid
         * 
         * @return {Boolean}
         */
        isValid: function() {
            var _this   = methods,
                data    = _this.dataDom.val().trim();
            
           if ($.isNumeric(data) === false || data <= 0) {
               return false;
           } 
            
           return true;
        },
        
        /**
         * Sent request and handler response from Server
         */        
        ajax: function() {
            var _this = methods,
                submitBtn;
            
            // retrieve data from cache
            if (_this.options.cache === true && typeof(_this.courseCache) !== "undefined") {
                _this.showResult(_this.courseCache);
                return;
            }
            
            submitBtn  = $('input[type=submit]', _this.options.form);
            
            // send request to server and handle reponse
            $.ajax({
	            type:       "POST",
	            url:        _this.options.form.attr("action"), 
                dataType:   "json",
	            data:       _this.options.form.serialize(),
	            
	            success: function(resp) {
	            	if (typeof(resp.success) !== "undefined" &&  resp.success === true) {
                        // update cache
                        _this.courseCache = resp.course; 
                        // show result
                        _this.showResult(resp.course);
                    } else {
                        _this.showError(resp.msg);
                    }
	            },
	            
	            error: function (jqXHR, textStatus, errorThrown) {	  	       	
                    _this.showError(_this.options.failed.msgErr);
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
        * @param {Object} domObj
        */ 
       showMsg: function (domObj, msg) {
           var _this = methods; 
           
           if(msg !== '') {
                domObj.html(msg);
                domObj.removeClass(_this.hideClass);
           }
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
           var _this    = methods, 
               fromVal  = _this.dataDom.val(),    
               result   = (fromVal*data.value).toFixed(2);
               
           // hide previous error
           _this.failedDom.addClass(_this.hideClass);
          
           // show success message
           _this.showMsg(
                _this.successDom,
                fromVal + " " + data.from + " = <strong>" + result + " " + data.to + "</strong>"
           ); 
       },
       
       /**
        * Show error message
        * 
        * @param {string} msg
        */
       showError: function (msg) {
            var _this = methods;
           
           // hide previous result
           $(_this.options.success.target).addClass(_this.hideClass); 
           // show message
           _this.showMsg(_this.failedDom, msg);
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
    
})((jQuery));