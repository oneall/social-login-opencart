<?php

error_reporting(E_ALL);
ini_set("display_errors" , 1);

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

// ////////////////////////////////////////////////////////////////////
// Admin Panel
// ////////////////////////////////////////////////////////////////////
class ControllerModuleOneall extends Controller

{	// Version
	const USER_AGENT = 'SocialLogin/2.0 OpenCart/2.0 (+http://www.oneall.com/)';
	
	private $error = array();
	
	// Display Admin
	public function index ()
	{
		// Language
		$data = $this->load->language ('module/oneall');
		
		// Page Title		
		$this->document->setTitle ($this->language->get ('heading_title'));
		
		// CSS & JS
		$this->document->addStyle ('view/stylesheet/oneall/oneall.css');
		$this->document->addScript ('view/javascript/oneall/oneall.js');
		
		// Load Models
		$this->load->model ('setting/setting');
		$this->load->model ('design/layout');
		
		// Save Settings
		if (($this->request->server ['REQUEST_METHOD'] == 'POST') && $this->validate ())
		{
			// OpenCart 2 mimic
			if (OC2)
			{
				$data ['layouts'] = $this->model_design_layout->getLayouts ();
				$this->load->model ('extension/module');
				
				$modules = $this->model_extension_module->getModulesByCode ('oneall');
				$old_modules = array();
				foreach ($modules as $module)
					$old_modules [$module ['module_id']] = 1;
				
				if (isset ($_POST ['oneall_module']))
					foreach ($_POST ['oneall_module'] as $module)
					{
						
						foreach ($data ['layouts'] as $layout)
							if ($layout ['layout_id'] == $module ['layout_id'])
								$module ['name'] = $layout ['name'];
						
						if (!empty ($module ['module_id']))
						{
							$module_id = $module ['module_id'];
							unset ($module ['module_id']);
							unset ($old_modules [$module_id]);
							$this->model_extension_module->editModule ($module_id, $module);
							$this->db->query ("DELETE FROM " . DB_PREFIX . "layout_module WHERE code = 'oneall.$module_id'");
						}
						else
						{
							$this->model_extension_module->addModule ('oneall', $module);
							$module_id = $this->db->getLastId ();
						}
						$this->db->query ("INSERT INTO " . DB_PREFIX . "layout_module SET layout_id = '" . $module ['layout_id'] . "', position = '" . $module ['position'] . "', sort_order = '" . $module ['sort_order'] . "', code = 'oneall.$module_id'");
					}
				
				foreach ($old_modules as $module_id => $value)
					$this->model_extension_module->deleteModule ($module_id);
				
				unset ($_POST ['oneall_module']);
			}
			
			
			$this->model_setting_setting->editSetting ('oneall', $this->request->post);
			
			$this->session->data ['success'] = $data['oa_text_settings_saved'];
			
			if (OC2)
				$this->response->redirect ($this->url->link ('module/oneall', 'token=' . $this->session->data ['token'], 'SSL'));
			else
				$this->redirect ($this->url->link ('module/oneall', 'token=' . $this->session->data ['token'], 'SSL'));
		}
		
		if (isset ($this->error ['warning']))
		{
			$data ['error_warning'] = $this->error ['warning'];
		}
		else
		{
			$data ['error_warning'] = '';
		}
		
		// BreadCrumbs
		$data ['breadcrumbs'] = array(
			array(
				'text' => $this->language->get ('text_home'),
				'href' => $this->url->link ('common/home', 'token=' . $this->session->data ['token'], 'SSL'),
				'separator' => false 
			),
			array(
				'text' => $this->language->get ('text_module'),
				'href' => $this->url->link ('extension/module', 'token=' . $this->session->data ['token'], 'SSL'),
				'separator' => ' :: ' 
			),
			array(
				'text' => $this->language->get ('heading_title'),
				'href' => $this->url->link ('module/oneall', 'token=' . $this->session->data ['token'], 'SSL'),
				'separator' => ' :: ' 
			) 
		);
		
		$data ['action'] = $this->url->link ('module/oneall', 'token=' . $this->session->data ['token'], 'SSL');
		
		$data ['cancel'] = $this->url->link ('extension/module', 'token=' . $this->session->data ['token'], 'SSL');
		
		$data ['modules'] = array();
		
		$data = array_merge ($data, $this->model_setting_setting->getSetting ('oneall'));
		
		if (OC2)
		{
			$data ['oneall_module'] = array();
			$this->load->model ('extension/module');
			$modules = $this->model_extension_module->getModulesByCode ('oneall');
			foreach ($modules as $module)
				$data ['oneall_module'] [] = array_merge ($this->model_extension_module->getModule ($module ['module_id']), array(
					'module_id' => $module ['module_id'] 
				));
		}
		
		////////////////////////////////////////////////////////////////////////////////////////
		// Default data
		////////////////////////////////////////////////////////////////////////////////////////
		
		// Communication Handler
		if (isset ($data ['oneall_api_handler']) && $data ['oneall_api_handler'] == 'fso')
		{
			$data ['oneall_api_handler'] = 'fso';
		}
		else
		{
			$data ['oneall_api_handler'] = 'crl';
		}
		
		// Communication Port
		if (isset ($data ['oneall_api_port']) && $data ['oneall_api_port'] == '80')
		{
			$data ['oneall_api_port'] = '80';
		}
		else
		{
			$data ['oneall_api_port'] = '443';
		}
		
		// Subdomain		
		if (!isset ($data ['oneall_subdomain']))
		{
			$data ['oneall_subdomain'] = '';
		}
		
		// Public Key		
		if (!isset ($data ['oneall_public']))
		{
			$data ['oneall_public'] = '';
		}
		
		// Private Key		
		if (!isset ($data ['oneall_private']))
		{
			$data ['oneall_private'] = '';
		}
		
		// Ask for email address
		if ( ! empty ($data ['oneall_ask_email']))
		{
			$data ['oneall_ask_email'] = 1;
		}
		else
		{
			$data ['oneall_ask_email'] = 0;
		}
		
		// Ask for phone number		
		if ( ! empty ($data ['oneall_ask_phone']))
		{
			$data ['oneall_ask_phone'] = 1;
		}
		else
		{
			$data ['oneall_ask_phone'] = 0;
		}
		
		// Library Language		
		if (! empty ($data ['oneall_store_lang']))
		{
			$data ['oneall_store_lang'] = 1;
		}
		else
		{
			$data ['oneall_store_lang'] = 0;
		}
		
		// Social Networks
		if (!isset ($data ['oneall_socials']))
		{
			$data ['oneall_socials'] = 'facebook,google,twitter';
		}

		////////////////////////////////////////////////////////////////////////////////////////
		// Other Information
		////////////////////////////////////////////////////////////////////////////////////////
		

		// All Social Networks		
		$data ['oa_social_networks'] = self::get_social_networks ();
		
		if (!isset ($data ['oneall_module']))
			$data ['oneall_module'] = array(
				array(
					'layout_id' => 1,
					'sort_order' => false,
					'css' => 'catalog/view/theme/default/stylesheet/oneall/Squares.css',
					'gridw' => false,
					'gridh' => false,
					'type' => 'module',
					'position' => 'column_right',
					'x' => 10,
					'y' => 20,
					'status' => 1 
				) 
			);



		
		$data ['layouts'] = $this->model_design_layout->getLayouts ();
		
		
		if (OC2)
		{
			
			$data ['header'] = $this->load->controller ('common/header');
			$data ['column_left'] = $this->load->controller ('common/column_left');
			$data ['footer'] = $this->load->controller ('common/footer');
			$this->response->setOutput ($this->load->view ('module/oneall.tpl', $data));
		}
		else
		{
			
			$this->data = $data;
			$this->template = 'module/oneall.tpl';
			$this->children = array(
				'common/header',
				'common/footer' 
			);
			$this->response->setOutput ($this->render ());
		}
	}

