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



    public function getSortedFiles()
    {
        $files = StaticFileManager::$plugin->getSettings()->filesList;

        $sortedFiles = array('css' => [], 'js' => []);

        foreach($files as $file) {

            if(is_callable($file)){
                $file = call_user_func($file);
            }

            $typeExploded = explode('.',$file);
            $type = end($typeExploded);

            $file = \craft\helpers\FileHelper::normalizePath($file);

            if ($type == 'css') {
                $sortedFiles['css'][] = $file;
            }
            if ($type == 'js') {
                $sortedFiles['js'][] = $file;
            }
        }

        return $sortedFiles;
    }

    public function injectAssets(){

        foreach($this->getSortedFiles()['js'] as $file){
            $file = $this->getFileUrl($file);
            Craft::$app->getView()->registerJsFile($file);
        }

        foreach($this->getSortedFiles()['css'] as $file){
            $file = $this->getFileUrl($file);
            Craft::$app->getView()->registerCssFile($file);
        }

    }

    public function getFileUrl($file){
        $file_url = Craft::getAlias('@web') . '/' . $file;
        $file_path = Craft::getAlias('@webroot') . DIRECTORY_SEPARATOR . $file;
        if(StaticFileManager::$plugin->getSettings()->bustCache === true && file_exists($file_path)){
            $file_url .= '?' . filemtime($file_path);
        }
        return $file_url;
    }



}
