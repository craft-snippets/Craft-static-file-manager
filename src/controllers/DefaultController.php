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
    
    public function actionIndex()
    {
        $processedFiles = StaticFileManagerService::getFilesPaths();
        if(StaticFileManager::$plugin->getSettings()->exposeJsonList === true) {
            return $this->asJson($processedFiles);
        }
    }


}