	// Validation
	private function validate ()
	{
		// Van this user modify the settings?
		if (!$this->user->hasPermission ('modify', 'module/oneall'))
		{
			$this->error ['warning'] = $this->language->get ('error_permission');
		}
		
		// Done
		return  ((!$this->error) ? true : false);
	}
	
	// Returns the list of available social networks.
	public function get_social_networks ()
	{
		$providers = array(
			'amazon' => 'Amazon',
			'blogger' => 'Blogger',
			'disqus' => 'Disqus',
			'draugiem' => 'Draugiem',
			'dribbble' => 'Dribbble',
			'facebook' => 'Facebook',
			'foursquare' => 'Foursquare',
			'github' => 'Github.com',
			'google' => 'Google',
			'instagram' => 'Instagram',
			'linkedin' => 'LinkedIn',
			'livejournal' => 'LiveJournal',
			'mailru' => 'Mail.ru',
			'odnoklassniki' => 'Odnoklassniki',
			'openid' => 'OpenID',
			'paypal' => 'PayPal',
			'reddit' => 'Reddit',
			'skyrock' => 'Skyrock.com',
			'stackexchange' => 'StackExchange',
			'steam' => 'Steam',
			'twitch' => 'Twitch.tv',
			'twitter' => 'Twitter',
			'vimeo' => 'Vimeo',
			'vkontakte' => 'VKontakte',
			'windowslive' => 'Windows Live',
			'wordpress' => 'WordPress.com',
			'yahoo' => 'Yahoo',
			'youtube' => 'YouTube',
			'battlenet' => 'BattleNet' 
		);
		
		return $providers;
	}
	
