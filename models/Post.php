<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\news\models;


use gromver\platform\core\behaviors\SearchBehavior;
use gromver\platform\core\behaviors\TaggableBehavior;
use gromver\platform\core\behaviors\upload\ThumbnailProcessor;
use gromver\platform\core\behaviors\UploadBehavior;
use gromver\platform\core\behaviors\VersionBehavior;
use gromver\platform\core\components\UrlManager;
use gromver\platform\core\interfaces\model\SearchableInterface;
use gromver\platform\core\interfaces\model\ViewableInterface;
use gromver\platform\core\modules\user\models\User;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * This is the model class for table "news_post".
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property integer $id
 * @property integer $category_id
 * @property string $title
 * @property string $alias
 * @property string $preview_text
 * @property string $preview_image
 * @property string $detail_text
 * @property string $detail_image
 * @property string $metakey
 * @property string $metadesc
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $published_at
 * @property integer $status
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $ordering
 * @property integer $hits
 * @property integer $lock
 *
 * @property Category $category
 * @property User[] $viewers
 * @property User $user
 * @property \gromver\platform\core\modules\tag\models\Tag[] $tags
 * @property Post[] $translations
 * @property PostViewed $postViewed
 */
class Post extends \yii\db\ActiveRecord implements ViewableInterface, SearchableInterface
{
    const STATUS_PUBLISHED = 1;
    const STATUS_UNPUBLISHED = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%news_post}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'category_id', 'title', 'detail_text'], 'required'],
            [['category_id', 'created_at', 'updated_at', 'status', 'created_by', 'updated_by', 'ordering', 'hits', 'lock'], 'integer'],
            [['preview_text', 'detail_text'], 'string'],
            [['title'], 'string', 'max' => 1024],
            [['alias', 'metakey'], 'string', 'max' => 255],
            [['metadesc'], 'string', 'max' => 2048],

            [['published_at'], 'date', 'format' => 'dd.MM.yyyy HH:mm', 'timestampAttribute' => 'published_at', 'when' => function () {
                    return is_string($this->published_at);
                }],
            [['published_at'], 'integer', 'enableClientValidation' => false],
            [['category_id'], 'exist', 'targetClass' => Category::className(), 'targetAttribute' => 'id', 'filter' => function($query) {
                /** @var $query \gromver\platform\news\models\CategoryQuery */
                $query->excludeRoots();
            }],
            [['alias'], 'filter', 'filter' => 'trim'],
            [['alias'], 'filter', 'filter' => function ($value) {
                    if (empty($value)) {
                        return Inflector::slug($this->title);
                    } else {
                        return Inflector::slug($value);
                    }
                }],
            [['alias'], 'unique', 'filter' => function ($query) {
                    /** @var $query \yii\db\ActiveQuery */
                    $query->andWhere(['category_id' => $this->category_id]);
                }, 'message' => '{attribute} - Another article from this category has the same alias'],
            [['tags', 'versionNote'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('gromver.platform', 'ID'),
            'category_id' => Yii::t('gromver.platform', 'Category'),
            'title' => Yii::t('gromver.platform', 'Title'),
            'alias' => Yii::t('gromver.platform', 'Alias'),
            'preview_text' => Yii::t('gromver.platform', 'Preview Text'),
            'preview_image' => Yii::t('gromver.platform', 'Preview Image'),
            'detail_text' => Yii::t('gromver.platform', 'Detail Text'),
            'detail_image' => Yii::t('gromver.platform', 'Detail Image'),
            'metakey' => Yii::t('gromver.platform', 'Meta keywords'),
            'metadesc' => Yii::t('gromver.platform', 'Meta description'),
            'created_at' => Yii::t('gromver.platform', 'Created At'),
            'updated_at' => Yii::t('gromver.platform', 'Updated At'),
            'published_at' => Yii::t('gromver.platform', 'Published At'),
            'status' => Yii::t('gromver.platform', 'Status'),
            'created_by' => Yii::t('gromver.platform', 'Created By'),
            'updated_by' => Yii::t('gromver.platform', 'Updated By'),
            'tags' => Yii::t('gromver.platform', 'Tags'),
            'ordering' => Yii::t('gromver.platform', 'Ordering'),
            'hits' => Yii::t('gromver.platform', 'Hits'),
            'lock' => Yii::t('gromver.platform', 'Lock'),
            'versionNote' => Yii::t('gromver.platform', 'Version Note')
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            BlameableBehavior::className(),
            TaggableBehavior::className(),
            SearchBehavior::className(),
            [
                'class' => VersionBehavior::className(),
                'attributes'=>['title', 'alias', 'preview_text', 'detail_text', 'metakey', 'metadesc']
            ],
            [
                'class' => UploadBehavior::className(),
                'attributes' => [
                    'detail_image'=>[
                        'fileName' => '{id}-full.#extension#'
                    ],
                    'preview_image'=>[
                        'fileName' => '{id}-thumb.#extension#',
                        'fileProcessor' => ThumbnailProcessor::className()
                    ]
                ],
                'options' => [
                    'savePath'=>'upload/posts'
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
//    public function afterSave($insert, $changedAttributes)
//    {
//        if ($insert && $this->translation_id === null) {
//            $this->updateAttributes([
//                'translation_id' => $this->id
//            ]);
//        }
//
//        parent::afterSave($insert, $changedAttributes);
//    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getViewers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('{{%news_post_viewed}}', ['post_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPostViewed()
    {
        return $this->hasOne(PostViewed::className(), ['post_id' => 'id'])->onCondition(['user_id' => Yii::$app->user ? Yii::$app->user->id : null])->inverseOf('post');
    }

    /**
     * @return bool
     */
    public function getPublished()
    {
        return $this->status == self::STATUS_PUBLISHED;
    }

    private static $_statuses = [
        self::STATUS_PUBLISHED => 'Published',
        self::STATUS_UNPUBLISHED => 'Unpublished',
    ];

    /**
     * @return array
     */
    public static function statusLabels()
    {
        return array_map(function($label) {
                return Yii::t('gromver.platform', $label);
            }, self::$_statuses);
    }

    /**
     * @param string|null $status
     * @return string
     */
    public function getStatusLabel($status=null)
    {
        if ($status === null) {
            return Yii::t('gromver.platform', self::$_statuses[$this->status]);
        }
        return Yii::t('gromver.platform', self::$_statuses[$status]);
    }

    /**
     * @inheritdoc
     * @return PostQuery
     */
    public static function find()
    {
        return new PostQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function optimisticLock()
    {
        return 'lock';
    }

    /**
     * Увеличивает счетчик просмотров
     * @return int
     */
    public function hit()
    {
        // todo любую статистику приложения собирать через логи
        return $this->updateAttributes(['hits' => $this->hits + 1]);
    }

    /**
     * Помечает модель как "просмотренная" для текущего пользователя
     */
    public function view()
    {
        if (Yii::$app->user->id && !$this->postViewed) {
            $this->link('postViewed', new PostViewed());
        }
    }

    /**
     * @return array
     */
    public function getDayLink()
    {
        return ['/news/post/day', 'category_id' => $this->category_id, 'year' => date('Y', $this->published_at), 'month' => date('m', $this->published_at), 'day' => date('j', $this->published_at)];
    }

    // ViewableInterface
    /**
     * @inheritdoc
     */
    public function getFrontendViewLink()
    {
        return ['/news/frontend/post/view', 'id' => $this->id, 'alias' => $this->alias, 'category_id' => $this->category_id];
    }

    /**
     * @inheritdoc
     */
    public static function frontendViewLink($model)
    {
        return ['/news/frontend/post/view', 'id' => $model['id'], 'alias' => $model['alias'], 'category_id' => $model['category_id']];
    }

    /**
     * @inheritdoc
     */
    public function getBackendViewLink()
    {
        return ['/news/backend/post/view', 'id' => $this->id];
    }

    /**
     * @inheritdoc
     */
    public static function backendViewLink($model)
    {
        return ['/news/backend/post/view', 'id' => $model['id']];
    }

    // SearchableInterface
    /**
     * @inheritdoc
     */
    public function getSearchTitle()
    {
        return $this->title;
    }

    /**
     * @inheritdoc
     */
    public function getSearchContent()
    {
        return $this->detail_text;
    }

    /**
     * @inheritdoc
     */
    public function getSearchTags()
    {
        return ArrayHelper::map($this->tags, 'id', 'title');
    }

    // SqlSearch integration
    /**
     * @param $event \gromver\platform\core\modules\search\modules\sql\widgets\events\SqlBeforeSearchEvent
     */
    static public function sqlBeforeFrontendSearch($event)
    {
        $event->query->leftJoin('{{%news_post}}', [
                'AND',
                ['=', 'model_class', self::className()],
                'model_id={{%news_post}}.id',
                ['=', '{{%news_post}}.status', self::STATUS_PUBLISHED],
                ['IN', '{{%news_post}}.category_id', Category::find()->published()->select('{{%news_category}}.id')->column()]
            ]
        )->addSelect('{{%news_post}}.id')
            ->andWhere('model_class=:postClassName XOR {{%news_post}}.id IS NULL', [':postClassName' => self::className()]);
    }

    // ElasticSearch integration
    /**
     * @param $event \gromver\platform\core\modules\search\modules\elastic\widgets\events\ElasticBeforeSearchEvent
     */
    static public function elasticBeforeFrontendSearch($event)
    {
        $event->sender->filters[] = [
            'not' => [
                'and' => [
                    [
                        'term' => ['model_class' => self::className()]
                    ],
                    [
                        'or' => [
                            [
                                'term' => ['params.published' => false]
                            ],
                            [
                                'terms' => ['params.category_id' => Category::find()->unpublished()->select('{{%news_category}}.id')->column()]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @param $event \gromver\platform\core\modules\search\modules\elastic\events\ElasticIndexEvent
     */
    static public function elasticBeforeCreateIndex($event)
    {
        $event->index->params = [
            'published' => $event->model->status == self::STATUS_PUBLISHED,
            'category_id' => $event->model->category_id
        ];
    }
}
