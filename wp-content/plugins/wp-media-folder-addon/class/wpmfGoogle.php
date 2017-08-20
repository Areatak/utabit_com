<?php
/**
 * WP Media Folder Addon
 *
 * @package WP Media Folder Addon
 * @author Joomunited
 * @version 1.0
 */

// no direct access
defined( 'ABSPATH' ) || die();
class wpmfAddonGoogleDrive {

    /**
     * @var $param
     */
    protected $params;

    /**
     * @var $lastError
     */
    protected $lastError;
    public $breadcrumb = '';
    protected $wpmffilesfields = 'nextPageToken,items(thumbnailLink,alternateLink,id,description,labels(hidden,restricted,trashed),embedLink,etag,downloadUrl,iconLink,exportLinks,mimeType,modifiedDate,fileExtension,webContentLink,fileSize,userPermission,imageMediaMetadata(width,height),kind,permissions(kind,name,role,type,value,withLink), parents(id,isRoot,kind),title,openWithLinks),kind';
    /**
     *  construct
     */
    public function __construct() {
        set_include_path(__DIR__ . PATH_SEPARATOR . get_include_path());
        require_once 'Google/autoload.php';
        $this->loadParams();
    }

    /**
     * @return mixed|void
     */
    function getAllCloudConfigs()
    {
        return wpmfAddonHelper::getAllCloudConfigs();
    }

    /**
     * @param $data
     * @return bool
     */
    function saveCloudConfigs($data)
    {
        return wpmfAddonHelper::saveCloudConfigs($data);
    }

    /**
     * @param $name
     * @return array|null
     */
    public function getDataConfigBySeverName($name)
    {
        return wpmfAddonHelper::getDataConfigBySeverName($name);
    }

    /**
     * @return mixed
     */
    public function getLastError(){
        return $this->lastError;
    }

    /**
     *
     */
    protected function loadParams(){
        $params = $this->getDataConfigBySeverName('google');
        $this->params = new stdClass();

        $this->params->google_client_id = $params['googleClientId'];
        $this->params->google_client_secret = $params['googleClientSecret'];
        $this->params->google_credentials = isset($params['googleCredentials'])? $params['googleCredentials'] : "";
    }

    /**
     *
     */
    protected function saveParams(){
        $params = $this->getAllCloudConfigs();
        $params['googleClientId'] = $this->params->google_client_id;
        $params['googleClientSecret'] = $this->params->google_client_secret;
        $params['googleCredentials'] = $this->params->google_credentials;
        $this->saveCloudConfigs($params);
    }

    /**
     * @return string
     */
    public function getAuthorisationUrl(){
        $client = new Google_Client();
        $client->setClientId($this->params->google_client_id);
        $client->setRedirectUri(admin_url('options-general.php?page=option-folder&task=wpmf&function=wpmf_authenticated'));
        $client->setAccessType('offline');
        $client->setApprovalPrompt('force');
        $client->setState('');
        $client->setScopes(array(
                    'https://www.googleapis.com/auth/drive', 
                    'https://www.googleapis.com/auth/userinfo.email',
                    'https://www.googleapis.com/auth/userinfo.profile'));
        $tmpUrl = parse_url($client->createAuthUrl());
        $query = explode('&', $tmpUrl['query']);
        return $tmpUrl['scheme'] . '://' . $tmpUrl['host'] . @$tmpUrl['port'] .$tmpUrl['path'] . '?' . implode('&', $query);
    }

    /**
     * @return string
     */
    public function authenticate(){
        $code = $this->getInput('code', 'GET', 'none');
        $client = new Google_Client();
        $client->setClientId($this->params->google_client_id);
        $client->setClientSecret($this->params->google_client_secret);
        $client->setRedirectUri(admin_url('options-general.php?page=option-folder&task=wpmf&function=wpmf_authenticated'));
        return $client->authenticate($code);
    }

    /**
     *
     */
    public function logout(){
        $client = new Google_Client();
        $client->setClientId($this->params->google_client_id);
        $client->setClientSecret($this->params->google_client_secret);
        $client->setAccessToken($this->params->google_credentials);
        $client->revokeToken();
    }

    /**
     * @param $credentials
     */
    public function storeCredentials($credentials){
        $this->params->google_credentials = $credentials;
        $this->saveParams();
    }

    /**
     * @return mixed
     */
    public function getCredentials(){
        return $this->params->google_credentials;
    }

