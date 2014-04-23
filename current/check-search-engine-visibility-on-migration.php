<?php
/*
Plugin Name:  Check Search Engine Visibility on Migration
Plugin URI: http://winwar.co.uk/plugins/check-search-engine-visibility-migration/
Description:  Checks if a site has been migrated and if it's inivisible to search engines. If so, then the site warns you on this and notifies you to act.
Version:      0.2.2
Author:       Rhys Wynne
Author URI:   http://winwar.co.uk/

*/

define("CSEVOM_PLUGIN_NAME","Check Search Engine Visibility on Migration");
define('CSEVOM_PLUGIN_TAGLINE',__('Checks if a site has been migrated and if it\'s inivisible to search engines. If so, then the site warns you on this and notifies you to act.','csevom'));
define("CSEVOM_PLUGIN_URL","http://winwar.co.uk/plugins/check-search-engine-visibility-migration/");
define("CSEVOM_EXTEND_URL","http://wordpress.org/plugins/check-search-engine-visibility-on-migration/");
define("CSEVOM_AUTHOR_TWITTER","rhyswynne");
define("CSEVOM_DONATE_LINK","https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=B7ZFLNM5KMP9C");

/* ==== ADMIN FUNCTIONS ==== */


/* THESE ARE THE ACTIONS THAT ARE CALLED WHENEVER THE ADMIN IS RUN */
if ( is_admin() ){ // admin actions

  add_action('admin_menu', 'csevom_menus');
  add_action('admin_init', 'csevom_process' );
  add_action('admin_init', 'csevom_add_admin_stylesheet' );
  
}

function csevom_add_admin_stylesheet() {
        wp_register_style( 'csevom-admin-style', plugins_url('csevom-admin.css', __FILE__) );
        wp_enqueue_style( 'csevom-admin-style' );
}


/* THIS FUNCTION CREATES THE MENU IN THE "SETTINGS" SECTION OF WORDPRESS */
function csevom_menus() {

  add_options_page('Check/Set Domain Visibility', 'Check/Set Domain Visibility', 'manage_options', 'csevomoptions', 'csevom_options');

}

