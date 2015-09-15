<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model gromver\platform\news\models\Category */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('gromver.platform', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="category-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('gromver.platform', 'Add'), ['create', 'parentId' => $model->parent_id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-pencil"></i> ' . Yii::t('gromver.platform', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-trash"></i> ' . Yii::t('gromver.platform', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger pull-right',
            'data' => [
                'confirm' => Yii::t('gromver.platform', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('<i class="glyphicon glyphicon-th-list"></i> ' . Yii::t('gromver.platform', 'Posts'), ['/news/backend/post/index', 'PostSearch' => ['category_id' => $model->id]], ['class' => 'btn btn-default']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'parent_id',
                'value' => $model->parent ? ($model->parent->isRoot() ? Yii::t('gromver.platform', 'Root') : Html::a($model->parent->title, ['view', 'id' => $model->parent->id])) : null,
                'format' => 'raw'
            ],
            'title',
            'alias',
            'path',
            'preview_text:ntext',
            'preview_image',
            'detail_text:ntext',
            'detail_image',
            'metakey',
            'metadesc',
            'created_at:datetime',
            'updated_at:datetime',
            'published_at:datetime',
            'status',
            'created_by',
            'updated_by',
            'lft',
            'rgt',
            'level',
            'ordering',
            'hits',
            'lock',
            [
                'attribute' => 'tags',
                'value' => implode(', ', \yii\helpers\ArrayHelper::map($model->tags, 'id', function($tag) {
                    /** @var $tag \gromver\platform\core\modules\tag\models\Tag */
                    return Html::a($tag->title, $tag->getBackendViewLink());
                })),
                'format' => 'raw'
            ]
        ],
    ]) ?>

</div>
