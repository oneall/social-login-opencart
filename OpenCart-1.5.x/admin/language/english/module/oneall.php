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
// Social Login Admin
//////////////////////////////////////////////////////////////////////
$_ ['heading_title'] = 'OneAll Social Login';
$_ ['heading_title2'] = 'Social Login';
$_ ['text_module'] = 'Modules';
$_ ['oa_text_settings_saved'] = 'Your settings have been saved';
$_ ['oa_text_api_communication'] = 'API Communication';
$_ ['oa_text_api_handler'] = 'API Communication Handler';
$_ ['oa_text_api_protocol'] = 'API Communication Protocol';
$_ ['oa_text_api_curl'] = 'PHP CURL';
$_ ['oa_text_api_fsockopen'] = 'PHP FSOCKOPEN';
$_ ['oa_text_api_autodetect'] = 'Autodetect Communication Settings';
$_ ['oa_text_api_verify'] = 'Verify API Settings';
$_ ['oa_text_api_port_443'] = 'Port 443/HTTPS';
$_ ['oa_text_api_port_80'] = 'Port 80/HTTP';
$_ ['oa_text_api_setup_welcome'] = '<p><strong>Thank you for downloading our OpenCart extension!</strong></p><p>Currently more than 250000 websites are using <a class="alert-link" href="https://www.oneall.com/" target="_blank">OneAll</a> and we are glad to count you amongst them!</p>';
$_ ['oa_text_api_setup'] = '<p>To enable Social Login you first of all need to create a free account at <a class="alert-link" href="https://app.oneall.com/signup/" target="_blank">http://www.oneall.com</a> and setup a Site.</p><p>After having created your account and setup your Site, please enter your API settings in the form below.</p><p><strong>Don\'t worry the setup is free and takes only a couple of minutes!</strong></p>';
$_ ['oa_text_api_create_view'] = 'Click here to create and view your API Credentials';
$_ ['oa_text_settings'] = 'Settings';
$_ ['oa_text_positions'] = 'Layout Positions';
$_ ['oa_text_plugin_language'] = 'Social Login Language';
$_ ['oa_text_plugin_language_desc'] = 'Select which language should be used by Social Login.';
$_ ['oa_text_plugin_language_app'] = 'Use the language selected in the Site Settings in the OneAll account';
$_ ['oa_text_plugin_language_opc'] = 'Use the same language as selected in OpenCart';
$_ ['oa_text_social_network_icons'] = 'You can change the icon theme in the <strong>Site Customisation</strong> panel in your <a href="https://app.oneall.com/" target="_blank">OneAll</a> account.';
$_ ['oa_text_social_networks'] = 'Social Networks';
$_ ['oa_text_enable'] = 'Enabled';
$_ ['oa_text_save'] = 'Save Social Login Settings';
$_ ['oa_text_api_settings'] = 'API Settings';
$_ ['oa_text_api_subdomain'] = 'API Subdomain';
$_ ['oa_text_api_public_key'] = 'API Public Key';
$_ ['oa_text_api_private_key'] = 'API Private Key';
$_ ['oa_text_layout'] = 'Layout';
$_ ['oa_text_plugin_status'] = 'Social Login Status';
$_ ['oa_text_plugin_enabled'] = 'Enabled';
$_ ['oa_text_plugin_disabled'] = 'Disabled';
$_ ['oa_text_ajax_working'] = 'Connecting. Please wait ...';
$_ ['oa_text_ajax_wait'] = 'Contacting API - please wait this may take a few minutes ...';
$_ ['oa_text_ajax_settings_ok'] = 'The settings are correct - do not forget to save your changes!';
$_ ['oa_text_ajax_fill_out'] = 'Please fill out each of the fields above.';
$_ ['oa_text_ajax_missing_subdomain'] = 'The API subdomain does not seem to exist. Have you filled it out correctly?';
$_ ['oa_text_ajax_wrong_subdomain'] = 'The subdomain has a wrong syntax!';
$_ ['oa_text_ajax_blocked_port'] = 'Could not contact API. Are outbound requests on port 443 allowed?';
$_ ['oa_text_ajax_wrong_key'] = 'The API subdomain is correct, but one or both of the keys are invalid.';
$_ ['oa_text_ajax_no_handler'] = 'Could not detect connection handler. Please try to install PHP CURL';
$_ ['oa_text_ajax_wrong_handler'] = 'Could not connect to API connection. Please try to use the autdetection first.';
$_ ['oa_text_ajax_curl_ok_443'] = 'Detected CURL on Port 443 - do not forget to save your changes!';
$_ ['oa_text_ajax_curl_ok_80'] = 'Detected CURL on Port 80 - do not forget to save your changes!';
$_ ['oa_text_ajax_curl_no_ports'] = 'CURL is available but both ports (80, 443) are blocked for outbound requests';
$_ ['oa_text_ajax_fsockopen_ok_443'] = 'Detected FSOCKOPEN on Port 443 - do not forget to save your changes!';
$_ ['oa_text_ajax_fsockopen_ok_80'] = 'Detected FSOCKOPEN on Port 80 - do not forget to save your changes!';
$_ ['oa_text_ajax_fsockopen_no_ports'] = 'FSOCKOPEN is available but both ports (80, 443) are blocked for outbound requests';
$_ ['oa_text_ajax_autodetect_error'] = 'Autodetection Error - our <a href="%s" target="_blank">documentation</a> helps you fix this issue.';
$_ ['oa_text_add_to_a_position'] = 'Add Social Login to a position in your shop';
$_ ['oa_text_layout'] = 'Layout';
$_ ['oa_text_position'] = 'Position';
$_ ['oa_text_status'] = 'Status';
$_ ['oa_text_sort_order'] = 'Sort Order';
$_ ['oa_text_add'] = 'Add';
$_ ['oa_text_add_new_position'] = 'Add new position';
$_ ['oa_text_add_position'] = 'Add to this position';		
$_ ['oa_text_save_positions'] = 'Save these positions';
$_ ['oa_text_remove_position'] = 'Remove from this position';
$_ ['oa_text_current_positions'] = 'Current Positions';
$_ ['oa_text_position_removed'] = 'Social Login has bene removed from that position';
$_ ['oa_text_settings_saved'] = 'The Social Login settings have successfully been saved!';
$_ ['oa_text_error_permission'] = 'Warning: You do not have permission to modify module OneAll Social Login';
$_ ['oa_text_account_creation'] = 'User Account Creation';
$_ ['oa_text_account_creation_desc'] = 'How should Social Login create new customers?';
$_ ['oa_text_account_creation_auto'] ='Automatically create new customer accounts based on the retrieved social network profile data.';
$_ ['oa_text_account_creation_form'] ='Display a registration form with the social network profile data and let the customer review his data before creating his account.';
$_ ['oa_text_account_creation_address'] = 'Ask for the customer\'s address in the Social Login registration form?';
$_ ['oa_text_account_creation_address_no'] = 'No, simplify the registration and don\'t ask for the address.';
$_ ['oa_text_account_creation_address_yes'] = 'Yes, ask for the address.';
$_ ['oa_text_account_link_desc'] = 'Try to automatically link Social Network accounts to existing OpenCart accounts?';
$_ ['oa_text_account_link_on'] = 'Yes, automatically link Social Network accounts to OpenCart accounts that have the same email address.';
$_ ['oa_text_account_link_off'] = 'No, do no automatically link Social Network accounts to existing OpenCart accounts.';

?>