    /**
     * @return bool
     */
    public function checkAuth(){
        $client = new Google_Client();
        $client->setClientId($this->params->google_client_id);
        $client->setClientSecret($this->params->google_client_secret);

        try {
            $client->setAccessToken($this->params->google_credentials);
            $service = new Google_Service_Drive($client);
            $service->files->listFiles(array());
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * @param $id
     * @return bool
     */
    public function folderExists($id){
        $client = new Google_Client();
        $wpmfAddon_cloud_config = get_option('_wpmfAddon_cloud_config');
        $client->setClientId($wpmfAddon_cloud_config['googleClientId']);
        $client->setClientSecret($wpmfAddon_cloud_config['googleClientSecret']);
        $client->setAccessToken($wpmfAddon_cloud_config['googleCredentials']);
        
        $service = new Google_Service_Drive($client);
        try{
            $file = $service->files->get($id);
            return true;
        }catch(Exception $e){
            $this->lastError = $e->getMessage();
            return false;
        }
        return false;
    }

    /**
     * @param $title
     * @param null $parentId
     * @return bool|Google_Service_Drive_DriveFile
     */
    public function ajaxcreateFolder(){
        if(isset($_POST['parentId'])){
            if(isset($_POST['title']) && $_POST['title'] != ''){
                $title = urldecode($_POST['title']);
            }else{
                $title = __('New Folder','wpmfAddon');
            }
            
            $parentId = $_POST['parentId'];
            $wpmfAddon_cloud_config = get_option('_wpmfAddon_cloud_config');
            $client = new Google_Client();
            $client->setClientId($wpmfAddon_cloud_config['googleClientId']);
            $client->setClientSecret($wpmfAddon_cloud_config['googleClientSecret']);
            $client->setAccessToken($wpmfAddon_cloud_config['googleCredentials']);

            $service = new Google_Service_Drive($client);
            $file = new Google_Service_Drive_DriveFile();
            $file->title = $title;
            $file->mimeType = "application/vnd.google-apps.folder";

            if ($parentId != null) {
                $parent = new Google_Service_Drive_ParentReference();
                $parent->setId($parentId);
                $file->setParents(array($parent));
              }

            try {
                $fileId = $service->files->insert($file);
            } catch (Exception $e) {
                $this->lastError = $e->getMessage();
                wp_send_json(false);
            }
            wp_send_json(true);
        }else{
            wp_send_json(false);
        }
    }
    
    /* add new folder when connect google drive */
    public function createFolder($title,$parentId=null){
        $wpmfAddon_cloud_config = get_option('_wpmfAddon_cloud_config');
        $client = new Google_Client();
        $client->setClientId($wpmfAddon_cloud_config['googleClientId']);
        $client->setClientSecret($wpmfAddon_cloud_config['googleClientSecret']);
        $client->setAccessToken($wpmfAddon_cloud_config['googleCredentials']);

        $service = new Google_Service_Drive($client);
        $file = new Google_Service_Drive_DriveFile();
        $file->title = $title;
        $file->mimeType = "application/vnd.google-apps.folder";

        if ($parentId != null) {
            $parent = new Google_Service_Drive_ParentReference();
            $parent->setId($parentId);
            $file->setParents(array($parent));
          }

        try {
            $fileId = $service->files->insert($file);
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
        return $fileId;
    }
    
    /* Ajax load folders and files */
    public function wpmf_get_google_filelist() {
        $google = new wpmfAddonGoogleDrive();
        if (!$google->checkAuth()) wp_send_json(array('status' => false , 'message' => __('Oops! This shouldn\'t happen... Try again!','wpmfAddon')));
        $wpmfAddon_cloud_config = get_option('_wpmfAddon_cloud_config');
        $client = new Google_Client();
        
        $client->setClientId($wpmfAddon_cloud_config['googleClientId']);
        $client->setClientSecret($wpmfAddon_cloud_config['googleClientSecret']);
        $client->setAccessToken($wpmfAddon_cloud_config['googleCredentials']);
        $service = new Google_Service_Drive($client);
        $file = $service->files->get($_POST['googleBaseFolder']);
        $parents = $file->getParents();
        $parent = $parents[0]->id;
        $parentTitle = $parents[0]->title;
        $parentfile = $service->files->get($parent);
        $results = $this->retrieveAllFiles($service,$file,$parent,$parentfile->title,$wpmfAddon_cloud_config['googleBaseFolder']);
        $res = $results['result'];
        $breadcrumb = $results['breadcrumb'];
        $html = '';
        ob_start();
        if($_POST['googleBaseFolder'] != $wpmfAddon_cloud_config['googleBaseFolder']){
            $thumbnail_pre = '<i class="material-icons wpmf_skip_previous">skip_previous</i>';
            require ( WPMFAD_PLUGIN_DIR . '/class/templates/htmlprevious.php');
        }
        
        $foldersarray = array();
        $filesarray = array();
        
        foreach ($res as $re){
            if($re->mimeType == 'application/vnd.google-apps.folder'){
                array_push($foldersarray, $re);
            }else{
                $extension = (isset($re->fileExtension)) ? $re->getFileExtension() : '';
                $re->openwithgoogle = false;
                $openwithlink = $re->getAlternateLink();
                if (!empty($openwithlink) && (!in_array($extension, array('jpg', 'jpeg', 'gif', 'png', 'ace','arj','bz2','cab','gzip','iso','jar','lzh','tar','uue','xz','z','7-zip','x-rar','rar','zip')))) {
                  $re->openwithgoogle = true;
                }
                $re->extension = $extension;
                //$re->basename = str_replace('.' . $extension, '', $re->getTitle());
                array_push($filesarray, $re);
            }
        }
        
        $orderby = 'title';
        if(isset($_POST['sortfilename'])){
            $order = $_POST['sortfilename'];
        }else{
            $order = 'asc';
        }
        $foldersarray = $this->subval_sort($foldersarray, $orderby,$order);
        $filesarray = $this->subval_sort($filesarray, $orderby,$order);
        foreach ($foldersarray as $re){
            $id = $re->id ; 
            $name = $re->title;
            $infofile = pathinfo($name);
            if(isset($infofile['extension'])){
                $extension = $infofile['extension'];
            }else{
                $extension = '';
            }
            $thumbnail = '<i class="material-icons wpmf_icon_folder">folder</i>';
            require ( WPMFAD_PLUGIN_DIR . '/class/templates/htmlfolder.php');
        }
        
        $thumbnail_newfolder = '<i class="material-icons wpmf_create_new_folder">create_new_folder</i>';
        require ( WPMFAD_PLUGIN_DIR . '/class/templates/htmladdfolder.php');
        
        $files = array();
        $type = '';
        $display_preview = 1;
        foreach ($filesarray as $re){
            $id = $re->id ; 
            $name = $re->title;
            $infofile = pathinfo($name);
            //$downloadlink = $re->webContentLink;
            $ext = $re->extension;
            $downloadlink = admin_url('admin-ajax.php') . "?action=wpmf-download-file&id=" . urlencode($id) ."&link=true&dl=1";
            $mimeType = $re->getMimeType();
                       
            $thumbnail = $this->wpmf_get_thumbnail($re);
            if ($re['openwithgoogle']) {
                $type = 'iframe';
                if($this->isMediaFile($ext)){
                    $lightboxlink = admin_url('admin-ajax.php') . "?action=wpmf-preview-file&id=" . urlencode($id) . "&openwithgoogle=1&mimetype=".$mimeType."&ext=".$ext;
                }elseif($ext == 'pdf'){
                    $type = 'pdf';
                    $lightboxlink = $downloadlink;
                }else{
                    $lightboxlink = 'https://docs.google.com/viewer?url='.urlencode($downloadlink).'&embedded=true';
                }
                $embedlink = $lightboxlink;
            }elseif (in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))){
                $type = 'image';
                $lightboxlink = str_replace('=s220', '', $thumbnail);
                $embedlink = admin_url('admin-ajax.php') . "?action=wpmf-preview-file&id=" . urlencode($id) . "&openwithgoogle=1&mimetype=".$mimeType."&ext=".$ext;
            }else{
                $type = 'download';
                $lightboxlink = '#';
                $embedlink = $this->wpmf_getEmbedLink($re);
            }
            
            $files[$id] = array('title' => $infofile['filename'] , 'type_insert' => $type , 'src' => $embedlink , 'lightboxlink' => $lightboxlink);
            require ( WPMFAD_PLUGIN_DIR . '/class/templates/htmlfile.php');
        }
        
        $html = ob_get_contents();
        ob_end_clean();
        wp_send_json(array('status' => true , 'message' => $html , 'files' => $files , 'breadcrumb' => $breadcrumb));
    }
    
    public function isMediaFile($ext) {
        $media_arr = array('mid','midi','mp2','mp3','mpga','ram','rm','rpm','ra','wav', //,'aif','aifc','aiff'
            'wmv','mp4','mpeg','mpe','mpg','mov','qt','rv','avi','movie','flv','webm','ogv', //'3gp',
            'jpg','png','gif','jpeg','jpe','bmp','ico','tiff','tif','svg','svgz');
        if(in_array($ext, $media_arr)) {
            return true;
        }
        return false;
    }
    
    /* get embed link */
    public function wpmf_getEmbedLink($entry) {
        $embedlink = $entry->getEmbedLink();
        if (empty($embedlink)) {
            $embedlink = 'https://docs.google.com/viewer?srcid=' . $entry->getId() . '&pid=explorer&embedded=true';
            /* As of 12 November 2014, the Google Doc viewer doesn't display PDF files anymore */
            if (strpos($entry->getMimeType(), 'application/pdf') !== false) {
                $embedlink = 'https://docs.google.com/file/d/' . $entry->getId() . '/preview';
                /* Powerpoints can't be showed embedded */
            } elseif (strpos($entry->getMimeType(), 'google-apps.presentation') !== false) {
                $embedlink = 'https://docs.google.com/presentation/d/' . $entry->getId() . '/preview';
            }
        } else {
            if (strpos($entry->getMimeType(), 'application/vnd.google-apps') === false) {
                $embedlink = 'https://docs.google.com/file/d/' . $entry->getId() . '/preview';
                /* Powerpoints can't be showed embedded */
            } elseif (strpos($entry->getMimeType(), 'google-apps.presentation') !== false) {
                
            } else {
                $embedlink = $entry->getAlternateLink();
                $embedlink = str_replace('http://', 'https://', $embedlink);
            }
        }
        return $embedlink;
    }
    
    /* get thumbnail icon file */
    public function wpmf_get_thumbnail($child) {
        $thumbnail = $child->getThumbnailLink();
        /* Thumbnails with feeds in URL give 404 without token? */
        if (strpos($thumbnail, 'google.com') !== false) {
            $thumbnail = 'https://googledrive.com/thumb/' . $child->getId() . '?width=400&height=400&crop=false';
        }

        /* Set default thumbnail if needed */
        switch ($child->getMimeType()) {
            case 'application/ace':
            case 'application/arj':
            case 'application/bz2':
            case 'application/cab':
            case 'application/gzip':
            case 'application/iso':
            case 'application/jar':
            case 'application/lzh':
            case 'application/tar':
            case 'application/uue':
            case 'application/xz':
            case 'application/z':
            case 'application/7-zip':
            case 'application/x-rar':
            case 'application/rar':
            case 'application/zip':
                $thumbnailicon = 'archive.png';
                break;
            case 'application/vnd.google-apps.folder':
                $thumbnailicon = 'folder.png';
                break;
            case 'audio/mp3':
            case 'application/vnd.google-apps.audio':
            case 'audio/mpeg':
                $thumbnailicon = 'audio.png';
                break;
            case 'application/vnd.google-apps.document':
            case 'application/vnd.oasis.opendocument.text':
            case 'text/plain':
                $thumbnailicon = 'document.png';
                break;
            case 'application/vnd.google-apps.drawing':
                $thumbnailicon = 'drawing.png';
                break;
            case 'application/vnd.google-apps.form':
                $thumbnailicon = 'form.png';
                break;
            case 'application/vnd.google-apps.fusiontable':
                $thumbnailicon = 'table.png';
                break;
            case 'application/vnd.google-apps.photo':
            case 'image/jpeg':
            case 'image/png':
            case 'image/gif':
            case 'image/bmp':
                $thumbnailicon = 'image.png';
                break;
            case 'application/vnd.google-apps.presentation':
            case 'application/vnd.oasis.opendocument.presentation':
                $thumbnailicon = 'presentation.png';
                break;
            case 'application/vnd.google-apps.script':
            case 'application/x-httpd-php':
            case 'text/js':
                $thumbnailicon = 'script.png';
                break;
            case 'application/vnd.google-apps.sites':
                $thumbnailicon = 'sites.png';
                break;
            case 'application/vnd.google-apps.spreadsheet':
            case 'application/vnd.oasis.opendocument.spreadsheet':
                $thumbnailicon = 'spreadsheet.png';
                break;
            case 'application/vnd.google-apps.video':
                $thumbnailicon = 'video.png';
                break;

            case 'application/vnd.ms-excel':
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                $thumbnailicon = 'excel.png';
                break;
            case 'application/msword':
                $thumbnailicon = 'word.png';
                break;


            case 'application/pdf':
                $thumbnailicon = 'pdf.png';
                break;
            default:
                $thumbnailicon = 'generic.png';
                break;
        }
        
        if ($thumbnail === null) {
            $thumbnail = WPMFAD_PLUGIN_URL . '/assets/images/icons/' . $thumbnailicon;
        }
        
        return $thumbnail;
    }
    
    /* download google file */
    public function wpmf_download_file() {
        if(empty($_REQUEST['id'])) die();
        $wpmfAddon_cloud_config = get_option('_wpmfAddon_cloud_config');
        $client = new Google_Client();

        $client->setClientId($wpmfAddon_cloud_config['googleClientId']);
        $client->setClientSecret($wpmfAddon_cloud_config['googleClientSecret']);
        $client->setAccessToken($wpmfAddon_cloud_config['googleCredentials']);
        $service = new Google_Service_Drive($client);
        $file = $service->files->get($_REQUEST['id']);
        if (!isset($authorizedlink)) {
            $authorizedlink = (isset($_REQUEST['auth']) && $_REQUEST['auth'] == 1) ? true : false;
        }

        $forcedownload = ((isset($_REQUEST['dl']) && $_REQUEST['dl'] === '1')) ? true : false;
        $downloadlink = $file->getDownloadUrl();
        if ($authorizedlink) {
            if (!$forcedownload) {
                $downloadlink = str_replace('e=download', 'e=export', $downloadlink);
            }
        }
        
        if ($downloadlink !== null) {
            $request = new Google_Http_Request($downloadlink, 'GET');

            $httpRequest = $client->getAuth()->authenticatedRequest($request);
            if ($httpRequest->getResponseHttpCode() == 200) {
                if (!$forcedownload) {
                    include_once 'includes/mime-types.php';
                    $contenType = getMimeType($file->fileExtension);
                }else{
                    $contenType = 'application/octet-stream';
                }
                
                $this->downloadHeader($file->getTitle(), (int)$file->fileSize, $contenType);    
                echo $httpRequest->getResponseBody();
            } else {
                
            }
        }

        die();
    }
    
    public function downloadHeader($file,$size,$contenType){
        @ob_end_clean();
        ob_start();
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $contenType);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        if ($size != 0) {
            header('Content-Length: ' . $size);
        }
        ob_clean();
        flush();
    }
    
