<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\news\models;


use creocoder\nestedsets\NestedSetsQueryBehavior;
use yii\db\Query;

/**
 * Class CategoryQuery
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class CategoryQuery extends \yii\db\ActiveQuery
{
    public function behaviors() {
        return [
            [
                'class' => NestedSetsQueryBehavior::className(),
            ],
        ];
    }
    /**
     * @return CategoryQuery
     */
    public function published()
    {
        $badcatsQuery = new Query([
            'select' => ['badcats.id'],
            'from' => ['{{%news_category}} AS unpublished'],
            'join' => [
                ['LEFT JOIN', '{{%news_category}} AS badcats', 'unpublished.lft <= badcats.lft AND unpublished.rgt >= badcats.rgt']
            ],
            'where' => 'unpublished.status != ' . Category::STATUS_PUBLISHED,
            'groupBy' => ['badcats.id']
        ]);

        return $this->andWhere(['NOT IN', '{{%news_category}}.id', $badcatsQuery]);
    }

    /**
     * @return CategoryQuery
     */
    public function unpublished()
    {
        return $this->innerJoin('{{%news_category}} AS ancestors', '{{%news_category}}.lft >= ancestors.lft AND {{%news_category}}.rgt <= ancestors.rgt')->andWhere('ancestors.status != ' . Category::STATUS_PUBLISHED)->addGroupBy(['{{%news_category}}.id']);
    }

    /**
     * Фильтр по категории
     * @param $id
     * @return $this
     */
    public function parent($id)
    {
        return $this->andWhere(['{{%news_category}}.parent_id' => $id]);
    }

    /**
     * @return static
     */
    public function excludeRoots()
    {
        return $this->andWhere('{{%news_category}}.lft!=1');
    }

    /**
     * Исключает из выборки категорию $category и все ее подкатегории
     * @param Category $category
     * @return static
     */
    public function excludeCategory($category)
    {
        return $this->andWhere('{{%news_category}}.lft < :excludeLft OR {{%news_category}}.lft > :excludeRgt', [':excludeLft' => $category->lft, ':excludeRgt' => $category->rgt]);
    }
} 