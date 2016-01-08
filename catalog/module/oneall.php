<?php
/**
 * @package   	OneAll Social Login
 * @copyright 	Copyright 2015 http://www.oneall.com - All rights reserved.
 * @license   	GNU/GPL 2 or later
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307,USA.
 *
 * The "GNU General Public License" (GPL) is available at
 * http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 */

// OpenCart2 ?
if (!defined ('OC2'))
{
	define ('OC2', VERSION >= '2');
}

//////////////////////////////////////////////////////////////////////
// Widget Display
//////////////////////////////////////////////////////////////////////
class ControllerModuleOneall extends Controller
{
	// Return the protocol of the request.
	public static function get_request_protocol ()
	{
		if (!empty ($_SERVER ['SERVER_PORT']))
		{
			if (trim ($_SERVER ['SERVER_PORT']) == '443')
			{
				return 'https';
			}
		}
		
		if (!empty ($_SERVER ['HTTP_X_FORWARDED_PROTO']))
		{
			if (strtolower (trim ($_SERVER ['HTTP_X_FORWARDED_PROTO'])) == 'https')
			{
				return 'https';
			}
		}
		
		if (!empty ($_SERVER ['HTTPS']))
		{
			if (strtolower (trim ($_SERVER ['HTTPS'])) == 'on' or trim ($_SERVER ['HTTPS']) == '1')
			{
				return 'https';
			}
		}
		
		return 'http';
	}
	
	// Social Login Administration
	public function index ($setting)
	{
		if (OC2)
		{
			$this->load->language ('module/oneall');
		}
		else
		{
			$this->language->load ('module/oneall');
		}
		
	
		
		// User Settings
		$data ['oasl_user_is_logged'] = $this->customer->isLogged ();
		
		// Plugin Settings
		$data ['oasl_heading_title'] = trim ($this->language->get ('heading_title'));
		$data ['oasl_login_button'] = trim ($this->language->get ('login_button'));
		$data ['oasl_login_button'] = (empty ($data ['oasl_login_button']) ? 'Social Login' : $data ['oasl_login_button']);
		$data ['oasl_type'] = $setting ['type'];
		$data ['oasl_pos_x'] = $setting ['x'];
		$data ['oasl_pos_y'] = $setting ['y'];
		$data ['oasl_custom_css_uri'] = ($setting ['css'] == 'modal' ? '' : $setting ['css']);
		$data ['oasl_display_modal'] = ($setting ['css'] == 'modal' ? 1 : 0);
		
		// Language Settings
		$data ['oasl_lib_lang'] = $this->config->get ('config_language');
		$data ['oasl_store_lang'] = $this->config->get ('oneall_store_lang');
		
		// Selected Subdomain
		$data ['oasl_subdomain'] = $this->config->get ('oneall_subdomain');
		
	
		// Add Library
		if (!empty ($data ['oasl_subdomain']))
		{
			$this->document->addScript (self::get_request_protocol () . '://' . $data ['oasl_subdomain'] . '.api.oneall.com/socialize/library.js' . ($data ['oasl_store_lang'] ? ('?lang=' . $data ['oasl_lib_lang']) : ''));
		}
		
		// Selected Providers
		$data ['oasl_providers'] = '';
		
		// Read Providers Config
		$providers_config = trim ($this->config->get ('oneall_socials'));
		if (!empty ($providers_config))
		{
			$providers_list = explode (',', $providers_config);
			if (is_array ($providers_list))
			{
				$data ['oasl_providers'] = implode ("','", $providers_list);
			}
		}
		
		// Griz Sizes
		$data ['oasl_grid_size_x'] = (is_numeric ($setting ['gridh']) ? $setting ['gridh'] : 99);
		$data ['oasl_grid_size_y'] = (is_numeric ($setting ['gridw']) ? $setting ['gridw'] : 99);
		
		// Callback URI
		$data ['oasl_callback_uri'] = HTTPS_SERVER . 'index.php?route=account/oneall&go=' . urlencode ($_SERVER ['REQUEST_URI']);

		// Display Wiget
		return $this->display_widget_template ($data);
	}
	
	// Display Widget
	protected function display_widget_template ($data)
	{
		// Widget Template
		$template = '/template/module/oneall.tpl';
		$template_folder = $this->config->get ('config_template');

		// Get Template Folder
		$template_folder = (file_exists (DIR_TEMPLATE . $template_folder . $template) ? $template_folder : 'default');
		
		// OpenCart2
		if (OC2)
		{
			return $this->load->view (($template_folder .$template) , $data);			
		}
		// OpenCart1
		else
		{		
			$this->data = $data;
			$this->template = ($template_folder . $template);
			$this->render ();
		}
	}
}
?>