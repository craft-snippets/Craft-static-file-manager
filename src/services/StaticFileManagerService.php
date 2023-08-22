<?php
/**
 * Static file manager plugin for Craft CMS 3.x
 *
 * Static file manager
 *
 * @link      http://craftsnippets.com
 * @copyright Copyright (c) 2019 Piotr Pogorzelski
 */

namespace craftsnippets\staticfilemanager\services;

use craftsnippets\staticfilemanager\StaticFileManager;

use Craft;
use craft\base\Component;
use craft\helpers;

/**
 * @author    Piotr Pogorzelski
 * @package   StaticFileManager
 * @since     1.0.0
 */
class StaticFileManagerService extends Component
{

    public function injectFrontendConcatd($key, $concateUrl)
    {
        $files = self::getSortedFiles(StaticFileManager::$plugin->getSettings()->filesList, true)[$key];
        $timestamps = [];
        $remoteFiles = [];

        foreach ($files as $file) {
            $filePath = Craft::getAlias('@webroot') . DIRECTORY_SEPARATOR . $file;
            if(file_exists($filePath)){
                $timestamps[] = filemtime($filePath);
            }else{
                // remote or missing files
                 $remoteFiles[] = $file;   
            }            
        }
        $latestTimestamp = !empty($timestamps) ? max($timestamps) : null;
        
        // register remote files
        foreach ($remoteFiles as $remoteFile) {
            Craft::$app->getView()->registerCssFile($remoteFile);
        }

        // register concated
        $concatePath = Craft::getAlias('@web') . '/' . $concateUrl;
        if(!is_null($latestTimestamp)){
            $concatePath .= '?v=' . $latestTimestamp;
        }
        if($key == 'css'){
            Craft::$app->getView()->registerCssFile($concatePath);
        }
        if($key == 'js'){
            Craft::$app->getView()->registerJsFile($concatePath);
        }
        
    }

    public function getConcatedContent($key)
    {
        $files = self::getSortedFiles(StaticFileManager::$plugin->getSettings()->filesList, true)[$key];
        $concatedContent = '';
        foreach ($files as $file) {
            $filePath = Craft::getAlias('@webroot') . DIRECTORY_SEPARATOR . $file;
            if(file_exists($filePath)){
                $concatedContent .= file_get_contents($filePath);
            }           
        }
        return $concatedContent;
    }

    public static function injectFrontend()
    {
        $files = StaticFileManager::$plugin->getSettings()->filesList;
        self::injectAssets($files);

        if(StaticFileManager::$plugin->getSettings()->concateJs === true){
            $sortedFiles = self::getSortedFiles($files, true);
            foreach($sortedFiles['js'] as $file){
                $file = self::getFileUrl($file);
                Craft::$app->getView()->registerJsFile($file);
            }
        }else{
            self::injectFrontendConcatd('js', 'static-scripts');
        }

        if(StaticFileManager::$plugin->getSettings()->concateJs === true){
            $sortedFiles = self::getSortedFiles($files, true);
            foreach($sortedFiles['css'] as $file){
                $file = self::getFileUrl($file);
                Craft::$app->getView()->registerJsFile($file);
            }
        }else{
            self::injectFrontendConcatd('css', 'static-styles');
        }

    }

    public static function injectCp()
    {   
        if(Craft::$app->getRequest()->isCpRequest){
            $files = StaticFileManager::$plugin->getSettings()->cpFileList;
            self::injectAssets($files);
        }
    }


    public static function injectAssets($files)
    {
        $sortedFiles = self::getSortedFiles($files, true);

        foreach($sortedFiles['js'] as $file){
            $file = self::getFileUrl($file);
            Craft::$app->getView()->registerJsFile($file);
        }

        foreach($sortedFiles['css'] as $file){
            $file = self::getFileUrl($file);
            Craft::$app->getView()->registerCssFile($file);
        }

    }


    public static function getSortedFiles($files, $includeGoogleFonts = true)
    {

        $sortedFiles = array('css' => [], 'js' => []);

        foreach($files as $file) {

            if(is_callable($file)){
                $file = call_user_func($file);
            }

            $typeExploded = explode('.',$file);
            $type = end($typeExploded);

            if ($type == 'css') {
                $sortedFiles['css'][] = $file;
            }
            if ($type == 'js') {
                $sortedFiles['js'][] = $file;
            }

            // google fonts
            if ($includeGoogleFonts){
                if(strpos($file, 'https://fonts.googleapis.com') !== false) {
                    $sortedFiles['css'][] = $file;
                }
            }

        }

        return $sortedFiles;
    }


    public static function getFileUrl($file)
    {
        
        $file_path = Craft::getAlias('@webroot') . DIRECTORY_SEPARATOR . $file;
        if(file_exists($file_path)){
            $file_url = Craft::getAlias('@web') . '/' . $file;
            // bust cache
            if(StaticFileManager::$plugin->getSettings()->bustCache === true){
                $file_url .= '?v=' . filemtime($file_path);
            }
        }else{
            // remote or missing files
             $file_url = $file;   
        }
        return $file_url;
    }


    public static function getFilesPaths()
    {
        $files = StaticFileManager::$plugin->getSettings()->filesList;
        $sortedFiles = self::getSortedFiles($files, false);
        $processedFiles = array();

        foreach ($sortedFiles as $categoryKey => $category){
            foreach ($category as $file){
                $path = Craft::getAlias('@webroot') . DIRECTORY_SEPARATOR . $file;
                if(file_exists($path)){
                    $processedFiles[$categoryKey][] = $path;
                }
            }
        }        
        return $processedFiles;
    }

}