<?php echo $header; if (!empty($column_left)) echo $column_left; ?>


<div id="content">
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
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
					<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<div class="container-fluid">
		<ul class="nav nav-tabs">
          <li class="active">
          	<a data-toggle="tab" href="#tab-settings" aria-expanded="true">
          		<i class="fa fa-wrench"></i> <?php echo $oa_text_settings; ?>
          	</a>
          </li>
          <li class="">
          	<a data-toggle="tab" href="#tab-position" aria-expanded="false">
          		<i class="fa fa-puzzle-piece"></i> <?php echo $oa_text_positions; ?>
          	</a>
          </li>
        </ul>
        
		<?php echo 
			<?php if ($error_warning) { ?>
				<div class="alert alert-danger">
					<i class="fa fa-exclamation-circle"></i>
					<?php echo $error_warning; ?>
					<button type="button" class="close" data-dismiss="alert">&times;</button>
				</div>
			<?php } ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">
						<i class="fa fa-plug"></i>
						<?php echo $oa_text_api_communication; ?>
					</h3>
				</div>				
				<div class="panel-body">
					<div role="alert" class="alert alert-info"><?php echo $oa_text_api_setup; ?></div>
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
						<i class="fa fa-user"></i>
						<?php echo $oa_text_settings; ?>
					</h3>
				</div>	
				<div class="panel-body">
					<div class="well">						
						<div class="row">
							<div class="col-sm-3">
								<label for="input-name" class="control-label">
									<?php echo $oa_text_account_ask_email; ?>									
								</label> 														
								<select name="oneall_ask_email" class="form-control">
									<option value="1" <?php if (! empty ($oneall_ask_email)) {echo 'selected="selected"';} ?>><?php echo $oa_text_account_ask_email_user; ?></option>
									<option value="0" <?php if (empty ($oneall_ask_email)) {echo 'selected="selected"';} ?>><?php echo $oa_text_account_ask_email_auto; ?></option>
				              </select>	
				              <p class="help-block"><?php echo $oa_text_account_ask_email_desc; ?></p>		
							</div>
				
							<div class="col-sm-3">
								<label for="input-name" class="control-label">
									<?php echo $oa_text_account_ask_phone; ?>									
								</label> 																
								<select name="oneall_ask_phone" class="form-control">
									<option value="1" <?php if (! empty ($oneall_ask_phone)) {echo 'selected="selected"';} ?>><?php echo $oa_text_account_ask_phone_user; ?></option>
									<option value="0" <?php if (empty ($oneall_ask_phone)) {echo 'selected="selected"';} ?>><?php echo $oa_text_account_ask_phone_auto; ?></option>
				              </select>	
				              <p class="help-block"><?php echo $oa_text_account_ask_phone_desc; ?></p>
							</div>
				
							<div class="col-sm-3">
								<label for="input-name" class="control-label">
									<?php echo $oa_text_plugin_language; ?>									
								</label> 															
								<select name="oneall_store_lang" class="form-control">
									<option value="0" <?php if (! empty ($oneall_store_lang)) {echo 'selected="selected"';} ?> ><?php echo $oa_text_plugin_language_app; ?></option>
									<option value="1" <?php if (empty ($oneall_store_lang)) {echo 'selected="selected"';} ?>><?php echo $oa_text_plugin_language_opc; ?></option>
				              </select>				              
				              <p class="help-block"><?php echo $oa_text_plugin_language_desc; ?></p>
				            </div>
				          </div>			
					</div>
				</div>
				
				<div class="panel-heading">
					<h3 class="panel-title">
						<i class="fa fa-user"></i>
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
										<div class="col-sm-3">
											<div class="well">
												<div class="row">
													<div class="col-sm-2">												
														<span class="oa_social_login_provider oa_social_login_provider_<?php echo $oa_key;?>"></span>											
													</div>
													<div class="col-sm-4">
														<div class="oa_social_login_provider_label"><?php echo $oa_name; ?></div>
													</div>													
													<div class="col-sm-6">													
														<select name="oneall_social_networks[<?php echo $key;?>]" class="form-control">
															<option value="0" <?php if (! $oa_social_network_enabled) {echo 'selected="selected"';}?>></option>
															<option value="1" <?php if ($oa_social_network_enabled) {echo 'selected="selected"';}?>><?php echo $oa_text_enable; ?></option>
				            				  			</select>	
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
</div>


<script type="text/javascript">
	<!--
		var oaL10n = {};
		oaL10n.token =  '<?php echo $_REQUEST['token']; ?>';
		oaL10n.working = '<?php echo $oa_text_ajax_working; ?>';
	//-->
</script>


<?php echo $footer; ?>
