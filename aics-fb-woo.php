<?php defined( 'ABSPATH' ) or die( 'nope.. just nope' );
/*
Plugin Name: Woocommerce Facebook Intergration. by AiCS
Plugin URI: http://www.aics.my/
Description: custom developed plugin by AiCS
Author: FAiZ
Version: 1.0
*/

$aics_fb_path = plugin_dir_path( __FILE__ );
$aics_fb_db_version = '1.0';

function aicsfb_initiate_install(){
    global $wpdb;
    
    $table_name = $wpdb->prefix.'aics_fb_login';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
    id int(11) NOT NULL AUTO_INCREMENT,
    fbid text NULL,
    fbemail varchar(255) NULL,
    fullname text NULL,
    user_id int(11) NULL,
    access_token text NULL,
    created_at datetime DEFAULT '0000-00-00 00:00:00' NULL,
    UNIQUE KEY id (id)
  ) $charset_collate;";
  
  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );
  update_option( 'aics_fb_dbversion', $aics_fb_db_version );
}
register_activation_hook( __FILE__, 'aicsfb_initiate_install' );

require_once($aics_fb_path.'vendor/autoload.php');

if(!function_exists('aidebug')):
    function aidebug($var){
        echo'<pre>'.print_r($var, true).'</pre>';
    }
endif;

include_once($aics_fb_path.'lib/init.php');
include_once($aics_fb_path.'admin/init.php');
include_once($aics_fb_path.'front/init.php');