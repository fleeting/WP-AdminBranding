<?php 
/*
Plugin Name: WP-AdminBranding
Version: 0.1
Plugin URI: http://paperkilledrock.com/projects/WP-AdminBranding
Description: Allows you to remove and/or replace WordPress mentions and logos in the Admin Area. This does NOT let you theme wp-admin but simply lets you replace the logo with your own. Perfect for clients or sites that lets users register but don't want the WordPress logo on wp-login.php.
Author: James Fleeting
Author URI: http://jamesfleeting.com/
*/

/*  Copyright 2009  James Fleeting (james.fleeting[at]gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//lets go ahead and define a few things for easy updating...
define(WPTS_CURRENT_VERSION, "0.1");
define(WPTS_PLUGIN_URL, "http://paperkilledrock.com/projects/WP-AdminBranding");
global $wp_version;



class WPAdminBranding {

  function WPAdminBranding() {
    //guess we should add some wordpress hooks and actions
    register_activation_hook(__FILE__, array( &$this, 'set_wpadminbranding_options'));
    register_deactivation_hook(__FILE__, array( &$this, 'unset_wpadminbranding_options'));
    
    if (is_admin()) {
      add_action('admin_menu', array( &$this, 'wp_wpadminbranding_menu')); 
    }
    add_action('admin_head', array( &$this, 'wp_adminbranding'));
    add_action('login_head', array( &$this, 'wp_loginbranding'));   
  }

  //on plugin activation create options in db
  function set_wpadminbranding_options() {
    add_option('hide_header_branding', '');
    add_option('hide_footer_branding', '');
    add_option('hide_login_branding', '');
    add_option('hide_wp_updatenotice', '');
    add_option('hide_plugin_updatenotice', '');
    add_option('wpab_small_logo', '');
    add_option('wpab_large_logo', '');
  }
  
  //on plugin deactivation delete options from db
  function unset_wpadminbranding_options() {
    delete_option('hide_header_branding', '');
    delete_option('hide_footer_branding', '');
    delete_option('hide_login_branding', '');
    delete_option('hide_wp_updatenotice', '');
    delete_option('hide_plugin_updatenotice', '');
    delete_option('wpab_small_logo', '');
    delete_option('wpab_large_logo', '');
  }
  
  //create menu items
  function wp_wpadminbranding_menu() {
    #add_menu_page('WP-AdminBranding Options', 'Branding', 8, __FILE__, array( &$this, 'wp_adminbranding_options'), plugins_url('wp-adminbranding/wpadminbranding_18.png'));
    add_options_page('WP-AdminBranding Options', 'Admin Branding', 8, __FILE__, array( &$this, 'wp_adminbranding_options'));
  }  
  
  function set_wpadminbranding_meta($links, $file) {
  	$plugin = plugin_basename(__FILE__);
  	// create link
  	if ($file == $plugin) {
  		return array_merge(
  			$links,
  			array( sprintf( '<a href="options-general.php?page=%s">%s</a>', $plugin, __('Settings') ) )
  		);
  	}
  	return $links;
  }

  //user defined options (values are stored in database in wp_options)
  function wp_adminbranding_options() {
?>
    
    <div class="wrap">
      <h2>WP-AdminBranding Options</h2>
        
        <form method="post" action="options.php">
        <?php wp_nonce_field('update-options'); ?>

          <table class="form-table">            
            <tr valign="top">
              <th scope="row">What to hide?</th>
              <td>
                <fieldset>
                  <legend class="screen-reader-text"><span>Branding to hide</span></legend>
                  <label for="hide_header_branding">
                    <input name="hide_header_branding" type="checkbox" id="hide_header_branding" value="1" <?php if (get_option('hide_header_branding') == '1') { echo 'checked="checked"'; } ?> /> The WP logo next to blog name in top left corner of admin.
                  </label><br />
                    
                  <label for="hide_footer_branding">
                    <input name="hide_footer_branding" type="checkbox" id="hide_footer_branding" value="1" <?php if (get_option('hide_footer_branding') == '1') { echo 'checked="checked"'; } ?> /> The WP message and links in the footer of the admin.
                  </label><br />
                  
                  <label for="hide_login_branding">
                    <input name="hide_login_branding" type="checkbox" id="hide_login_branding" value="1" <?php if (get_option('hide_login_branding') == '1') { echo 'checked="checked"'; } ?> /> The WP logo on the login page.
                  </label><br />
                  
                  <label for="hide_wp_updatenotice">
                    <input name="hide_wp_updatenotice" type="checkbox" id="hide_wp_updatenotice" value="1" <?php if (get_option('hide_wp_updatenotice') == '1') { echo 'checked="checked"'; } ?> /> <strong>*</strong> The WP needs an update notice.
                  </label><br />
                  
                  <label for="hide_plugin_updatenotice">
                    <input name="hide_plugin_updatenotice" type="checkbox" id="hide_plugin_updatenotice" value="1" <?php if (get_option('hide_plugin_updatenotice') == '1') { echo 'checked="checked"'; } ?> /> <strong>*</strong> The notice that a plugin needs updating.
                  </label><br />                  
                </fieldset>
                
                <p style="font-size:10px;">* Will continue to show for Admins only, hides it from all other users.</p>
              </td>
            </tr>
            
            <tr valign="top">
              <th scope="row">Small Logo (65 x 66px) [not implemented]</th>
              <td><input disabled="disabled" type="text" name="wpab_small_logo" value="<?php echo get_option('wpab_small_logo'); ?>" /></td>
            </tr>
            
            <tr valign="top">
              <th scope="row">Full Size Logo (310 x 70px) [not implemented]</th>
              <td><input disabled="disabled" type="text" name="wpab_large_logo" value="<?php echo get_option('wpab_large_logo'); ?>" /></td>
            </tr>
          </table>

          <input type="hidden" name="action" value="update" />
          <input type="hidden" name="page_options" value="hide_header_branding,hide_footer_branding,wpab_small_logo,wpab_large_logo,hide_login_branding,hide_wp_updatenotice,hide_plugin_updatenotice" />

          <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
          </p>
        </form>
    </div>

<?php   
  } //wp_adminbranding_options
  
  // lets brand wp-admin
  function wp_adminbranding() { ?>
    <style>
      /* WP-AdminBranding Over-ride CSS */
      <?php if (get_option('hide_header_branding') == 1) { ?>
        #header-logo {display:none;}
      <?php } ?>
      
      <?php if (get_option('hide_footer_branding') == 1) { ?>
        #footer-left {display:none;}
      <?php } ?>
      
      <?php if (!is_admin()) { ?>
        <?php if (get_option('hide_wp_updatenotice') == 1) { ?>
          #update-nag {display:none;}
        <?php } ?>
        
        <?php if (get_option('hide_plugin_updatenotice') == 1) { ?>
          .plugin-update-tr, .update-plugins {display:none;}
        <?php } ?>
        
        #footer-upgrade {display:none;}
      <?php } ?>
    </style>
  <?php } //wp_adminbranding
  
  // now lets brand wp-login
  function wp_loginbranding() { ?>
    <style>
      /* WP-AdminBranding Over-ride CSS */
      <?php if (get_option('hide_login_branding') == 1) { ?>
        #login h1 {display: none;}
        <?php if (get_option('wpab_large_logo')) { ?>
          #login h1 a {
            background: none;
            text-indent: 0; 
            text-align: center; 
            text-decoration:none;}
        <?php } else { ?>
          #login h1 a {background: none;text-indent: 0; text-align: center; text-decoration:none;}
        <?php } ?>
      <?php } ?>
    </style>
  <?php } //wp_adminbranding

} //WPAdminBranding

$wpAdminBranding = new WPAdminBranding;
?>