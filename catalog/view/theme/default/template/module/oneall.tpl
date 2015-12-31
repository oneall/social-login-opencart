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
 
 // Display widget in this container
$oasl_container = 'oneall_social_login_'.mt_rand(99999, 9999999); 

// Do not display for guests
if ( ! $oasl_user_is_logged  && ! empty ($oasl_subdomain)) 
{

	if ($oasl_type == 'module')
	{ 
		if (OC2)
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
		}
		else        
		{ 
			?>
				<div class="box">
					<?php
						if ( ! empty ($oasl_heading_title))
						{
							?>
								<div class="box-heading"><?php echo $oasl_heading_title; ?></div>
							<?php
						}
					?>
					<div class="box-content">
						<?php 
							if ($oasl_display_modal) 
							{
	                    		echo '<a id="'.$oasl_container.'" class="button">'.$login_button.'</a>';
							}
	                    	else
	                    	{
	                    		echo '<div id="'.$oasl_container.'"></div>';
	                    	}
	                    ?>
					</div>
				</div>
			<?php
		}
	} 
	elseif ( ! $oasl_display_modal && $oasl_type == 'floating')
	{
		?>
			<div style="position:relative;">
	    		<div id="<?php echo $oasl_container; ?>" style="-webkit-user-select: none; -khtml-user-select: none; -moz-user-select: none; -o-user-select: none; z-index:1000000; width:800px; position:absolute; left: <?php echo $oasl_pos_x ?>px; top: <?php echo $oasl_pos_y ?>px;"></div>
	    	</div>
		<?php
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