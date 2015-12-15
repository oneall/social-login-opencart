<?php if (!$logged) { ?>

<script type="text/javascript"
        src="<?php echo ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://' ?><?php echo $subdomain ?>.api.oneall.com/socialize/library.js">
</script>
<?php if ($type == 'module') { ?>

        <?php if (OC2) { ?>

            <h3><?php echo $heading_title; ?></h3>
                    <?php if ($css=='modal') { ?>
                        <a id="social_login_container" class="button"><?php echo $login_button; ?></a>
                    <? } else { ?>
                        <div id="social_login_container"></div>
                    <? } ?>

        <? } else { ?>

            <div class="box">
                <div class="box-heading"><?php echo $heading_title; ?></div>
                <div class="box-content">
                    <?php if ($css=='modal') { ?>
                       <a id="social_login_container" class="button"><?php echo $login_button; ?></a>
                    <? } else { ?>
                        <div id="social_login_container"></div>
                    <? } ?>
                </div>
            </div>

        <? } ?>

<?php } else if ($css != 'modal' && $type == 'floating') { ?>
<div style="position:relative;">
    <div id="social_login_container" style="
-webkit-user-select: none;
-khtml-user-select: none;
-moz-user-select: none;
-o-user-select: none;
z-index:1000000; 
width:800px; 
position:absolute; 
left: <?php echo $x ?>px; 
top: <?php echo $y ?>px;"></div>
</div>
<?php } ?>
<script type="text/javascript">

    function LoadOneall() {
    w=''; h=''; modal=false;
    css='<?php echo $css ?>';
    if (css) {
        modal=(css=='modal');
        if (css.substr(0,2)!='//') css='<?php echo substr(HTTP_SERVER,5) ?>'+css;
        css=document.location.protocol+css;
        if (modal) css='';
        w='<?php echo $gridw ?>'; h='<?php echo $gridh ?>';
    }
    
    socials='<?php echo $socials ?>';
    socials=socials.split(',');
    oneall.api.plugins.social_login.build("social_login_container", {
        'providers' :  socials,
        'css_theme_uri': css,
        'grid_size_x': w,
        'grid_size_y': h,
        'modal': modal,
        'callback_uri': '<?php echo $callback ?>'
    });
    
    }

    $(document).ready(function() { LoadOneall(); });
</script>

<?php } ?>