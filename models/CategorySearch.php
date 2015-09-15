<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\news\models;


use Yii;
use yii\data\ActiveDataProvider;

/**
 * Class CategorySearch represents the model behind the search form about `gromver\platform\news\models\Category`.
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class CategorySearch extends Category
{
    /**
     * @var integer[]
     */
    public $tags;
    /**
     * @var string
     */
    public $published_at_to;
    /**
     * @var integer
     */
    public $published_at_to_timestamp;
    /**
     * @var integer
     */
    public $published_at_timestamp;
    /**
     * @var bool
     */
    public $excludeRoots = true;
    /**
     * @var integer
     */
    public $excludeCategory;

    public function rules()
    {
        return [
            [['id', 'parent_id', 'created_at', 'updated_at', 'status', 'created_by', 'updated_by', 'lft', 'rgt', 'level', 'ordering', 'hits', 'lock', 'excludeCategory'], 'integer'],
            [['title', 'alias', 'path', 'preview_text', 'preview_image', 'detail_text', 'detail_image', 'metakey', 'metadesc', 'tags', 'versionNote'], 'safe'],
            [['published_at'], 'date', 'format' => 'dd.MM.yyyy', 'timestampAttribute' => 'published_at_timestamp'],
            [['published_at_to'], 'date', 'format' => 'dd.MM.yyyy', 'timestampAttribute' => 'published_at_to_timestamp'],
        ];
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Category::find();

        if ($this->excludeRoots) {
            $query->excludeRoots();
        }

        $query->with(['tags', 'parent']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'lft' => SORT_ASC
                ]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            '{{%news_category}}.id' => $this->id,
            '{{%news_category}}.parent_id' => $this->parent_id,
            '{{%news_category}}.created_at' => $this->created_at,
            '{{%news_category}}.updated_at' => $this->updated_at,
            '{{%news_category}}.status' => $this->status,
            '{{%news_category}}.created_by' => $this->created_by,
            '{{%news_category}}.updated_by' => $this->updated_by,
            '{{%news_category}}.lft' => $this->lft,
            '{{%news_category}}.rgt' => $this->rgt,
            '{{%news_category}}.level' => $this->level,
            '{{%news_category}}.ordering' => $this->ordering,
            '{{%news_category}}.hits' => $this->hits,
            '{{%news_category}}.lock' => $this->lock,
        ]);

        if ($this->published_at_timestamp) {
            $query->andWhere('{{%news_category}}.published_at >= :timestamp_from', ['timestamp_from' => $this->published_at_timestamp]);
        }
        if ($this->published_at_to_timestamp) {
            $query->andWhere('{{%news_category}}.published_at <= :timestamp_to', ['timestamp_to' => $this->published_at_to_timestamp]);
        }

        $query->andFilterWhere(['like', '{{%news_category}}.title', $this->title])
            ->andFilterWhere(['like', '{{%news_category}}.alias', $this->alias])
            ->andFilterWhere(['like', '{{%news_category}}.path', $this->path])
            ->andFilterWhere(['like', '{{%news_category}}.preview_text', $this->preview_text])
            ->andFilterWhere(['like', '{{%news_category}}.preview_image', $this->preview_image])
            ->andFilterWhere(['like', '{{%news_category}}.detail_text', $this->detail_text])
            ->andFilterWhere(['like', '{{%news_category}}.detail_image', $this->detail_image])
            ->andFilterWhere(['like', '{{%news_category}}.metakey', $this->metakey])
            ->andFilterWhere(['like', '{{%news_category}}.metadesc', $this->metadesc]);

        if ($this->excludeCategory && $category = Category::findOne($this->excludeCategory)) {
            /** @var $category Category */
            $query->excludeCategory($category);
        }

        if($this->tags) {
            $query->innerJoinWith('tags')->andFilterWhere(['{{%core_tag}}.id' => $this->tags]);
        }

        return $dataProvider;
    }
}
