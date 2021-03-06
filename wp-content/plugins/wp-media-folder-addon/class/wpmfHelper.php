<?php
/**
 * WP Media Folder Addon
 *
 * @package WP Media Folder Addon
 * @author Joomunited
 * @version 1.0
 */

defined( 'ABSPATH' ) || die();

/**
 * Class wpmfAddonHelper
 */
class wpmfAddonHelper {

    /**
     * @return mixed|void
     */
    public static function getAllCloudConfigs()
    {
         $default = array(
            'googleClientId' =>'',    
            'googleClientSecret' =>'');
        return get_option('_wpmfAddon_cloud_config',$default);
    }

    /**
     * @param $data
     * @return bool
     */
    public static function saveCloudConfigs($data)
    {
        $result = update_option('_wpmfAddon_cloud_config', $data);
        return $result;
    }

    /**
     * @param $name
     * @return array|null
     */
    public static function getDataConfigBySeverName($name)
    {
        $googleDriveParams = array();
        if (self::getAllCloudConfigs()) {
            foreach (self::getAllCloudConfigs() as $key=>$val)
            {
                if (strpos($key, 'google') !== false)
                {
                   
                    $googleDriveParams[$key] = $val;
                }
            }
            
            $result = null;
            switch ($name) {
                case 'google':
                    $result =  $googleDriveParams;
                    break;
            }
            return $result;
        }
    }

    /**
     * @return mixed|void
     */
    public static function getAllCloudParams()
    {
        return get_option('_wpmfAddon_cloud_category_params');
    }

    /**
     * @param $cloudParams
     * @return bool
     */
    public static function setCloudConfigsParams($cloudParams)
    {
        $result = update_option('_wpmfAddon_cloud_category_params', $cloudParams);
        return $result;
    }

    /**
     * @return mixed
     */
    public static function getGoogleDriveParams()
    {
        $params = self::getAllCloudParams();
        return isset($params['googledrive'])? $params['googledrive']: false;
    }

     /**
     * @return mixed
     */
    public static function setCloudParam($key, $val)
    {
        $params = self::getAllCloudConfigs();              
        $params[$key]= $val;
        self::saveCloudConfigs($params);       
    }

    
    /**
     * @param $googleDriveId
     * @return bool
     */
    public static function getTermIdGoogleDriveByGoogleId($googleDriveId)
    {
        $returnData = false;
        $googleParams = self::getGoogleDriveParams();
        if ($googleParams) {
            foreach ($googleParams as $key=>$val)
            {
                if ($val['idCloud'] == $googleDriveId)
                {
                    $returnData =  $val['termId'];
                }
            }
        }
        return $returnData;
    }

    /**
     * @param $termId
     * @return bool
     */
    public static function getGoogleDriveIdByTermId($termId)
    {
        $returnData = false;
        $googleParams = self::getGoogleDriveParams();
        if ($googleParams) {
            foreach ($googleParams as $key=>$val)
            {
                if ($val['termId'] == $termId)
                {
                    $returnData =  $val['idCloud'];
                }
            }
        }
        return $returnData;
    }
    /**
     * @param $termId
     * @return bool
     */
    public static function getCatIdByCloudId($cloud_id)
    {
        $returnData = false;
        $googleParams = self::getGoogleDriveParams();
        if ($googleParams) {
            foreach ($googleParams as $key=>$val)
            {
                if ($val['idCloud'] == $cloud_id)
                {
                    $returnData =  $val['termId'];
                }
            }
        }
        return $returnData;
    }
    /**
     * @return array
     */
    public static function getAllGoogleDriveId()
    {
        $returnData = array();
        $googleParams = self::getGoogleDriveParams();
        if ($googleParams) {
            foreach ($googleParams as $key=>$val)
            {
                $returnData[] =  $val['idCloud'];
            }
        }
        return $returnData;
    }
    
    public static function curSyncInterval() {
         //get last_log param
        $config = self::getAllCloudConfigs(); 
        if(isset($config['last_log']) && !empty($config['last_log']) ) {
            $last_log = $config['last_log'];
            $last_sync =(int)strtotime($last_log);
        }
        else{
            $last_sync=0;
        }

        $time_new=(int)strtotime(date('Y-m-d H:i:s'));
        $timeInterval=$time_new- $last_sync;
        $curtime=$timeInterval/60;

        return $curtime;
    }
    
