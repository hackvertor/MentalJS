//
// General settings and global helpers
// -----------------------------------------------------------------------------

QUnit.testDone = function (name, bad, total) {
	// Collapse failed tests
	$('#qunit-tests li.fail > ol:visible').hide();
};

// Setup/teardown procedures for testing MentalJS
var MentalJSTestEnv = {
	setup: function () {
		this.MJS = MentalJS();		
        this.MJS.init({dom: false});        
		var self = this;
		this.runV8Test = jQuery.proxy(function (jsFile) {
			window['assertEquals$'] =  function (expected, actual, title) {
				equal(actual, expected, title);
			};
			window['assertTrue$'] = function (value, title) {
				ok(value, title);
			};
			window['assertFalse$'] = function (value, title) {			    
				ok(!value, title);
			};
			window['assertThrows$'] = function (code, title) {
				raises(function () {					    			    				
					self.MJS.parse({code:code});
				}, title);
			};
			
			var xhr = jQuery.ajax({
				url: jsFile+'?'+Math.random(),
				async: false,
				dataType: "text"
			});  									       
			this.MJS.parse({code:xhr.responseText});								
		}, this);
	},
	teardown: function () {	
	}
};

function v8test(name, ex) {
	var module = QUnit.config.currentModule;
	test(name, function () {
		expect(ex);
		this.runV8Test(module+'/'+name+'.js');
	});
};

// -----------------------------------------------------------------------------