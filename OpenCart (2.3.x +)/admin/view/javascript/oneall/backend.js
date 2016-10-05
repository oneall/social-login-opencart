jQuery(document).ready(function($) {

	/* Toggle Provider Selection */
	$(".oa_social_login_provider_toggle").on("click",function(event)
	{
	    var target = $(event.target);	    
	    if (target.is('input:checkbox')) return;

	    var checkbox = $(this).find("input[type='checkbox']");

	    if( !checkbox.prop("checked"))
	    {
	        checkbox.prop("checked",true);
	    } 
	    else
	    {
	        checkbox.prop("checked",false);
	    }
	});
	
	/* Autodetect API Connection Handler */
	$('#oneall_api_autodetect').click(function() {	
						
		var oneall_api_handler = jQuery("#oneall_api_handler");
		var oneall_api_port = jQuery("#oneall_api_port");
		
		var message_container = jQuery('#oneall_api_autodetect_result');	
		message_container.removeClass('alert-danger alert-success').addClass('alert alert-info');
		message_container.html(oaL10n.working);
		
		var data = {
			'route': 'extension/module/oneall',
			'do': 'autodetect_api_connection',
			'token': oaL10n.token,
		};		
		
		jQuery.get('index.php',data, function(response_string) {				
	
			var response_parts = response_string.split('|');
			var response_status = response_parts[0];
			var response_flag = response_parts[1];
			var response_text = response_parts[2];			
	
			/* CURL detected, HTTPS */
			if (response_flag == 'curl_443')
			{
				oneall_api_handler.val('crl');
				oneall_api_port.val('443');		
			}		
			/* CURL detected, HTTP */
			else if (response_flag == 'curl_80')
			{
				oneall_api_handler.val('crl');
				oneall_api_port.val('80');	
			}										
			/* FSOCKOPEN detected, HTTPS */
			else if (response_flag == 'fsockopen_443')
			{
				oneall_api_handler.val('fso');
				oneall_api_port.val('443');
			}
			/* FSOCKOPEN detected, HTTP */
			else if (response_flag == 'fsockopen_80')
			{
				oneall_api_handler.val('fso');
				oneall_api_port.val('80');
			}		
	
			message_container.removeClass("alert-info");
			message_container.html(response_text);

			if (response_status == "success") {
				message_container.addClass("alert-success");
			} else {
				message_container.addClass("alert-danger");				
			}		
							
		});
		return false;	
	});
	
	/* Test API Settings */
	$('#oneall_api_verify').click(function(){
		
		var oneall_api_handler = jQuery("#oneall_api_handler").val();
		var oneall_api_port = jQuery("#oneall_api_port").val();
		var oneall_subdomain = jQuery("#oneall_subdomain").val();
		var oneall_public = jQuery("#oneall_public").val();
		var oneall_private = jQuery("#oneall_private").val();
	
		var message_container = jQuery('#oneall_api_verify_result');	
		message_container.removeClass('alert-danger alert-success').addClass('alert alert-info');
		message_container.html(oaL10n.working);
		
		var data = {
			'route': 'extension/module/oneall',
			'token': oaL10n.token,
			'do': 'verify_api_settings',
			'oneall_api_handler': oneall_api_handler,
			'oneall_api_port': oneall_api_port,
			'oneall_subdomain': oneall_subdomain,
			'oneall_public': oneall_public,
			'oneall_private': oneall_private
		};		
		
		jQuery.get('index.php', data, function(response_string) {		
			
			var response_parts = response_string.split('|');
			var response_status = response_parts[0];
			var response_text = response_parts[1];
			
			message_container.removeClass("alert-info");
			message_container.html(response_text);

			if (response_status == "success") {
				message_container.addClass("alert-success");
			} else {
				message_container.addClass("alert-danger");				
			}
		});
		return false;
	});
});