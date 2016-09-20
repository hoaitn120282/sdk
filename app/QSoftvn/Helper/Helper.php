<?php
namespace QSoftvn\Helper;

use App\Exceptions\ErrorDefinition;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * This class provide convenient static methods for the developer
 * @package QSoftvn\Helper
 */
class Helper
{
    /**
     * Get user's menu
     * @return array
     */
    public static function getUserMenu(){
        $config = app('config');
        $menu = $config['menu'];
        $m = [];
        foreach($menu as $mnu){
            foreach($mnu as $mn){
                $rm = self::removeUnaccessibleItem($mn);
                if($rm){
                    $m[] = $rm;
                }
            }
        }
        self::deleteArrayKeys($m,array('route'));
        return $m;
    }

    /**
     * Get all configured routes for permissions
     * @return array
     */
    public static function getAllRoutes(){
        $data = [];
        $widgetList = Helper::getWidgetList();
        foreach($widgetList as $widget=>$config){
            if(isset($config['routes'])){
                foreach($config['routes'] as $route=>$cfg){
                    $data[str_replace('\\','__',$route)] = array(
                        'method'=>$cfg['method'],
                        'name'=>$cfg['name'],
                        'widget'=>$widget,
                        'related'=>(isset($cfg['related'])?$cfg['related']:array())
                    );
                }
            }
        }
        return $data;
    }

    public static function getAccessibleRoutesForAdmin(){
        $data = [];
        $widgetList = Helper::getWidgetList();
        foreach($widgetList as $widget=>$config){
            if(isset($config['routes'])){
                foreach($config['routes'] as $route=>$cfg){
                    $data[] = str_replace('\\','__',$route);
                }
            }
        }
        return $data;
    }
    /**
     * Get a widget's route
     * @param $widget
     * @return array
     */
    public static function getWidgetRoutes($widget){
        $data = [];
        $widgetList = self::getWidgetList();
        if(isset($widgetList[$widget])){
            $config = $widgetList[$widget];
            if(isset($config['routes'])){
                foreach($config['routes'] as $route=>$cfg){
                    $data[str_replace('\\','__',$route)] = array(
                        'method'=>$cfg['method'],
                        'name'=>$cfg['name']
                    );
                }
            }
        }
        /*$config = self::getWidgetConfig($widget);
        if(count($config)){
            if(isset($config[$widget]['routes'])){
                foreach($config[$widget]['routes'] as $route=>$cfg){
                    $data[str_replace('\\','__',$route)] = array(
                        'method'=>$cfg['method'],
                        'name'=>$cfg['name']
                    );
                }
            }
        }*/
        return $data;
    }

    /**
     * Get all the public routes
     * @return array
     */
    protected static function getPublicWidgetRoutes(){
        $data = [];
        $widgetList = Helper::getWidgetList();
        foreach($widgetList as $widget=>$config){
            if($config['public']==true){
                if(isset($config['routes'])){
                    foreach($config['routes'] as $route=>$cfg){
                        $data[] = str_replace('\\','__',$route);
                    }
                }
            }

        }
        return $data;
    }
    /**
     * Get all accessible routes of current user, including shared, related and custom
     * @return array|mixed
     */
    public static function getAccessibleRoutesForMe(){
        if(self::isSuperAdmin()===true){
            return self::getAccessibleRoutesForAdmin();
        }
        else{
            $myAccount = app('Dingo\Api\Auth\Auth')->user();
            if($myAccount->routes || $myAccount->routes_related || $myAccount->routes_shared || $myAccount->routes_custom){
                return array_merge(self::getPublicWidgetRoutes(),json_decode((Helper::simpleDecrypt($myAccount->routes)?Helper::simpleDecrypt($myAccount->routes):'[]'),true),json_decode((Helper::simpleDecrypt($myAccount->routes_shared)?Helper::simpleDecrypt($myAccount->routes_shared):'[]'),true),json_decode((Helper::simpleDecrypt($myAccount->routes_related)?Helper::simpleDecrypt($myAccount->routes_related):'[]'),true),json_decode((Helper::simpleDecrypt($myAccount->routes_custom)?Helper::simpleDecrypt($myAccount->routes_custom):'[]'),true));
            }
            else{
                return self::getPublicWidgetRoutes();
            }
        }

    }

    /**
     * Get all assigned accessible routes of current user (Exclude shared and related)
     * @return array|mixed
     */
    public static function getAssignedAccessibleRoutesForMe(){
        if(self::isSuperAdmin()===true){
            return self::getAccessibleRoutesForAdmin();
        }
        else{
            $myAccount = app('Dingo\Api\Auth\Auth')->user();
            if($myAccount->routes || $myAccount->routes_related || $myAccount->routes_shared || $myAccount->routes_custom){
                return array_merge(self::getPublicWidgetRoutes(),json_decode((Helper::simpleDecrypt($myAccount->routes)?Helper::simpleDecrypt($myAccount->routes):'[]'),true),json_decode((Helper::simpleDecrypt($myAccount->routes_custom)?Helper::simpleDecrypt($myAccount->routes_custom):'[]'),true));
            }
            else{
                return self::getPublicWidgetRoutes();
            }
        }

    }

