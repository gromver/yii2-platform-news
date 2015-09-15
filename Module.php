<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\news;


use gromver\modulequery\ModuleEventsInterface;
use gromver\platform\core\components\MenuUrlRule;
use gromver\platform\core\modules\main\widgets\Desktop;
use gromver\platform\core\modules\menu\widgets\MenuItemRoutes;
use gromver\platform\news\components\MenuRouterNews;
use gromver\platform\news\models\Category;
use gromver\platform\news\models\Post;
use gromver\platform\core\modules\search\modules\elastic\Module as ElasticModule;
use gromver\platform\core\modules\search\modules\elastic\widgets\SearchResultsFrontend as ElasticSearchResults;
use gromver\platform\core\modules\search\modules\sql\widgets\SearchResultsFrontend as SqlSearchResults;
use Yii;

/**
 * Class Module
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Module extends \yii\base\Module implements ModuleEventsInterface
{
    public $controllerNamespace = 'gromver\platform\news\controllers';
    public $defaultRoute = 'backend/post';
    public $rssPageSize = 50;

    /**
     * @param $event \gromver\platform\core\modules\main\widgets\events\DesktopEvent
     */
    public function addDesktopItem($event)
    {
        $event->items[] = [
            'label' => Yii::t('gromver.platform', 'News'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Categories'), 'url' => ['/news/backend/category/index']],
                ['label' => Yii::t('gromver.platform', 'Posts'), 'url' => ['/news/backend/post/index']],
            ]
        ];
    }

    /**
     * @param $event \gromver\platform\core\modules\menu\widgets\events\MenuItemRoutesEvent
     */
    public function addMenuItemRoutes($event)
    {
        $event->items[] = [
            'label' => Yii::t('gromver.platform', 'News'),
            'items' => [
                ['label' => Yii::t('gromver.platform', 'Post View'), 'url' => ['/news/backend/post/select']],
                ['label' => Yii::t('gromver.platform', 'Category View'), 'url' => ['/news/backend/category/select']],
                ['label' => Yii::t('gromver.platform', 'All Posts'), 'route' => 'news/frontend/post/index'],
            ]
        ];
    }

    /**
     * @param $event \gromver\platform\core\components\events\FetchRoutersEvent
     */
    public function addMenuRouter($event)
    {
        $event->routers[] = MenuRouterNews::className();
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            Desktop::EVENT_FETCH_ITEMS => 'addDesktopItem',
            MenuItemRoutes::EVENT_FETCH_ITEMS => 'addMenuItemRoutes',
            MenuUrlRule::EVENT_FETCH_MODULE_ROUTERS => 'addMenuRouter',
            ElasticModule::EVENT_BEFORE_CREATE_INDEX . Post::className() => [Post::className(), 'elasticBeforeCreateIndex'],
            ElasticSearchResults::EVENT_BEFORE_SEARCH . Post::className()  => [Post::className(), 'elasticBeforeFrontendSearch'],
            ElasticSearchResults::EVENT_BEFORE_SEARCH . Category::className()  => [Category::className(), 'elasticBeforeFrontendSearch'],
            SqlSearchResults::EVENT_BEFORE_SEARCH . Post::className() => [Post::className(), 'sqlBeforeFrontendSearch'],
            SqlSearchResults::EVENT_BEFORE_SEARCH . Category::className() => [Category::className(), 'sqlBeforeFrontendSearch'],
        ];
    }
}