    /* get publish link file */
    public function wpmf_preview_file() {
//        
        ob_start();
        $html = '';
        if(isset($_REQUEST['id']) && isset($_REQUEST['mimetype']) && isset($_REQUEST['ext'])){
            $ext = $_REQUEST['ext'];
            $imagesType = array('jpg','png','gif','jpeg','jpe','bmp','ico','tiff','tif','svg','svgz');
            $videoType = array('mp4','wmv','mpeg','mpe','mpg','mov','qt','rv','avi','movie','flv','webm','ogv');//,'3gp'
            $audioType = array('mid','midi','mp2','mp3','mpga','ram','rm','rpm','ra','wav');  // ,'aif','aifc','aiff'
            if(in_array($ext, $imagesType)) {
                $mediaType= 'image';
            }else if(in_array($ext, $videoType)) {
                $mediaType ='video';
            }else if(in_array($ext, $audioType)) {
                $mediaType='audio';
            }else {
                $mediaType='';
            }
            
            $mimetype = $_REQUEST['mimetype'];
            $downloadLink = admin_url('admin-ajax.php') . "?action=wpmf-download-file&id=" . urlencode($_REQUEST['id']) . "&link=true&dl=1";
            require ( WPMFAD_PLUGIN_DIR . '/class/templates/media.php');
            $html = ob_get_contents();
            ob_end_clean();
            echo $html;
        }
        die();
    }
    
