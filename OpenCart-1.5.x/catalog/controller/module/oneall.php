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

//////////////////////////////////////////////////////////////////////
// Widget Display
//////////////////////////////////////////////////////////////////////
class ControllerModuleOneall extends Controller
{
	// Errors
	protected $error;
	
	// Custom Registration Form
	public function register()
	{
		// User is already logged in
		if ($this->customer->isLogged())
		{
			$this->redirect($this->url->link('account/account', '', 'SSL'));
		}
		
		// Retrieve User Data
		$user_data = (isset ($this->session->data['oneall_user_data']) ?  @unserialize ($this->session->data['oneall_user_data']) : array());

		// Make sure we have the correct data
		if ( ! is_array ($user_data) || empty ($user_data['user_token']))
		{
			$this->redirect($this->url->link('common/home', '', 'SSL'));
		}	
		
		// Load Language
		$data = array_merge ($this->load->language('account/register'), $this->load->language('module/oneall'));
		
		// Replace Custom Variables
		$data['oa_heading_title'] = sprintf ($data['oa_heading_title'], $user_data['identity_provider']);
			
		// Set Document Title
		$this->document->setTitle($data['heading_title']);
	
		// Add Scripts
    	$this->document->addScript('catalog/view/javascript/jquery/colorbox/jquery.colorbox-min.js');
        $this->document->addStyle('catalog/view/javascript/jquery/colorbox/colorbox.css');		
	
		// Load Model
		$this->load->model('account/customer');
	
		//////////////////////////////////////////////////////////////////////////////////
		// Post Form
		//////////////////////////////////////////////////////////////////////////////////
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate())
		{
			// Read Customer Data
			$customer_data = $this->request->post;
			
			// Add Password
			$customer_data['password'] = $this->generate_hash (8);
			
			// We didn't ask for the address
			if ($this->config->get ('oneall_ask_address') == 0)
			{
				$customer_data['company'] = '';
				$customer_data['companyid'] = ''	;			
				$customer_data['tax_id'] = '';				
				$customer_data['address_1'] = '';
				$customer_data['address_2'] = '';
				$customer_data['city'] = '';
				$customer_data['postcode'] = '';
				$customer_data['country_id'] = 0;
				$customer_data['zone_id'] = 0;		
				
			}
			
			// Create Customer
			$this->model_account_customer->addCustomer($customer_data);
			$customer_id = $this->get_customer_id_by_email ($customer_data ['email']);
	
			// Link the customer to this social network.
			if ($this->link_tokens_to_customer_id ($customer_id, $user_data ['user_token'], $user_data ['identity_token'], $user_data ['identity_provider']) !== false)
			{
				// Login
				if ($this->login_customer($customer_id))
				{				
					// Update statistics
					$this->count_login_identity_token ($user_data ['identity_token']);
					
					// Remove Session Data
					unset ($this->session->data['oneall_user_data']);
					
					// Redirect Target
					if (isset($this->request->post['oa_redirect']) && strlen (trim ($this->request->post['oa_redirect'])) > 0)
					{
						$redirect_to = trim ($this->request->post['oa_redirect']);
					}
					else
					{
						$redirect_to = 'account/success';
					}
					
					// Redirect User
					$this->redirect($this->url->link($redirect_to, '', 'SSL'));
				}
			}
		}
		
		//////////////////////////////////////////////////////////////////////////////////
		// Display Form
		//////////////////////////////////////////////////////////////////////////////////
	
