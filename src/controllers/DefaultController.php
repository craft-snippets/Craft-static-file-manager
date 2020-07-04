<?php
/**
 * Static file manager plugin for Craft CMS 3.x
 *
 * Static file manager
 *
 * @link      http://craftsnippets.com
 * @copyright Copyright (c) 2019 Piotr Pogorzelski
 */

namespace craftsnippets\staticfilemanager\controllers;

use craftsnippets\staticfilemanager\StaticFileManager;

use Craft;
use craft\web\Controller;
use craftsnippets\staticfilemanager\services\StaticFileManagerService as StaticFileManagerService;
use craft\helpers;
/**
 * @author    Piotr Pogorzelski
 * @package   StaticFileManager
 * @since     1.0.0
 */
class DefaultController extends Controller
{

    protected $allowAnonymous = ['index'];

    // Public Methods
    // =========================================================================

    /**
     * @return mixed
     */

    public function init()
    {
        if(StaticFileManager::$plugin->getSettings()->exposeJsonList === true) {
            parent::init();
        }
    }

    public function actionIndex()
    {
        $service = new StaticFileManagerService();
        // files list without google fonts
        $files = $service->getSortedFiles(false);
        $processedFiles = array();
        foreach ($files as $categoryKey => $category){
            foreach ($category as $file){
                $path = Craft::getAlias('@webroot') . DIRECTORY_SEPARATOR . $file;
                if(file_exists($path)){
                    $processedFiles[$categoryKey][] = $path;
                }
            }
        }

        return json_encode($processedFiles);
    }


}