    /* import google file to media library */
    public function wpmf_ggimport_file() {
        if(isset($_POST['ids'])){
            $ids = explode(',', $_POST['ids']);
            $term_id = (!empty($_POST['wpmfcurrentFolderId']))?$_POST['wpmfcurrentFolderId']:0;
            $wpmfAddon_cloud_config = get_option('_wpmfAddon_cloud_config');
            $client = new Google_Client();
            $client->setClientId($wpmfAddon_cloud_config['googleClientId']);
            $client->setClientSecret($wpmfAddon_cloud_config['googleClientSecret']);
            $client->setAccessToken($wpmfAddon_cloud_config['googleCredentials']);
            $service = new Google_Service_Drive($client);
            
            $upload_dir = wp_upload_dir();
            if(!empty($ids)){
                $precent = ceil(100 / count($ids));
            }else{
                $precent = 100;
            }
            $i = 0;
            foreach ($ids as $id){
                if ($i >= 1) {
                    wp_send_json(array('status' => 'error time','precent' => $precent)); // run again ajax
                } else {
                    $file = $service->files->get($id);
                    $downloadlink = $file->getDownloadUrl();
                    if(!empty($downloadlink)){
                        $filename = sanitize_file_name($file->getTitle());
                        $list_imported = get_option('wpmf_ggfiles_imported');
                        if(!in_array($term_id.'_'.$filename, $list_imported) || empty($list_imported)){
                            $content = $service->files->get($id, array('alt' => 'media' ));
                            $extension = (isset($file->fileExtension)) ? $file->getFileExtension() : '';
                            $status = $this->wpmf_insert_attachment_metadata($id,$upload_dir['path'], $upload_dir['url'], $filename, $content, $file->getMimeType(), $extension, $term_id);
                            if($status) $i++;
                        }
                    }
                }
                
                
            }
            wp_send_json(array('status' => true,'precent' => '100')); // run again ajax
        }
        wp_send_json(false);
    }
    
