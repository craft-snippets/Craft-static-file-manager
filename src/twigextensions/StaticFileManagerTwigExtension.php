<?php
/**
 * Static file manager plugin for Craft CMS 3.x
 *
 * Static file manager
 *
 * @link      http://craftsnippets.com
 * @copyright Copyright (c) 2019 Piotr Pogorzelski
 */

namespace craftsnippets\staticfilemanager\twigextensions;

use craftsnippets\staticfilemanager\StaticFileManager;
use craftsnippets\staticfilemanager\services\StaticFileManagerService as StaticFileManagerService;


use Craft;

/**
 * @author    Piotr Pogorzelski
 * @package   StaticFileManager
 * @since     1.0.0
 */
class StaticFileManagerTwigExtension extends \Twig_Extension
{


    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('version', [$this, 'bustCache']),
            new \Twig_SimpleFilter('bustCache', [$this, 'bustCache']),
        ];
    }


    public function bustCache($path)
    {
        $service = new StaticFileManagerService();
        return $service->getFileUrl($path);
    }
}
