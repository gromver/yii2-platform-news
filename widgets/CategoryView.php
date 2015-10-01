<?php
/**
 * @link https://github.com/gromver/yii2-platform-news.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-news/blob/master/LICENSE
 * @package yii2-platform-news
 * @version 1.0.0
 */

namespace gromver\platform\news\widgets;


use gromver\platform\news\models\Category;
use gromver\platform\core\modules\widget\widgets\Widget;
use yii\base\InvalidConfigException;
use Yii;

/**
 * Class CategoryView
 * @package yii2-platform-news
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class CategoryView extends Widget
{
    /**
     * Category or CategoryId or CategoryId:CategoryPath
     * @var Category|string
     * @field modal
     * @url /news/backend/category/select
     * @translation gromver.platform
     */
    public $category;
    /**
     * @field list
     * @items layouts
     * @editable
     * @translation gromver.platform
     */
    public $layout = 'category/viewDefault';

    protected function launch()
    {
        if ($this->category && !$this->category instanceof Category) {
            $this->category = Category::findOne(intval($this->category));
        }

        if (empty($this->category)) {
            throw new InvalidConfigException(Yii::t('gromver.platform', 'Category not found.'));
        }

        echo $this->render($this->layout, [
            'model' => $this->category,
        ]);
    }

    public function customControls()
    {
        return [
            [
                'url' => ['/news/backend/category/update', 'id' => $this->category->id, 'backUrl' => $this->getBackUrl()],
                'label' => '<i class="glyphicon glyphicon-pencil"></i>',
                'options' => ['title' => Yii::t('gromver.platform', 'Update Category')]
            ],
        ];
    }

    public static function layouts()
    {
        return [
            'category/viewDefault' => Yii::t('gromver.platform', 'Default'),
            'category/viewVerbose' => Yii::t('gromver.platform', 'Verbose'),
            'category/viewOnlyCategories' => Yii::t('gromver.platform', 'Only categories list'),
            'category/viewOnlyPosts' => Yii::t('gromver.platform', 'Only posts list'),
        ];
    }
}