    /* Insert a attachment to database */
    public function wpmf_insert_attachment_metadata($idfile,$upload_path, $upload_url, $file, $content, $mime_type, $ext, $term_id) {
        remove_filter('wp_generate_attachment_metadata', array($GLOBALS['wp_media_folder'], 'wpmf_after_upload'));
        $list_imported = get_option('wpmf_ggfiles_imported');
        if(!in_array($term_id.'_'.$idfile, $list_imported) || empty($list_imported)){
            if(!empty($list_imported) && is_array($list_imported)){
                $list_imported[] = $term_id.'_'.$idfile;
            }else{
                $list_imported = array($term_id.'_'.$idfile);
            }
            $file = wp_unique_filename($upload_path, $file);
            $upload = file_put_contents($upload_path . '/' . $file, $content);
            if ($upload) {
                $attachment = array(
                    'guid' => $upload_url . '/' . $file,
                    'post_mime_type' => $mime_type,
                    'post_title' => str_replace('.' . $ext, '', $file),
                    'post_status' => 'inherit'
                );

                $image_path = $upload_path . '/' . $file;
                // Insert attachment
                $attach_id = wp_insert_attachment($attachment, $image_path);
                $attach_data = wp_generate_attachment_metadata($attach_id, $image_path);
                wp_update_attachment_metadata($attach_id, $attach_data);
                // set attachment to term
                wp_set_object_terms((int) $attach_id, (int) $term_id, WPMF_TAXO, false);
                update_option('wpmf_ggfiles_imported', $list_imported);
            }
            return true;
        }
        return false;
    }
    
