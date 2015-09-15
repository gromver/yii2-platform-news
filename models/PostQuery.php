<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\news\models;


use yii\db\Query;

/**
 * Class PostQuery
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */

class PostQuery extends \yii\db\ActiveQuery {
    /**
     * @return static
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

        return $this->andWhere(['{{%news_post}}.status' => Post::STATUS_PUBLISHED])->andWhere(['NOT IN', '{{%news_post}}.category_id', $badcatsQuery]);
    }

    /**
     * Фильтр по категории
     * @param $id
     * @return $this
     */
    public function category($id = null)
    {
        return $this->andFilterWhere(['{{%news_post}}.category_id' => $id]);
    }

    /**
     * Послдение новости
     * @return static
     */
    public function last()
    {
        return $this->andWhere('{{%news_post}}.published_at<=:now', [':now' => time()]);
    }

    /**
     * Статьи за указанный день
     * @param $year integer
     * @param $month integer
     * @param $day integer
     * @return $this
     */
    public function day($year, $month, $day)
    {
        $from = mktime(0,0,0,$month,$day,$year);
        $to = $from + 86400;

        return $this->andWhere('{{%news_post}}.published_at>=:from AND {{%news_post}}.published_at<:to', [':from' => $from, ':to' => $to]);
    }

    /**
     * Статьи до указанного дня
     * @param $year integer
     * @param $month integer
     * @param $day integer
     * @return $this
     */
    public function beforeDay($year, $month, $day)
    {
        $date = mktime(0,0,0,$month,$day,$year);

        return $this->andWhere('{{%news_post}}.published_at<=:date', [':date' => $date])->orderBy('{{%news_post}}.published_at DESC');
    }

    /**
     * Статьи после указанного дня
     * @param $year integer
     * @param $month integer
     * @param $day integer
     * @return $this
     */
    public function afterDay($year, $month, $day)
    {
        $date = mktime(0,0,0,$month,$day,$year)+86400;

        return $this->andWhere('{{%news_post}}.published_at>=:date', [':date' => $date])->orderBy('{{%news_post}}.published_at ASC');
    }
} 