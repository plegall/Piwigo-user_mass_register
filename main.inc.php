<?php 
/*
Plugin Name: User Mass Register
Version: auto
Description: Register several users at once
Plugin URI: auto
Author: plg
Author URI: http://le-gall.net/pierrick
*/

defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

global $prefixeTable;

// +-----------------------------------------------------------------------+
// | Define plugin constants                                               |
// +-----------------------------------------------------------------------+

defined('UMR_ID') or define('UMR_ID', basename(dirname(__FILE__)));
define('UMR_PATH' ,   PHPWG_PLUGINS_PATH . UMR_ID . '/');
define('UMR_ADMIN',   get_root_url() . 'admin.php?page=plugin-' . UMR_ID);

// +-----------------------------------------------------------------------+
// | Add event handlers                                                    |
// +-----------------------------------------------------------------------+

// admin plugins menu link
add_event_handler('get_admin_plugin_menu_links', 'umr_admin_plugin_menu_links');

/**
 * admin plugins menu link
 */
function umr_admin_plugin_menu_links($menu) 
{
  array_push(
    $menu,
    array(
      'NAME' => 'User Mass Register',
      'URL' => UMR_ADMIN,
      )
    );
  
  return $menu;
}
?>