    /**
     * @param $a
     * @param $subkey
     * @param $direction
     * @return array
     */
    private function subval_sort($a,$subkey,$direction) {
            if(empty($a)){
                return $a;
            }
            foreach($a as $k=>$v) {
                $b[$k] = strtolower($v->$subkey);
            }
            if($direction=='asc'){
                asort($b);
            }else{
                arsort($b);
            }
            foreach($b as $key=>$val) {
                    $c[] = $a[$key];
            }
            return $c;
    }


    /**
     * @param $filename
     * @param $fileContent
     * @param $mime
     * @param $id_folder
     * @return bool|Google_Service_Drive_DriveFile
     */
    public function uploadFile() {
        if (!empty($_POST['parentID'])) {
            $id_folder = $_POST['parentID'];
        }else{
            $id_folder = '';
        }
        
        /* Upload File to server */
        require('includes/UploadHandler.php');
        $max_file_size = 104857600;

        $uploadir = wp_upload_dir();

        $options = array(
            'upload_dir' => $uploadir['path'] . '/',
            'upload_url' => $uploadir['url'] . '/',
            'access_control_allow_methods' => array('POST', 'PUT'),
            'accept_file_types' => '/.(.)$/i',
            'inline_file_types' => '/\.____$/i',
            'orient_image' => false,
            'image_versions' => array(),
            'max_file_size' => $max_file_size,
            'print_response' => false
        );

        $error_messages = array(
            1 => __('The uploaded file exceeds the upload_max_filesize directive in php.ini', 'wpmfAddon'),
            2 => __('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form', 'wpmfAddon'),
            3 => __('The uploaded file was only partially uploaded', 'wpmfAddon'),
            4 => __('No file was uploaded', 'wpmfAddon'),
            6 => __('Missing a temporary folder', 'wpmfAddon'),
            7 => __('Failed to write file to disk', 'wpmfAddon'),
            8 => __('A PHP extension stopped the file upload', 'wpmfAddon'),
            'post_max_size' => __('The uploaded file exceeds the post_max_size directive in php.ini', 'wpmfAddon'),
            'max_file_size' => __('File is too big', 'wpmfAddon'),
            'min_file_size' => __('File is too small', 'wpmfAddon'),
            'accept_file_types' => __('Filetype not allowed', 'wpmfAddon'),
            'max_number_of_files' => __('Maximum number of files exceeded', 'wpmfAddon'),
            'max_width' => __('Image exceeds maximum width', 'wpmfAddon'),
            'min_width' => __('Image requires a minimum width', 'wpmfAddon'),
            'max_height' => __('Image exceeds maximum height', 'wpmfAddon'),
            'min_height' => __('Image requires a minimum height', 'wpmfAddon')
        );

        $upload_handler = new UploadHandler($options, false, $error_messages);
        $response = @$upload_handler->post(false);

        $client = new Google_Client();
        $wpmfAddon_cloud_config = get_option('_wpmfAddon_cloud_config');
        $client->setClientId($wpmfAddon_cloud_config['googleClientId']);
        $client->setClientSecret($wpmfAddon_cloud_config['googleClientSecret']);
        $client->setAccessToken($wpmfAddon_cloud_config['googleCredentials']);
        /* Upload files to Google Drive */
        foreach ($response['files'] as &$file) {
            /* Set return Object */
            $file->hash = $_REQUEST['hash'];
            $return = array('file' => $file, 'status' => array('bytes_down_so_far' => 0, 'total_bytes_down_expected' => 0, 'percentage' => 0, 'progress' => 'starting'));
            set_transient('wpmfgg_upload_' . substr($file->hash, 0, 40), $return, HOUR_IN_SECONDS);
            if (!isset($file->error)) {
                /* Write file */
                $filePath = $file->tmp_path;
                $chunkSizeBytes = 1 * 1024 * 1024;

                /* Update Mime-type if needed (for IE8 and lower?) */
                include_once 'includes/mime-types.php';
                $fileExtension = pathinfo($file->name, PATHINFO_EXTENSION);
                $file->type = getMimeType($fileExtension);

                try {
                    /* Create new Google File */
                    $googledrive_file = new Google_Service_Drive_DriveFile();
                    $googledrive_file->setTitle($file->name);
                    $googledrive_file->setMimeType($file->type);

                    /* Add Parent to Google File */
                    $parent = new Google_Service_Drive_ParentReference();
                    $parent->setId($id_folder);
                    $googledrive_file->setParents(array($parent));

                    /* Call the API with the media upload, defer so it doesn't immediately return. */
                    $service = new Google_Service_Drive($client);
                    $client->setDefer(true);
                    $request = $service->files->insert($googledrive_file, array('convert' => false));
                    $request->disableGzip();

                    /* Create a media file upload to represent our upload process. */
                    $media = new Google_Http_MediaFileUpload(
                            $client, $request, $file->type, null, true, $chunkSizeBytes
                    );

                    $filesize = filesize($filePath);
                    $media->setFileSize($filesize);

                    /* Start partialy upload 
                      Upload the various chunks. $status will be false until the process is
                      complete. */
                    $uploadStatus = false;
                    $bytesup = 0;
                    $handle = fopen($filePath, "rb");
                    while (!$uploadStatus && !feof($handle)) {
                        set_time_limit(60);
                        $chunk = fread($handle, $chunkSizeBytes);
                        $uploadStatus = $media->nextChunk($chunk);

                        /* Update progress */
                        $bytesup += $chunkSizeBytes;
                        $percentage = ( round(($bytesup / $file->size) * 100) );
                        $return['status'] = array('bytes_up_so_far' => $bytesup, 'total_bytes_up_expected' => $filesize, 'percentage' => $percentage, 'progress' => 'uploading');
                        set_transient('wpmfgg_upload_' . substr($file->hash, 0, 40), $return, HOUR_IN_SECONDS);
                    }

                    fclose($handle);
                } catch (Exception $ex) {
                    $file->error = __('Not uploaded to Google Drive', 'wpmfAddon') . ': ' . $ex->getMessage();
                    $return['status']['progress'] = 'failed';
                }

                $client->setDefer(false);
            } else {
                
            }
        }

        $return['file'] = $file;
        set_transient('wpmfgg_upload_' . substr($file->hash, 0, 40), $return, HOUR_IN_SECONDS);

        /* Create response */
        wp_send_json($return);
    }

