<div class="content-box content-wpmf-files-folders">
    <div class="cboption">
        <div class="wpmf_row_full">
            <input type="hidden" name="wpmf_option_media_remove" value="0">
            <label alt="<?php _e('When you remove a folder all media inside will also be removed if this option is activated. Use with caution.', 'wpmf'); ?>" class="text"><?php _e('Remove a folder with its media', 'wpmf') ?></label>
            <div class="switch-optimization">
                <label class="switch switch-optimization">
                    <input type="checkbox" id="cb_option_media_remove" name="wpmf_option_media_remove" value="1" <?php if (isset($option_media_remove) && $option_media_remove == 1) echo 'checked' ?>>
                    <div class="slider round"></div>
                </label>
            </div>
        </div>
    </div>

    <div class="cboption">
        <div class="wpmf_row_full">
            <input type="hidden" name="wpmf_media_rename" value="0">
            <label alt="<?php _e('Tag avaiable: {sitename} - {foldername} - {date} - {original name} . Note: # will be replaced by increasing numbers', 'wpmf') ?>" class="text"><?php _e('Activate media rename on upload', 'wpmf') ?></label>
            <div class="switch-optimization">
                <label class="switch switch-optimization">
                    <input type="checkbox" name="wpmf_media_rename" value="1" <?php if (isset($wpmf_media_rename) && $wpmf_media_rename == 1) echo 'checked' ?>>
                    <div class="slider round"></div>
                </label>
            </div>
        </div>
        <div class="wpmf_row_full">
            <label alt="<?php _e('Tag avaiable: {sitename} - {foldername} - {date} - {original name} . Note: # will be replaced by increasing numbers', 'wpmf') ?>"><?php _e('Patern', 'wpmf') ?></label>
            <input type="text" name="wpmf_patern" id="wpmf_patern" class="regular-text" value="<?php echo $wpmf_patern; ?>">
        </div>
    </div>

    <div class="cboption">
        <h3><?php _e('Format Media Titles', 'wpmf'); ?></h3>
        <div class="wpmf_row_full">
            <label alt="<?php _e('Remove characters automatically on media upload', 'wpmf'); ?>" class="text"><?php _e('Remove Characters', 'wpmf') ?></label>
            <div style="float: left">
                <input type="hidden" name="wpmf_options_format_title[hyphen]" value="0">
                <div class="pure-checkbox">
                    <input id="wpmf_hyphen" type="checkbox" name="wpmf_options_format_title[hyphen]" <?php checked($opts_format_title['hyphen'],1) ?> value="1">
                    <label for="wpmf_hyphen"><?php _e('Hyphen','wpmf') ?> (-)</label>
                </div>

                <div class="pure-checkbox">
                    <input type="hidden" name="wpmf_options_format_title[underscore]" value="0">
                    <input id="wpmf_underscore" type="checkbox" name="wpmf_options_format_title[underscore]" <?php checked($opts_format_title['underscore'],1) ?> value="1">
                    <label for="wpmf_underscore"><?php _e('Underscore','wpmf') ?> (_)</label>
                </div>

                <div class="pure-checkbox">
                    <input type="hidden" name="wpmf_options_format_title[period]" value="0">
                    <input id="wpmf_period" type="checkbox" name="wpmf_options_format_title[period]" <?php checked($opts_format_title['period'],1) ?> value="1">
                    <label for="wpmf_period"><?php _e('Period','wpmf') ?> (.)</label>
                </div>

                <div class="pure-checkbox">
                    <input type="hidden" name="wpmf_options_format_title[tilde]" value="0">
                    <input id="wpmf_tilde" type="checkbox" name="wpmf_options_format_title[tilde]" <?php checked($opts_format_title['tilde'],1) ?> value="1">
                    <label for="wpmf_tilde"><?php _e('Tilde','wpmf') ?> (~)</label>
                </div>

                <div class="pure-checkbox">
                    <input type="hidden" name="wpmf_options_format_title[plus]" value="0">
                    <input id="wpmf_plus" type="checkbox" name="wpmf_options_format_title[plus]" <?php checked($opts_format_title['plus'],1) ?> value="1">
                    <label for="wpmf_plus"><?php _e('Plus','wpmf') ?> (+)</label>
                </div>
            </div>
        </div>

        <div class="wpmf_row_full">
            <label alt="<?php _e('Automatic media information completion on upload', 'wpmf'); ?>" class="text"><?php _e('Other options', 'wpmf') ?></label>
            <div style="float: left">
                <div class="pure-checkbox">
                    <input type="hidden" name="wpmf_options_format_title[alt]" value="0">
                    <input id="wpmf_alt" type="checkbox" name="wpmf_options_format_title[alt]" <?php checked($opts_format_title['alt'],1) ?> value="1">
                    <label for="wpmf_alt"><?php _e("Copy title to \'Alternative Text\' Field?","wpmf") ?> (-)</label>
                </div>

                <div class="pure-checkbox">
                    <input type="hidden" name="wpmf_options_format_title[caption]" value="0">
                    <input id="wpmf_caption" type="checkbox" name="wpmf_options_format_title[caption]" <?php checked($opts_format_title['caption'],1) ?> value="1">
                    <label for="wpmf_caption"><?php _e("Copy title to \'Caption\' Field?","wpmf") ?> (_)</label>
                </div>

                <div class="pure-checkbox">
                    <input type="hidden" name="wpmf_options_format_title[description]" value="0">
                    <input id="wpmf_description" type="checkbox" name="wpmf_options_format_title[description]" <?php checked($opts_format_title['description'],1) ?> value="1">
                    <label for="wpmf_description"><?php _e("Copy title to \'Description\' Field?","wpmf") ?> (.)</label>
                </div>
            </div>
        </div>

        <div class="wpmf_row_full">
            <label alt="<?php _e('Add capital letters automatically on media upload', 'wpmf'); ?>" class="text"><?php _e('Automatic capitalization', 'wpmf') ?></label>
            <div class="wpmf_rdo_cap">
                <label class="radio">
                    <input id="radio1" type="radio" name="wpmf_options_format_title[capita]" checked value="cap_all">
                    <span class="outer"><span class="inner"></span></span><?php _e('Capitalize All Words', 'wpmf'); ?></label>
                <label class="radio">
                    <input id="radio2" type="radio" name="wpmf_options_format_title[capita]" <?php checked($opts_format_title['capita'],'cap_first') ?> value="cap_first">
                    <span class="outer"><span class="inner"></span></span><?php _e('Capitalize First Word Only', 'wpmf'); ?></label>
                <label class="radio">
                    <input id="radio2" type="radio" name="wpmf_options_format_title[capita]" <?php checked($opts_format_title['capita'],'all_lower') ?> value="all_lower">
                    <span class="outer"><span class="inner"></span></span><?php _e('All Words Lower Case', 'wpmf'); ?></label>
                <label class="radio">
                    <input id="radio2" type="radio" name="wpmf_options_format_title[capita]" <?php checked($opts_format_title['capita'],'all_upper') ?> value="all_upper">
                    <span class="outer"><span class="inner"></span></span><?php _e('All Words Upper Case', 'wpmf'); ?></label>
                <label class="radio">
                    <input id="radio2" type="radio" name="wpmf_options_format_title[capita]" <?php checked($opts_format_title['capita'],'dont_alter') ?> value="dont_alter">
                    <span class="outer"><span class="inner"></span></span><?php _e("Don\'t Alter (title text isn\'t modified in any way)", "wpmf"); ?></label>
            </div>
        </div>
    </div>
</div>