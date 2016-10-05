(function() {

	/* Extract URL Parameter */
	function oa_get_url_param(url, param) {
		var regex, results;
		param = param.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
		regex = new RegExp("[\\?&]" + param + "=([^&#]*)");
		results = regex.exec(url);
		return (results == null ? '' : results[1]);
	}

	/* Include OneAll Library */
	function oa_include_library() {
		var tags, tag_src, subdomain, lang, lib;

		tags = document.getElementsByTagName('script');
		tag_src = tags[tags.length - 1].src;

		subdomain = oa_get_url_param(tag_src, 'subdomain');
		if (subdomain.length == 0) {
			if (window.console) {
				console.log('[OneAll] Missing subdomain. Library cannot not be loaded.');
			}
		} else {
			lang = oa_get_url_param(tag_src, 'lang');
			lang = (lang.length > 0 ? ('?lang=' + lang) : '');

			lib = document.createElement('script');
			lib.type = 'text/javascript'; lib.async = true;
			lib.src = '//' + subdomain + '.api.oneall.com/socialize/library.js' + lang;
			tags[0].parentNode.insertBefore(lib, tags[0]);
		}
	}
	
	oa_include_library();
})();