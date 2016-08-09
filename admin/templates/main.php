<?php defined( 'ABSPATH' ) or die( 'nope.. just nope' );
$sttings = getFbAppSetting();
?>
<div class="wrap">
    <?php if(isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="updated"><p>Settings Updated.</p></div>
    <?php endif; ?>
    <h1>Facebook Login Plugin</h1><hr>
    <form action="admin-post.php" method="POST">
        <input type="hidden" name="action" value="process_fb_aics_setting">
        <input type="hidden" name="aics_fbsetting" value="1">
        <table class="form-table">
            <tr>
                <td><i class="fa fa-facebook"></i> App ID</td>
                <td><input type="text" name="facebook_app_id" value="<?php echo $sttings['id']; ?>"></td>
            </tr>
            <tr>
                <td><i class="fa fa-facebook"></i> App Secret</td>
                <td><input type="text" name="facebook_secret" value="<?php echo $sttings['secret']; ?>"></td>
            </tr>
            <tr>
                <td colspan="2"><i class="fa fa-facebook"></i> App ID & App Secret can be generated on <a href="http://developers.facebook.com" target="_blank">http://developers.facebook.com</a></td>
            </tr>
            <tr>
                <td colspan="2">
                    <button class="button button-primary">Save Setting</button>
                </td>
            </tr>
        </table>
    </form>
    
    <?php
    if(isset($sttings['id']) && !empty($sttings['id'])):
    textFbLogin();
    endif;
    ?>
</div>