	/////////////////////////////////////////////////////////////////////////////////////////////
	// AJAX
	/////////////////////////////////////////////////////////////////////////////////////////////
	

	// Check API Settings
	public function verify_api_settings ()
	{
		// Load Language
		$lang = $this->load->language ('module/oneall');
	
		// Read arguments.
		$get = (is_array ($this->request->get) ? $this->request->get : array());
		
		// Parse arguments
		$oneall_subdomain = ( ! empty ($get['oneall_subdomain']) ? trim ($get['oneall_subdomain']) : '');
		$oneall_public = ( ! empty ($get['oneall_public']) ? trim ($get['oneall_public']) : '');
		$oneall_private = ( ! empty ($get['oneall_private']) ? trim ($get['oneall_private']) : '');
		$oneall_api_handler = ( ! empty ($get['oneall_api_handler']) ? trim ($get['oneall_api_handler']) : '');
		$oneall_api_port = ( ! empty ($get['oneall_api_port']) ? trim ($get['oneall_api_port']) : '');
		
		// Init status message.
		$status_message = null;
	
		// Check if all fields have been filled out.
		if (strlen ($oneall_subdomain) == 0 || strlen ($oneall_public) == 0 || strlen ($oneall_private) == 0)
		{
			$status_message = 'error|' . $lang['oa_text_ajax_fill_out'];
		}
		else
		{
			// Check the handler
			$oneall_api_handler = ($oneall_api_handler == 'fso' ? 'fsockopen' : 'curl');
			$oneall_api_use_https = ($oneall_api_port == 443 ? true : false);
				
			// FSOCKOPEN
			if ($oneall_api_handler == 'fsockopen')
			{
				if (!self::check_fsockopen ($oneall_api_use_https))
				{
					$status_message = 'error|' . $lang ['oa_text_ajax_wrong_handler'];
				}
			}
			// CURL
			else
			{
				if (!self::check_curl ($oneall_api_use_https))
				{
					$status_message = 'error|' . $lang ['oa_text_ajax_wrong_handler'];
				}
			}
				
			// No errors until now.
			if (empty ($status_message))
			{
				// The full domain has been entered.
				if (preg_match ("/([a-z0-9\-]+)\.api\.oneall\.com/i", $oneall_subdomain, $matches))
				{
					$oneall_subdomain = $matches [1];
				}
	
				// Check format of the subdomain.
				if (!preg_match ("/^[a-z0-9\-]+$/i", $oneall_subdomain))
				{
					$status_message = 'error|' . $lang ['oa_text_ajax_wrong_subdomain'];
				}
				else
				{
					// Construct full API Domain.
					$oneall_api_domain = $oneall_subdomain . '.api.oneall.com';
					$oneall_api_resource_url = ($oneall_api_use_https ? 'https' : 'http') . '://' . $oneall_api_domain . '/tools/ping.json';
						
					// API Credentialls.
					$oneall_api_credentials = array();
					$oneall_api_credentials ['api_key'] = $oneall_public;
					$oneall_api_credentials ['api_secret'] = $oneall_private;
						
					// Try to establish a connection.
					$result = self::do_api_request ($oneall_api_handler, $oneall_api_resource_url, $oneall_api_credentials);
						
					// Parse result.
					if (is_object ($result) && property_exists ($result, 'http_code') && property_exists ($result, 'http_data'))
					{
						switch ($result->http_code)
						{
							// Connection successfull.
							case 200 :
								$status_message = 'success|' . $lang ['oa_text_ajax_settings_ok'];
								break;
									
								// Authentication Error.
							case 401 :
								$status_message = 'error|' . $lang ['oa_text_ajax_wrong_key'];
								break;
									
								// Wrong Subdomain.
							case 404 :
								$status_message = 'error|' . $lang ['oa_text_ajax_missing_subdomain'];
								break;
									
								// Other error.
							default :
								$status_message = 'error|' . $lang ['oa_text_ajax_autodetect_error'];
								break;
						}
					}
					else
					{
						$status_message = 'error|' . $lang ['oa_text_ajax_autodetect_error'];
					}
				}
			}
		}	
		
		// Output for Ajax.
		die ($status_message);
	}
	
