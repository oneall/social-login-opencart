<?php
 // Display widget in this container
$oasl_container = 'oneall_social_login_'.mt_rand(99999, 9999999); 

// Do not display for guests
if ( ! $oasl_user_is_logged  && ! empty ($oasl_subdomain)) 
{
	if (! empty ($oasl_heading_title))
   	{
		echo '<h3>'.$oasl_heading_title.'</h3>';
	}                    
	 
	if ($oasl_display_modal) 
	{ 
		echo '<a id="'.$oasl_container.'" class="button">'.$oasl_login_button.'</a>';
	}
	else
	{
		echo '<div id="'.$oasl_container.'"></div>';
	}

	// Plugin
	$oasl_widget = array();
	$oasl_widget[] = "<script type='text/javascript'>";
	$oasl_widget[] = "/* OneAll Social Login - http://www.oneall.com/ */";
	$oasl_widget[] = "var _oneall = _oneall || [];";
	$oasl_widget[] = "_oneall.push(['social_login', 'set_providers', ['" . $oasl_providers. "']]);";
	$oasl_widget[] = "_oneall.push(['social_login', 'set_callback_uri', '" . $oasl_callback_uri . "']);";
		
	// Modal Popup	
	if ($oasl_display_modal)
	{
		$oasl_widget[] = "_oneall.push(['social_login', 'attach_onclick_popup_ui', '" . $oasl_container . "']);";
	}
	// Inline Display
	else
	{
		$oasl_widget[] = "_oneall.push(['social_login', 'set_grid_size', 'x', " . $oasl_grid_size_x . "]);";
		$oasl_widget[] = "_oneall.push(['social_login', 'set_grid_size', 'y', " . $oasl_grid_size_y . "]);";
	    $oasl_widget[] = "_oneall.push(['social_login', 'set_custom_css_uri', '" .  $oasl_custom_css_uri . "']);";
	    $oasl_widget[] = "_oneall.push(['social_login', 'do_render_ui', '" . $oasl_container . "']);";
	}
	 $oasl_widget[] = "</script>";
	
	// Display Widget   	 
	 echo "\n\t" . implode ("\n\t", $oasl_widget)."\n"; 
   	
}
?>