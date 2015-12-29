<?php echo $header; if (!empty($column_left)) echo $column_left; ?>

<div id="content">

    <?php if (OC2) { ?>
        <style type="text/css">

            #content {
                padding: 20px;
            }

            td, th, input, select, textarea, option, optgroup {
                font-family: Arial, Helvetica, sans-serif;
                font-size: 12px;
                color: #000000;
            }

            select {
                padding: 1px;
            }

            a, a:visited {
                color: #003366;
                text-decoration: underline;
                cursor: pointer;
            }
            a img {
                border: 0;
            }
            form {
                margin: 0;
                padding: 0;
            }
            label {
                cursor: pointer;
            }

            .breadcrumb, .breadcrumb a {
                font-size: 12px;
                color: #666;
                margin-bottom: 15px;
            }

            .warning {
                padding: 10px 10px 10px 33px;
                margin-bottom: 15px;
                background: #FFD1D1 url('../image/warning.png') 10px center no-repeat;
                border: 1px solid #F8ACAC;
                color: #555555;
                -webkit-border-radius: 5px 5px 5px 5px;
                -moz-border-radius: 5px 5px 5px 5px;
                -khtml-border-radius: 5px 5px 5px 5px;
                border-radius: 5px 5px 5px 5px;
            }

            .image {
                border: 1px solid #EEEEEE;
                padding: 10px;
                display: inline-block;
            }
            .image img {
                margin-bottom: 5px;
            }
            .box {
                margin-bottom: 15px;
            }
            .box > .heading {
                height: 38px;
                padding-left: 7px;
                padding-right: 7px;
                border: 1px solid #DBDBDB;
                background: url('../image/box.png') repeat-x;
                -webkit-border-radius: 7px 7px 0px 0px;
                -moz-border-radius: 7px 7px 0px 0px;
                -khtml-border-radius: 7px 7px 0px 0px;
                border-radius: 7px 7px 0px 0px;
            }
            .box > .heading h1 {
                margin: 0px;
                padding: 11px 0px 0px 0px;
                color: #003A88;
                font-size: 16px;
                float: left;
            }
            .box > .heading h1 img {
                float: left;
                margin-top: -1px;
                margin-left: 3px;
                margin-right: 8px;
            }
            .box > .heading .buttons {
                float: right;
                padding-top: 7px;
                margin-right: 5px;
            }
            .box > .heading .buttons .button {
                margin-left: 5px;
            }
            .box > .content h2 {
                text-transform: uppercase;
                color: #FF802B;
                font-size: 15px;
                font-weight: bold;
                padding-bottom: 3px;
                border-bottom: 1px dotted #000000;
            }
            .box > .content {
                padding: 10px;
                border-left: 1px solid #CCCCCC;
                border-right: 1px solid #CCCCCC;
                border-bottom: 1px solid #CCCCCC;
                min-height: 300px;
                overflow: auto;
            }
            a.button, .list a.button {
                text-decoration: none;
                color: #FFF;
                display: inline-block;
                padding: 5px 15px 5px 15px;
                background: #003A88;
                -webkit-border-radius: 10px 10px 10px 10px;
                -moz-border-radius: 10px 10px 10px 10px;
                -khtml-border-radius: 10px 10px 10px 10px;
                border-radius: 10px 10px 10px 10px;
            }

            .list td {
                border-right: 1px solid #DDDDDD;
                border-bottom: 1px solid #DDDDDD;
            }
            .list thead td {
                background-color: #EFEFEF;
                padding: 0px 5px;
            }
            .list thead td a, .list thead td {
                text-decoration: none;
                color: #222222;
                font-weight: bold;
            }
            .list tbody td a {
                text-decoration: underline;
            }
            .list tbody td {
                vertical-align: middle;
                padding: 0px 5px;
            }
            .list tbody tr:hover td {
                background-color: #FFFFCB;
            }
            .list tbody tr:nth-child(2n) {
                background-color: #F4F4F8;
            }

            .list tr.filter td, .list tr:hover.filter td {
                padding: 5px;
                background: #E7EFEF;
            }

            table.form {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            table.form > tbody > tr > td:first-child {
                width: 200px;
            }
            table.form > tbody > tr > td {
                padding: 10px;
                color: #000000;
                border-bottom: 1px dotted #CCCCCC;
            }

            .scrollbox div {
                padding: 3px;
            }
            .scrollbox div input {
                margin: 0px;
                padding: 0px;
                margin-right: 3px;
            }
            .scrollbox div.even {
                background: #FFFFFF;
            }
            .scrollbox div.odd {
                background: #E4EEF7;
            }
            .overview {
                float: left;
                width: 49%;
                margin-bottom: 20px;
            }
            .overview table {
                width: 100%;
            }
            .overview td + td {
                text-align: right;
            }

        </style>
    <?php } ?>

  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator'] ?><a href="<?php echo $breadcrumb['href'] ?>"><?php echo $breadcrumb['text'] ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="http://secure.oneallcdn.com/img/oneall_header_logo.png" style="margin-top: -7px; width:auto; height:28px; vertical-align: center;" alt="" /> <?php echo $heading_title2 ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save ?></a><a onclick="location = '<?php echo $cancel ?>';" class="button"><?php echo $button_cancel ?></a></div>
    </div>
    <div class="content">
      <form action="<?php echo $action ?>" method="post" enctype="multipart/form-data" id="form">
          <table><tr>
            <td style="vertical-align:top; padding-right:40px;">
            	<strong><?php echo $text_account ?></strong>
                <div style="min-width: 150px">
                <div style="border: 5px solid #DDDDDD; padding:5px; height:300px" id="haccount">
                    
                    <?php echo $text_oneall_subdomain ?><br/><input name="oneall_subdomain" value="<?php echo $oneall_subdomain ?>" /><br/><br/>
                    <?php echo $text_oneall_public ?><br/><input style="width: 250px;" name="oneall_public" value="<?php echo $oneall_public ?>" /><br/><br/>
                    <?php echo $text_oneall_private ?><br/><input style="width: 250px;" name="oneall_private" value="<?php echo $oneall_private ?>" /><br/><br/>
                    <a onclick="$('#form').submit();" class="button"><?php echo $button_save ?></a>
                </div>
            </td>
            
            <td style="vertical-align: top; padding-right:40px;">
            	<strong><?php echo $text_social_networks ?></strong>
                <div style="min-width: 200px">
                <div style="border: 5px solid #DDDDDD; padding:5px; height:300px" id="haccount">
	                <select style="margin-bottom: 8px;" onchange="
	                    if (this.value) {
	                     s=$('#socials').val();
	                     if (s) s+=',';
	                     s+= this.value;
	                     $('#socials').val(s);
	                     this.value='';
	                     Preview(-1);
	                    }
	                ">
	                  <option value=""><?php echo $text_add_social ?></option>
	                    <?php foreach ($all_socials as $social) { ?>
	                    <option value="<?php echo preg_replace('/[^a-z]/i', '',strtolower($social)) ?>"><?php echo $social ?></option>
	                    <?php } ?>
	                </select><br/>
	                <a class="button" onclick="
	                     s=$('#socials').val();
	                     p=s.lastIndexOf(',');
	                     s=s.substr(0,p);
	                     $('#socials').val(s);
	                     Preview(-1);
	                "><?php echo $text_remove_social ?></a>
	                <input type="hidden" name="oneall_socials" id="socials" value="<?php echo $oneall_socials ?>" />
	                    <div style="margin-top:20px; color: grey"><?php echo $text_request ?></div>
	                    	<input type="checkbox" <?php if ($oneall_ask_email) echo "checked='1'" ?> name="oneall_ask_email" /><?php echo $text_ask_email ?><br/>
	                    	<input type="checkbox" <?php if ($oneall_ask_phone) echo "checked='1'" ?> name="oneall_ask_phone" /><?php echo $text_ask_phone ?><br/><br/>
	                    </div>
	          	</div>
                </div>
            </td>
            
            <td style="width:100%; padding-right:40px;">
                <strong><?php echo $text_preview ?></strong>
                <!--ONEALL-->
                <script type="text/javascript" src="<?php echo ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://' ?><?php echo $oneall_subdomain ?>.api.oneall.com/socialize/library.js">
                </script>
                <script type="text/javascript">$(document).ready(function(){Preview(-1)})</script>
                <!-- The plugin will be embedded into this div //-->
                <div id="social_login_container" style="vertical-align: top; border: 5px solid #EEEEEE; padding: 10px; width:auto; ; height:300px;"></div>

                <br />
            </td>
          </tr></table>
          <br/>
        <table id="module" class="list">
          <thead>
            <tr>
              <td class="left"><?php echo $entry_layout ?></td>
              <td class="left"><?php echo $entry_CSS ?></td>
              <td class="left"><?php echo $entry_grid ?></td>
              <td class="left"><?php echo $entry_type ?></td>
              <td class="left"><?php echo $entry_position ?></td>
              <td class="left"><?php echo $entry_status ?></td>
            </tr>
          </thead>
          <?php $module_row = 0 ?>
          <?php foreach ($oneall_module as $module) { ?>
          <tbody id="module-row<?php echo $module_row ?>">
          <?php if (!empty($module['module_id'])) { ?>
              <input type="hidden" name="oneall_module[<?php echo $module_row; ?>][module_id]" value="<?php echo $module['module_id']; ?>" />
          <?php } ?>
            <tr>
              <td class="left"><select name="oneall_module[<?php echo $module_row ?>][layout_id]">
                  <?php foreach ($layouts as $layout) { ?>
                  <?php if ($layout['layout_id'] == $module['layout_id']) { ?>
                  <option value="<?php echo $layout['layout_id'] ?>" selected="selected"><?php echo $layout['name'] ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $layout['layout_id'] ?>"><?php echo $layout['name'] ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
              </td>

              <td class="left">
                <select name="oneall_module[<?php echo $module_row ?>][css]" onchange="Preview(<?php echo $module_row ?>)">
                <?php foreach ($css as $cs) { ?>
                    <option value="<?php echo $cs['url'] ?>"<?php if ($cs['url'] == $module['css']) echo ' selected="1"' ?>>
                        <?php echo $cs['name'] ?></option>
                <?php } ?>
                </select>
              </td>

              <td class="left">
                <select name="oneall_module[<?php echo $module_row ?>][gridw]" onchange="Preview(<?php echo $module_row ?>)">';
                    <option value=""><?php echo $entry_unlimited ?></option>';
                    <?php for ($i = 1; $i <= 20; $i++) { ?>
                        <option value="<?php echo $i ?>"<?php if ($i == $module['gridw']) echo ' selected="1"' ?>><?php echo $i ?></option>';
                    <?php } ?>
                </select> x
                <select name="oneall_module[<?php echo $module_row ?>][gridh]" onchange="Preview(<?php echo $module_row ?>)">';
                      <option value=""><?php echo $entry_unlimited ?></option>';
                      <?php for ($i = 1; $i <= 20; $i++) { ?>
                      <option value="<?php echo $i ?>"<?php if ($i == $module['gridh']) echo ' selected="1"' ?>><?php echo $i ?></option>';
                      <?php } ?>
                </select> <?php echo $entry_icons ?>
              </td>

              <td class="left">
                  <select onchange="PosChange(<?php echo $module_row ?>, $(this).val())" name="oneall_module[<?php echo $module_row ?>][type]">
                      <option value="module"><?php echo $type_module ?></option>
                      <option value="floating" <?php if ($module['type']=='floating') echo ' selected="1"' ?>><?php echo $type_floating ?></option>
                      <option value="template" <?php if ($module['type']=='template') echo ' selected="1"' ?>><?php echo $type_template ?></option>
                  </select>
              </td>

               <td class="left">
                   <span id="pos<?php echo $module_row ?>" <?php if ($module['type']!='module') echo ' style="display:none"' ?>>
                      <select name="oneall_module[<?php echo $module_row; ?>][position]">
                          <?php if ($module['position'] == 'content_top') { ?>
                              <option value="content_top" selected="selected"><?php echo $text_content_top; ?></option>
                          <?php } else { ?>
                              <option value="content_top"><?php echo $text_content_top; ?></option>
                          <?php } ?>
                          <?php if ($module['position'] == 'content_bottom') { ?>
                              <option value="content_bottom" selected="selected"><?php echo $text_content_bottom; ?></option>
                          <?php } else { ?>
                              <option value="content_bottom"><?php echo $text_content_bottom; ?></option>
                          <?php } ?>
                          <?php if ($module['position'] == 'column_left') { ?>
                              <option value="column_left" selected="selected"><?php echo $text_column_left; ?></option>
                          <?php } else { ?>
                              <option value="column_left"><?php echo $text_column_left; ?></option>
                          <?php } ?>
                          <?php if ($module['position'] == 'column_right') { ?>
                              <option value="column_right" selected="selected"><?php echo $text_column_right; ?></option>
                          <?php } else { ?>
                              <option value="column_right"><?php echo $text_column_right; ?></option>
                          <?php } ?>
                      </select>
                       &nbsp; <?php echo $entry_sort_order ?>
                       <input type="text" name="oneall_module[<?php echo $module_row; ?>][sort_order]" value="<?php echo $module['sort_order']; ?>" size="2" />
                  </span>
                  <span id="coords<?php echo $module_row ?>" <?php if ($module['type']!='floating') echo ' style="display:none"' ?>>
                      X: <input type="text" name="oneall_module[<?php echo $module_row ?>][x]" value="<?php echo $module['x'] ?>" size="3" />px &nbsp;&nbsp;&nbsp;
                      Y: <input type="text" name="oneall_module[<?php echo $module_row ?>][y]" value="<?php echo $module['y'] ?>" size="3" />px
                  </span>
                  <span id="explain<?php echo $module_row ?>" <?php if ($module['type']!='template') echo ' style="display:none"' ?>>
                      <?php echo $template_explain ?>
                  </span>
               </td>


              <td class="left"><select name="oneall_module[<?php echo $module_row ?>][status]">
                  <?php if ($module['status']) { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled ?></option>
                  <option value="0"><?php echo $text_disabled ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_enabled ?></option>
                  <option value="0" selected="selected"><?php echo $text_disabled ?></option>
                  <?php } ?>
                </select></td>
              <td class="left"><a onclick="$('#module-row<?php echo $module_row ?>').remove();" class="button"><?php echo $button_remove ?></a></td>
            </tr>
          </tbody>
          <?php $module_row++ ?>
          <?php } ?>
          <tfoot>
            <tr>
              <td colspan="5"></td>
              <td class="left"><a onclick="addModule();" class="button"><?php echo $add_module ?></a></td>
            </tr>
          </tfoot>
        </table>
      </form>
      <input type="hidden" id="row_save" value="0"/>
      <br/>
    </div>
  </div>
</div>
<script type="text/javascript"><!--

    function PosChange(row, type) {
        $('#pos'+row).hide();
        $('#coords'+row).hide();
        $('#explain'+row).hide();
        if (type=='module') $('#pos'+row).show(300);
        if (type=='floating') $('#coords'+row).show(300);
        if (type=='template') $('#explain'+row).show(300);
    }

var module_row = <?php echo $module_row ?>;

function addModule() {	
	html  = '<tbody id="module-row' + module_row + '">';
	html += '  <tr>';
	html += '    <td class="left"><select name="oneall_module[' + module_row + '][layout_id]">';
	<?php foreach ($layouts as $layout) { ?>
	html += '      <option value="<?php echo $layout['layout_id'] ?>"><?php echo addslashes($layout['name']) ?></option>';
	<?php } ?>
	html += '    </select><span style="display:none;">';
	html += '    <select name="oneall_module[' + module_row + '][position]">';
	html += '      <option value="content_top"><?php echo $text_content_top ?></option>';
	html += '    </select><input type="text" name="oneall_module[' + module_row + '][sort_order]" value="" size="3" /></span></td>';

    html += '    <td class="left"><select name="oneall_module[' + module_row + '][css]" onchange="Preview(' + module_row + ');">';
    <?php foreach ($css as $cs) { ?>
    html += '    <option value="<?php echo $cs['url'] ?>"><?php echo $cs['name'] ?></option>';
    <?php } ?>
    html += '    </select></td>';

    html += '    <td class="left">';

    html += '    <select name="oneall_module[' + module_row + '][gridw]" onchange="Preview(' + module_row + ');">';
    html += '    <option value=""><?php echo $entry_unlimited ?></option>';
    <?php for ($i = 1; $i <= 20; $i++) { ?>
    html += '    <option value="<?php echo $i ?>"><?php echo $i ?></option>';
    <?php } ?>
    html += '    </select> x ';

    html += '    <select name="oneall_module[' + module_row + '][gridh]" onchange="Preview(' + module_row + ');">';
    html += '    <option value=""><?php echo $entry_unlimited ?></option>';
    <?php for ($i = 1; $i <= 20; $i++) { ?>
    html += '    <option value="<?php echo $i ?>"><?php echo $i ?></option>';
    <?php } ?>
    html += '    </select> <?php echo $entry_icons ?>';

    html += '    </td>';


    html += '    <td class="left">';
    html += '    <select onchange="PosChange(' + module_row + ', $(this).val())" name="oneall_module[' + module_row + '][type]">';
    html += '    <option value="module"><?php echo $type_module ?></option>';
    html += '    <option value="floating" ><?php echo $type_floating ?></option>';
    html += '    <option value="template" ><?php echo $type_template ?></option>';
    html += '    </select>';
    html += '    </td>';

    html += '    <td class="left">';
    html += '    <span id="pos' + module_row + '">';
    html += '    <select name="oneall_module[' + module_row + '][position]">';
    html += '    <option value="content_top"><?php echo $text_content_top; ?></option>';
    html += '    <option value="content_bottom"><?php echo $text_content_bottom; ?></option>';
    html += '    <option value="column_left"><?php echo $text_column_left; ?></option>';
    html += '    <option value="column_right" selected="selected"><?php echo $text_column_right; ?></option>';
    html += '    </select>';
    html += '    &nbsp; <?php echo $entry_sort_order ?>';
    html += '    <input type="text" name="oneall_module[' + module_row + '][sort_order]" value="" size="2" />';
    html += '    </span>';
    html += '    <span id="coords' + module_row + '" style="display:none">';
    html += '    X: <input type="text" name="oneall_module[' + module_row + '][x]" value="10" size="3" />px &nbsp;&nbsp;&nbsp;';
    html += '    Y: <input type="text" name="oneall_module[' + module_row + '][y]" value="10" size="3" />px';
    html += '    </span>';
    html += '    <span id="explain' + module_row + '" style="display:none">';
    html += '    <?php echo $template_explain ?>';
    html += '    </span>';
    html += '    </td>';

	html += '    <td class="left"><select name="oneall_module[' + module_row + '][status]">';
    html += '      <option value="1" selected="selected"><?php echo $text_enabled ?></option>';
    html += '      <option value="0"><?php echo $text_disabled ?></option>';
    html += '    </select></td>';
	html += '    <td class="left"><a onclick="$(\'#module-row' + module_row + '\').remove();" class="button"><?php echo $button_remove ?></a></td>';
	html += '  </tr>';
	html += '</tbody>';
	
	$('#module tfoot').before(html);
	
	module_row++;
}

function Preview(module_row) {
    if (module_row==-1) module_row=$('#row_save').val();
    else $('#row_save').val(module_row);
    css=document.getElementsByName('oneall_module[' + module_row + '][css]');
    if (css[0]) {
        css=css[0].value;
        modal=(css=='modal');
        if (css.substr(0,2)!='//') css='<?php echo substr(HTTP_SERVER,5,-6) ?>'+css;
        css=document.location.protocol+css;
        if (modal) {
            css='';
            $('#social_login_container').html('<a href="#" id="social_login_link" class="button">Social Login</a>');
        }
        w=document.getElementsByName('oneall_module[' + module_row + '][gridw]')[0].value;
        h=document.getElementsByName('oneall_module[' + module_row + '][gridh]')[0].value;
    } else {
        css=''; w=''; h=''; modal=false;
    }

    socials=$('#socials').val().split(',');


    oneall.api.plugins.social_login.build("social_login_container", {
        'providers' :  socials,
        'css_theme_uri': css,
        'grid_size_x': w,
        'grid_size_y': h,
        'modal': modal,
        'callback_uri': 'INSERT YOUR CALLBACK URI HERE'
    });
}

//--></script> 
<?php echo $footer ?>