/* THIS FUNCTION CREATES THE OPTIONS PAGE WITH ALL OPTIONS */
function csevom_options() {
?>
        <div class="pea_admin_wrap">
                <div class="pea_admin_top">
                    <h1><?php echo CSEVOM_PLUGIN_NAME?> <small> - <?php echo CSEVOM_PLUGIN_TAGLINE?></small></h1>
                </div>
        
                <div class="pea_admin_main_wrap">
                    <div class="pea_admin_main_left">
                        <div class="pea_admin_signup">
                            <?php _e('Want to know about updates to this plugin without having to log into your site every time? Want to know about other cool plugins we\'ve made? Add your email and we\'ll add you to our very rare mail outs.','csevom'); ?>
        
                            <!-- Begin MailChimp Signup Form -->
                            <div id="mc_embed_signup">
                            <form action="http://gospelrhys.us1.list-manage.com/subscribe/post?u=c656fe50ec16f06f152034ea9&amp;id=d9645e38c2" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
                            <div class="mc-field-group">
                                <label for="mce-EMAIL"> <?php _e('Email Address','csevom'); ?>
                            </label>
                                <input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL"><button type="submit" name="subscribe" id="mc-embedded-subscribe" class="pea_admin_green">Sign Up!</button>
                            </div>
                                <div id="mce-responses" class="clear">
                                    <div class="response" id="mce-error-response" style="display:none"></div>
                                    <div class="response" id="mce-success-response" style="display:none"></div>
                                </div>	<div class="clear"></div>
                            </form>
                            </div>
                            <!--End mc_embed_signup-->

                        </div>
                    <?php $visibility = get_option('blog_public'); ?>
                    <?php $searchurl = "https://www.google.com/search?q=site:".urlencode(get_option('home'));?>
                    <?php $readingpageurl = get_admin_url('', 'options-reading.php'); ?>
                    <?php if ($visibility == 1)
                    { ?>
                     
                    <h2 class="csevomsuccess"><?php _e('Good News!','csevom'); ?></h2>
                    <p><?php _e('As per your <a href="'.$readingpageurl.'">reading page settings</a>, the site is visible to search engines. <a href="'.$searchurl.'">Please check in a search engine whether this is true</a>, as other aspects (such as robots.txt) could block your site.','csevom'); ?></p>
                    <p><?php _e('But it is all good! Feel free to close this window or return to the WordPress Dashboard','csevom'); ?></p>
                    <?php } else { ?>
                            <form method="post" action="options.php" id="options">
                            
                            <?php wp_nonce_field('update-options'); ?>
                            <?php settings_fields( 'csevom-group' ); ?>
                            <?php $oldurl = get_option('csevom-oldurl'); 
                            if ($oldurl)
                            {
                               $oldurl = strrev($oldurl);
                            }
                            $currenturl = strrev(get_option('home')); ?>
                            
                            <table class="form-table">
                                <tbody>
        
                                <tr valign="top">
                                    <th scope="row" style="width:250px"><?php _e('Current WordPress Search Engine site visibility.','csevom'); ?></th>
                                    <td class="csevomdanger"><?php _e('Invisible','csevom'); ?></td>
                                </tr>
        
                                <tr valign="top">
                                    <th scope="row" style="width:250px"><?php _e('Current WordPress URL','csevom'); ?></th>
                                    <td>
                                    <strong><?php echo get_option('home'); ?></strong><br />
                                    <?php _e('This is the option as in WordPress settings.','csevom'); ?>
                                    </td>
                                </tr>
                                
                                <?php if ($oldurl) { ?>
                                    <tr valign="top">
                                        <th scope="row" style="width:250px"><?php _e('Last known WordPress URL for this installation','csevom'); ?></th>
                                        <td><strong><?php echo $oldurl; ?></strong></td>
                                    </tr>
                                    
                                    <?php
                                     
                                     
                                        if ($oldurl == get_option('home'))
                                        {
                                        ?>
                                        <tr valign="top">
                                            <td colspan="2">
                                            <p><?php _e('There is no difference between the URL of the blog and the URL that the plugin knows about. This means that you are aware that this URL is invisbile to search engines. This is <em>probably</em> a development URL, and shouldn\'t be visible in search engines.','csevom'); ?></p>
                                            <p><?php _e('If this <strong>isn\'t</strong> the case then head to the <a href="'.$readingpageurl.'">reading settings</a> page and untick the "Discourage search engines from indexing this site" box.','csevom'); ?></p>
                                            </td>
                                        </tr>
                                    <?php } else { ?>
                                    <tr valign="top">
                                    <td class="csevomdanger" colspan="2">
                                        <p><?php _e('The previous known URL is different from the home URL!','csevom'); ?></p>
                                        <p><?php _e('This <em>probably</em> means you have migrated from a developmental site to a live site. More than likely you want to now allow search engines. Go to the <a href="'.$readingpageurl.'">reading settings</a> page and untick the "Discourage search engines from indexing this site" box.','csevom'); ?></p>
                                    </td>
                                    </tr>
                                    <?php }
                                } else {
                                ?>    
                                <tr valign="top">
                                    <td class="csevomdanger" colspan="2">
                                    <p><?php _e('We have no previous URL recorded.','csevom'); ?></p>
                                    <p><?php _e('This occurs as you have just installed the plugin and you are set to be invisible to search engines in WordPress. This is fine, if this is what you intended, then click "Save Changes". We will monitor the site and notify you of any changes.','csevom'); ?></p>
                                    <p><?php _e('If this <strong>isn\'t</strong> the case then head to the <a href="'.$readingpageurl.'">reading settings</a> page and untick the "Discourage search engines from indexing this site" box.','csevom'); ?></p>
                                    </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
        
                            <input type="hidden" name="action" value="update" />
                            <input type="hidden" name="csevom-oldurl" value="<?php echo $currenturl; ?>" />
                            <p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Changes','csevom'); ?>" /></p>
                
                            </form>
                    <?php } ?>
                    </div>
                </div>
                
                <div class="pea_admin_main_right">
                    <div class="pea_admin_box">
                    
                        <h2><?php _e('Like this Plugin?','csevom'); ?></h2>
                        <a href="<?php echo CSEVOM_EXTEND_URL; ?>" target="_blank"><button type="submit" class="pea_admin_green"><?php _e('Rate this plugin','csevom'); ?>	&#9733;	&#9733;	&#9733;	&#9733;	&#9733;</button></a><br><br>
                        
                        <div id="fb-root"></div>
                        
                        <script>(function(d, s, id) {
                                var js, fjs = d.getElementsByTagName(s)[0];
                                if (d.getElementById(id)) return;
                                js = d.createElement(s); js.id = id;
                                js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId=181590835206577";
                                fjs.parentNode.insertBefore(js, fjs);
                            }(document, 'script', 'facebook-jssdk'));
                        </script>
                        
                        <div class="fb-like" data-href="<?php echo CSEVOM_PLUGIN_URL; ?>" data-send="true" data-layout="button_count" data-width="250" data-show-faces="true"></div>
                        <br>
                        <a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo CSEVOM_PLUGIN_URL; ?>" data-text="Just been using <?php echo CSEVOM_PLUGIN_NAME; ?> #WordPress plugin" data-via="<?php echo CSEVOM_AUTHOR_TWITTER; ?>" data-related="WPBrewers">Tweet</a>
                        
                        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                        
                        <br>
                        <a href="http://bufferapp.com/add" class="buffer-add-button" data-text="Just been using <?php echo CSEVOM_PLUGIN_NAME; ?> #WordPress plugin" data-url="<?php echo CSEVOM_PLUGIN_URL; ?>" data-count="horizontal" data-via="<?php echo CSEVOM_AUTHOR_TWITTER; ?>">Buffer</a><script type="text/javascript" src="http://static.bufferapp.com/js/button.js"></script>
                        
                        <br>
                        <div class="g-plusone" data-size="medium" data-href="<?php echo CSEVOM_PLUGIN_URL; ?>"></div>
                        
                        <script type="text/javascript">
                            window.___gcfg = {lang: 'en-GB'};

                            (function() {
                                var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                                po.src = 'https://apis.google.com/js/plusone.js';
                                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
                            })();
                        </script>

                        <br>
 
                        <su:badge layout="3" location="<?php echo CSEVOM_PLUGIN_URL?>"></su:badge>

                        <script type="text/javascript">
                          (function() {
                            var li = document.createElement('script'); li.type = 'text/javascript'; li.async = true;
                            li.src = ('https:' == document.location.protocol ? 'https:' : 'http:') + '//platform.stumbleupon.com/1/widgets.js';
                            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(li, s);
                          })();
                        </script>
                    </div>

                    <center><a href="<?php echo CSEVOM_DONATE_LINK; ?>" target="_blank"><img class="paypal" src="<?php echo plugins_url( 'paypal.gif' , __FILE__ ); ?>" width="147" height="47" title="Please Donate - it helps support this plugin!"></a></center>

                <div class="pea_admin_box">
                    <h2><?php _e('About the Author','csevom'); ?></h2>

                    <?php
                    $default = "http://reviews.evanscycles.com/static/0924-en_gb/noAvatar.gif";
                    $size = 70;
                    $rhys_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( "rhys@rhyswynne.co.uk" ) ) ) . "?d=" . urlencode( $default ) . "&s=" . $size;
                    ?>

                    <p class="pea_admin_clear"><img class="pea_admin_fl" src="<?php echo $rhys_url; ?>" alt="Rhys Wynne" /> <h3>Rhys Wynne</h3><br><a href="https://twitter.com/rhyswynne" class="twitter-follow-button" data-show-count="false">Follow @rhyswynne</a>
                    <div class="fb-subscribe" data-href="https://www.facebook.com/rhysywynne" data-layout="button_count" data-show-faces="false" data-width="220"></div>
                    </p>
                    
                    <p class="pea_admin_clear"><?php _e('Rhys Wynne is the Lead WordPress Developer at FireCask and a freelance WordPress developer and blogger. His plugins have had a total of 100,000 downloads, and his premium plugins have generated four figure sums in terms of sales. Rhys likes rubbish football (supporting Colwyn Bay FC) and Professional Wrestling.','csevom'); ?></p>
                
                </div>

            </div>
    </div>
    
