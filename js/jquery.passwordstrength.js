/**
 * jquery.passwordstrength.js
 * Copyright (c) 2012 Uwe Steinmann
 

 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:

 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 * @author Uwe Steinmann <uwe@steinmann.cx>
 * @date 2012-06-08
 * @projectDescription Password Strength Meter is a jQuery plug-in provide you smart algorithm to detect a password strength.
 * @version 0.0.1
 * 
 * @requires jquery.js
 * @param url: ajax url,
 * @param pwd: password
 * 
*/

(function($){

	$.fn.passStrength = function(options) {  
	  
		var defaults = {
			onError: function(data) {},
			onChange: function(data) {},
		}; 
		var opts = $.extend(defaults, options);  
		      
		return this.each(function() { 
			var obj = $(this);
			var target = $(obj).attr('rel');

		 	$(obj).unbind().keyup(function() {
				$.ajax({url: opts.url,
					type: 'POST',
					dataType: "json",
					data: {command: 'checkpwstrength', pwd: $(this).val()},
					success: function(data) {
						if(data.error) {
							opts.onError(data, target);
						} else {
							opts.onChange(data, target);
						}
					}
				}); 
			});
		});
	};  
})(jQuery);