	// Automatic API Detection
	public function autodetect_api_connection ()
	{
		// Load Language
		$lang = $this->load->language ('module/oneall');

		// Check CURL HTTPS - Port 443.
		if (self::check_curl (true) === true)
		{
			$status_message = 'success|curl_443|'.$lang['oa_text_ajax_curl_ok_443'];
		}
		// Check CURL HTTP - Port 80.
		elseif (self::check_curl (false) === true)
		{
			$status_message = 'success|curl_80|'.$lang['oa_text_ajax_curl_ok_80'];
		}
		// Check FSOCKOPEN HTTPS - Port 443.
		elseif (self::check_fsockopen (true) == true)
		{
			$status_message = 'success|fsockopen_443|'.$lang['oa_text_ajax_fsockopen_ok_443'];
		}
		// Check FSOCKOPEN HTTP - Port 80.
		elseif (self::check_fsockopen (false) == true)
		{
			$status_message = 'success|fsockopen_80|'.$lang['oa_text_ajax_fsockopen_ok_80'];
		}
		// No working handler found.
		else
		{
			$status_message = 'error|none|'.$lang['oa_text_ajax_no_handler'];
		}

		// Output for AJAX.
		die ($status_message);
	}
	
	// Returns a list of disabled PHP functions.
	protected static function get_php_disabled_functions ()
	{
		$disabled_functions = trim (ini_get ('disable_functions'));
		if (strlen ($disabled_functions) == 0)
		{
			$disabled_functions = array();
		}
		else
		{
			$disabled_functions = explode (',', $disabled_functions);
			$disabled_functions = array_map ('trim', $disabled_functions);
		}
		return $disabled_functions;
	}
	
	// Sends an API request by using the given handler.
	public function do_api_request ($handler, $url, $options = array(), $timeout = 30)
	{
		// FSOCKOPEN
		if ($handler == 'fsockopen')
		{
			return self::fsockopen_request ($url, $options, $timeout);
		}
		// CURL
		else
		{
				
			return self::curl_request ($url, $options, $timeout);
		}
	}
	
	// Checks if CURL can be used.
	public static function check_curl ($secure = true)
	{
		if (in_array ('curl', get_loaded_extensions ()) && function_exists ('curl_exec') && !in_array ('curl_exec', self::get_php_disabled_functions ()))
		{
			$result = self::curl_request (($secure ? 'https' : 'http') . '://www.oneall.com/ping.html');
			if (is_object ($result) && property_exists ($result, 'http_code') && $result->http_code == 200)
			{
				if (property_exists ($result, 'http_data'))
				{
					if (strtolower ($result->http_data) == 'ok')
					{
						return true;
					}
				}
			}
		}
		return false;
	}
	
	// Checks if fsockopen can be used.
	public static function check_fsockopen ($secure = true)
	{
		if (function_exists ('fsockopen') && !in_array ('fsockopen', $this->get_php_disabled_functions ()))
		{
			$result = $this->fsockopen_request (($secure ? 'https' : 'http') . '://www.oneall.com/ping.html');
			if (is_object ($result) && property_exists ($result, 'http_code') && $result->http_code == 200)
			{
				if (property_exists ($result, 'http_data'))
				{
					if (strtolower ($result->http_data) == 'ok')
					{
						return true;
					}
				}
			}
		}
		return false;
	}
	
