<div id="WpmfGoogleDrive"><div class="WpmfGoogleDrive files uyd-grid" data-list="files" data-layout="grid"><div class="list-container" style="width:100%;max-width:100%;">
            <div class="nav-header">
                <a class="nav-home">
                    <?php if($mediatype == 'google'): ?>
                    <i class="zmdi zmdi-google-drive"></i>
                    <?php else: ?>
                    <i class="zmdi zmdi-dropbox"></i>
                    <?php endif; ?>
                </a>
                <a class="nav-refresh" title="<?php _e('Delete selected files','wpmfAddon') ?>">
                    <i class="wpmfaddondel_selected zmdi zmdi-delete"></i>
                </a>
                
                <a class="nav-refresh" title="<?php _e('Select all','wpmfAddon') ?>">
                    <i class="wpmfaddoncheckall zmdi zmdi-check-all"></i>
                </a>
                
                <a class="nav-refresh" title="<?php _e('Refresh','wpmfAddon') ?>">
                    <i class="wpmfaddonrefresh zmdi zmdi-refresh"></i>
                </a>
                
                <a class="nav-refresh" title="<?php _e('Sort (Descending)','wpmfAddon') ?>">
                    <i data-sort="desc" class="wpmfaddonsort zmdi zmdi-sort-desc"></i>
                </a>
                
                <a class="nav-refresh" title="<?php _e('Sort (Ascending)','wpmfAddon') ?>">
                    <i data-sort="asc" class="wpmfaddonsort zmdi zmdi-sort-asc active"></i>
                </a>
                
                <div class="wpmfaddon-search-div">
                    <input type="search" size="40" placeholder="<?php echo __('Search for files', 'wpmfAddon'); ?>" class="wpmfaddon-search-input" />
                </div>

                
                <div class="nav-title"><?php _e('Media','wpmfAddon') ?></div>
            </div>
            
            <div class="loading loading_<?php echo $mediatype ?>" style="opacity: 1; display: none;">&nbsp;</div>
            <div class="ajax-filelist" style="">
                <?php if($mediatype == 'google'): ?>
                <div class="wpmf_ggbreadcrumb"></div>
                <?php else: ?>
                <div class="wpmf_dbxbreadcrumb"></div>
                <?php endif; ?>
                
                <div class="files layout-grid" style="opacity: 1;">
                    
                </div>
                <?php if($mediatype == 'google'): ?>
                <div id="wrap-wpmfggjao" class="white-popup mfp-hide">
                    <div id="wpmfggjao" class="wpmfggjao"></div>
                    
                    <div class="process_ggimport_full"><span class="process_ggimport process_btnimport" data-w="0">0%</span></div>
                    <div class="message_import"></div>
                    <button type="button" class="btnggimport wpmfbutton-primary" ><?php _e('Import','wpmfAddon') ?></button>
                </div>
                <?php else: ?>
                <div id="wrap-wpmfdbxjao" class="white-popup mfp-hide">
                    <div id="wpmfdbxjao" class="wpmfdbxjao"></div>
                    
                    <div class="process_dbximport_full"><span class="process_dbximport process_btnimport" data-w="0">0%</span></div>
                    <div class="message_import"></div>
                    <button type="button" class="btndbximport wpmfbutton-primary" ><?php _e('Import','wpmfAddon') ?></button>
                </div>
                <?php endif; ?>
                
                
                <?php if($mediatype == 'google'): ?>
                <a href="#wrap-wpmfggjao" class="wpmf-open-popup-media wpmf-open-popup-ggmedia wpmfbutton-primary"><?php _e('Import files in media library','wpmfAddon') ?></a>
                <button type="button" class="wpmfmedia-button-insert wpmfmedia-button-gginsert wpmfbutton-primary" onclick="if (window.parent) wpmfgginsertFile();"><?php _e('Insert files in content','wpmfAddon') ?></button>
                <button type="button" class="wpmfmedia-button-insert wpmfmedia-button-ggembed wpmfbutton-primary" onclick="if (window.parent) wpmfEmbedPdf();"><?php _e('Embed PDF','wpmfAddon') ?></button>
                <?php else: ?>
                <a href="#wrap-wpmfdbxjao" class="wpmf-open-popup-media wpmf-open-popup-dbxmedia wpmfbutton-primary"><?php _e('Import files in media library','wpmfAddon') ?></a>
                <button type="button" class="wpmfmedia-button-insert wpmfmedia-button-dbxinsert wpmfbutton-primary" onclick="if (window.parent) wpmfdbxinsertFile();"><?php _e('Insert files in content','wpmfAddon') ?></button>
                <button type="button" class="wpmfmedia-button-insert wpmfmedia-button-dbxembed wpmfbutton-primary" onclick="if (window.parent) wpmfEmbedPdf();"><?php _e('Embed PDF','wpmfAddon') ?></button>
                <?php endif; ?>
            </div>
            
        </div>
        <div class="fileupload-container" style="width:100%;max-width:100%">
            <div>
                <div class="fileuploadform">
                    <input type="hidden" name="acceptfiletypes" value=".(.)$">
                    <div class="fileupload-drag-drop">
                        <div>
                            <i class="material-icons icon_file_upload">file_upload</i>
                            <p><?php _e('Drag your files here ...','wpmfAddon') ?></p>
                        </div>
                    </div>

                    <div class="fileupload-list">
                        <div role="presentation">
                            <div class="files"></div>

                        </div>
                        <input type="hidden" name="fileupload-filelist" id="fileupload-filelist" class="fileupload-filelist" value="">
                    </div>
                    <div class="fileupload-buttonbar">

                        <div class="fileupload-buttonbar-text">
                            <?php _e('Browse and upload files to Google Drive','wpmfAddon') ?></div>
                        <div class="upload-btn-container upload-btn upload-btn-primary button button-primary">
                            <?php if($mediatype == 'google'): ?>
                            <span><?php _e('Upload files to Google Drive','wpmfAddon') ?></span>
                            <?php else: ?>
                            <span><?php _e('Upload files to Dropbox','wpmfAddon') ?></span>
                            <?php endif; ?>
                            <input type="file" name="files[]" multiple="multiple" class="upload-input-button">

                        </div>
                    </div>
                </div>
            </div>
            <div class="template-row">
                <div class="upload-thumbnail">
                    <img class="" src="">
                </div>

                <div class="upload-file-info">
                    <div class="upload-status-container"><i class="upload-status-icon fa fa-circle"></i> <span class="upload-status"></span></div>
                    <div class="file-size"></div>
                    <div class="file-name"></div>
                    <div class="upload-progress">
                        <div class="progress progress-striped active ui-progressbar ui-widget ui-widget-content ui-corner-all" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                            <div class="ui-progressbar-value ui-widget-header ui-corner-left" style="display: none; width: 0%;"></div>
                        </div>
                    </div>
                    <div class="upload-error"></div>
                </div>
            </div>
            <div class="fileupload-info-container">
                <?php _e('Max file size:','wpmfAddon') ?>  <span class="max-file-size">100 MB</span>
            </div>
        </div></div></div>