		// BreadCrums
		$data['breadcrumbs'] = array(
			array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home'),
				'separator' => false
			), 
			array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', 'SSL'),
				'separator' => $this->language->get('text_separator')				
			),
			array(
				'text' => $this->language->get('oa_social_login'),
				'href' => $this->url->link('module/oneall/register', '', 'SSL'),
				'separator' => $this->language->get('text_separator')				
			)
		);
	
		// Errors	
		$data['error_warning'] = (isset($this->error['warning']) ? $this->error['warning'] : '');
		$data['error_firstname'] = (isset($this->error['firstname']) ? $this->error['firstname'] : '');
		$data['error_lastname'] = (isset($this->error['lastname']) ? $this->error['lastname'] : '');
		$data['error_email'] = (isset($this->error['email']) ? $this->error['email'] : '');
		$data['error_telephone'] = (isset($this->error['telephone']) ? $this->error['telephone'] : '');
		$data['error_address_1'] = (isset($this->error['address_1']) ? $this->error['address_1'] : '');
		$data['error_city'] = (isset($this->error['city']) ? $this->error['city'] : '');
		$data['error_postcode'] = (isset($this->error['postcode']) ? $this->error['postcode'] : '');
		$data['error_country'] = (isset($this->error['country']) ? $this->error['country'] : '');
		$data['error_zone'] = (isset($this->error['zone']) ? $this->error['zone'] : '');
		$data['error_company_id'] = (isset($this->error['company_id']) ? $this->error['company_id'] : '');
		$data['error_tax_id'] = (isset($this->error['tax_id']) ? $this->error['tax_id'] : '');
		$data['error_confirm'] = (isset($this->error['confirm']) ? $this->error['confirm'] : '');

		
		
	
		// Form Action	
		$data['action'] = $this->url->link('module/oneall/register', '', 'SSL');
		$data['oneall_ask_address'] = $this->config->get ('oneall_ask_address');
	
		// Customer Groups
		$data['customer_groups'] = array();
	
		if (is_array($this->config->get('config_customer_group_display')))
		{
			$this->load->model('account/customer_group');
	
			$customer_groups = $this->model_account_customer_group->getCustomerGroups();
	
			foreach ($customer_groups as $customer_group)
			{
				if (in_array($customer_group['customer_group_id'], $this->config->get('config_customer_group_display')))
				{
					$data['customer_groups'][] = $customer_group;
				}
			}
		}
	
		if (isset($this->request->post['customer_group_id']))
		{
			$data['customer_group_id'] = $this->request->post['customer_group_id'];
		} 
		else
		{
			$data['customer_group_id'] = $this->config->get('config_customer_group_id');
		}
	
		// First Name
		if (isset($this->request->post['firstname']))
		{
			$data['firstname'] = $this->request->post['firstname'];
		} 
		else
		{
			// Restore from social network profile
			if ( ! empty ($user_data ['user_first_name']))
			{
				$data['firstname'] = $user_data ['user_first_name'];
			}
			else
			{
				$data['firstname'] = '';
			}
		}
	
		// Last Name
		if (isset($this->request->post['lastname']))
		{
			$data['lastname'] = $this->request->post['lastname'];
		}
		else
		{
			// Restore from social network profile
			if ( ! empty ($user_data ['user_last_name']))
			{
				$data['lastname'] = $user_data ['user_last_name'];
			}
			else
			{
				$data['lastname'] = '';
			}
		}
	
		// Email
		if (isset($this->request->post['email']))
		{
			$data['email'] = $this->request->post['email'];
		}
		else
		{
			// Restore from social network profile
			if ( ! empty ($user_data ['user_email']))
			{
				$data['email'] = $user_data ['user_email'];
			}
			else
			{
				$data['email'] = '';
			}
		}
	
		// Telephone Number
		if (isset($this->request->post['telephone']))
		{
			$data['telephone'] = $this->request->post['telephone'];
		}
		else
		{
			$data['telephone'] = '';
		}
	
		// Fax Number
		if (isset($this->request->post['fax']))
		{
			$data['fax'] = $this->request->post['fax'];
		}
		else
		{
			$data['fax'] = '';
		}
	
		// Company Name
		if (isset($this->request->post['company']))
		{
			$data['company'] = $this->request->post['company'];
		}
		else
		{
			$data['company'] = '';
		}
	
		// Adresse Line 1
		if (isset($this->request->post['address_1']))
		{
			$data['address_1'] = $this->request->post['address_1'];
		}
		else
		{
			$data['address_1'] = '';
		}
		
		// Adresse Line 2
		if (isset($this->request->post['address_2']))
		{
			$data['address_2'] = $this->request->post['address_2'];
		}
		else
		{
			$data['address_2'] = '';
		}
		
		// Company ID
		if (isset($this->request->post['company_id']))
		{
			$data['company_id'] = $this->request->post['company_id'];
		} 
		else
		{
			$data['company_id'] = '';
		}
		
		// Tax ID
		if (isset($this->request->post['tax_id']))
		{
			$data['tax_id'] = $this->request->post['tax_id'];
		} 
		else
		{
			$data['tax_id'] = '';
		}
				
		// Postal Code
		if (isset($this->request->post['postcode']))
		{
			$data['postcode'] = $this->request->post['postcode'];
		} 
		elseif (isset($this->session->data['shipping_address']['postcode']))
		{
			$data['postcode'] = $this->session->data['shipping_address']['postcode'];
		}
		else
		{
			$data['postcode'] = '';
		}
	
		// City
		if (isset($this->request->post['city']))
		{
			$data['city'] = $this->request->post['city'];
		}
		else
		{
			$data['city'] = '';
		}
	
		// Country
		if (isset($this->request->post['country_id']))
		{
			$data['country_id'] = $this->request->post['country_id'];
		} 
		elseif (isset($this->session->data['shipping_address']['country_id']))
		{
			$data['country_id'] = $this->session->data['shipping_address']['country_id'];
		}
		else
		{
			$data['country_id'] = $this->config->get('config_country_id');
		}
	
		// Zone
		if (isset($this->request->post['zone_id']))
		{
			$data['zone_id'] = $this->request->post['zone_id'];
		}
		elseif (isset($this->session->data['shipping_address']['zone_id']))
		{
			$data['zone_id'] = $this->session->data['shipping_address']['zone_id'];
		}
		else
		{
			$data['zone_id'] = '';
		}
		
		// Redirect
		if (isset($this->request->post['oa_redirect']))
		{
			$data['oa_redirect'] = $this->request->post['oa_redirect'];
		}
		else
		{		
			if (isset ($this->request->get['oa_redirect']))
			{
				$data['oa_redirect'] = $this->request->get['oa_redirect'];
			}
			else
			{
				$data['oa_redirect'] = '';
			}
		}
	
		// Country List
		$this->load->model('localisation/country');	
		$data['countries'] = $this->model_localisation_country->getCountries();
	
	
		// Newsletter	
		if (isset($this->request->post['newsletter']))
		{
			$data['newsletter'] = $this->request->post['newsletter'];
		}
		else
		{
			$data['newsletter'] = '';
		}
	
		// Agree		
		if ($this->config->get('config_account_id'))
		{
			$this->load->model('catalog/information');		
			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
		
			if ($information_info)
			{
				$data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/info', 'information_id=' . $this->config->get('config_account_id'), 'SSL'), $information_info['title'], $information_info['title']);
			}
			else
			{
				$data['text_agree'] = '';
			}
		}
		else
		{
			$data['text_agree'] = '';
		}
		
		if (isset($this->request->post['agree']))
		{
			$data['agree'] = $this->request->post['agree'];
		}
		else
		{
			$data['agree'] = false;
		}
		
		// Display Template
		$template = '/template/module/oneall_register.tpl';
		$template_folder = $this->config->get ('config_template');
		
		// Get Template Folder
		$template_folder = (file_exists (DIR_TEMPLATE . $template_folder . $template) ? $template_folder : 'default');
		
		// Display
		$this->data = $data;
		$this->template = $template_folder . $template;
		$this->children = array('common/column_left', 'common/column_right', 'common/content_top', 'common/content_bottom', 'common/footer', 'common/header');
		$this->response->setOutput($this->render());
	}
	
	// Validate Account Creation	
	private function validate()
	{
		if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32))
		{
			$this->error['firstname'] = $this->language->get('error_firstname');
		}
	
		if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32))
		{
			$this->error['lastname'] = $this->language->get('error_lastname');
		}
	
		if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['email']))
		{
			$this->error['email'] = $this->language->get('error_email');
		}
	
		if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email']))
		{
			$this->error['warning'] = $this->language->get('error_exists');
		}
	
		if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32))
		{
			$this->error['telephone'] = $this->language->get('error_telephone');
		}
	
		// Check Address?
		if ($this->config->get ('oneall_ask_address') <> 0)
		{		
			if ((utf8_strlen(trim($this->request->post['address_1'])) < 3) || (utf8_strlen(trim($this->request->post['address_1'])) > 128))
			{
				$this->error['address_1'] = $this->language->get('error_address_1');
			}
	
			if ((utf8_strlen(trim($this->request->post['city'])) < 2) || (utf8_strlen(trim($this->request->post['city'])) > 128))
			{
				$this->error['city'] = $this->language->get('error_city');
			}
	
			$this->load->model('localisation/country');
	
			$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);
	
			if ($country_info && $country_info['postcode_required'] && (utf8_strlen(trim($this->request->post['postcode'])) < 2 || utf8_strlen(trim($this->request->post['postcode'])) > 10))
			{
				$this->error['postcode'] = $this->language->get('error_postcode');
			}
	
			if ($this->request->post['country_id'] == '')
			{
				$this->error['country'] = $this->language->get('error_country');
			}
	
			if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '')
			{
				$this->error['zone'] = $this->language->get('error_zone');
			}
		}
		
		// Privacy Policy
		if ($this->config->get('config_account_id'))
		{
			$this->load->model('catalog/information');
		
			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
		
			if ($information_info && !isset($this->request->post['agree']))
			{
				$this->error['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
			}
		}
		
	
		// Done
		return !$this->error;
	}
	
	
	// Social Login Administration
	public function index ()
	{		
		// Callback Handler
		$this->callback_handler();
		
		// Load Language
		$this->load->language ('module/oneall');
		
		// User Settings
		$data ['oasl_user_is_logged'] = $this->customer->isLogged ();
		
		// Plugin Settings
		$data ['oasl_heading_title'] = trim ($this->language->get ('oa_social_login'));
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
			$this->document->addScript ($this->get_request_protocol () . '://' . $data ['oasl_subdomain'] . '.api.oneall.com/socialize/library.js' . ($data ['oasl_store_lang'] ? ('?lang=' . $data ['oasl_lib_lang']) : ''));
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
		$oasl_callback_uri = HTTPS_SERVER . 'index.php?route=module/oneall';
		
		// Redirection
		if ( ! empty ($this->request->get['route']))
		{
			if (stripos ($this->request->get['route'], 'account') === false)
			{
				$oasl_callback_uri .= '&oa_redirect='.$this->request->get['route'];
			}
		}
		
		$data ['oasl_callback_uri'] = $oasl_callback_uri;
		
		// Display Wiget
		return $this->display_widget_template ($data);
	}
	
	// Display Widget
	private function display_widget_template ($data)
	{
		// Widget Template
		$template = '/template/module/oneall.tpl';
		$template_folder = $this->config->get ('config_template');

		// Get Template Folder
		$template_folder = (file_exists (DIR_TEMPLATE . $template_folder . $template) ? $template_folder : 'default');
		
		// Display
		$this->data = $data;
		$this->template = ($template_folder . $template);
		$this->render ();	
	}
	

	////////////////////////////////////////////////////////////////////////
	// Tools
	////////////////////////////////////////////////////////////////////////
	
	// Callback Handler
	public function callback_handler ()
	{
		// OneAll Callback handler
		$error = '';

		// Check if we have received a connection_token
		if ( isset ($this->request->post) && ! empty ($this->request->post['connection_token']))
		{
			// Get connection_token
			$token = trim ($this->request->post['connection_token']);
				
			// OneAll Site Settings
			$api_subdomain = $this->config->get ('oneall_subdomain');
	
			// Without the API Credentials it does not work
			if (!empty ($api_subdomain))
			{
				// API Connection Settings.
				$api_connection_handler = ($this->config->get ('oneall_api_handler') == 'fso' ? 'fsockopen' : 'curl');
				$api_connection_protocol = ($this->config->get ('oneall_api_port') == '80' ? 'http' : 'https');
				
				// API Credentials.
				$api_credentials = array();
				$api_credentials ['api_key'] = $this->config->get ('oneall_public');
				$api_credentials ['api_secret'] = $this->config->get ('oneall_private');								
		
				// Connection Resource
				// http://docs.oneall.com/api/resources/connections/read-connection-details/
				$api_connection_url = $api_connection_protocol . '://' . $api_subdomain . '.api.oneall.com/connections/' . $token . '.json';
	
				// Make Request.
				$result = $this->do_api_request ($api_connection_handler, $api_connection_url, $api_credentials);
					
				// Parse result
				if (is_object ($result) && property_exists ($result, 'http_code') && $result->http_code == 200)
				{
					// Customer to login
					$customer_id = null;
					
					// Extract data
					if (($user_data = $this->extract_social_network_profile ($result)) !== false)
					{			
						// Wrapper for previous plugin data format			
						if ( ! empty ($user_data['profile_url']))
						{
							$user_key = $user_data['profile_url'];
						}
						elseif ( ! empty ($user_data['user_id']))
						{
							$user_key = $user_data['user_id'];
						}
						else
						{
							$user_key = null;
						}
						
						// Key Found
						if ( ! empty ($user_key))		
						{		
							// Check if we have data from an older version of the plugin
							$column = $this->db->query ("SHOW COLUMNS FROM " . DB_PREFIX . "customer LIKE 'oneall_profile'")->row;
							if ($column)
							{							
								// Try to retrieve the user
								$customer = $this->db->query ("SELECT customer_id FROM `" . DB_PREFIX . "customer` WHERE oneall_profile='" . $this->db->escape ($user_key) . "'")->row;
								if ($customer)
								{
									$customer_id = $customer->customer_id;
									
									// Add new data format
									if ($this->link_tokens_to_customer_id ($customer_id, $user_data ['user_token'], $user_data ['identity_token'], $user_data ['identity_provider']) !== false)
									{
										// Remove old data
										$this->db->query ("UPDATE `" . DB_PREFIX . "customer` SET oneall_profile='' WHERE customer_id='".intval ($customer_id)."' LIMIT 1");
									}
								}
							}
						}					
								
						// Read Customer By Token
						if (!is_numeric ($customer_id))
						{	
							// Get user_id by token.
							$customer_id_tmp = $this->get_customer_id_for_user_token ($user_data ['user_token']);
							
							// We already have a customer for this token.
							if (is_numeric ($customer_id_tmp))
							{
								// Process this customer.
								$customer_id = $customer_id_tmp;				
							}
						}
						
						// Read Customer By Link
						if (!is_numeric ($customer_id))
						{		
							// Automatic Linking
							$setting_auto_link = $this->config->get ('oneall_auto_link');
								
							// Make sure that account linking is enabled.
							if ( ! empty ($setting_auto_link))
							{
								// Make sure that the email has been verified.
								if (!empty ($user_data ['user_email']) && isset ($user_data ['user_email_is_verified']) && $user_data ['user_email_is_verified'] === true)
								{
									// Read existing user
									$customer_id_tmp = $this->get_customer_id_by_email ($user_data ['user_email']);
											
									// Existing user found
									if (is_numeric ($customer_id_tmp))
									{
										// Link the customer to this social network.
										if ($this->link_tokens_to_customer_id ($customer_id_tmp, $user_data ['user_token'], $user_data ['identity_token'], $user_data ['identity_provider']) !== false)
										{
											$customer_id = $customer_id_tmp;
										}
									}
								}
							}
						}
								
								// Create Customer
						if (!is_numeric ($customer_id))
						{
							// Automatic Account Creation
							$setting_auto_account = $this->config->get ('oneall_auto_account');
								
							// Display the registration form?
							$display_registration_form = false;
									
							// Manual Account Creation
							if (empty ($setting_auto_account))
							{
								$display_registration_form = true;
							}
							// Require Email
							elseif ($setting_auto_account == 1)
							{
								if (empty ($user_data ['user_email']))
								{
									$display_registration_form = true;
								}									
							}
									
							// Display Form?
							if ($display_registration_form)
							{
								// Add Data
								$this->session->data['oneall_user_data'] = serialize ($user_data);
																		
								// Custom Redirection
								if (isset ($this->request->get['oa_redirect']))
								{
									$redirect_to = '&oa_redirect=' . $this->request->get['oa_redirect'];
								}
								// Default Redirection
								else
								{
									$redirect_to = '';
								}
													
								// Redirect
								$this->redirect($this->url->link('module/oneall/register' . $redirect_to, '', 'SSL'));
							}
							// Create Customer
							else
							{												
								// Create Customer
								if (($customer_id = $this->create_customer ($user_data)) !== false)
								{									
									// Link the customer to his social network account.
									$this->link_tokens_to_customer_id ($customer_id, $user_data ['user_token'], $user_data ['identity_token'], $user_data ['identity_provider']);
								
									// Login Customer
									if ($this->login_customer ($customer_id))
									{										
										// Update statistics
										$this->count_login_identity_token ($user_data ['identity_token']);
									
										// Redirect to Account
										$this->redirect($this->url->link('account/account', '', 'SSL'));
									}
								}
							}					
						}
						
						
						// User Found
						if (is_numeric ($customer_id))
						{
							// Everything OK
							if ($this->login_customer ($customer_id))
							{			
								// Update statistics
								$this->count_login_identity_token ($user_data ['identity_token']);
								
								// Custom Redirection
								if (isset ($this->request->get['oa_redirect']))
								{
									$redirect_to = $this->request->get['oa_redirect'];
								}
								// Default Redirection
								else
								{
									$redirect_to = 'account/account';
								}
								
								// Redirect
								$this->redirect($this->url->link($redirect_to, '', 'SSL'));
							}
						}	
					}
				}
			}
		}	
	}
	
	// Create customser
	protected function create_customer($data)
	{
		// Load Models
		$this->load->model('account/customer');
		$this->load->model('account/customer_group');
		
		// Read Group
		$customer_group_id = $this->config->get('config_customer_group_id');
                
		// Add to newsletter?
		$newletter = 1;
		
		// Email Address
		if (empty ($data ['user_email']) || $this->get_customer_id_by_email ($data ['user_email']) !== false)
		{
			$data ['user_email'] = $this->generate_random_email();
			$newletter = 0;
		}
		
		// Customer Data
		$customer_data = array();
		$customer_data['firstname'] = $data ['user_first_name'];
		$customer_data['lastname'] = $data ['user_last_name'];
		$customer_data['email'] = $data ['user_email'];
		$customer_data['telephone'] = $data ['user_phone_number'];
		$customer_data['fax'] = '';
		$customer_data['password'] = $this->generate_hash (8);
		$customer_data['newletter'] = $newletter;
		$customer_data['customer_group_id'] = (int) $customer_group_id;		
		$customer_data['company'] = '';
		$customer_data['company_id'] = '';
		$customer_data['tax_id'] = '';
		$customer_data['address_1'] = '';
		$customer_data['address_2'] = '';
		$customer_data['city'] = '';
		$customer_data['postcode'] = '';
		$customer_data['country_id'] = 0;
		$customer_data['zone_id'] = 0;

		// Add Customer
		$this->model_account_customer->addCustomer($customer_data);
		
		// Done
		return $this->get_customer_id_by_email ($data ['user_email']);
	}
	
	
	// Login a customer
	protected function login_customer($customer_id)
	{
		// Retrieve the customer
		$result = $this->db->query ("SELECT email FROM `" . DB_PREFIX . "customer` WHERE customer_id = '" . intval ($customer_id) . "'")->row;
		if (is_array ($result) && ! empty ($result['email']))
		{
			// Login		
			if ($this->customer->login($result['email'], '', true))
			{			
				// Default Addresses
				$this->load->model('account/address');
				$address_info = $this->model_account_address->getAddress($this->customer->getAddressId());
				
				if ($address_info)
				{
					if ($this->config->get('config_tax_customer') == 'shipping')
					{
						$this->session->data['shipping_country_id'] = $address_info['country_id'];
						$this->session->data['shipping_zone_id'] = $address_info['zone_id'];
						$this->session->data['shipping_postcode'] = $address_info['postcode'];
					}
				
					if ($this->config->get('config_tax_customer') == 'payment')
					{
						$this->session->data['payment_country_id'] = $address_info['country_id'];
						$this->session->data['payment_zone_id'] = $address_info['zone_id'];
					}
				} 
				else
				{
					unset($this->session->data['shipping_country_id']);
					unset($this->session->data['shipping_zone_id']);
					unset($this->session->data['shipping_postcode']);
					unset($this->session->data['payment_country_id']);
					unset($this->session->data['payment_zone_id']);
				}
						
				// Logged in
				return true;
			}
		}
		
		// Not logged in
		return false;		
	}
	
	// Generates a random email address
	protected function generate_random_email ()
	{
		do
		{
			$email = $this->generate_hash (10) . "@example.com";
		}
		while ( $this->get_customer_id_by_email ($email) !== false );
	
		// Done
		return $email;
	}
	

	////////////////////////////////////////////////////////////////////////
	// Tools
	////////////////////////////////////////////////////////////////////////
	
	// Return the protocol of the request.
	public function get_request_protocol ()
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
		$sql = "SELECT customer_id FROM `" . DB_PREFIX . "customer` WHERE email  = '" . $this->db->escape ($email) . "'";
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
				$sql = "INSERT INTO `" . DB_PREFIX . "oasl_identity` SET oasl_user_id='".intval ($oasl_user_id)."', identity_token = '".$this->db->escape ($identity_token) ."', identity_provider = '".$this->db->escape ($identity_provider) ."', num_logins=1, date_added=NOW(), date_updated=NOW()";
				$this->db->query ($sql);
	
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
	private function get_user_agent ()
	{
		// System Versions
		$social_login = 'SocialLogin/1.0';
		$opencart = 'OpenCart' . (defined ('VERSION') ? ('/' . substr (VERSION, 0, 3)) : '1.5.x');
	
		// Build User Agent
		return ($social_login . ' ' . $opencart . ' (+http://www.oneall.com/)');
	}
	
	// Generates a random hash of the given length
	protected function generate_hash ($length)
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
	
	/////////////////////////////////////////////////////////////////////////////////////////////
	// API
	/////////////////////////////////////////////////////////////////////////////////////////////
	
	// Sends an API request by using the given handler.
	public function do_api_request ($handler, $url, $options = array(), $timeout = 30)
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
	
	/**
	 * Sends a CURL request.
	 */
	public function curl_request ($url, $options = array(), $timeout = 30, $num_redirects = 0)
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
	
	
	/**
	 * Counts a login for the identity token
	 */
	public function count_login_identity_token ($identity_token)
	{
		$sql = "UPDATE `" . DB_PREFIX . "oasl_identity` SET num_logins=num_logins+1, date_updated=NOW() WHERE identity_token = '" .  $this->db->escape ($identity_token) . "' LIMIT 1";
		$query =$this->db->query ($sql);
	}
	
	/**
	 * Extracts the social network data from a result-set returned by the OneAll API.
	 */
	public function extract_social_network_profile ($reply)
	{
		// Check API result.
		if (is_object ($reply) && property_exists ($reply, 'http_code') && $reply->http_code == 200 && property_exists ($reply, 'http_data'))
		{
			// Decode the social network profile Data.
			$social_data = json_decode ($reply->http_data);
				
			// Make sur that the data has beeen decoded properly
			if (is_object ($social_data))
			{
				// Provider may report an error inside message:
				if (!empty ($social_data->response->result->status->flag) && $social_data->response->result->status->code >= 400)
				{
					return false;
				}
	
				// Container for user data
				$data = array();
	
				// Parse plugin data.
				if (isset ($social_data->response->result->data->plugin))
				{
					// Plugin.
					$plugin = $social_data->response->result->data->plugin;
						
					// Add plugin data.
					$data ['plugin_key'] = $plugin->key;
					$data ['plugin_action'] = (isset ($plugin->data->action) ? $plugin->data->action : null);
					$data ['plugin_operation'] = (isset ($plugin->data->operation) ? $plugin->data->operation : null);
					$data ['plugin_reason'] = (isset ($plugin->data->reason) ? $plugin->data->reason : null);
					$data ['plugin_status'] = (isset ($plugin->data->status) ? $plugin->data->status : null);
				}
	
				// Do we have a user?
				if (isset ($social_data->response->result->data->user) && is_object ($social_data->response->result->data->user))
				{
					// User.
					$user = $social_data->response->result->data->user;
						
					// Add user data.
					$data ['user_token'] = $user->user_token;
						
					// Do we have an identity ?
					if (isset ($user->identity) && is_object ($user->identity))
					{
						// Identity.
						$identity = $user->identity;
	
						// Add identity data.
						$data ['identity_token'] = $identity->identity_token;
						$data ['identity_provider'] = !empty ($identity->source->name) ? $identity->source->name : '';
	
						$data ['user_id'] = !empty ($identity->id) ? $identity->id : '';
						$data ['user_first_name'] = !empty ($identity->name->givenName) ? $identity->name->givenName : '';
						$data ['user_last_name'] = !empty ($identity->name->familyName) ? $identity->name->familyName : '';
						$data ['user_formatted_name'] = !empty ($identity->name->formatted) ? $identity->name->formatted : '';
						$data ['user_location'] = !empty ($identity->currentLocation) ? $identity->currentLocation : '';
						$data ['user_constructed_name'] = trim ($data ['user_first_name'] . ' ' . $data ['user_last_name']);
						$data ['user_picture'] = !empty ($identity->pictureUrl) ? $identity->pictureUrl : '';
						$data ['user_thumbnail'] = !empty ($identity->thumbnailUrl) ? $identity->thumbnailUrl : '';
						$data ['user_current_location'] = !empty ($identity->currentLocation) ? $identity->currentLocation : '';
						$data ['user_about_me'] = !empty ($identity->aboutMe) ? $identity->aboutMe : '';
						$data ['user_note'] = !empty ($identity->note) ? $identity->note : '';
	
						// Birthdate - MM/DD/YYYY
						if (!empty ($identity->birthday) && preg_match ('/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/', $identity->birthday, $matches))
						{
							$data ['user_birthdate'] = str_pad ($matches [2], 2, '0', STR_PAD_LEFT);
							$data ['user_birthdate'] .= '/' . str_pad ($matches [1], 2, '0', STR_PAD_LEFT);
							$data ['user_birthdate'] .= '/' . str_pad ($matches [3], 4, '0', STR_PAD_LEFT);
						}
						else
						{
							$data ['user_birthdate'] = '';
						}
	
						// Fullname.
						if (!empty ($identity->name->formatted))
						{
							$data ['user_full_name'] = $identity->name->formatted;
						}
						elseif (!empty ($identity->name->displayName))
						{
							$data ['user_full_name'] = $identity->name->displayName;
						}
						else
						{
							$data ['user_full_name'] = $data ['user_constructed_name'];
						}
	
						// Preferred Username.
						if (!empty ($identity->preferredUsername))
						{
							$data ['user_login'] = $identity->preferredUsername;
						}
						elseif (!empty ($identity->displayName))
						{
							$data ['user_login'] = $identity->displayName;
						}
						else
						{
							$data ['user_login'] = $data ['user_full_name'];
						}			
						$data ['user_login'] = trim ($data ['user_login']);
						
						// Profile URL
						$data ['profile_url'] = (isset ($identity->profileUrl) ? $identity->profileUrl : null);
						
						// Website/Homepage.
						$data ['user_website'] = '';
						if (!empty ($identity->profileUrl))
						{
							$data ['user_website'] = $identity->profileUrl;
						}
						elseif (!empty ($identity->urls [0]->value))
						{
							$data ['user_website'] = $identity->urls [0]->value;
						}
	
						// Gender.
						$data ['user_gender'] = '';
						if (!empty ($identity->gender))
						{
							switch ($identity->gender)
							{
								case 'male' :
									$data ['user_gender'] = 'm';
									break;
	
								case 'female' :
									$data ['user_gender'] = 'f';
									break;
							}
						}
	
						// Email Addresses.
						$data ['user_emails'] = array();
						$data ['user_emails_simple'] = array();
	
						// Email Address.
						$data ['user_email'] = '';
						$data ['user_email_is_verified'] = false;
	
						// Extract emails.
						if (property_exists ($identity, 'emails') && is_array ($identity->emails))
						{
							// Loop through emails.
							foreach ($identity->emails as $email)
							{
								// Add to simple list.
								$data ['user_emails_simple'] [] = $email->value;
	
								// Add to list.
								$data ['user_emails'] [] = array(
									'user_email' => $email->value,
									'user_email_is_verified' => $email->is_verified
								);
	
								// Keep one, if possible a verified one.
								if (empty ($data ['user_email']) || $email->is_verified)
								{
									$data ['user_email'] = $email->value;
									$data ['user_email_is_verified'] = $email->is_verified;
								}
							}
						}
	
						// Addresses.
						$data ['user_addresses'] = array();
						$data ['user_addresses_simple'] = array();
	
						// Extract entries.
						if (property_exists ($identity, 'addresses') && is_array ($identity->addresses))
						{
							// Loop through entries.
							foreach ($identity->addresses as $address)
							{
								// Add to simple list.
								$data ['user_addresses_simple'] [] = $address->formatted;
	
								// Add to list.
								$data ['user_addresses'] [] = array(
									'formatted' => $address->formatted
								);
							}
						}
	
						// Phone Numbers.
						$data ['user_phone_numbers'] = array();
						$data ['user_phone_numbers_simple'] = array();
						
						// Phone Number.
						$data ['user_phone_number'] = '';
	
						// Extract entries.
						if (property_exists ($identity, 'phoneNumbers') && is_array ($identity->phoneNumbers))
						{
							// Loop through entries.
							foreach ($identity->phoneNumbers as $phone_number)
							{
								// Add to simple list.
								$data ['user_phone_numbers_simple'] [] = $phone_number->value;
	
								// Single Phone Number.
								if ( empty ($data ['user_phone_number']))
								{
									$data ['user_phone_number'] = trim ($phone_number->value);
								}
								
								// Add to list.
								$data ['user_phone_numbers'] [] = array(
									'value' => $phone_number->value,
									'type' => (isset ($phone_number->type) ? $phone_number->type : null)
								);
							}
						}
	
						// URLs.
						$data ['user_interests'] = array();
						$data ['user_interests_simple'] = array();
	
						// Extract entries.
						if (property_exists ($identity, 'interests') && is_array ($identity->interests))
						{
							// Loop through entries.
							foreach ($identity->interests as $interest)
							{
								// Add to simple list.
								$data ['user_interests_simple'] [] = $interest->value;
	
								// Add to list.
								$data ['users_interests'] [] = array(
									'value' => $interest->value,
									'category' => (isset ($interest->category) ? $interest->category : null)
								);
							}
						}
	
						// URLs.
						$data ['user_urls'] = array();
						$data ['user_urls_simple'] = array();
	
						// Extract entries.
						if (property_exists ($identity, 'urls') && is_array ($identity->urls))
						{
							// Loop through entries.
							foreach ($identity->urls as $url)
							{
								// Add to simple list.
								$data ['user_urls_simple'] [] = $url->value;
	
								// Add to list.
								$data ['user_urls'] [] = array(
									'value' => $url->value,
									'type' => (isset ($url->type) ? $url->type : null)
								);
							}
						}
	
						// Certifications.
						$data ['user_certifications'] = array();
						$data ['user_certifications_simple'] = array();
	
						// Extract entries.
						if (property_exists ($identity, 'certifications') && is_array ($identity->certifications))
						{
							// Loop through entries.
							foreach ($identity->certifications as $certification)
							{
								// Add to simple list.
								$data ['user_certifications_simple'] [] = $certification->name;
	
								// Add to list.
								$data ['user_certifications'] [] = array(
									'name' => $certification->name,
									'number' => (isset ($certification->number) ? $certification->number : null),
									'authority' => (isset ($certification->authority) ? $certification->authority : null),
									'start_date' => (isset ($certification->startDate) ? $certification->startDate : null)
								);
							}
						}
	
						// Recommendations.
						$data ['user_recommendations'] = array();
						$data ['user_recommendations_simple'] = array();
	
						// Extract entries.
						if (property_exists ($identity, 'recommendations') && is_array ($identity->recommendations))
						{
							// Loop through entries.
							foreach ($identity->recommendations as $recommendation)
							{
								// Add to simple list.
								$data ['user_recommendations_simple'] [] = $recommendation->value;
	
								// Build data.
								$data_entry = array(
									'value' => $recommendation->value
								);
	
								// Add recommender
								if (property_exists ($recommendation, 'recommender') && is_object ($recommendation->recommender))
								{
									$data_entry ['recommender'] = array();
										
									// Add recommender details
									foreach (get_object_vars ($recommendation->recommender) as $field => $value)
									{
										$data_entry ['recommender'] [$this->undo_camel_case ($field)] = $value;
									}
								}
	
								// Add to list.
								$data ['user_recommendations'] [] = $data_entry;
							}
						}
	
						// Accounts.
						$data ['user_accounts'] = array();
	
						// Extract entries.
						if (property_exists ($identity, 'accounts') && is_array ($identity->accounts))
						{
							// Loop through entries.
							foreach ($identity->accounts as $account)
							{
								// Add to list.
								$data ['user_accounts'] [] = array(
									'domain' => (isset ($account->domain) ? $account->domain : null),
									'userid' => (isset ($account->userid) ? $account->userid : null),
									'username' => (isset ($account->username) ? $account->username : null)
								);
							}
						}
	
						// Photos.
						$data ['user_photos'] = array();
						$data ['user_photos_simple'] = array();
	
						// Extract entries.
						if (property_exists ($identity, 'photos') && is_array ($identity->photos))
						{
							// Loop through entries.
							foreach ($identity->photos as $photo)
							{
								// Add to simple list.
								$data ['user_photos_simple'] [] = $photo->value;
	
								// Add to list.
								$data ['user_photos'] [] = array(
									'value' => $photo->value,
									'size' => $photo->size
								);
							}
						}
	
						// Languages.
						$data ['user_languages'] = array();
						$data ['user_languages_simple'] = array();
	
						// Extract entries.
						if (property_exists ($identity, 'languages') && is_array ($identity->languages))
						{
							// Loop through entries.
							foreach ($identity->languages as $language)
							{
								// Add to simple list
								$data ['user_languages_simple'] [] = $language->value;
	
								// Add to list.
								$data ['user_languages'] [] = array(
									'value' => $language->value,
									'proficiency' => (! empty ($language->proficiency) ? $language->proficiency : null)
								);
							}
						}
	
						// Educations.
						$data ['user_educations'] = array();
						$data ['user_educations_simple'] = array();
	
						// Extract entries.
						if (property_exists ($identity, 'educations') && is_array ($identity->educations))
						{
							// Loop through entries.
							foreach ($identity->educations as $education)
							{
								// Add to simple list.
								$data ['user_educations_simple'] [] = $education->value;
	
								// Add to list.
								$data ['user_educations'] [] = array(
									'value' => $education->value,
									'type' => $education->type
								);
							}
						}
	
						// Organizations.
						$data ['user_organizations'] = array();
						$data ['user_organizations_simple'] = array();
	
						// Extract entries.
						if (property_exists ($identity, 'organizations') && is_array ($identity->organizations))
						{
							// Loop through entries.
							foreach ($identity->organizations as $organization)
							{
								// At least the name is required.
								if (!empty ($organization->name))
								{
									// Add to simple list.
									$data ['user_organizations_simple'] [] = $organization->name;
										
									// Build entry.
									$data_entry = array();
										
									// Add all fields.
									foreach (get_object_vars ($organization) as $field => $value)
									{
										$data_entry [$this->undo_camel_case ($field)] = $value;
									}
										
									// Add to list.
									$data ['user_organizations'] [] = $data_entry;
								}
							}
						}
					}
				}
				return $data;
			}
		}
		return false;
	}
	
	// Inverts CamelCase -> camel_case.
	public function undo_camel_case ($input)
	{
		$result = $input;
	
		if (preg_match_all ('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches))
		{
			$ret = $matches [0];
				
			foreach ($ret as &$match)
			{
				$match = ($match == strtoupper ($match) ? strtolower ($match) : lcfirst ($match));
			}
				
			$result = implode ('_', $ret);
		}
	
		return $result;
	}
	
}
?>