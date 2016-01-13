<?php
/**
 * @package   	OneAll Social Login
 * @copyright 	Copyright 2016 http://www.oneall.com - All rights reserved.
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

// ////////////////////////////////////////////////////////////////////
// Admin Panel
// ////////////////////////////////////////////////////////////////////
class ControllerModuleOneall extends Controller
{		
	private $error = array();
		
	// Display Admin
	public function index ()
	{
		// Language
		$data = $this->load->language ('module/oneall');
		
		// Page Title		
		$this->document->setTitle ($this->language->get ('heading_title'));
		
		// CSS & JS
		$this->document->addStyle ('view/stylesheet/oneall/bootstrap.min.css');
		$this->document->addStyle ('view/stylesheet/oneall/oneall.css');
		$this->document->addScript ('view/javascript/oneall/oneall.js');
		
		// Load Models
		$this->load->model ('setting/setting');
		$this->load->model ('design/layout');
				
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
		
		// Buttons
		$data ['action'] = $this->url->link ('module/oneall', 'token=' . $this->session->data ['token'], 'SSL');
		$data ['cancel'] = $this->url->link ('extension/module', 'token=' . $this->session->data ['token'], 'SSL');
		
		// Add Settings
		$data = array_merge ($data, $this->model_setting_setting->getSetting ('oneall'));
		
		/////////////////////////////////////////////////////////////////////
		// Save
		/////////////////////////////////////////////////////////////////////		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate())
		{			
			// Build Social Network List
			if (isset ($this->request->post['oneall_social_networks']))
			{
				if (is_array ($this->request->post['oneall_social_networks']))
				{
					$oneall_socials = array();
			
					foreach ($this->request->post['oneall_social_networks'] AS $key => $is_enabled)
					{
						if ( ! empty ($is_enabled))
						{
							$oneall_socials[] = $key;
						}
					}
			
					$this->request->post['oneall_socials'] = implode (",", $oneall_socials);
				}
			}
			
			// Save
			$this->model_setting_setting->editSetting('oneall', $this->request->post);
		
			// Redirect
			$this->redirect ($this->url->link ('module/oneall', ('token=' . $this->session->data ['token'] . '&oa_action=saved'), 'SSL'));
		}	
		
	
		////////////////////////////////////////////////////////////////////////
		// Positions
		////////////////////////////////////////////////////////////////////////		
		$data ['layouts'] = $this->model_design_layout->getLayouts ();
		
		// Modules
		$data['modules'] = array();
		
		if (isset($this->request->post['oneall_module']))
		{
			$data['modules'] = $this->request->post['oneall_module'];
		}
		elseif ($this->config->get('oneall_module'))
		{
			$data['modules'] = $this->config->get('oneall_module');
		}		

		////////////////////////////////////////////////////////////////////////////////////////
		// Settings
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
		
		// Account Creation
		if (!isset ($data ['oneall_auto_account']) || ! in_array ($data ['oneall_auto_account'], array (0,1)))
		{
			$data ['oneall_auto_account'] = '1';
		}
		
		// Account Creation - Address
		if (!isset ($data ['oneall_ask_address']) || ! in_array ($data ['oneall_ask_address'], array (0,1)))
		{
			$data ['oneall_auto_account'] = '0';
		}
		
		// Account Link
		if (! isset ($data ['oneall_auto_link']) || ! in_array ($data ['oneall_auto_link'], array (0,1)))
		{
			$data ['oneall_auto_link'] = '1';
		}
		
		// Library Language
		if ( ! isset ($data ['oneall_store_lang']) || ! in_array ($data ['oneall_store_lang'], array (0,1)))
		{
			$data ['oneall_store_lang'] = 1;
		}
		else
		{
			$data ['oneall_store_lang'] = 0;
		}
			
		// Social Networks
		if ( ! isset ($data ['oneall_socials']))
		{
			$data ['oneall_socials'] = 'facebook,google,twitter';
		}
		
		// Social Login Status
		if ( ! isset ($data ['oneall_status']) || ! in_array ($data ['oneall_status'], array (0,1)))
		{
			$data ['oneall_status'] = '1';
		}
		
		// All Social Networks
		$data ['oa_social_networks'] = $this->get_social_networks ();
		
		////////////////////////////////////////////////////////////////////////////////////////
		// Notices
		////////////////////////////////////////////////////////////////////////////////////////
	
		// Success Message
		if (isset ($this->request->get) && ! empty ($this->request->get['oa_action']) == 'saved')
		{
			$data ['oa_success_message'] = $data['oa_text_settings_saved'];
		}
			
		// Error Message
		if ( ! empty ($this->error ['warning']))
		{
			$data ['oa_error_message'] = $this->error ['warning'];
		}
		
		// Display Layout		
		$this->data = $data;
		$this->template = 'module/oneall.tpl';
		$this->children = array('common/header', 'common/footer');
		$this->response->setOutput ($this->render ());
	}

	// Validation
	private function validate ()
	{
		// Van this user modify the settings?
		if (!$this->user->hasPermission ('modify', 'module/oneall'))
		{
			$this->error ['warning'] = $this->language->get ('oa_text_error_permission');
			return false;
		}
		
		// Done
		return  true;
	}
	
	// Returns the list of available social networks.
	private function get_social_networks ()
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
				if (!$this->check_fsockopen ($oneall_api_use_https))
				{
					$status_message = 'error|' . $lang ['oa_text_ajax_wrong_handler'];
				}
			}
			// CURL
			else
			{
				if (!$this->check_curl ($oneall_api_use_https))
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
					$result = $this->do_api_request ($oneall_api_handler, $oneall_api_resource_url, $oneall_api_credentials);
						
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
		if ($this->check_curl (true) === true)
		{
			$status_message = 'success|curl_443|'.$lang['oa_text_ajax_curl_ok_443'];
		}
		// Check CURL HTTP - Port 80.
		elseif ($this->check_curl (false) === true)
		{
			$status_message = 'success|curl_80|'.$lang['oa_text_ajax_curl_ok_80'];
		}
		// Check FSOCKOPEN HTTPS - Port 443.
		elseif ($this->check_fsockopen (true) == true)
		{
			$status_message = 'success|fsockopen_443|'.$lang['oa_text_ajax_fsockopen_ok_443'];
		}
		// Check FSOCKOPEN HTTP - Port 80.
		elseif ($this->check_fsockopen (false) == true)
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
	protected function get_php_disabled_functions ()
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
	protected function do_api_request ($handler, $url, $options = array(), $timeout = 30)
	{
		// FSOCKOPEN
		if ($handler == 'fsockopen')
		{
			return $this->fsockopen_request ($url, $options, $timeout);
		}
		// CURL
		else
		{
				
			return $this->curl_request ($url, $options, $timeout);
		}
	}
	
	// Checks if CURL can be used.
	protected function check_curl ($secure = true)
	{
		if (in_array ('curl', get_loaded_extensions ()) && function_exists ('curl_exec') && !in_array ('curl_exec', $this->get_php_disabled_functions ()))
		{
			$result = $this->curl_request (($secure ? 'https' : 'http') . '://www.oneall.com/ping.html');
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
	protected function check_fsockopen ($secure = true)
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
	private function curl_request ($url, $options = array(), $timeout = 30, $num_redirects = 0)
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
		curl_setopt ($curl, CURLOPT_USERAGENT, $this->get_user_agent());
	
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
								$result = $this->curl_request ($url_tmp, $options, $timeout, $num_redirects + 1);
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
	protected function fsockopen_request ($url, $options = array(), $timeout = 30, $num_redirects = 0)
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
		$defaults ['User-Agent'] = 'User-Agent: ' . $this->get_user_agent();
	
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
							$result = $this->fsockopen_request ($url_tmp, $options, $timeout, $num_redirects + 1);
						}
					}
				}
			}
		}
	
		// Done
		return $result;
	}
	
	// Build User Agent
	private function get_user_agent ()
	{
		// System Versions
		$social_login = 'SocialLogin/1.0';
		$opencart = 'OpenCart' . (defined ('VERSION') ? ('/' . substr (VERSION, 0, 3)) : '1.5.x');
	
		// Build User Agent
		return ($social_login . ' ' . $opencart . ' (+http://www.oneall.com/)');
	}
	
	////////////////////////////////////////////////////////////////////////////////////////////////////
	// Installer
	////////////////////////////////////////////////////////////////////////////////////////////////////
	
	// Installation Script
	public function install ()
	{
		// User Token Storage
		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "oasl_user` (
					`oasl_user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`customer_id` int(11) unsigned NOT NULL DEFAULT '0',
					`user_token` char(36) COLLATE utf8_bin NOT NULL DEFAULT '',
					`date_added` datetime NOT NULL,
				PRIMARY KEY (`oasl_user_id`),
				KEY `user_id` (`customer_id`),
				KEY `user_token` (`user_token`));";
		$this->db->query ($sql);
	
		// Identity Token Storage
		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "oasl_identity` (
					`oasl_identity_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
					`oasl_user_id` int(11) unsigned NOT NULL DEFAULT '0',
					`identity_token` char(36) COLLATE utf8_bin NOT NULL DEFAULT '',
					`identity_provider` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
					`num_logins` int(11) NOT NULL DEFAULT '0',
					`date_added` datetime NOT NULL ,
					`date_updated` datetime NOT NULL,
				PRIMARY KEY (`oasl_identity_id`),
				UNIQUE KEY `oaid` (`oasl_identity_id`));";
		$this->db->query ($sql);
		
		
		// Add to default positions
		$result = $this->db->query ("SELECT layout_id FROM `" . DB_PREFIX . "layout` WHERE name IN ('Account', 'Checkout')");
		if ($result->num_rows > 0)
		{
			// Positions to add
			$positions = array();		
			foreach ($result->rows as $row)
			{
				$positions[] = array (
    				'layout_id' => intval ($row['layout_id']),
    				'position' => 'content_top',
    				'status' => '1',
					'sort_order' => '1',
 				);
			}
			
			// Prepare
			if (count ($positions) > 0)
			{
				$data = @serialize ($positions);
				
				// Make sure it worked
				if ( ! empty ($data))
				{			
					// Prevent Duplicates
					$this->db->query ("DELETE FROM `" . DB_PREFIX . "setting` WHERE `group`='oneall' AND `key`='oneall_module'");
									
					// Add Positions
					$this->db->query ("INSERT INTO `" . DB_PREFIX . "setting` SET `group`='oneall', `key`='oneall_module', `value`='".$this->db->escape ($data)."', `serialized`=1");
				}
			}
		}
		
	}
	
	// UnInstallation Script
	public function uninstall()
	{
		// Force Remove
		$force_remove = false;
		
		// These table should normally not be dropped, otherwise the customers can no longer login if the webmaster re-installs the extension.
		if ($force_remove === true)
		{
			// User Token Storage
			$sql = "DROP TABLE IF EXISTS `" . DB_PREFIX . "oasl_user`;";
			$this->db->query ($sql);
		
			// Identity Token Storage
			$sql = "DROP TABLE IF EXISTS `" . DB_PREFIX . "oasl_identity`;";
			$this->db->query ($sql);
		}
			
	}
}
?>