    /**
     * @param $id
     * @param $filename
     * @return bool
     */
    public function changeFilename(){
        if(isset($_POST['id']) && isset($_POST['filename']) && $_POST['filename'] != ''){
            $id = $_POST['id'];
            $filename = urldecode($_POST['filename']);
            $client = new Google_Client();
            $wpmfAddon_cloud_config = get_option('_wpmfAddon_cloud_config');
            $client->setClientId($wpmfAddon_cloud_config['googleClientId']);
            $client->setClientSecret($wpmfAddon_cloud_config['googleClientSecret']);
            $client->setAccessToken($wpmfAddon_cloud_config['googleCredentials']);

            try{
                $service = new Google_Service_Drive($client);
                $file = $service->files->get($id);
                $file->setTitle($filename);
                $service->files->update($id, $file, array());
            }catch(Exception $e){
                $this->lastError = $e->getMessage(); 
                return false;
            }
            return true;
        }else{
            return false;
        }
    }

    /* Delete file or folder */
    public function delete(){
        if(isset($_POST['ids'])){
            $ids = $_POST['ids'];
            $cloud_id = $_POST['parentId'];
            $client = new Google_Client();
            $wpmfAddon_cloud_config = get_option('_wpmfAddon_cloud_config');
            $client->setClientId($wpmfAddon_cloud_config['googleClientId']);
            $client->setClientSecret($wpmfAddon_cloud_config['googleClientSecret']);
            $client->setAccessToken($wpmfAddon_cloud_config['googleCredentials']);

            $service = new Google_Service_Drive($client);
            try {
                $array_ids = explode(',', $ids);
                foreach ($array_ids as $id){
                    $file = $service->files->get($id);
                    if($cloud_id!==null){
                        $found = false;
                        foreach ($file->getParents() as $parent) {
                            if($parent->id==$cloud_id){
                                $found = true;
                                break;
                            }
                        }
                        if(!$found){
                            return false;
                        }
                    }
                    $service->files->delete($id);
                }
            } catch (Exception $e) {
                $this->lastError = $e->getMessage();
                return false;
            }
            return true;
        }else{
            return false;
        }
    }

    /**
     * Move a file.
     */
    function moveFile() {
        if(isset($_POST['fileIds']) && isset($_POST['newParentId'])){
            $fileIds = explode(',', $_POST['fileIds']);
            
                $newParentId = $_POST['newParentId'];
                $client = new Google_Client();
                $wpmfAddon_cloud_config = get_option('_wpmfAddon_cloud_config');
                $client->setClientId($wpmfAddon_cloud_config['googleClientId']);
                $client->setClientSecret($wpmfAddon_cloud_config['googleClientSecret']);
                $client->setAccessToken($wpmfAddon_cloud_config['googleCredentials']);
                $service = new Google_Service_Drive($client);
                try {
                    $file = new Google_Service_Drive_DriveFile();

                    $parent = new Google_Service_Drive_ParentReference();
                    $parent->setId($newParentId);

                    $file->setParents(array($parent));
                    foreach ($fileIds as $fileId){
                        $updatedFile = $service->files->patch($fileId, $file);
                    }

                    wp_send_json(true);
                }
                catch (Exception $e) {
                    print "An error occurred: " . $e->getMessage();
                }
            
        }else{
            wp_send_json(false);
        }
    }
        
