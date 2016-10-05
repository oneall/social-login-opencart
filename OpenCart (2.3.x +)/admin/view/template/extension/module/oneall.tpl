<?php 
	echo $header;
 
	if (!empty($column_left)) 
	{
		echo $column_left;
	}
 ?>
 <div id="content">
<?php

if ($do == 'settings')
{
	?>	
		<form id="form-layout" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
			<div class="page-header">
				<div class="container-fluid">
					<div class="pull-right">
						<button type="submit" form="form-account" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary">
							<i class="fa fa-save"></i>
						</button>
						<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i	class="fa fa-reply"></i></a>
					</div>
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
				<ul class="nav nav-tabs">
		          <li class="active">
		          	<a href="<?php echo $action; ?>">
		          		<i class="fa fa-wrench"></i> <?php echo $oa_text_settings; ?>
		          	</a>
		          </li>
		          <li class="">
		          	<a href="<?php echo $action; ?>&amp;do=positions">
		          		<i class="fa fa-puzzle-piece"></i> <?php echo $oa_text_positions; ?>
		          	</a>
		          </li>
		        </ul>
		        
				<?php  
				
					// Success
					if ( ! empty ($oa_success_message))
					{
						?>
							<div class="alert alert-success">
								<i class="fa fa-cogs"></i> <?php echo $oa_success_message; ?>
								<button type="button" class="close" data-dismiss="alert">&times;</button>
							</div>
						<?php
					}

		
					// Error
					if ( ! empty ($oa_error_message))
					{
						?>
							<div class="alert alert-danger">
								<i class="fa fa-exclamation-circle"></i><?php echo $oa_error_message; ?>
								<button type="button" class="close" data-dismiss="alert">&times;</button>
							</div>
						<?php
					}

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
								<i class="fa fa-plug"></i>
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
								<i class="fa fa-cog"></i>
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
								<i class="fa fa-cog"></i>
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
								
								<div class="row">	
									<div class="col-sm-6">
										<label for="input-name" class="control-label">
											<?php echo $oa_text_account_customer_group; ?>				
										</label> 
										<select name="oneall_customer_group" class="form-control">
											<option value="store_config"
												<?php echo ($oa_customer_group_selected == 'store_config' ? 'selected="selected"' : ''); ?>>
												<?php echo $oa_text_account_customer_group_default; ?>
											</option>
											<?php foreach ($oa_customer_groups AS $key => $customer_group) { ?>
												<option value="<?php echo $customer_group['customer_group_id']; ?>" 
													<?php echo ($oa_customer_group_selected == $customer_group['customer_group_id'] ? 'selected="selected"' : ''); ?>>
													<?php echo $customer_group['name']; ?>
												</option>
											<?php } ?>
						              </select>		
						            </div>
								</div>	
									
							</div>
						</div>				
						
						<div class="panel-heading">
							<h3 class="panel-title">
								<i class="fa fa-users"></i>
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
					</div>
					<div class="text-center">
						<input type="hidden" name="area" value="settings">
						<button type="submit" form="form-account" class="btn btn-primary">
							<i class="fa fa-save"></i> <?php echo $oa_text_save ?>
						</button>
					</div>		
			</div>
		</form>			
			
	<?php
}
else
{
	?>
		<div class="page-header">
			<div class="container-fluid">
				<div class="pull-right">
					<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
				</div>
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
			<ul class="nav nav-tabs">
				<li><a href="<?php echo $action; ?>"> <i class="fa fa-wrench"></i> <?php echo $oa_text_settings; ?></a></li>
				<li class="active"><a href="<?php echo $action; ?>&amp;do=positions"> <i class="fa fa-puzzle-piece"></i> <?php echo $oa_text_positions; ?></a></li>
			</ul>
			<?php  
			
				// Success
				if ( ! empty ($oa_success_message))
				{
					?>
						<div class="alert alert-success">
							<i class="fa fa-cogs"></i> <?php echo $oa_success_message; ?>
							<button type="button" class="close" data-dismiss="alert">&times;</button>
						</div>
					<?php
				}

				// Error
				if ( ! empty ($oa_error_message))
				{
					?>
						<div class="alert alert-danger">
							<i class="fa fa-exclamation-circle"></i> <?php echo $oa_error_message; ?>
						</div>
					<?php
				}
			?>
				
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">
						<i class="fa fa-plus-square"></i>
						<?php echo $oa_text_add_to_a_position; ?>
					</h3>
				</div>
				<div class="panel-body">
					<form id="form-layout" action="<?php echo $action; ?>&amp;do=positions" method="post" enctype="multipart/form-data">
						<div class="row">
							<div class="col-sm-12">
								<table class="table table-striped">
									<thead>
										<tr>
											<td class="text-center col-sm-3"><?php echo $oa_text_layout; ?></td>
											<td class="text-center col-sm-3"><?php echo $oa_text_position; ?></td>
											<td class="text-center col-sm-3"><?php echo $oa_text_sort_order; ?></td>
											<td class="text-center col-sm-3">&nbsp;</td>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td class="text-center col-sm-3">
												<select name="oa_layout_id">
													<?php
														$oa_i = 0;
          												foreach ($oa_oc_layouts as $oa_oc_layout)
														{
															?>
																<option value="<?php echo $oa_oc_layout['layout_id']; ?>" <?php if (++$oa_i == '1') { echo 'selected="selected"';}?>><?php echo $oa_oc_layout['name']; ?></option>
															<?php
	 													}
													?>
												</select>
											</td>
											<td class="text-center col-sm-3">
												<select name="oa_position">
													<option value="content_top" selected="selected">Content Top</option>
													<option value="content_bottom">Content Bottom</option>
													<option value="column_left">Colum Left</option>
													<option value="column_right">Colum Right</option>
												</select>
											</td>
											<td class="text-center col-sm-3">
												<select name="oa_sort_order">
													<?php
	          											for ($oa_i = 1; $oa_i < 99; $oa_i++)
														{
 															?>
																<option value="<?php echo $oa_i; ?>" <?php if ($oa_i == '1') { echo 'selected="selected"';}?>><?php echo $oa_i; ?></option>
															<?php
	 													}
													?>
												</select>
											</td>
											<td class="text-center col-sm-3">										
												<button class="btn btn-info" type="submit">
													<i class="fa fa-plus-circle"></i> <?php echo $oa_text_add_position; ?>
												</button>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</form>
				</div>
				
				<div class="panel-heading">
					<h3 class="panel-title">
						<i class="fa fa-check-square"></i>
						<?php echo $oa_text_current_positions; ?>
					</h3>
				</div>
				<div class="panel-body">					
					<div class="row">
						<div class="col-sm-12">
							<table class="table table-striped">
								<thead>
									<tr>
										<td class="text-center col-sm-3"><?php echo $oa_text_layout; ?></td>
										<td class="text-center col-sm-3"><?php echo $oa_text_position; ?></td>
										<td class="text-center col-sm-3"><?php echo $oa_text_sort_order; ?></td>
										<td class="text-center col-sm-3">&nbsp;</td>
									</tr>
								</thead>								
								<tbody>
									<?php
										if (isset ($oa_oc_positions) && is_array ($oa_oc_positions) && count ($oa_oc_positions) > 0)
										{
											foreach ($oa_oc_positions AS $oa_oc_position)
											{
												?>								
													<tr>
														<td class="text-center col-sm-3">
															<?php echo $oa_oc_position['name']; ?>
														</td>																	
														<td class="text-center col-sm-3">
															<?php echo ucwords (str_replace ("_", " ", $oa_oc_position['position'])); ?>
														</td>
														<td class="text-center col-sm-3">
															<?php echo $oa_oc_position['sort_order']; ?>
														</td>										
														<td class="text-center col-sm-3">										
															<a href="<?php echo $action; ?>&amp;do=positions&remove=<?php echo $oa_oc_position['layout_module_id']; ?>" class="btn btn-danger" type="button">
																<i class="fa fa-plus-circle"></i> <?php echo $oa_text_remove_position; ?>
															</a>
														</td>
													</tr>
												<?php
											}
										}
									?>
								</tbody>
							</table>
						</div>
					</div>					
				</div>
			</div>
		</div>
	<?php
}
?>		
	</div>
	<script type="text/javascript">
		<!--
			var oaL10n = {};
			oaL10n.token =  '<?php echo $_REQUEST['token']; ?>';
			oaL10n.working = '<?php echo $oa_text_ajax_working; ?>';
		//-->
	</script>
<?php echo $footer; ?>