    //TODO: Better implementation
    /**
     * Detect whether a widget is public or not
     * @param $widgetName
     * @return bool
     */
    public static function isPublic($widgetName){
        $v = false;
        $widgetList = Helper::getWidgetList();
        foreach($widgetList as $widget=>$config){
            if($widget==$widgetName && $config['public']===true){
                $v = true;
                break;
            }
        }
        return $v;
    }

    /**
     * Get the rightsConfig from User and filter it.
     * @param $widget
     * @return array
     */
    public static function getRightsConfig($widget){
        $myAccount = app('Dingo\Api\Auth\Auth')->user();
        if($myAccount->permissions){
            $fromDbConfig = json_decode(self::simpleDecrypt($myAccount->permissions),true);
        }
        else{
            $fromDbConfig = array();
        }

        //If no rights config for the widget, he is not able to access data, throw UnauthorizedHttpException
        if (!isset($fromDbConfig[$widget])) {
            if(self::isSuperAdmin() !== true && !self::isPublic($widget)){
                throw new UnauthorizedHttpException(ErrorDefinition::UNAUTHORIZED_MESSAGE);
            }
        }
        else{
            $widgetRightsConfig = $fromDbConfig[$widget];
        }
        if(Helper::isSuperAdmin() === true || self::isPublic($widget)){
            $widgetList = self::getWidgetList('V1');
            return array(
                array(
                    'viewableFields' => $widgetList[$widget]['viewableFields'],
                    'validFrom' => '1970-01-01',
                    'validTo' => null,
                    'viewableByAccounts' => array('*'),
                    'viewableExceptAccounts' => array(),
                    'viewableType' => 3,
                    'viewableConditions' => array(),
                    'viewableMaxRecord' => null,
                    'editableFields' => array('*'),
                    'editableByAccounts' => array('*'),
                    'editableExceptAccounts' => array(),
                    'editableType' => 3,
                    'editableConditions' => array(),
                    'deletableByAccounts' => array('*'),
                    'deletableExceptAccounts' => array(),
                    'deletableType' => 3,
                    'deletableConditions' => array(),
                    'exportableFields' => array('*'),
                    'exportableByAccounts' => array('*'),
                    'exportableExceptAccounts' => array(),
                    'exportableType' => 3,
                    'exportableConditions' => array()
                )
            );
        }
        else{
            $today = time();
            //Check validFrom later than today.
            $rightsConfig = array_filter($widgetRightsConfig, function($item) use ($today){
                if (strtotime($item['validFrom']) - $today > 0) {
                    return false;
                }
                //Check if it has validTo and validTo was passed. Should plus 86399 for the end of day of valid to
                if ($item['validTo']) {
                    if (strtotime($item['validTo']) + 86399 - $today < 0) {
                        return false;
                    }
                }
                return true;
            });
            //Helper::d($rightsConfig);
            return $rightsConfig;
        }
    }

    /**
     * Get list of the available widgets
     * @param string $version
     * @return array
     */
    public static function getWidgetList($version='V1'){
        $widgets = array();

        //Get the modules' config files
        $directories = glob(app_path().'/Api/'.$version.'/Modules/*' , GLOB_ONLYDIR);
        foreach($directories as $dir){
            if(file_exists($dir.'/config.php')) {
                $widgets = array_merge($widgets,include $dir.'/config.php');
            }
        }
        //Get the core modules config files
        $directories = glob(app_path().'/QSoftvn/Modules/*' , GLOB_ONLYDIR);
        foreach($directories as $dir){
            if(file_exists($dir.'/config.php')) {
                $widgets = array_merge($widgets,include $dir.'/config.php');
            }
        }
        return $widgets;
    }

    public static function getWidgetConfig($widgetName, $version='V1'){
        $widgetConfig = [];
        $dir = app_path().'/Api/'.$version.'/Modules/'.Inflector::singularize($widgetName);

        if(file_exists($dir.'/config.php')) {
            $widgetConfig = include $dir.'/config.php';
        }
        return $widgetConfig;
    }

    /**
     * Verify whether the current user is a super admin
     * @return bool
     */
    public static function isSuperAdmin(){
        $myAccount = app('Dingo\Api\Auth\Auth')->user();
        $myEmail = trim($myAccount->email);
        $mySecretKey = $myAccount->secret_key;
        if(Helper::simpleDecrypt($mySecretKey)==$myEmail){
            return true;
        }
        return false;
    }
    /**
     * Convert strings with underscores into CamelCase
     *
     * @param    string    $string    The string to convert
     * @param    bool    $first_char_caps    camelCase or CamelCase
     * @return    string    The converted string
     *
     */
    public static function underscoreToCamelCase( $string, $first_char_caps = false)
    {
        if( $first_char_caps == true )
        {
            $string[0] = strtoupper($string[0]);
        }
        $func = create_function('$c', 'return strtoupper($c[1]);');

        return preg_replace_callback('/_([a-z])/', $func, $string);
    }

