<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>     
<div class="wrap">
<?php $fm_nonce = wp_create_nonce( 'wp-file-manager' ); ?>
<script>
var security_key = "<?php echo $fm_nonce;?>";
</script>
<?php
$this->load_custom_assets();
$this->load_help_desk();?>
<h2><img src="<?php echo plugins_url( 'images/wp_file_manager.png', dirname(__FILE__) ); ?>"><?php  _e(' WP File Manager', 'ffm'); ?> <a href="http://filemanager.webdesi9.com/product/file-manager" class="button button-primary" target="_blank" title="Click to Buy PRO"><?php  _e('Buy PRO', 'ffm'); ?></a></h2>
<div id="wp_file_manager"></div>

<?php /* Donation Form */ ?>
<div id="submitdiv" class="postbox" style="padding: 6px; margin-top:20px; border-left: 5px solid #0073aa;">
<hr />
    <h3><strong style="color:#c15454"><?php _e('Please contribute some donation, to make plugin more stable. You can pay amount of your choice.', 'ffm')?></strong></h3>
    <form name="_xclick" action="https://www.paypal.com/yt/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_xclick">
    <input type="hidden" name="business" value="mandeep.singh@mysenseinc.com">
    <input type="hidden" name="item_name" value="WP File Manager - Donation">
    <input type="hidden" name="currency_code" value="USD">
    <table style="text-align:center">
<tbody>
<tr>
<th scope="row"><label for="default_email_category"><code>$</code></label></th>
<td>
 <input type="text" name="amount" value="" required="required" placeholder="Enter amount" class="regular-text ltr">
</td>
<td>
 <input type="image" src="http://www.paypal.com/en_US/i/btn/x-click-butcc-donate.gif" border="0" name="submit" alt="Make Donations with Paypal">
</td>
</tr>
</tbody></table>    
    </form>
      <hr />
    </div>    
<?php /* End Donation Form */ ?>
</div>