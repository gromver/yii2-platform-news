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
use gromver\platform\news\models\Post;
use gromver\platform\core\modules\widget\widgets\Widget;
use yii\data\ActiveDataProvider;
use Yii;

/**
 * Class PostList
 * @package yii2-platform-news
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class PostList extends Widget
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
    public $layout = 'post/listDefault';
    /**
     * @field list
     * @items itemLayouts
     * @editable
     * @translation gromver.platform
     */
    public $itemLayout = '_itemIssue';
    /**
     * @translation gromver.platform
     */
    public $pageSize = 20;
    /**
     * @field list
     * @editable
     * @items sortColumns
     * @var string
     * @translation gromver.platform
     */
    public $sort = 'published_at';
    /**
     * @field list
     * @editable
     * @items sortDirections
     * @translation gromver.platform
     */
    public $dir = SORT_DESC;
    /**
     * @ignore
     */
    public $listViewOptions = [];

    protected function launch()
    {
        if ($this->category && !$this->category instanceof Category) {
            $this->category = Category::findOne(intval($this->category));
        }

        echo $this->render($this->layout, [
            'dataProvider' => new ActiveDataProvider([
                    'query' => $this->getQuery(),
                    'pagination' => [
                        'pageSize' => $this->pageSize
                    ],
                    'sort' => [
                        'defaultOrder' => [$this->sort => intval($this->dir)]
                    ]
                ]),
            'itemLayout' => $this->itemLayout,
            'category' => $this->category,
            'listViewOptions' => $this->listViewOptions
        ]);
    }

    protected function getQuery()
    {
        return Post::find()->published()->category($this->category ? $this->category->id : null)->with(['tags', 'postViewed'])->last();
    }

    public function customControls()
    {
        return [
            [
                'url' => ['/news/backend/post/create', 'category_id' => $this->category ? $this->category->id : null, 'backUrl' => $this->getBackUrl()],
                'label' => '<i class="glyphicon glyphicon-plus"></i>',
                'options' => ['title' => Yii::t('gromver.platform', 'Create Post')]
            ],
            [
                'url' => ['/news/backend/post/index', 'PostSearch' => ['category_id' => ($this->category ? $this->category->id : null)]],
                'label' => '<i class="glyphicon glyphicon-th-list"></i>',
                'options' => ['title' => Yii::t('gromver.platform', 'Posts list'), 'target' => '_blank']
            ],
        ];
    }

    public static function layouts()
    {
        return [
            'post/listDefault' => Yii::t('gromver.platform', 'Default'),
            'post/listBlog' => Yii::t('gromver.platform', 'List with calendar and tags'),
        ];
    }

    public static function itemLayouts()
    {
        return [
            '_itemArticle' => Yii::t('gromver.platform', 'Article'),
            '_itemIssue' => Yii::t('gromver.platform', 'Issue'),
        ];
    }


    public static function sortColumns()
    {
        return [
            'published_at' => Yii::t('gromver.platform', 'By publish date'),
            'created_at' => Yii::t('gromver.platform', 'By create date'),
            'title' => Yii::t('gromver.platform', 'By name'),
            'ordering' => Yii::t('gromver.platform', 'By order'),
        ];
    }

    public static function sortDirections()
    {
        return [
            SORT_ASC => Yii::t('gromver.platform', 'Asc'),
            SORT_DESC => Yii::t('gromver.platform', 'Desc'),
        ];
    }
}