    public static function getExt($file)
    {
	$dot = strrpos($file, '.') + 1;

	return substr($file, $dot);
    }
    
     /**
	 * Strips the last extension off of a file name
	 *
	 * @param   string  $file  The file name
	 *
	 * @return  string  The file name without the extension
	 *
	 * @since   11.1
	 */
    public static function stripExt($file)
    {
	return preg_replace('#\.[^.]*$#', '', $file);
    }
    
      //write log only in debug mode
    public static function write_log ( $log )  {
       if(defined('WP_DEBUG') && WP_DEBUG) {
        if ( is_array( $log ) || is_object( $log ) ) {
           error_log( print_r( $log, true ) );
        } else {
           error_log( $log );
        }    
       }
    }
    
    /*----------- Dropbox -----------------*/
    
    public static function getAllDropboxConfigs()
    {
        $default = array(
            'dropboxKey' =>'',    
            'dropboxSecret' =>'', 
            'dropboxSyncTime' => '5', 
            'dropboxSyncMethod'=>'sync_page_curl' ) ;
        return get_option('_wpmfAddon_dropbox_config',$default);
    }
    
    public static function saveDropboxConfigs($data)
    {
       
        $result = update_option('_wpmfAddon_dropbox_config', $data);
        return $result;
    }
    
    public static function getDataConfigByDropbox($name)
    {
        $DropboxParams = array();

        if (self::getAllDropboxConfigs()) {
            foreach (self::getAllDropboxConfigs() as $key=>$val)
            {
                
                if (strpos($key, 'dropbox') !== false)
                {  
                    $DropboxParams[$key] = $val;
                }
            }
            $result = null;
            switch ($name) {
                case 'dropbox':
                    $result =  $DropboxParams;
                    break;
            }
            return $result;
        }
    }
    
    /**
     * @param $cloudParams
     * @return bool
     */
    public static function setDropboxConfigsParams($dropboxParams)
    {
        $result = update_option('_wpmfAddon_dropbox_category_params', $dropboxParams);
        return $result;
    }
    
     public static function getDropboxParams()
    {
        return get_option('_wpmfAddon_dropbox_category_params', array());        
    }
    
     //get id by termID
    public static function getDropboxIdByTermId($termId){
         $returnData = false;
        $dropParams = self::getDropboxParams();
        if ($dropParams && isset($dropParams[$termId]) ) {
            $returnData =  $dropParams[$termId]['idDropbox'];           
        }
        return $returnData;
    }
    public static function getIdFolderByTermId($termId){
        $returnData = FALSE;
        $dropParams = self::getDropboxParams();
        if ($dropParams && isset($dropParams[$termId]) ) {
            $returnData =  $dropParams[$termId]['id'];           
        }
        return $returnData;
    }
    
    //get term id by Path
    public static function getTermIdByDropboxPath($path) {
        $dropbox_list = self::getDropboxParams();
        $result = false;
        $path = strtolower($path);
        if(!empty($dropbox_list)) {
            foreach($dropbox_list as $k => $v) {
                if(strtolower($v['idDropbox']) == $path) {
                    $result =  $k;
                }
            }
        }
        
        return $result;
    }
    //get path by id
     public static function getPathByDropboxId($id) {
        $dropbox_list = self::getDropboxParams();
        $result = false;
        if(!empty($dropbox_list)) {
            foreach($dropbox_list as $k => $v) {
                if($v['id'] == $id) {
                    $result =  $v['idDropbox'];
                }
            }
        }
        
        return $result;
    }
    
    public static function setDropboxFileInfos($params){
        $result = update_option('_wpmfAddon_dropbox_fileInfo', $params);
        return $result;
    }
    public static function getDropboxFileInfos(){
        return get_option('_wpmfAddon_dropbox_fileInfo');
    }
    
     public static function curSyncIntervalDropbox() {
         //get last_log param
        $config = self::getAllDropboxConfigs(); 
        if(isset($config['last_log']) && !empty($config['last_log']) ) {
            $last_log = $config['last_log'];
            $last_sync =(int)strtotime($last_log);
        }
        else{
            $last_sync=0;
        }

        $time_new=(int)strtotime(date('Y-m-d H:i:s'));
        $timeInterval=$time_new- $last_sync;
        $curtime=$timeInterval/60;

        return $curtime;
    }
}