    /* get breadcrumb */
    public function wpmf_get_breadcrumb($folderid,$service,$file,$parent,$parentTitle,$googleBaseFolder) {
        
        if($folderid != $googleBaseFolder){
            $this->breadcrumb .= "<a href='javascript:void(0)' class='wpmf_breadcrumb_folder' data-id='" . $googleBaseFolder . "'><i class='wpmf-home zmdi zmdi-home'></i></a> ";
            if($parent != $googleBaseFolder){
                $this->breadcrumb .= "<a href='javascript:void(0)' class='wpmf_breadcrumb_folder' data-id='" . $parent . "'>" . $parentTitle . "</a> / ";
            }
        }
        if($file->id == $googleBaseFolder){
            $this->breadcrumb .= "<a href='javascript:void(0)' class='wpmf_breadcrumb_folder' data-id='" . $file->id . "'><i class='wpmf-home zmdi zmdi-home'></i></a>";
        }else{
            $this->breadcrumb .= "<a href='javascript:void(0)' class='wpmf_breadcrumb_folder' data-id='" . $file->id . "'>" . $file->title . "</a> / ";
        }
    }
    
    /**
     * Retrieve a list of File resources.
     *
     * @param Google_Service_Drive $service Drive API service instance.
     * @return Array List of Google_Service_Drive_DriveFile resources.
     */
    public function retrieveAllFiles($service,$file,$parent,$parentTitle,$googleBaseFolder) {
        $result = array();
        $pageToken = NULL;
        
        if(!empty($_POST['googleBaseFolder'])){
            $folderid = $_POST['googleBaseFolder'];
            
            // get breadcrumb
            $breadcrumb = __('You are here  : ','wpmfAddon');
            $this->wpmf_get_breadcrumb($folderid,$service,$file,$parent,$parentTitle,$googleBaseFolder);
            $breadcrumb .= $this->breadcrumb;
            do {
                try {
                    $parameters = array();
                    if ($pageToken) {
                        $parameters['pageToken'] = $pageToken;
                    }
                    $q = "trashed=false";

                    //$params = array('q' => "'root' in parents and trashed = false", "fields" => $this->apilistfilesfields, "maxResults" => 999, "userIp" => $this->userip);
                    if(isset($_POST['searchfilename'])){
                        $s = " and title contains '".$_POST['searchfilename']."'";
                    }else{
                        $s = '';
                    }
                    $params = array('q' => "'" . $folderid . "' in parents and trashed = false".$s , "fields" => $this->wpmffilesfields, "maxResults" => 999);
                    //and mimeType = 'application/vnd.google-apps.folder'
                    $files = $service->files->listFiles($params);
                    $result = array_merge($result, $files->getItems());
                    $pageToken = $files->getNextPageToken();
                } catch (Exception $e) {
                    print "An error occurred: " . $e->getMessage();
                    $pageToken = NULL;
                }
            } while ($pageToken);
        }
        
        return array('result' => $result , 'breadcrumb' => $breadcrumb);
    }
   
    function getInput($name,$type='GET',$filter='cmd'){
        $input = null;
        switch (strtoupper($type)){
            case 'GET':
                if(isset($_GET[$name])){
                    $input = $_GET[$name];
                }
                break;
            case 'POST':
                if(isset($_POST[$name])){
                    $input = $_POST[$name];
                }
                break;
            case 'FILES':
                if(isset($_FILES[$name])){
                    $input = $_FILES[$name];
                }
                break;
            case 'COOKIE':
                if(isset($_COOKIE[$name])){
                    $input = $_COOKIE[$name];
                }
                break;
            case 'ENV':
                if(isset($_ENV[$name])){
                    $input = $_ENV[$name];
                }
                break;
            case 'SERVER':
                if(isset($_SERVER[$name])){
                    $input = $_SERVER[$name];
                }
                break;
            default:
                    break;
        }

        switch (strtolower($filter)){
            case 'cmd' :
                $input = preg_replace('/[^a-z\.]+/', '', strtolower($input));
                break;
            case 'int' :
                $input = intval($input);
                break;
            case 'bool':
                $input = $input ? 1 : 0;
                break;
            case 'string':
                $input = sanitize_text_field($input);
                break;
            case 'none':
                break;
            default :
                $input = null;
                break;
        }
        return $input;
    }
}

?>
