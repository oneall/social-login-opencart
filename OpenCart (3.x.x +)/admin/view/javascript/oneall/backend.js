jQuery(document).ready(function($) {

	/* Toggle Provider Selection */
	$(".oa_social_login_provider_toggle").on("click",function(event)	
	{
	    var checkbox = $(this).find("input[type='checkbox']");
	    var target = $(event.target);
	    
	    $(this).removeClass('disabled');
	    $(this).removeClass('enabled');     
	        
	    // Trigger the underlying checkbox
	    if ( ! target.is('input:checkbox'))
	    {
	        if( !checkbox.prop("checked"))
	        {
	            checkbox.prop("checked",true);
	        } 
	        else
	        {
	            checkbox.prop("checked",false);
	        }
	    }
	    
	    // Set appropriate class	    
	    if(checkbox.prop("checked"))
	    {
            $(this).addClass('enabled');
	    }
	    else
	    {
	        $(this).addClass('disabled');
	    }
	});
	
	/* Autodetect API Connection Handler */
	$('#module_oneall_api_autodetect').click(function()
	{							
		var module_oneall_api_handler = jQuery("#module_oneall_api_handler");
		var module_oneall_api_port = jQuery("#module_oneall_api_port");
		
		var message_container = jQuery('#module_oneall_api_autodetect_result');	
		message_container.removeClass('alert-danger alert-success').addClass('alert alert-info');
		message_container.html(oaL10n.working);
		
		var data = {
			'route': 'extension/module/oneall',
			'do': 'autodetect_api_connection',
			'user_token': oaL10n.token,
		};		
		
		jQuery.get('index.php',data, function(response_string)
		{		
			var response_parts = response_string.split('|');
			var response_status = response_parts[0];
			var response_flag = response_parts[1];
			var response_text = response_parts[2];			
	
			/* CURL detected, HTTPS */
			if (response_flag == 'curl_443')
			{
				module_oneall_api_handler.val('crl');
				module_oneall_api_port.val('443');		
			}		
			/* CURL detected, HTTP */
			else if (response_flag == 'curl_80')
			{
				module_oneall_api_handler.val('crl');
				module_oneall_api_port.val('80');	
			}										
			/* FSOCKOPEN detected, HTTPS */
			else if (response_flag == 'fsockopen_443')
			{
				module_oneall_api_handler.val('fso');
				module_oneall_api_port.val('443');
			}
			/* FSOCKOPEN detected, HTTP */
			else if (response_flag == 'fsockopen_80')
			{
				module_oneall_api_handler.val('fso');
				module_oneall_api_port.val('80');
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
	$('#module_oneall_api_verify').click(function()
	{		
		var module_oneall_api_handler = jQuery("#module_oneall_api_handler").val();
		var module_oneall_api_port = jQuery("#module_oneall_api_port").val();
		var module_oneall_subdomain = jQuery("#module_oneall_subdomain").val();
		var module_oneall_public = jQuery("#module_oneall_public").val();
		var module_oneall_private = jQuery("#module_oneall_private").val();
	
		var message_container = jQuery('#module_oneall_api_verify_result');	
		message_container.removeClass('alert-danger alert-success').addClass('alert alert-info');
		message_container.html(oaL10n.working);
		
		var data = {
			'route': 'extension/module/oneall',
			'user_token': oaL10n.token,
			'do': 'verify_api_settings',
			'module_oneall_api_handler': module_oneall_api_handler,
			'module_oneall_api_port': module_oneall_api_port,
			'module_oneall_subdomain': module_oneall_subdomain,
			'module_oneall_public': module_oneall_public,
			'module_oneall_private': module_oneall_private
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