    /**
     * Translates a camel case string into a string with
     * underscores (e.g. firstName -> first_name)
     *
     * @param string $str String in camel case format
     * @return string $str Translated into underscore format
     */
    public static function camelCaseToUnderscore($str) {
        $str[0] = strtolower($str[0]);
        $func = create_function('$c', 'return "_" . strtolower($c[1]);');
        return preg_replace_callback('/([A-Z])/', $func, $str);
    }
    public static function d($data){
        header("Access-Control-Allow-Origin: *");
        var_dump($data); //die();
    }
    public static function getRandomString($length = 10){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz!~@#$^&*_+[]{},.?';
        $string = '';
        for ($p = 0; $p < $length; $p++) {
            $string.= $characters[mt_rand(0, strlen($characters)-1)];
        }
        return $string;
    }

    /**
     * Encrypt data with hash. Used in database
     * @param $string
     * @return bool|null|string
     */
    public static function simpleEncrypt($string){
        if(!$string) return null;
        $config = app('config');
        $key = hash('SHA256', $config['key'],true);
        srand(); $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
        if (strlen($iv_base64 = rtrim(base64_encode($iv), '=')) != 22) return false;
        $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $string . md5($string), MCRYPT_MODE_CBC, $iv));
        return $iv_base64 . $encrypted;
    }

    /**
     * Decrypt data with hash. Used in database
     * @param $encryptedString
     * @return bool|null|string
     */
    public static function simpleDecrypt($encryptedString){
        if(!$encryptedString)  return null;
        $config = app('config');
        $key = hash('SHA256', $config['key'],true);
        $iv = base64_decode(substr($encryptedString, 0, 22) . '==');
        $encrypted = substr($encryptedString, 22);
        $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($encrypted), MCRYPT_MODE_CBC, $iv), "\0\4");
        $hash = substr($decrypted, -32);
        $decrypted = substr($decrypted, 0, -32);
        if (md5($decrypted) != $hash) return false;
        return $decrypted;
    }

    /**
     * @ignore
     * @param $item
     * @return bool
     */
    protected static function hasAccessibleItem($item){
        if(self::isSuperAdmin()===true){
            return true;
        }
        $myAccount = app('Dingo\Api\Auth\Auth')->user();
        $accessibleRoutes = [];
        $publicRoutes = self::getPublicWidgetRoutes();
        if($myAccount->routes || $myAccount->routes_custom){
            $myRoutes = json_decode((Helper::simpleDecrypt($myAccount->routes)?Helper::simpleDecrypt($myAccount->routes):'[]'),true);
            $myCustomRoutes = json_decode((Helper::simpleDecrypt($myAccount->routes_custom)?Helper::simpleDecrypt($myAccount->routes_custom):'[]'),true);
            $accessibleRoutes = array_merge($myRoutes, $myCustomRoutes, $publicRoutes);
        }
        if(isset($item['children'])){
            foreach($item['children'] as $arrChild){
                $r = self::hasAccessibleItem($arrChild);
                if($r == true){
                    return true;
                }
            }
        }
        if((isset($item['route']) && in_array(str_replace('\\','__',$item['route']),$accessibleRoutes)) || (isset($item['public']) && $item['public']==true)){
            return true;
        }
        return false;
    }

    /**
     * @ignore
     * @param $item
     * @return mixed
     */
    protected static function removeUnaccessibleItem(&$item){
        if(isset($item['children'])){
            $mc = [];
            foreach($item['children'] as &$arrChild){
                $rcm = self::removeUnaccessibleItem($arrChild);
                if($rcm){
                    $mc[] = $rcm;
                }
            }
            if($mc){
                $item['children'] = ($mc);
            }
            else{
                unset($item['children']);
            }

        }
        if(!isset($item['children']) || count($item['children'])==0){
            $item['leaf']=true;
        }

        if(!self::hasAccessibleItem($item)){
            unset($item);
        }

        if(isset($item)){
            return $item;
        }

    }

    /**
     * Get all public widgets
     * @return array
     */
    protected static function getAllPublicWidgets(){
        $data = [];
        $widgetList = Helper::getWidgetList();
        foreach($widgetList as $widget=>$config){
            if($config['public']===true){
                $data[]=$widget;
            }
        }
        return $data;
    }

    /**
     * Delete keys from multidimentional array.
     * @param $array
     * @param $keys
     */
    protected static function deleteArrayKeys(&$array, $keys) {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                self::deleteArrayKeys($value, $keys);
            } else {
                if (in_array($key, $keys)){
                    unset($array[$key]);
                }
            }
        }
    }
}