<?php
/**
 * @link https://github.com/gromver/yii2-platform-news.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-news/blob/master/LICENSE
 * @package yii2-platform-news
 * @version 1.0.0
 */

namespace gromver\platform\news\components;


use gromver\platform\core\components\UrlManager;
use gromver\platform\news\models\Category;
use gromver\platform\core\modules\menu\models\MenuItem;
use gromver\platform\news\models\Post;
use gromver\platform\core\modules\tag\models\Tag;

/**
 * Class MenuRouterNews
 * @package yii2-platform-news
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MenuRouterNews extends \gromver\platform\core\components\MenuRouter
{
    public $postSuffix = 'html';

    /**
     * @inheritdoc
     */
    public function parseUrlRules()
    {
        return [
            [
                'menuRoute' => 'news/frontend/category/view',
                'handler' => 'parseCategory'
            ],
            [
                'menuRoute' => 'news/frontend/post/index',
                'handler' => 'parseAllPosts'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function createUrlRules()
    {
        return [
            [
                'requestRoute' => 'news/frontend/post/view',
                'requestParams' => ['id'],
                'handler' => 'createPost'
            ],
            [
                'requestRoute' => 'news/frontend/category/view',
                'requestParams' => ['id'],
                'handler' => 'createCategory'
            ],
            [
                'requestRoute' => 'news/frontend/post/day',
                'requestParams' => ['year', 'month', 'day'],
                'handler' => 'createDayPosts'
            ],
            [
                'requestRoute' => 'news/frontend/post/index',
                'handler' => 'createAllPosts'
            ],
            [
                'requestRoute' => 'news/frontend/post/rss',
                'handler' => 'createRss'
            ],
            [
                'requestRoute' => 'news/frontend/post/tag',
                'handler' => 'createTag'
            ],
        ];
    }

    /**
     * @param \gromver\platform\core\components\MenuRequestInfo $requestInfo
     * @return array
     */
    public function parseCategory($requestInfo)
    {
        if (preg_match("#((.*)/)?(rss)$#", $requestInfo->requestRoute, $matches)) {
            //rss лента
            if ($menuCategory = Category::findOne($requestInfo->menuParams['id'])) {
                $categoryPath = $matches[2];
                /** @var Category $category */
                $category = empty($categoryPath) ? $menuCategory : Category::findOne([
                    'path' => $menuCategory->path . '/' . $categoryPath,
                ]);
                if ($category) {
                    return ['news/frontend/post/rss', ['category_id' => $category->id]];
                }
            }
        } elseif (preg_match("#((.*)/)?(\d{4})/(\d{1,2})/(\d{1,2})$#", $requestInfo->requestRoute, $matches)) {
            //новости за определенную дату
            /** @var Category $menuCategory */
            if ($menuCategory = Category::findOne($requestInfo->menuParams['id'])) {
                $categoryPath = $matches[2];
                $year = $matches[3];
                $month = $matches[4];
                $day = $matches[5];
                /** @var Category $category */
                $category = empty($categoryPath) ? $menuCategory : Category::findOne([
                    'path' => $menuCategory->path . '/' . $categoryPath,
                ]);
                if ($category) {
                    return ['news/frontend/post/day', ['category_id' => $category->id, 'year' => $year, 'month' => $month, 'day' => $day]];
                }
            }
        } elseif (preg_match("#((.*)/)?(([^/]+)\.{$this->postSuffix})$#", $requestInfo->requestRoute, $matches)) {
            //ищем пост
            /** @var Category $menuCategory */
            if ($menuCategory = Category::findOne($requestInfo->menuParams['id'])) {
                $categoryPath = $matches[2];
                $postAlias = $matches[4];
                /** @var Category $category */
                $category = empty($categoryPath) ? $menuCategory : Category::findOne([
                    'path' => $menuCategory->path . '/' . $categoryPath,
                ]);
                if ($category && $postId = Post::find()->select('id')->where(['alias' => $postAlias, 'category_id' => $category->id])->scalar()) {
                    return ['news/frontend/post/view', ['id' => $postId, 'alias' => $postAlias]];
                }
            }
        } elseif (preg_match("#((.*)/)?(tag/([^/]+))$#", $requestInfo->requestRoute, $matches)) {
            //ищем тэг
            /** @var Category $menuCategory */
            if ($menuCategory = Category::findOne($requestInfo->menuParams['id'])) {
                $categoryPath = $matches[2];
                $tagAlias = $matches[4];
                /** @var Category $category */
                $category = empty($categoryPath) ? $menuCategory : Category::findOne([
                    'path' => $menuCategory->path . '/' . $categoryPath,
                ]);
                if ($category && $tagId = Tag::find()->select('id')->where(['alias' => $tagAlias])->scalar()) {
                    return ['news/frontend/post/tag', ['tag_id' => $tagId, 'tag_alias' => $tagAlias, 'category_id' => $category->id]];
                }
            }
        } else {
            //ищем категорию
            /** @var Category $menuCategory */
            if ($menuCategory = Category::findOne($requestInfo->menuParams['id'])) {
                /** @var Category $category */
                if ($category = Category::findOne([
                    'path' => $menuCategory->path . '/' . $requestInfo->requestRoute,
                ])) {
                    return ['news/frontend/category/view', ['id' => $category->id]];
                }
            }
        }
    }

    /**
     * @param \gromver\platform\core\components\MenuRequestInfo $requestInfo
     * @return array
     */
    public function parseAllPosts($requestInfo)
    {
        if ($requestInfo->requestRoute == 'rss') {
            return ['news/frontend/post/rss', []];
        } elseif (preg_match("#^(\d{4})/(\d{1,2})/(\d{1,2})$#", $requestInfo->requestRoute, $matches)) {
            //новости за определенную дату
            return ['news/frontend/post/day', ['year' => $matches[1], 'month' => $matches[2], 'day' => $matches[3]]];
        } elseif (preg_match("#^((.*)/)(([^/]+)\.{$this->postSuffix})$#", $requestInfo->requestRoute, $matches)) {
            //ищем пост
            $categoryPath = $matches[2];    //путь категории поста
            $postAlias = $matches[4];       //алиас поста
            /** @var Category $category */
            $category = Category::findOne([
                'path' => $categoryPath,
            ]);
            if ($category && $postId = Post::find()->select('id')->where(['alias' => $postAlias, 'category_id' => $category->id])->scalar()) {
                return ['news/frontend/post/view', ['id' => $postId]];
            }
        } elseif (preg_match("#^(tag/([^/]+))$#", $requestInfo->requestRoute, $matches)) {
            //ищем тег
            $tagAlias = $matches[2];
            if ($tagId = Tag::find()->select('id')->where(['alias' => $tagAlias])->scalar()) {
                return ['news/frontend/post/tag', ['tag_id' => $tagId]];
            }
        }
    }

    /**
     * @param \gromver\platform\core\components\MenuRequestInfo $requestInfo
     * @return mixed|null|string
     */
    public function createPost($requestInfo)
    {
        //пытаемся найти пункт меню ссылющийся на данный пост
        if ($path = $requestInfo->menuMap->getMenuPathByRoute(MenuItem::toRoute('news/frontend/post/view', ['id' => $requestInfo->requestParams['id']]))) {
            unset($requestInfo->requestParams['id'], $requestInfo->requestParams['category_id'], $requestInfo->requestParams['alias']);
            return MenuItem::toRoute($path, $requestInfo->requestParams);
        }
        //ищем пункт меню ссылающийся на категорию данного поста либо ее предков
        if (isset($requestInfo->requestParams['category_id']) && isset($requestInfo->requestParams['alias'])) {
            //можем привязаться к пункту меню ссылающемуся на категорию новостей к которой принадлежит данный пост(напрямую либо косвенно)
            if ($path = $this->findCategoryMenuPath($requestInfo->requestParams['category_id'], $requestInfo->menuMap)) {
                $path .= '/' . $requestInfo->requestParams['alias'] . '.' . $this->postSuffix;
                unset($requestInfo->requestParams['id'], $requestInfo->requestParams['category_id'], $requestInfo->requestParams['alias']);
                return MenuItem::toRoute($path, $requestInfo->requestParams);
            }
        }
        //привязываем ко всем новостям, если пукнт меню существует
        if ($path = $requestInfo->menuMap->getMenuPathByRoute('news/frontend/post/index')) {
            $path .= '/' . Post::findOne($requestInfo->requestParams['id'])->category->path . '/' . $requestInfo->requestParams['alias'] . '.' . $this->postSuffix;
            unset($requestInfo->requestParams['id'], $requestInfo->requestParams['category_id'], $requestInfo->requestParams['alias']);
            return MenuItem::toRoute($path, $requestInfo->requestParams);
        }
    }

    /**
     * @param \gromver\platform\core\components\MenuRequestInfo $requestInfo
     * @return mixed|null|string
     */
    public function createCategory($requestInfo)
    {
        if ($path = $this->findCategoryMenuPath($requestInfo->requestParams['id'], $requestInfo->menuMap)) {
            unset($requestInfo->requestParams['id']);
            return MenuItem::toRoute($path, $requestInfo->requestParams);
        }
    }

    /**
     * @param \gromver\platform\core\components\MenuRequestInfo $requestInfo
     * @return mixed|null|string
     */
    public function createDayPosts($requestInfo)
    {
        if ($requestInfo->requestParams['category_id']) {
            $path = $this->findCategoryMenuPath($requestInfo->requestParams['category_id'], $requestInfo->menuMap);
        } else {
            $path = $requestInfo->menuMap->getMenuPathByRoute('news/frontend/post/index');
        }

        if ($path) {
            $path .= "/{$requestInfo->requestParams['year']}/{$requestInfo->requestParams['month']}/{$requestInfo->requestParams['day']}";
            unset($requestInfo->requestParams['category_id'], $requestInfo->requestParams['year'], $requestInfo->requestParams['month'], $requestInfo->requestParams['day']);
            return MenuItem::toRoute($path, $requestInfo->requestParams);
        }
    }

    /**
     * @param \gromver\platform\core\components\MenuRequestInfo $requestInfo
     * @return mixed|null|string
     */
    public function createAllPosts($requestInfo)
    {
        if ($path = $requestInfo->menuMap->getMenuPathByRoute('news/frontend/post/index')) {
            return MenuItem::toRoute($path, $requestInfo->requestParams);
        }
    }

    /**
     * @param \gromver\platform\core\components\MenuRequestInfo $requestInfo
     * @return mixed|null|string
     */
    public function createTag($requestInfo)
    {
        //строим ссылку на основе пункта меню на категорию
        if (isset($requestInfo->requestParams['category_id']) && isset($requestInfo->requestParams['tag_alias']) && $path = $this->findCategoryMenuPath($requestInfo->requestParams['category_id'], $requestInfo->menuMap)) {
            $path .= '/tag/' . $requestInfo->requestParams['tag_alias'];
            unset($requestInfo->requestParams['tag_alias'], $requestInfo->requestParams['category_id'], $requestInfo->requestParams['tag_id']);
        }
        //строим ссылку на основе пункта меню на все новости
        if (isset($requestInfo->requestParams['tag_alias']) && $path = $requestInfo->menuMap->getMenuPathByRoute('news/frontend/post/index')) {
            $path .= '/tag/' . $requestInfo->requestParams['tag_alias'];
            unset($requestInfo->requestParams['tag_alias'], $requestInfo->requestParams['category_id'], $requestInfo->requestParams['tag_id']);
        }

        if (isset($path)) {
            return MenuItem::toRoute($path, $requestInfo->requestParams);
        }
    }

    /**
     * @param \gromver\platform\core\components\MenuRequestInfo $requestInfo
     * @return mixed|null|string
     */
    public function createRss($requestInfo)
    {
        if (isset($requestInfo->requestParams['category_id'])) {
            if ($path = $this->findCategoryMenuPath($requestInfo->requestParams['category_id'], $requestInfo->menuMap)) {
                unset($requestInfo->requestParams['category_id']);
                return MenuItem::toRoute($path . '/rss', $requestInfo->requestParams);
            }
        } else {
            if ($path = $requestInfo->menuMap->getMenuPathByRoute('news/frontend/post/index')) {
                return MenuItem::toRoute($path . '/rss', $requestInfo->requestParams);
            }
        }
    }

    private $_categoryPaths = [];

    /**
     * Находит путь к пункту меню ссылающемуся на категорию $categoryId, либо ее предка
     * Если путь ведет к предку, то достраиваем путь категории $categoryId
     * @param $categoryId
     * @param $menuMap \gromver\platform\core\components\MenuMap
     * @return null|string
     */
    private function findCategoryMenuPath($categoryId, $menuMap)
    {
        /** @var Category $category */
        if (!isset($this->_categoryPaths[$categoryId])) {
            if ($path = $menuMap->getMenuPathByRoute(MenuItem::toRoute('news/frontend/category/view', ['id' => $categoryId]))) {
                $this->_categoryPaths[$categoryId] = $path;
            } elseif (($category = Category::findOne($categoryId)) && !$category->isRoot() && $path = $this->findCategoryMenuPath($category->parent_id, $menuMap)) {
                $this->_categoryPaths[$categoryId] = $path . '/' . $category->alias;
            } else {
                $this->_categoryPaths[$categoryId] = false;
            }
        }

        return $this->_categoryPaths[$categoryId];
    }
}