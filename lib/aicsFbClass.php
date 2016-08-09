<?php defined( 'ABSPATH' ) or die( 'nope.. just nope' );

class AicsFbClass {
    private $appID;
    private $appSecret;
    public $fb;
    public $enabled;
    public function __construct(){
        $this->enabled = 'no';
        if(!session_start()):
            session_start();
        endif;
        $settings = getFbAppSetting();
        $this->appID = $settings['id'];
        $this->appSecret = $settings['secret'];
        
        if(!empty($settings['secret'])):
            $this->initializeFb();
            $this->enabled = 'yes';
        endif;
    }
    
    public function initializeFb(){
        $this->fb = new Facebook\Facebook([
                                     'app_id' => $this->appID,
                                     'app_secret' => $this->appSecret,
                                     'default_graph_version' => 'v2.6',
                                     //'default_access_token' => '{access-token}', // optional
                                    ]);
    }
    
    public function displayLoginButton($extra_class = ''){
        if($this->enabled == 'yes'):
            $helper = $this->fb->getRedirectLoginHelper();
            $permissions = ['email','public_profile']; // Optional permissions
            $wp_login_url = $this->determineLoginPageLanding();
            $loginUrl = $helper->getLoginUrl($wp_login_url, $permissions);
            return '<a href="' . htmlspecialchars($loginUrl) . '" class="'.$extra_class.'"><i class="fa fa-facebook"></i> Log in with Facebook!</a>';
        else:
            return '<i class="fa fa-facebook"></i><i>acebook login is installed but not setup properly.</i>';
        endif;
    }
    
    public function determineLoginPageLanding(){
        if(class_exists( 'WooCommerce' )):
            return WC_Cart::get_checkout_url();
        else:
            return wp_login_url( home_url() );
        endif;
    }
    
    public function processFbResponse(){
        if($this->enabled == 'yes'):
            $helper = $this->fb->getRedirectLoginHelper();
            try {
                $accessToken = $helper->getAccessToken();
                setcookie("aicsfb_cookie_token", $accessToken, time()+3600);
                $_SESSION['aifb_atoken'] = $accessToken;
              } catch(Facebook\Exceptions\FacebookResponseException $e) {
                // When Graph returns an error
                echo 'Graph returned an error: ' . $e->getMessage();
                exit;
              } catch(Facebook\Exceptions\FacebookSDKException $e) {
                // When validation fails or other local issues
                echo 'Facebook SDK returned an error: ' . $e->getMessage();
                exit;
              }
              
            // The OAuth 2.0 client handler helps us manage access tokens
            $oAuth2Client = $this->fb->getOAuth2Client();
        endif;
        
    }
    
    public function getFbUserData(){
        if($this->enabled == 'yes'):
            global $wpdb;
            try {
                // Returns a `Facebook\FacebookResponse` object
                $response = $this->fb->get('/me?fields=id,name,email', $_SESSION['aifb_atoken']);
            } catch(Facebook\Exceptions\FacebookResponseException $e) {
                echo 'Graph returned an error: ' . $e->getMessage(); exit;
            } catch(Facebook\Exceptions\FacebookSDKException $e) {
                echo 'Facebook SDK returned an error: ' . $e->getMessage(); exit;
            }
            
            $user = $response->getGraphUser();
            //$arr = array('fbid'=>$user['id'],'fbname'=>$user['name'],'fbemail'=>$user['email']);
            //aidebug($arr);
            
            $check_email = $this->checkUserEmail($user['email']);
            if($check_email == false):
                // if not yet registered. we regiter.
                $username = $this->generateUsernameFromEmail($user['email']);
                $user_id = register_new_user($username,$user['email']);
                $arr_fbinsert = array(
                                      'fbid'=>$user['id'],
                                      'fbemail'=>$user['email'],
                                      'user_id'=>$user_id,
                                      'fullname'=>$user['name'],
                                      'created_at'=>date('YmdHis'));
                $wpdb->insert($wpdb->prefix.'aics_fb_login', $arr_fbinsert);
            else:
                // already registered. we login.
                $userdata = $this->getUserIdFromEmail($user['email']);
                $user = get_user_by( 'id', $userdata['id'] );
                //aidebug($user); exit;
                if($user):
                     wp_set_current_user($user->ID, $user->user_login);
                     wp_set_auth_cookie( $user->ID );
                     do_action( 'wp_login', $user->user_login );
                endif;
            endif;
        endif;
    }
    
    public function generateUsernameFromEmail($mail){
        $parts = explode("@", $mail);
        return $parts[0];
    }
    
    /* return true if user exist in db */
    public function checkUserEmail($email){
        global $wpdb;
        $user = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'users WHERE user_email = "'.$email.'"');
        if(isset($user->user_email) && $user->user_email == $email):
            return true;
        else:
            return false;
        endif;
    }
    
    public function getUserIdFromEmail($email){
        global $wpdb;
        $user = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'users WHERE user_email = "'.$email.'"');
        return array('id'=>$user->ID,'user_login'=>$user->user_login);
    }
    
}