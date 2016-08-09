<?php defined( 'ABSPATH' ) or die( 'nope.. just nope' );

function ai_init_fb_admin(){
    add_menu_page('Facebook Login', 'Facebook Login','manage_options','ai-fb-login','ai_fb_mainpage','dashicons-facebook');
    add_submenu_page('ai-fb-login', 'User List', 'User List', 'manage_options', 'ai-fb-users', 'ai_fb_userlistPage');
}
add_action('admin_menu','ai_init_fb_admin');

// admin pages
function ai_fb_mainpage(){
    global $aics_fb_path;
    include_once($aics_fb_path.'admin/templates/main.php');
}
function ai_fb_userlistPage(){
    global $aics_fb_path;
    include_once($aics_fb_path.'admin/templates/user-list.php');
}
// end admin pages

// include fontawesome only in admin.
function include_fawesome(){
    wp_enqueue_style('ai-fb-fico', '//maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css');
}
add_action('wp_enqueue_scripts','include_fawesome');


function process_aics_fbsetting(){
    if(isset($_POST['aics_fbsetting']) && $_POST['aics_fbsetting'] == '1'):
        $facebook_app_id = $_POST['facebook_app_id'];
        $facebook_secret = $_POST['facebook_secret'];
        //$fb_setting = getFbAppSetting();
        update_option('aics_fb_app_id', $facebook_app_id);
        update_option('aics_fb_secret', $facebook_secret);
        
        wp_redirect(admin_url('admin.php?page=ai-fb-login&success=1'));
    endif;
}
add_action('admin_post_process_fb_aics_setting','process_aics_fbsetting');

function getFbAppSetting(){
    $app_id = get_option('aics_fb_app_id');
    $app_secret = get_option('aics_fb_secret');
    
    return array('id'=>$app_id,'secret'=>$app_secret);
}

function textFbLogin(){
    if(isset($_GET['code']) && !empty($_GET['code'])):
        $aifb = new AicsFbClass();
        $aifb->processFbResponse();
        $aifb->getFbUserData();
    else:
        $aifb = new AicsFbClass();
        $aifb->displayLoginButton();
    endif;
    
}

function getFbLoggedinUsers(){
    global $wpdb;
    return $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'aics_fb_login ORDER BY created_at DESC');
}