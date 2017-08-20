<?php
$api_key = get_option('artbees_api_key');
$is_registered = !empty($api_key) ? '' : 'mka-call-to-register-product';
$mk_control_panel = new mk_control_panel();
$updates  = new Mk_Wp_Theme_Update();
$releases = $updates->get_release_note();
$latest_theme_package = $updates->get_theme_latest_package_url();
$check_latest_version = $updates->check_latest_version();
?>

<div class="mka-cp-pane-box <?php echo $is_registered; ?>" id="mk-cp-image-sizes">
    <div class="mka-cp-pane-title">
        <?php esc_html_e( 'Update', 'mk_framework' ); ?>
        <?php echo THEME_NAME; ?>
        <!-- <div class="mka-wrap mka-tip-wrap">
            <a class="mka-tip" href="#">
                <span class="mka-tip-icon">
                    <span class="mka-tip-arrow">
                    </span>
                </span>
                <span class="mka-tip-ripple">
                </span>
            </a>
            <div class="mka-tip-content">
                <?php esc_html_e('', 'mk_framework'); ?>
            </div>
        </div> -->
    </div>
    <!-- <div class="mka-cp-check-updates-wrap">
        <a class="mka-button mka-button--gray mka-button--small" href="#" id="js__check-for-update-btn">
            <?php esc_html_e( 'Check for Updates', 'mk_framework' ); ?>
        </a>
        <span class="mka-cp-last-checked">
            Last checked in March 12, 2017
        </span>
    </div> -->
    <div class="mka-cp-new-version-wrap">
        <div class="mka-cp-new-version-title">
            <span class="mka-cp-version-number"><?php echo str_replace('V', 'Version ', $releases->post_title); ?></span>
            <span class="mka-cp-version-date"><?php echo mysql2date( 'j F Y', $releases->post_date ); ?></span>
        </div>
        <div class="mka-cp-new-version-content">
            <?php echo $releases->post_content; ?>
        </div>
        <?php if(!empty($check_latest_version)) { ?>
            <a class="mka-button mka-button--green mka-button--small" href="<?php echo $updates->get_theme_update_url(); ?>" id="js__update-theme-btn">
                <?php esc_html_e( 'Update', 'mk_framework' ); ?>
            </a>
        <?php } ?>
        <?php if($latest_theme_package) { ?>
            <a class="mka-button mka-button--gray mka-button--small" target="_blank" href="<?php echo $latest_theme_package; ?>" id="js__download-theme-package-btn">
                <?php esc_html_e( 'Download', 'mk_framework' ); ?>
            </a>
        <?php } ?>
    </div>
</div>