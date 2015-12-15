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
if (!defined ('OC2'))
{
	define ('OC2', VERSION >= '2');
}

class ControllerModuleOneall extends Controller
{
	private $error = array();

	public function index ()
	{
		$data = array();
		$data = array_merge ($data, $this->load->language ('module/oneall'));
		
		$this->document->setTitle ($this->language->get ('heading_title'));
		
		$this->load->model ('setting/setting');
		$this->load->model ('design/layout');
		
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
			
			if (empty ($this->request->post ['oneall_ask_email']))
				$this->request->post ['oneall_ask_email'] = false;
			if (empty ($this->request->post ['oneall_ask_phone']))
				$this->request->post ['oneall_ask_phone'] = false;
			
			$this->model_setting_setting->editSetting ('oneall', $this->request->post);
			
			$this->session->data ['success'] = $this->language->get ('text_success');
			
			if (OC2)
				$this->response->redirect ($this->url->link ('extension/module', 'token=' . $this->session->data ['token'], 'SSL'));
			else
				$this->redirect ($this->url->link ('extension/module', 'token=' . $this->session->data ['token'], 'SSL'));
		}
		
		if (isset ($this->error ['warning']))
		{
			$data ['error_warning'] = $this->error ['warning'];
		}
		else
		{
			$data ['error_warning'] = '';
		}
		
		$data ['breadcrumbs'] = array();
		
		$data ['breadcrumbs'] [] = array(
			'text' => $this->language->get ('text_home'),
			'href' => $this->url->link ('common/home', 'token=' . $this->session->data ['token'], 'SSL'),
			'separator' => false 
		);
		
		$data ['breadcrumbs'] [] = array(
			'text' => $this->language->get ('text_module'),
			'href' => $this->url->link ('extension/module', 'token=' . $this->session->data ['token'], 'SSL'),
			'separator' => ' :: ' 
		);
		
		$data ['breadcrumbs'] [] = array(
			'text' => $this->language->get ('heading_title'),
			'href' => $this->url->link ('module/oneall', 'token=' . $this->session->data ['token'], 'SSL'),
			'separator' => ' :: ' 
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
		
		// Default data
		if (!isset ($data ['oneall_subdomain']))
			$data ['oneall_subdomain'] = 'openshop';
		if (!isset ($data ['oneall_public']))
			$data ['oneall_public'] = '96b9a245-a162-442d-8c8a-ec0c25febd53';
		if (!isset ($data ['oneall_private']))
			$data ['oneall_private'] = '27521278-485f-4b45-bf6c-8f751fc46570';
		if (!isset ($data ['oneall_socials']))
			$data ['oneall_socials'] = 'facebook,google,twitter,instagram';
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
		
		if (!isset ($data ['oneall_ask_email']))
			$data ['oneall_ask_email'] = true;
		if (!isset ($data ['oneall_ask_phone']))
			$data ['oneall_ask_phone'] = false;
		
		$data ['layouts'] = $this->model_design_layout->getLayouts ();
		
		$data ['all_socials'] = array(
			'Amazon',
			'Battle.net',
			'Blogger',
			'Disqus',
			'Draugiem',
			'Dribbble',
			'Facebook',
			'Foursquare',
			'Github.com',
			'Google',
			'Instagram',
			'LinkedIn',
			'LiveJournal',
			'Mail.ru',
			'Odnoklassniki',
			'OpenID',
			'PayPal',
			'Pinterest',
			'PixelPin',
			'Reddit',
			'Skyrock.com',
			'StackExchange',
			'Steam',
			'Twitch.tv',
			'Twitter',
			'Vimeo',
			'VKontakte',
			'Windows Live',
			'WordPress.com',
			'Yahoo',
			'YouTube' 
		);
		
		$data ['css'] = array();
		$data ['css'] [] = array(
			'name' => 'Flat Squares',
			'url' => '//oneallcdn.com/css/api/themes/flat_w32_h32_wc_v1.css' 
		);
		$data ['css'] [] = array(
			'name' => 'Big Flat Squares',
			'url' => '//oneallcdn.com/css/api/themes/flat_w64_h64_wc_v1.css' 
		);
		$data ['css'] [] = array(
			'name' => 'Flat Bars',
			'url' => '//oneallcdn.com/css/api/themes/flat_w188_h32_wc_v1.css' 
		);
		$data ['css'] [] = array(
			'name' => 'Squares',
			'url' => 'catalog/view/theme/default/stylesheet/oneall/Squares.css' 
		);
		$data ['css'] [] = array(
			'name' => 'Small Squares',
			'url' => '//oneallcdn.com/css/api/socialize/themes/buildin/connect/small-v1.css' 
		);
		$data ['css'] [] = array(
			'name' => 'Signin',
			'url' => '//oneallcdn.com/css/api/socialize/themes/buildin/signin/large-v1.css' 
		);
		$data ['css'] [] = array(
			'name' => 'Signup',
			'url' => '//oneallcdn.com/css/api/socialize/themes/buildin/signup/large-v1.css' 
		);
		$data ['css'] [] = array(
			'name' => 'Connect',
			'url' => '//oneallcdn.com/css/api/themes/beveled_connect_w208_h30_wc_v1.css' 
		);
		$data ['css'] [] = array(
			'name' => 'Modal',
			'url' => 'modal' 
		);
		$i = 0;
		
		$files = glob (DIR_CATALOG . 'view/theme/default/stylesheet/oneall/*.css');
		foreach ($files as $file)
		{
			$file = basename ($file, '.css');
			if ($file == 'Squares')
				continue;
			$data ['css'] [] = array(
				'name' => $file . ' ($)',
				'url' => 'catalog/view/theme/default/stylesheet/oneall/' . $file . '.css' 
			);
		}
		
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

	private function validate ()
	{
		if (!$this->user->hasPermission ('modify', 'module/oneall'))
		{
			$this->error ['warning'] = $this->language->get ('error_permission');
		}
		
		if (!$this->error)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
?>