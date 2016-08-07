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
    if(isset($_GET['code']) && !empty($_GET['code'])):
        $aifb = new AicsFbClass();
        $aifb->processFbResponse();
        $aifb->getFbUserData();
    endif;
}
add_action('init', 'process_facebook_login');