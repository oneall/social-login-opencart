<?php 
	echo $header;
 
	if (!empty($column_left)) 
	{
		echo $column_left;
	}
 ?>
 <div id="content">
	<form id="form" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
		<div class="page-header">
			<div class="container-fluid">
				<h1>
					<?php echo $heading_title; ?>
				</h1>
				<ul class="breadcrumb">
					<?php 
						foreach ($breadcrumbs as $breadcrumb)
						{
							?>
								<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
							<?php
						}
					?>
				</ul>
			</div>
		</div>
		<div class="container-fluid">
		<?php  
				
			// Success
			if ( ! empty ($oa_success_message))
			{
				?>
					<div class="alert alert-success">
						<?php echo $oa_success_message; ?>
						<button type="button" class="close" data-dismiss="alert">&times;</button>
					</div>
				<?php
			}
		
			// Error
			if ( ! empty ($oa_error_message))
			{
				?>
					<div class="alert alert-danger">
						<?php echo $oa_error_message; ?>
						<button type="button" class="close" data-dismiss="alert">&times;</button>
					</div>
				<?php
			}

			// Subdomain not filled out
			if (empty ($oneall_subdomain))
			{
				?>						
					<div role="alert" class="alert alert-success"><?php echo $oa_text_api_setup_welcome; ?></div>
				<?php
			}
 

		?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">								
					<?php echo $oa_text_api_communication; ?>
				</h3>
			</div>				
			<div class="panel-body">
			<?php
				if (empty ($oneall_subdomain))
				{
					?>						
						<div role="alert" class="alert alert-info"><?php echo $oa_text_api_setup; ?></div>
					<?php
				}
			?>
			<div class="well">
				<div class="row">
					<div class="col-sm-2">
						<label for="input-name" class="control-label">
							<?php echo $oa_text_api_handler; ?>
						</label>
						<select name="oneall_api_handler" id="oneall_api_handler" class="form-control">								
							<option value="crl" <?php if ($oneall_api_handler <> 'fso') {echo 'selected="selected"';} ?>><?php echo $oa_text_api_curl; ?></option>
							<option value="fso" <?php if ($oneall_api_handler == 'fso') {echo 'selected="selected"';} ?>><?php echo $oa_text_api_fsockopen; ?></option>
			              </select>								
					</div>
					<div class="col-sm-2">
						<label for="input-model" class="control-label">
							<?php echo $oa_text_api_protocol; ?>
						</label>
						<select name="oneall_api_port" id="oneall_api_port" class="form-control">					
							<option value="443" <?php if ($oneall_api_port <> '80') {echo 'selected="selected"';} ?>><?php echo $oa_text_api_port_443; ?></option>
							<option value="80" <?php if ($oneall_api_port == '80') {echo 'selected="selected"';} ?>><?php echo $oa_text_api_port_80; ?></option>
			              </select>	
						</div>				
					</div>					
					 <div class="row">
					 	<div class="col-sm-4">
					 		<div id="oneall_api_autodetect_result"></div>
					 	</div>						
					</div>
					<div class="row">
						<div class="col-sm-2">								
					 		<button type="button" class="btn btn-success" id="oneall_api_autodetect"><?php echo $oa_text_api_autodetect; ?></button>
					 	</div>
					 </div>
				</div>
				<div class="well">
					<div class="row">
						<div class="col-sm-12">
							<a href="https://app.oneall.com/" target="_blank"><?php echo $oa_text_api_create_view; ?></a>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-4">
							<label for="input-name" class="control-label">
								<?php echo $oa_text_api_subdomain; ?>
							</label> <input type="text" class="form-control" value="<?php echo $oneall_subdomain; ?>" id="oneall_subdomain" name="oneall_subdomain" autocomplete="off">
						</div>
					</div>
					<div class="row">
						<div class="col-sm-4">
							<label for="input-model" class="control-label">
								<?php echo $oa_text_api_public_key; ?>
							</label> <input type="text" class="form-control" value="<?php echo $oneall_public; ?>" id="oneall_public" name="oneall_public" autocomplete="off">
						</div>
					</div>
					<div class="row">
						<div class="col-sm-4">
							<label for="input-model" class="control-label">
								<?php echo $oa_text_api_private_key; ?>
							</label> <input type="text" class="form-control" value="<?php echo $oneall_private; ?>" id="oneall_private" name="oneall_private" autocomplete="off">
						</div>
					</div>
					 <div class="row">
					 	<div class="col-sm-4">
					 		<div id="oneall_api_verify_result"></div>
					 	</div>						
					</div>
					<div class="row">
						<div class="col-sm-2">
							<button type="button" class="btn btn-success" id="oneall_api_verify"><?php echo $oa_text_api_verify; ?></button>						 
					 	</div>										
					</div>					
				</div>
			</div>
			<div class="panel-heading">
				<h3 class="panel-title">								
					<?php echo $oa_text_settings; ?>
				</h3>
			</div>			
			<div class="panel-body">
				<div class="well">	
					<div class="row">	
						<div class="col-sm-3">
							<label for="input-name" class="control-label">
								<?php echo $oa_text_plugin_status; ?>									
							</label> 																					
							<select name="oneall_status" class="form-control">
								<option value="1" <?php if ( ! empty ($oneall_status)) {echo 'selected="selected"';} ?>><?php echo $oa_text_plugin_enabled; ?></option>
								<option value="0" <?php if (empty ($oneall_status)) {echo 'selected="selected"';} ?>><?php echo $oa_text_plugin_disabled; ?></option>
				              </select>		
			            </div>
						<div class="col-sm-3">
							<label for="input-name" class="control-label">
								<?php echo $oa_text_plugin_language; ?>									
							</label> 																					
							<select name="oneall_store_lang" class="form-control">
								<option value="0" <?php if (empty ($oneall_store_lang)) {echo 'selected="selected"';} ?>><?php echo $oa_text_plugin_language_app; ?></option>
								<option value="1" <?php if ( ! empty ($oneall_store_lang)) {echo 'selected="selected"';} ?>><?php echo $oa_text_plugin_language_opc; ?></option>
			              </select>	
			            </div>
			         </div>
				</div>
			</div>
			<div class="panel-heading">
				<h3 class="panel-title">								
					<?php echo $oa_text_account_creation; ?>
				</h3>
			</div>
			<div class="panel-body">
				<div class="well">									
					<div class="row">	
						<div class="col-sm-6">
							<label for="input-name" class="control-label">
								<?php echo $oa_text_account_creation_desc; ?>				
							</label> 																					
							<select name="oneall_auto_account" class="form-control">
								<option value="0" <?php if (empty ($oneall_auto_account)) {echo 'selected="selected"';} ?>><?php echo $oa_text_account_creation_form; ?></option>
								<option value="1" <?php if ( ! empty ($oneall_auto_account)) {echo 'selected="selected"';} ?>><?php echo $oa_text_account_creation_auto; ?></option>
				              </select>		
			    		</div>
					</div>							
					<div class="row">	
						<div class="col-sm-6">
							<label for="input-name" class="control-label">
								<?php echo $oa_text_account_creation_address; ?>				
							</label> 																					
							<select name="oneall_ask_address" class="form-control">
								<option value="0" <?php if (empty ($oneall_ask_address)) {echo 'selected="selected"';} ?>><?php echo $oa_text_account_creation_address_no; ?></option>
								<option value="1" <?php if ( ! empty ($oneall_ask_address)) {echo 'selected="selected"';} ?>><?php echo $oa_text_account_creation_address_yes; ?></option>
							</select>		
						</div>
					</div>
					<div class="row">	
						<div class="col-sm-6">
							<label for="input-name" class="control-label">
								<?php echo $oa_text_account_link_desc; ?>				
							</label> 																					
							<select name="oneall_auto_link" class="form-control">
								<option value="1" <?php if ( ! empty ($oneall_auto_link)) {echo 'selected="selected"';} ?>><?php echo $oa_text_account_link_on; ?></option>
								<option value="0" <?php if (empty ($oneall_auto_link)) {echo 'selected="selected"';} ?>><?php echo $oa_text_account_link_off; ?></option>											
							</select>		
						</div>
					</div>	
				</div>
			</div>				
			<div class="panel-heading">
				<h3 class="panel-title">							
					<?php echo $oa_text_social_networks; ?>
				</h3>
			</div>	
			<div class="panel-body">
				<div role="alert" class="alert alert-info"><?php echo $oa_text_social_network_icons; ?></div>
				<div class="row">
			 	<?php 
					// Social Networks
					$oa_enabled_social_networks = explode (',', $oneall_socials); 						 	
						 	
					// Display
					foreach ($oa_social_networks AS $oa_key => $oa_name)
					{
						$oa_social_network_enabled = (in_array ($oa_key, $oa_enabled_social_networks) ? true : false);
						?>
							<div class="col-sm-3 oa_social_login_provider_toggle">
								<div class="well">
									<div class="row">
										<div class="col-sm-3">												
											<span class="oa_social_login_provider oa_social_login_provider_<?php echo $oa_key;?>"></span>											
										</div>
										<div class="col-sm-6">
											<div class="oa_social_login_provider_label"><?php echo $oa_name; ?></div>
										</div>													
										<div class="col-sm-3">	
											<div class="oa_social_login_provider_label">
												<input type="checkbox" value="1" name="oneall_social_networks[<?php echo $oa_key;?>]" class="form-control" <?php if ($oa_social_network_enabled) {echo 'checked="checked"';}?>>
											</div>
										</div>
									</div>									
								</div>
							</div>
						<?php
					}
				?>	
				</div>
			</div>	
			<div class="panel-heading">
				<h3 class="panel-title">						
					<?php echo $oa_text_add_to_a_position; ?>
				</h3>
			</div>
			<div class="panel-body">					
				<div class="row">
					<div class="col-sm-12">								 
	       				<table id="module" class="table table-bordered">
	          				<thead>
	           					<tr>
									<th class="text-center col-sm-3"><?php echo $oa_text_layout; ?></th>
									<th class="text-center col-sm-3"><?php echo $oa_text_position; ?></th>
									<th class="text-center col-sm-2"><?php echo $oa_text_status; ?></th>
									<th class="text-center col-sm-1"><?php echo $oa_text_sort_order; ?></th>
									<th class="text-center col-sm-3">&nbsp;</th>
								</tr>
	          				</thead>
	         				<?php 
								$module_row = 0;

          						foreach ($modules as $module)
								{
									?>
	          							<tbody id="module-row<?php echo $module_row; ?>">
	            							<tr>
	            								<td class="text-center col-sm-3">
	            									<select name="oneall_module[<?php echo $module_row; ?>][layout_id]">
	                								<?php
	                									foreach ($layouts as $layout)
														{
															?>
																<option value="<?php echo $layout['layout_id']; ?>"<?php if ($layout['layout_id'] == $module['layout_id']) {echo ' selected="selected"';}?>><?php echo $layout['name']; ?></option>
															<?php
														}
													?>
	                								</select>
	                							</td>
												<td class="text-center col-sm-3">
													<select name="oneall_module[<?php echo $module_row; ?>][position]">															
														<option value="content_top"<?php if ($module['position'] == 'content_top') {echo ' selected="selected"';} ?>>Content Top</option>
														<option value="content_bottom"<?php if ($module['position'] == 'content_bottom') {echo ' selected="selected"';} ?>>Content Bottom</option>
														<option value="column_left"<?php if ($module['position'] == 'column_left') {echo ' selected="selected"';} ?>>Column Left</option>
														<option value="column_right"<?php if ($module['position'] == 'column_right') {echo ' selected="selected"';} ?>>Column Right</option>
													</select>						
	        									</td>
	             								<td class="text-center col-sm-2">
	             								 	<select name="oneall_module[<?php echo $module_row; ?>][status]">
	             								 		<option value="1"<?php if ( ! empty ($module['status'])) {echo ' selected="selected"';} ?>><?php echo $text_enabled; ?></option>
	             								 		<option value="0"<?php if (empty ($module['status'])) {echo ' selected="selected"';} ?>><?php echo $text_disabled; ?></option>
	             									</select>
	             								</td>
	              								<td class="text-center col-sm-1">
	              									<input type="text" name="oneall_module[<?php echo $module_row; ?>][sort_order]" value="<?php echo $module['sort_order']; ?>" size="3" />
	              								</td>
	              								<td class="text-center col-sm-3">
	              									<a onclick="$('#module-row<?php echo $module_row; ?>').remove();" class="btn btn-danger"><?php echo $oa_text_remove_position; ?></a>
	              								</td>
											</tr>
										</tbody>
									<?php 
	
										$module_row++; 
								} 
							?>
	          				<tfoot>
	            				<tr>
	              					<td colspan="5" class="text-right">
	              						<button type="button" class="btn btn-primary" onclick="addModule();">
											<?php echo $oa_text_add_new_position; ?>
										</button>
	              					</td>
	            				</tr>
	          				</tfoot>
	        			</table>		
					</div>
				</div>				
			</div>		
		</div>
		<div class="text-center">
			<button type="submit" form="form-account" class="btn btn-primary" onclick="$('#form').submit();">
				<?php echo $oa_text_save ?>
			</button>				
		</div>		
	</div>
