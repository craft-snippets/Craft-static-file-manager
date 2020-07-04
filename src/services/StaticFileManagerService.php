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

    public function getSortedFiles($includeGoogleFonts = true)
    {
        $files = StaticFileManager::$plugin->getSettings()->filesList;

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

    public function injectAssets()
    {

        foreach($this->getSortedFiles()['js'] as $file){
            $file = $this->getFileUrl($file);
            Craft::$app->getView()->registerJsFile($file);
        }

        foreach($this->getSortedFiles()['css'] as $file){
            $file = $this->getFileUrl($file);
            Craft::$app->getView()->registerCssFile($file);
        }

    }

    public function getFileUrl($file)
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

}