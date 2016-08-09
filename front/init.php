<?php

function ai_login_button_fb(){
    $aifb = new AicsFbClass();
    echo $aifb->displayLoginButton('btn btn-primary');
}

function load_login_button_areas(){
    if(class_exists( 'WooCommerce' )):
        add_action('woocommerce_login_form_end', 'ai_login_button_fb');
    endif;
    //add_filter('login_form_bottom', 'login_button');
}
add_action('plugins_loaded', 'load_login_button_areas');

function process_facebook_login(){
    if(!isset($_SESSION['aifb_atoken']) && empty($_SESSION['aifb_atoken'])):
        session_start();
    endif;
    if(isset($_GET['code']) && !empty($_GET['code'])):
        $aifb = new AicsFbClass();
        $aifb->processFbResponse();
        $aifb->getFbUserData();
    endif;
}
add_action('init', 'process_facebook_login');

function after_logged_in(){
    if(isset($_SESSION['finished_logged_in']) && $_SESSION['finished_logged_in'] == 'yes'):
        global $post;
        $url = get_permalink($post->ID);
        $_SESSION['finished_logged_in'] = null;
        wp_redirect($url.'?successloginfb=yes');
    endif;
}
add_action('init','after_logged_in');

function if_denied(){
    //error=access_denied
    // coming soon
}