	/**
	 * Sends a CURL request.
	 */
	public static function curl_request ($url, $options = array(), $timeout = 30, $num_redirects = 0)
	{
		// Store the result
		$result = new \stdClass ();
	
		// Send request
		$curl = curl_init ();
		curl_setopt ($curl, CURLOPT_URL, $url);
		curl_setopt ($curl, CURLOPT_HEADER, 1);
		curl_setopt ($curl, CURLOPT_TIMEOUT, $timeout);
		curl_setopt ($curl, CURLOPT_REFERER, $url);
		curl_setopt ($curl, CURLOPT_VERBOSE, 0);
		curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($curl, CURLOPT_USERAGENT, self::USER_AGENT);
	
		// Does not work in PHP Safe Mode, we manually follow the locations if necessary.
		curl_setopt ($curl, CURLOPT_FOLLOWLOCATION, 0);
	
		// BASIC AUTH?
		if (isset ($options ['api_key']) && isset ($options ['api_secret']))
		{
			curl_setopt ($curl, CURLOPT_USERPWD, $options ['api_key'] . ':' . $options ['api_secret']);
		}
	
		// Proxy Settings
		if ( ! empty ($options ['proxy_url']))
		{
			// Proxy Location
			curl_setopt ($curl, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
			curl_setopt ($curl, CURLOPT_PROXY, $options ['proxy_url']);
				
			// Proxy Port
			if ( ! empty ($options ['proxy_port']))
			{
				curl_setopt ($curl, CURLOPT_PROXYPORT, $options ['proxy_port']);
			}
	
			// Proxy Authentication
			if ( ! empty ($options ['proxy_username']) && ! empty ($options ['proxy_password']))
			{
				curl_setopt ($curl, CURLOPT_PROXYAUTH, CURLAUTH_ANY);
				curl_setopt ($curl, CURLOPT_PROXYUSERPWD, $options ['proxy_username'] . ':' . $options ['proxy_password']);
			}
		}
	
		// Make request
		if (($response = curl_exec ($curl)) !== false)
		{
			// Get Information
			$curl_info = curl_getinfo ($curl);
				
			// Save result
			$result->http_code = $curl_info ['http_code'];
			$result->http_headers = preg_split ('/\r\n|\n|\r/', trim (substr ($response, 0, $curl_info ['header_size'])));
			$result->http_data = trim (substr ($response, $curl_info ['header_size']));
			$result->http_error = null;
				
			// Check if we have a redirection header
			if (in_array ($result->http_code, array(301, 302)) && $num_redirects < 4)
			{
				// Make sure we have http headers
				if (is_array ($result->http_headers))
				{
					// Header found ?
					$header_found = false;
						
					// Loop through headers.
					while ( !$header_found && (list (, $header) = each ($result->http_headers)) )
					{
						// Try to parse a redirection header.
						if (preg_match ("/(Location:|URI:)[^(\n)]*/", $header, $matches))
						{
							// Sanitize redirection url.
							$url_tmp = trim (str_replace ($matches [1], "", $matches [0]));
							$url_parsed = parse_url ($url_tmp);
							if (!empty ($url_parsed))
							{
								// Header found!
								$header_found = true;
	
								// Follow redirection url.
								$result = self::curl_request ($url_tmp, $options, $timeout, $num_redirects + 1);
							}
						}
					}
				}
			}
		}
		else
		{
			$result->http_code = -1;
			$result->http_data = null;
			$result->http_error = curl_error ($curl);
		}
	
		// Done
		return $result;
	}
	
	// Sends an fsockopen request.
	protected static function fsockopen_request ($url, $options = array(), $timeout = 30, $num_redirects = 0)
	{
		// Store the result
		$result = new \stdClass ();
	
		// Make that this is a valid URL
		if (($uri = parse_url ($url)) == false)
		{
			$result->http_code = -1;
			$result->http_data = null;
			$result->http_error = 'invalid_uri';
			return $result;
		}
	
		// Make sure we can handle the schema
		switch ($uri ['scheme'])
		{
			case 'http' :
				$port = (isset ($uri ['port']) ? $uri ['port'] : 80);
				$host = ($uri ['host'] . ($port != 80 ? ':' . $port : ''));
				$fp = @fsockopen ($uri ['host'], $port, $errno, $errstr, $timeout);
				break;
					
			case 'https' :
				$port = (isset ($uri ['port']) ? $uri ['port'] : 443);
				$host = ($uri ['host'] . ($port != 443 ? ':' . $port : ''));
				$fp = @fsockopen ('ssl://' . $uri ['host'], $port, $errno, $errstr, $timeout);
				break;
					
			default :
				$result->http_code = -1;
				$result->http_data = null;
				$result->http_error = 'invalid_schema';
				return $result;
				break;
		}
	
		// Make sure the socket opened properly
		if (!$fp)
		{
			$result->http_code = -$errno;
			$result->http_data = null;
			$result->http_error = trim ($errstr);
			return $result;
		}
	
		// Construct the path to act on
		$path = (isset ($uri ['path']) ? $uri ['path'] : '/');
		if (isset ($uri ['query']))
		{
			$path .= '?' . $uri ['query'];
		}
	
		// Create HTTP request
		$defaults = array();
		$defaults ['Host'] = 'Host: ' . $host;
		$defaults ['User-Agent'] = 'User-Agent: ' . self::USER_AGENT;
	
		// BASIC AUTH?
		if (isset ($options ['api_key']) && isset ($options ['api_secret']))
		{
			$defaults ['Authorization'] = 'Authorization: Basic ' . base64_encode ($options ['api_key'] . ":" . $options ['api_secret']);
		}
	
		// Build and send request
		$request = 'GET ' . $path . " HTTP/1.0\r\n";
		$request .= implode ("\r\n", $defaults);
		$request .= "\r\n\r\n";
		fwrite ($fp, $request);
	
		// Fetch response
		$response = '';
		while ( !feof ($fp) )
		{
			$response .= fread ($fp, 1024);
		}
	
		// Close connection
		fclose ($fp);
	
		// Parse response
		list ($response_header, $response_body) = explode ("\r\n\r\n", $response, 2);
	
		// Parse header
		$response_header = preg_split ("/\r\n|\n|\r/", $response_header);
		list ($header_protocol, $header_code, $header_status_message) = explode (' ', trim (array_shift ($response_header)), 3);
	
		// Set result
		$result->http_code = $header_code;
		$result->http_headers = $response_header;
		$result->http_data = $response_body;
	
		// Make sure we we have a redirection status code
		if (in_array ($result->http_code, array(301, 302)) && $num_redirects <= 4)
		{
			// Make sure we have http headers
			if (is_array ($result->http_headers))
			{
				// Header found?
				$header_found = false;
	
				// Loop through headers.
				while ( !$header_found && (list (, $header) = each ($result->http_headers)) )
				{
					// Check for location header
					if (preg_match ("/(Location:|URI:)[^(\n)]*/", $header, $matches))
					{
						// Found
						$header_found = true;
	
						// Clean url
						$url_tmp = trim (str_replace ($matches [1], "", $matches [0]));
						$url_parsed = parse_url ($url_tmp);
	
						// Found
						if (!empty ($url_parsed))
						{
							$result = self::fsockopen_request ($url_tmp, $options, $timeout, $num_redirects + 1);
						}
					}
				}
			}
		}
	
		// Done
		return $result;
	}
	
	/////////////////////////////////////////////////////////////////////////////////////////////
	// SETUP
	/////////////////////////////////////////////////////////////////////////////////////////////
	
	// Installation Script
	public function install ()
	{
		// User Token Storage
		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "oasl_user` (
					`oasl_user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`customer_id` int(11) unsigned NOT NULL DEFAULT '0',
					`user_token` char(32) COLLATE utf8_bin NOT NULL DEFAULT '',
					`date_added` datetime NOT NULL,
				PRIMARY KEY (`oasl_user_id`),
				KEY `user_id` (`customer_id`),
				KEY `user_token` (`user_token`));";
		$this->db->query ($sql);
		
		// Identity Token Storage
		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "oasl_identity` (
					`oasl_identity_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`oasl_user_id` int(11) unsigned NOT NULL DEFAULT '0',
					`identity_token` char(32) COLLATE utf8_bin NOT NULL DEFAULT '',
					`identity_provider` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
					`num_logins` int(11) NOT NULL DEFAULT '0',
					`date_added` datetime NOT NULL ,
					`date_updated` datetime NOT NULL,
				PRIMARY KEY (`oasl_identity_id`),
				UNIQUE KEY `oaid` (`oasl_identity_id`));";
		$this->db->query ($sql);
	}
}
?>
