<?php
/**
 * @link https://github.com/gromver/yii2-platform-news.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-news/blob/master/LICENSE
 * @package yii2-platform-news
 * @version 1.0.0
 */

namespace gromver\platform\news\models;

use gromver\platform\core\components\ParamsObject;

/**
 * Class NewsParams
 * @package yii2-platform-news
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class NewsParams extends ParamsObject {
    /**
     * @field object
     * @object \gromver\platform\news\models\Common
     * @translation gromver.platform
     * @label Category Params
     */
    public $category = ['previewWidth' => 160, 'previewHeight' => 160, 'previewResize' => true];

    /**
     * @field object
     * @object \gromver\platform\news\models\Common
     * @translation gromver.platform
     * @label Post Params
     */
    public $post = ['previewWidth' => 160, 'previewHeight' => 160, 'previewResize' => true];

    /**
     * @return string
     */
    public static function paramsName()
    {
        return \Yii::t('gromver.platform', 'News');
    }

    /**
     * @return string
     */
    public static function paramsType()
    {
        return 'news';
    }
}

class Common {
    /**
     * @field text
     * @translation gromver.platform
     * @label Preview Width
     */
    public $previewWidth;
    /**
     * @field text
     * @translation gromver.platform
     * @label Preview Height
     */
    public $previewHeight;
    /**
     * @field yesno
     * @translation gromver.platform
     * @label Resize
     */
    public $previewResize;
}