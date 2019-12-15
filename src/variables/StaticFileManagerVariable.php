<?php
/**
 * Static file manager plugin for Craft CMS 3.x
 *
 * Static file manager
 *
 * @link      http://craftsnippets.com
 * @copyright Copyright (c) 2019 Piotr Pogorzelski
 */

namespace craftsnippets\staticfilemanager\variables;


use Craft;
use craftsnippets\staticfilemanager\services\StaticFileManagerService as StaticFileManagerService;


/**
 * @author    Piotr Pogorzelski
 * @package   StaticFileManager
 * @since     1.0.0
 */
class StaticFileManagerVariable
{
    // Public Methods
    // =========================================================================

    public function outputFiles($inject = true)
    {    
        if($inject){
            $service = new StaticFileManagerService();
            $service->injectAssets();           
        }
    }
}
