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
	
	// Social Login Administration
	public function index ($setting)
	{		
		// Callback Handler
		$this->callback_handler();
		
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
		
		// Language Settings
		$data ['oasl_lib_lang'] = $this->config->get ('config_language');
		$data ['oasl_store_lang'] = $this->config->get ('oneall_store_lang');
		$data ['oasl_display_modal'] = 0;
		$data ['oasl_grid_size_x'] = 99;
		$data ['oasl_grid_size_y'] = 99;
		$data ['oasl_custom_css_uri'] = '';
		
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
	

	////////////////////////////////////////////////////////////////////////
	// Tools
	////////////////////////////////////////////////////////////////////////
	
	// Callback Handler
	public function callback_handler ()
	{
		// ONEALL Callback handler
		$error = '';
	
		// Check if we have received a connection_token
		if ( isset ($this->request->post) && ! empty ($this->request->post['connection_token']))
		{
			// Get connection_token
			$token = trim ($this->request->post['connection_token']);
				
			// OneAll Site Settings
			$site_subdomain = $this->config->get ('oneall_subdomain');
			$site_public_key = $this->config->get ('oneall_public');
			$site_private_key = $this->config->get ('oneall_private');
				
			// With the API Credentials it does not work
			if (!empty ($site_subdomain))
			{
				// API Access domain
				$site_domain = $site_subdomain . '.api.oneall.com';
	
				// Connection Resource
				// http://docs.oneall.com/api/resources/connections/read-connection-details/
				$resource_uri = 'http://' . $site_domain . '/connections/' . $token . '.json';
	
				// Setup connection
				$curl = curl_init ();
				curl_setopt ($curl, CURLOPT_URL, $resource_uri);
				curl_setopt ($curl, CURLOPT_HEADER, 0);
				curl_setopt ($curl, CURLOPT_USERPWD, $site_public_key . ":" . $site_private_key);
				curl_setopt ($curl, CURLOPT_TIMEOUT, 15);
				curl_setopt ($curl, CURLOPT_VERBOSE, 0);
				curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt ($curl, CURLOPT_FAILONERROR, 0);
				curl_setopt ($curl, CURLOPT_USERAGENT, self::get_user_agent ());
	
				// Send request
				$result_json = curl_exec ($curl);

				// Error
				if ($result_json === false)
				{
					// You may want to implement your custom error handling here
					$error = 'Curl error: ' . curl_error ($curl) . '<br />' . 'Curl info: ' . curl_getinfo ($curl) . '';
					curl_close ($curl);
				}
				// Success
				else
				{
					// Close connection
					curl_close ($curl);
						
					// Decode
					$json = json_decode ($result_json);
			
					// Extract data
					$data = $json->response->result->data;
						
					// Check for plugin
					if ($data->plugin->key == 'social_login')
					{
						// Operation successfull
						if ($data->plugin->data->status == 'success')
						{
							$lastname = '';
							$firstname = '';
								
							if (isset ($data->user->identity->name))
							{
								if (isset ($data->user->identity->name->givenName))
									$firstname = $data->user->identity->name->givenName;
								if (isset ($data->user->identity->name->familyName))
									$lastname = $data->user->identity->name->familyName;
							}
							else if (isset ($data->user->identity->displayName))
								$firstname = $data->user->identity->displayName;
							else if (isset ($data->user->identity->preferredUsername))
								$firstname = $data->user->identity->preferredUsername;
								
							$email = '';
							if (isset ($data->user->identity->emails))
								$email = $data->user->identity->emails [0]->value;
								
							if (isset ($data->user->identity->profileUrl))
								$profile = $data->user->identity->profileUrl;
							else if (isset ($data->user->identity->identity_token))
								$profile = $data->user->identity->id;
								
							$phone = '';
							if (isset ($data->user->identity->phoneNumbers))
								$phone = $data->user->identity->phoneNumbers [0]->value;
								
							// Support for older plugin versions
							$column = $this->db->query ("SHOW COLUMNS FROM " . DB_PREFIX . "customer LIKE 'oneall_profile'")->row;
							if ($column)
							{
								$customer = $this->db->query ("SELECT * FROM `" . DB_PREFIX . "customer` WHERE oneall_profile='" . $this->db->escape ($profile) . "'")->row;
								if ($customer)
								{
									//$sql = "INSERT "
										
								}
	
								$this->db->query ("ALTER TABLE " . DB_PREFIX . "customer ADD `oneall_profile` varchar(128)");
								$this->db->query ("ALTER TABLE " . DB_PREFIX . "customer ADD INDEX `oneall_profile` (`oneall_profile`)");
							}
								
							// Check if customer already exists
							$exists = '';
							if ($email)
								$q = $this->db->query ("SELECT * FROM " . DB_PREFIX . "customer WHERE email='" . $this->db->escape ($email) . "'")->row;
							else
								$q = $this->db->query ("SELECT * FROM " . DB_PREFIX . "customer WHERE oneall_profile='" . $this->db->escape ($profile) . "'")->row;
							if ($q)
							{
								if ($email)
									$exists = 1;
								else
									$exists = 2;
								$email = $q ['email'];
								if (!strpos ($email, '@'))
									$email = '';
								if ($q ['telephone'])
									$phone = $q ['telephone'];
								if ($q ['firstname'])
									$firstname = $q ['firstname'];
								if ($q ['lastname'])
									$lastname = $q ['lastname'];
							}
								
							$_POST = array();
							$_POST ['email'] = $email;
							$_POST ['firstname'] = $firstname;
							$_POST ['lastname'] = $lastname;
							$_POST ['phone'] = $phone;
							$_POST ['_profile'] = $profile;
							$_POST ['_exists'] = $exists;
							$_POST ['_go'] = $_GET ['go'];
								
							$this->ask ();
							return;
						}
					}
				}
	
				echo '<br/><br/><br/><b>Transferred data:</b><br/>';
				print_r ($data);
				echo '<br/><br/><br/><br/><h2 style="color:red">Social authenfication failed.</h2><br/>';
				if ($error)
					echo 'REASON: ' . $error . '<br/><br/>';
				echo "<a href='" . $_GET ['go'] . "'>Press here</a> to continue.";
				exit ();
			}
		}
	}
	
	public function ask ()
	{
	
		// Validate
		$valid = array();
		$validated = 0;
		foreach ($_POST as $name => &$value)
		{
			$valid [$name] = 0;
			$value = trim ($value);
			if ($name [0] == '_' or ($name == 'email' && !$this->config->get ('oneall_ask_email')) or ($name == 'phone' && !$this->config->get ('oneall_ask_phone')))
			{
				$valid [$name] = 1;
				$validated ++;
				if ($name [0] == '_')
					$valid [$name] = -1;
				continue;
			}
			if (!$value)
				continue;
			if ($name == 'email' && !strpos ($value, '@'))
				continue;
			$valid [$name] = 1;
			$validated ++;
		}
	
		if (!$this->config->get ('oneall_ask_phone'))
			$valid ['phone'] = -1;
		if (!$this->config->get ('oneall_ask_email') or $valid ['email'])
			$valid ['email'] = -1;
	
		if ($validated == count ($_POST))
		{ // Validated, lets go
			foreach ($_POST as &$val)
				$val = $this->db->escape ($val);
			if ($_POST ['_exists'] == 1)
				$this->db->query ("UPDATE " . DB_PREFIX . "customer SET telephone='$_POST[phone]' WHERE email='$_POST[email]'");
			elseif ($_POST ['_exists'] == 2)
			$this->db->query ("UPDATE " . DB_PREFIX . "customer SET email='$_POST[email]', telephone='$_POST[phone]' WHERE oneall_profile='$_POST[profile]'");
				
			if (!$_POST ['email'])
				$_POST ['email'] = $_POST ['_profile'];
			if ($_POST ['_profile'])
			{
				// Everything OK
				if (!$this->customer->login ($_POST ['email'], '', true))
				{
					// Generate a random Password
					$password = self::generate_hash (8);
						
					if (substr (VERSION, 4, 1) > 3)
						$this->db->query ("INSERT INTO " . DB_PREFIX . "customer SET store_id = '" . (int) $this->config->get ('config_store_id') . "', firstname = '$_POST[firstname]', lastname = '$_POST[lastname]', email = '$_POST[email]', telephone = '$_POST[phone]', oneall_profile = '$_POST[_profile]', salt = '" . $this->db->escape ($salt = substr (md5 (uniqid (rand (), true)), 0, 9)) . "', password = '" . $this->db->escape (sha1 ($salt . sha1 ($salt . sha1 ($password)))) . "', newsletter = '1', customer_group_id = '1', ip = '" . $this->db->escape ($this->request->server ['REMOTE_ADDR']) . "', status = '1', approved = '1', date_added = NOW()");
					else
						$this->db->query ("INSERT INTO " . DB_PREFIX . "customer SET store_id = '" . (int) $this->config->get ('config_store_id') . "', firstname = '$_POST[firstname]', lastname = '$_POST[lastname]', email = '$_POST[email]', telephone = '$_POST[phone]', oneall_profile = '$_POST[_profile]', password = '" . $this->db->escape (md5 ($password)) . "', newsletter = '1', customer_group_id = '1', ip = '" . $this->db->escape ($this->request->server ['REMOTE_ADDR']) . "', status = '1', approved = '1', date_added = NOW()");
					$this->customer->login ($_POST ['email'], '', true);
				}
				// Check if user is registered already
	
				header ("Location: " . $_POST ['_go']);
				exit ();
			}
			else
				die ('No profile in return data.');
		}
	
		// Display asking form
		$data = $_POST;
		$data = array_merge ($data, $this->load->language ('module/oneall'));
		$data ['valid'] = $valid;
		$data ['lang'] = $this->language->get ('code');
		$data ['direction'] = $this->language->get ('direction');
		$data ['action'] = $this->url->link ('account/oneall/ask');
	
		if (!defined ('OC2'))
			define ('OC2', VERSION >= '2');
	
		if (OC2)
		{
				
			if (file_exists (DIR_TEMPLATE . $this->config->get ('config_template') . '/template/account/oneall_ask.tpl'))
			{
				$this->response->setOutput ($this->load->view ($this->config->get ('config_template') . '/template/account/oneall_ask.tpl', $data));
			}
			else
			{
				$this->response->setOutput ($this->load->view ('default/template/account/oneall_ask.tpl', $data));
			}
		}
		else
		{
				
			$this->data = $data;
			if (file_exists (DIR_TEMPLATE . $this->config->get ('config_template') . '/template/account/oneall_ask.tpl'))
			{
				$this->template = $this->config->get ('config_template') . '/template/account/oneall_ask.tpl';
			}
			else
			{
				$this->template = 'default/template/account/oneall_ask.tpl';
			}
			$this->response->setOutput ($this->render ());
		}
	}
	
	////////////////////////////////////////////////////////////////////////
	// Tools
	////////////////////////////////////////////////////////////////////////
	
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
	

	// Returns the user_id for a given token.
	protected function get_customer_id_for_user_token ($user_token)
	{
		// Make sure it is not empty.
		$user_token = trim ($user_token);
		if (strlen ($user_token) == 0)
		{
			return false;
		}
	
		// Read the user_id for this user_token.
		$sql = "SELECT oasl_user_id, customer_id FROM `" . DB_PREFIX . "oasl_user` WHERE user_token = '" .  $this->db->escape ($user_token) . "'";
		$result = $this->db->query ($sql)->row;
	
		// The user_token exists
		if (is_array ($result) && !empty ($result ['customer_id']))
		{
			$customer_id = intval ($result ['customer_id']);
			$oasl_user_id = intval ($result ['oasl_user_id']);
	
			// Check if the user account exists.
			$sql = "SELECT customer_id FROM `" . DB_PREFIX . "customer` WHERE customer_id = " . intval ($customer_id);
			$result = $this->db->query ($sql)->row;
	
			// The user account exists, return it's identifier.
			if (is_array ($result) && !empty ($result ['customer_id']))
			{
				return $result ['customer_id'];
			}
	
			// Delete the wrongly linked user_token.
			$sql = "DELETE FROM `" . DB_PREFIX . "oasl_user` WHERE user_token = '" . $this->db->escape ($user_token) . "'";
			$query = $this->db->query ($sql);
	
			// Delete the wrongly linked identity_token.
			$sql = "DELETE FROM `" . DB_PREFIX . "oasl_identity` WHERE oasl_user_id = " . intval ($oasl_user_id) . "";
			$query = $this->db->query ($sql);
		}
	
		// No entry found.
		return false;
	}
	
	// Get the user_token from a user_id
	public function get_user_token_for_customer_id ($customer_id)
	{
		// Read the customer_id for this user_token.
		$sql = "SELECT user_token FROM `" . DB_PREFIX . "oasl_user` WHERE customer_id = " . intval ($customer_id);
		$result = $this->db->query ($sql)->row;
	
		// The user_token exists
		if (is_array ($result) && !empty ($result ['user_token']))
		{
			return $result ['user_token'];
		}
	
		// Not found
		return false;
	}
	
	// Get the user_id for a given email address.
	protected function get_customer_id_by_email ($email)
	{
		// Read the customer_id for this email address.
		$sql = "SELECT customer_id FROM `" . DB_PREFIX . "customer` WHERE user_email  = '" . $this->db->escape ($email) . "'";
		$result = $this->db->query ($sql)->row;
	
		// We have found a customer_id.
		if (is_array ($result) && !empty ($result ['customer_id']))
		{
			return $result ['customer_id'];
		}
	
		// Not found.
		return false;
	}
	
	// Links the user/identity tokens to a customer
	public function link_tokens_to_customer_id ($customer_id, $user_token, $identity_token, $identity_provider)
	{
		// Make sure that that the user exists.
		$sql = "SELECT customer_id FROM `" . DB_PREFIX . "customer` WHERE customer_id  = " . intval ($customer_id) . "";
		$result = $this->db->query ($sql)->row;
	
		// We have found a customer_id.
		if (is_array ($result) && !empty ($result ['customer_id']))
		{
			$customer_id = $result ['customer_id'];
	
			$oasl_user_id = null;
			$oasl_identity_id = null;
	
			// Delete superfluous user_token.
			$sql = "SELECT oasl_user_id	FROM `" . DB_PREFIX . "oasl_user` WHERE customer_id = '" . intval ($customer_id) . "' AND user_token <> '" . $this->db->escape ($user_token) . "'";
			$query = $this->db->query ($sql);
			if ($query->num_rows > 0)
			{
				foreach ($query->rows as $row)
				{
					// Delete the wrongly linked user_token.
					$sql = "DELETE FROM `" . DB_PREFIX . "oasl_user` WHERE oasl_user_id = '" . $this->db->escape ($row ['oasl_user_id']) . "'";
					$this->db->query ($sql);
	
					// Delete the wrongly linked identity_token.
					$sql = "DELETE FROM `" . DB_PREFIX . "oasl_identity` WHERE oasl_user_id = '" . $this->db->escape ($row ['oasl_user_id']) . "'";
					$this->db->query ($sql);
				}
			}
	
	
			// Read the entry for the given user_token.
			$sql = "SELECT oasl_user_id, customer_id FROM `" . DB_PREFIX . "oasl_user` WHERE user_token = '" . $this->db->escape ($user_token) . "'";
			$result = $this->db->query ($sql)->row;
	
			// The user_token exists
			if (is_array ($result) && !empty ($result ['oasl_user_id']))
			{
				$oasl_user_id = $result ['oasl_user_id'];
			}
	
			// The user_token either does not exist or has been reset.
			if (empty ($oasl_user_id))
			{
				$sql = "INSERT INTO `" . DB_PREFIX . "oasl_user` SET customer_id='".intval($customer_id)."', user_token='".$this->db->escape($user_token)."', date_added=NOW()";
				$this->db->query ($sql);
	
				// Identifier of the newly created user_token entry.
				$oasl_user_id = $this->db->getLastId();
			}
	
			// Read the entry for the given identity_token.
			$sql = "SELECT oasl_identity_id, oasl_user_id, identity_token FROM `" . DB_PREFIX . "oasl_identity` WHERE identity_token = '" . $this->db->escape ($identity_token) . "'";
			$result = $this->db->query ($sql)->row;
	
			// The identity_token exists
			if (is_array ($result) && !empty ($result ['oasl_identity_id']))
			{
				$oasl_identity_id = $result ['oasl_identity_id'];
	
				// The identity_token is linked to another user_token.
				if (!empty ($result ['oasl_user_id']) && $result ['oasl_user_id'] != $oasl_user_id)
				{
					// Delete the wrongly linked identity_token.
					$sql = "DELETE FROM `" . DB_PREFIX . "oasl_identity` WHERE oasl_identity_id = '" . intval ($oasl_identity_id)."'";
					$this->db->query ($sql);
	
					// Reset the identifier
					$oasl_identity_id = null;
				}
			}
	
			// The identity_token either does not exist or has been reset.
			if (empty ($oasl_identity_id))
			{
				// Add new link.
				$sql = "INSERT INTO `" . DB_PREFIX . "oasl_identity` SET oasl_user_id='".intval ($oasl_user_id)."', identity_token = '".$this->db->escape ($identity_token) ."', identity_provider = '".$this->db->escape ($identity_provider) ."', 'num_logins' => 1, 'date_added' => NOW (), 'date_updated' => NOW ()";
				$query = $db->sql_query ($sql);
	
				// Identifier of the newly created identity_token entry.
				$oasl_identity_id = $this->db->getLastId();
			}
	
			// Done.
			return true;
		}
	
		// An error occured.
		return false;
	}
	
	// Build User Agent
	private static function get_user_agent ()
	{
		// System Versions
		$social_login = 'SocialLogin/1.0';
		$opencart = 'OpenCart' . (defined ('VERSION') ? ('/' . substr (VERSION, 0, 3)) : '');
	
		// Build User Agent
		return ($social_login . ' ' . $opencart . ' (+http://www.oneall.com/)');
	}
	
	// Generates a random hash of the given length
	private static function generate_hash ($length)
	{
		$hash = '';
	
		for($i = 0; $i < $length; $i ++)
		{
		do
			{
			$char = chr (mt_rand (48, 122));
			}
			while ( !preg_match ('/[a-zA-Z0-9]/', $char) );
			$hash .= $char;
		}
	
			// Done
			return $hash;
	}
	
	
}
?>