<?php

}

/* THIS FUNCTION SAVES THE OPTIONS FROM THE PREVIOUS FUNCTION */
function csevom_process() { // whitelist options

  register_setting( 'csevom-group', 'csevom-oldurl' );

}

add_action( 'admin_notices', 'csevom_admin_notice' );

function csevom_admin_notice() {
    $visibility = get_option('blog_public'); 
    
    if ($visibility == 0)
    {
        $oldurl = strrev(get_option('csevom-oldurl')); 
        $readingpageurl = get_admin_url('', 'options-reading.php');
        $optionspageurl = get_admin_url('', 'options-general.php?page=csevomoptions');
            if ($oldurl)
            {
                if ($oldurl != get_option('home'))
                {
                ?>
                    <div class="error">
                        <p><?php _e('This blog is invisible to search engines and we are not sure this is the developmental URL.','csevom'); ?></p>
                        <p><?php _e('If this blog <strong>should</strong> be visible to search engines, go to the <a href="'.$readingpageurl.'">reading settings</a> page and untick the "Discourage search engines from indexing this site" box.','csevom'); ?></p>
                        <p><?php _e('If this blog <strong>should not</strong> be visible to search engines, go to the <a href="'.$optionspageurl.'">Check/Set Domain Visibility</a> page and save settings. We won\'t bother you unless something changes :).','csevom'); ?></p>
                    </div>                    
                <?php       
                }
            } else {
                ?>
                <div class="error">
                    <p><?php _e('This blog is invisible to search engines and we have no record whether this domain is your developmental URL.','csevom'); ?></p>
                    <p><?php _e('If this blog <strong>should</strong> be visible to search engines, go to the <a href="'.$readingpageurl.'">reading settings</a> page and untick the "Discourage search engines from indexing this site" box.','csevom'); ?></p>
                    <p><?php _e('If this blog <strong>should not</strong> be visible to search engines, go to the <a href="'.$optionspageurl.'">Check/Set Domain Visibility</a> page and save settings. We won\'t bother you unless something changes :).','csevom'); ?></p>
                </div>
                <?php                
            }   
    ?>
    
    <?php
    }
}

?>
