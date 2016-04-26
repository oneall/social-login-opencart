(function() {
	// Gather the parameters:
	var scripttags = document.getElementsByTagName('script');
	var src = scripttags[scripttags.length - 1].src;
	var parts = unescape(src).split('?domain=');
	if (parts.length != 2) {
		if (window.console) {
			console.log('OneAll: missing subdomain name. Library will not be loaded.');
		}
	} else {
		var partsv = parts[1];
		var partsva = partsv.split('&lang=');
		var domain = partsva[0];
		var lang = partsva.length == 2 ? ('?lang=' + partsva[1]) : '';
		
		// Load the library:
	    var oa = document.createElement('script');
	    oa.type = 'text/javascript'; oa.async = true;
	    oa.src = '//' + domain + '.api.oneall.com/socialize/library.js' + lang;
	    scripttags[0].parentNode.insertBefore(oa, scripttags[0]);
	}
})();