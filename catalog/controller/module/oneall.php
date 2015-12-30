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

if (!defined('OC2'))
{
	define ('OC2', VERSION >= '2');
}

class ControllerModuleOneall extends Controller {
	public function index($setting) {

        if (OC2) $this->load->language('module/oneall');
        else $this->language->load('module/oneall');

        $data['logged'] = $this->customer->isLogged();
        
       	$data ['oneall_lib_lang'] = $this->config->get('config_language');
       	$data['oneall_store_lang'] = $this->config->get('oneall_store_lang');
               
        $data['heading_title'] = $this->language->get('heading_title');
        $data['login_button'] = $this->language->get('login_button');

        $data['subdomain']=$this->config->get('oneall_subdomain');
        $data['socials']=$this->config->get('oneall_socials');
        $data['css']=$setting['css'];
        $data['gridw']=$setting['gridw'];
        $data['gridh']=$setting['gridh'];
        $data['type']=$setting['type'];
        $data['x']=$setting['x'];
        $data['y']=$setting['y'];
        $data['callback']=HTTPS_SERVER.'index.php?route=account/oneall&go='.urlencode($_SERVER['REQUEST_URI']);

        if (OC2) {

            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/oneall.tpl')) {
                return $this->load->view($this->config->get('config_template') . '/template/module/oneall.tpl', $data);
            } else {
                return $this->load->view('default/template/module/oneall.tpl', $data);
            }

        } else {

            $this->data = $data;
            if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/oneall.tpl')) {
                $this->template = $this->config->get('config_template') . '/template/module/oneall.tpl';
            } else {
                $this->template = 'default/template/module/oneall.tpl';
            }
            $this->render();
        }
	}
}
?>