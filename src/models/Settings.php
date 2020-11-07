<?php
/**
 * Static file manager plugin for Craft CMS 3.x
 *
 * Static file manager
 *
 * @link      http://craftsnippets.com
 * @copyright Copyright (c) 2019 Piotr Pogorzelski
 */

namespace craftsnippets\staticfilemanager\models;

use craftsnippets\staticfilemanager\StaticFileManager;

use Craft;
use craft\base\Model;

/**
 * @author    Piotr Pogorzelski
 * @package   StaticFileManager
 * @since     1.0.0
 */
class Settings extends Model
{
    public $bustCache = true;
    public $exposeJsonList = false;
    public $filesList = [];
    public $cpFileList = [];
}