</form>			

		
		<script type="text/javascript"><!--
var module_row = <?php echo $module_row; ?>;

function addModule() {	
	html  = '<tbody id="module-row' + module_row + '">';
	html += '  <tr>';
	html += '    <td class="text-center"><select name="oneall_module[' + module_row + '][layout_id]">';
	<?php foreach ($layouts as $layout) { ?>
	html += '      <option value="<?php echo $layout['layout_id']; ?>"><?php echo addslashes($layout['name']); ?></option>';
	<?php } ?>
	html += '    </select></td>';
	html += '    <td class="text-center"><select name="oneall_module[' + module_row + '][position]">';
	html += '      <option value="content_top">Content Top</option>';
	html += '      <option value="content_bottom">Content Bottom</option>';
	html += '      <option value="column_left">Column Left</option>';
	html += '      <option value="column_right">Column Right </option>';
	html += '    </select></td>';
	html += '    <td class="text-center"><select name="oneall_module[' + module_row + '][status]">';
    html += '      <option value="1" selected="selected"><?php echo $text_enabled; ?></option>';
    html += '      <option value="0"><?php echo $text_disabled; ?></option>';
    html += '    </select></td>';
	html += '    <td class="text-center"><input type="text" name="oneall_module[' + module_row + '][sort_order]" value="1" size="3" /></td>';
	html += '    <td class="text-center"><a onclick="$(\'#module-row' + module_row + '\').remove();" class="btn btn-danger"><?php echo $oa_text_remove_position; ?></a></td>';
	html += '  </tr>';
	html += '</tbody>';
	
	$('#module tfoot').before(html);
	
	module_row++;
}

			var oaL10n = {};
			oaL10n.token =  '<?php echo $_REQUEST['token']; ?>';
			oaL10n.working = '<?php echo $oa_text_ajax_working; ?>';
		//-->
	</script>
<?php echo $footer; ?>