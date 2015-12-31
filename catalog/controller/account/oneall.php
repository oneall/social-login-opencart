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
class ControllerAccountOneall extends Controller
{
	// Build User Agent
	public static function get_user_agent ()
	{
		// System Versions
		$social_login = 'SocialLogin/1.0';
		$opencart = 'OpenCart'.(defined ('VERSION') ? ('/'.substr (VERSION, 0, 3)) : '');
				
		// Build User Agent
		return ($social_login.' '.$opencart.' (+http://www.oneall.com/)');
	}
		
	// Callback Handler
	public function index ()
	{
		
		// ONEALL Callback handler
		$error = '';
		
		// Check if we have received a connection_token
		if (!empty ($_POST ['connection_token']))
		{
			// Get connection_token
			$token = $_POST ['connection_token'];
			
			// DB
			// require_once('../../../config.php');
			// require_once('../../../system/library/db.php');
			// require_once(DIR_SYSTEM . 'startup.php');
			// $db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
			
			// Your Site Settings
			$site_subdomain = $this->config->get ('oneall_subdomain');
			$site_public_key = $this->config->get ('oneall_public');
			$site_private_key = $this->config->get ('oneall_private');
			
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
			curl_setopt ($curl, CURLOPT_USERAGENT, self::get_user_agent());
			
			// Send request
			$result_json = curl_exec ($curl);
			
			// Error
			if ($result_json === false)
			{
				// You may want to implement your custom error handling here
				$error = 'Curl error: ' . curl_error ($curl) . '<br />' . 'Curl info: ' . curl_getinfo ($curl) . '';
				curl_close ($curl);
			} // Success
			else
			{
				// Close connection
				curl_close ($curl);
				
				// Decode
				$json = json_decode ($result_json);
				
				// Extract data
				$data = $json->response->result->data;
				
				// print_r($data);
				
				// Check for plugin
				if ($data->plugin->key == 'social_login')
				{
					// Operation successfull
					if ($data->plugin->data->status == 'success')
					{
						$UserToken = $data->user->user_token;
						$lastname = '';
						$firstname = '';
						
						if (isset ($data->user->identity->name))
						{
							if (isset($data->user->identity->name->givenName))
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
						
						$q = $this->db->query ("SHOW COLUMNS FROM " . DB_PREFIX . "customer LIKE 'oneall_profile'")->row;
						if (!$q)
						{
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
							if ($q['firstname'])
								$firstname = $q['firstname'];
							if ($q['lastname'])
								$lastname = $q['lastname'];
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
					// Create Account
					$passwords = array(
						"qwerty",
						"letmein",
						"test",
						"love",
						"hello",
						"monkey",
						"dragon",
						"iloveyou",
						"shadow",
						"sunshine",
						"master",
						"computer",
						"princess",
						"tiger",
						"football",
						"angel",
						"whatever",
						"freedom",
						"soccer",
						"superman",
						"michael",
						"cheese",
						"internet",
						"blessed",
						"baseball",
						"starwars",
						"purple",
						"jordan",
						"faith",
						"summer",
						"ashley",
						"buster",
						"heaven",
						"pepper",
						"hunter",
						"lovely",
						"angels",
						"charlie",
						"daniel",
						"jennifer",
						"single",
						"happy",
						"matrix",
						"amanda",
						"nothing",
						"ginger",
						"mother",
						"snoopy",
						"jessica",
						"welcome",
						"pokemon",
						"mustang",
						"jasmine",
						"orange",
						"apple",
						"michelle",
						"peace",
						"secret",
						"grace",
						"nicole",
						"muffin",
						"gateway",
						"blessing",
						"canada",
						"silver",
						"forever",
						"rainbow",
						"guitar",
						"peanut",
						"batman",
						"cookie",
						"bailey",
						"mickey",
						"dakota",
						"compaq",
						"diamond",
						"taylor",
						"forum",
						"cool",
						"flower",
						"scooter",
						"banana",
						"victory",
						"london",
						"startrek",
						"winner",
						"maggie",
						"trinity",
						"online",
						"chicken",
						"junior",
						"sparky",
						"merlin",
						"google",
						"friends",
						"hope",
						"nintendo",
						"harley",
						"smokey",
						"lucky",
						"digital",
						"thunder",
						"spirit",
						"enter",
						"corvette",
						"hockey",
						"power",
						"viper",
						"genesis",
						"knight",
						"creative",
						"adidas",
						"slayer",
						"wisdom",
						"praise",
						"dallas",
						"green",
						"maverick",
						"mylove",
						"friend",
						"destiny",
						"bubbles",
						"cocacola",
						"loving",
						"emmanuel",
						"scooby",
						"maxwell",
						"baby",
						"prince",
						"chelsea",
						"dexter",
						"kitten",
						"stella",
						"prayer",
						"hotdog" 
					);
					$password = $passwords [array_rand ($passwords)];
					
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
}

?>
