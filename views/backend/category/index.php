<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel gromver\platform\news\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('gromver.platform', 'Категории');
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="category-index">

    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'id' => 'table-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pjax' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
        ],
        'columns' => [
            ['class' => '\kartik\grid\CheckboxColumn'],
            [
                'attribute' => 'id',
                'hAlign' => GridView::ALIGN_CENTER,
                'vAlign' => GridView::ALIGN_MIDDLE,
                'width' => '60px'
            ],
            [
                'attribute' => 'title',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function($model){
                        /** @var \gromver\platform\news\models\Category $model */
                        return str_repeat(" • ", max($model->level-2, 0)) . $model->title . '<br/>' . Html::tag('small', ' — ' . $model->path, ['class' => 'text-muted']);
                    },
                'format' => 'html'
            ],
            //'alias',
            //'path',
            [
                'attribute' => 'tags',
                'width' => '120px',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function($model){
                    /** @var $model \gromver\platform\news\models\Category */
                    return implode(', ', \yii\helpers\ArrayHelper::map($model->tags, 'id', 'title'));
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filterWidgetOptions' => [
                    'data' => \yii\helpers\ArrayHelper::map(\gromver\platform\core\modules\tag\models\Tag::find()->where(['id' => $searchModel->tags])->all(), 'id', 'title'),
                    'theme' => \kartik\select2\Select2::THEME_BOOTSTRAP,
                    'pluginOptions' => [
                        'allowClear' => true,
                        'placeholder' => Yii::t('gromver.platform', 'Select ...'),
                        'ajax' => [
                            'url' => \yii\helpers\Url::to(['/tag/backend/default/tag-list']),
                        ],
                    ],
                ]
            ],
            [
                'attribute' => 'published_at',
                'vAlign' => GridView::ALIGN_MIDDLE,
                'format' => ['date', 'd MMM Y H:mm'],
                'width' => '160px',
                'filterType' => GridView::FILTER_DATE,
                'filterWidgetOptions' => [
                    'pluginOptions' => [
                        'format' => 'dd.mm.yyyy'
                    ],
                    'type' => \kartik\date\DatePicker::TYPE_RANGE,
                    'attribute2' => 'published_at_to',
                ]
            ],
            [
                'attribute' => 'status',
                'hAlign' => GridView::ALIGN_CENTER,
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function ($model, $index, $widget) {
                        /** @var $model \gromver\platform\news\models\Category */
                        return $model->status === \gromver\platform\news\models\Category::STATUS_PUBLISHED ? Html::a('<i class="glyphicon glyphicon-ok-circle"></i>', \yii\helpers\Url::to(['unpublish', 'id' => $model->id]), ['class' => 'btn btn-default btn-xs', 'data-pjax' => '0', 'data-method' => 'post']) : Html::a('<i class="glyphicon glyphicon-remove-circle"></i>', \yii\helpers\Url::to(['publish', 'id' => $model->id]), ['class' => 'btn btn-danger btn-xs', 'data-pjax' => '0', 'data-method' => 'post']);
                    },
                'filter' => \gromver\platform\news\models\Category::statusLabels(),
                'format' => 'raw',
                'width' => '100px'
            ],
            [
                'attribute' => 'ordering',
                'hAlign' => GridView::ALIGN_CENTER,
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function($model, $index) {
                        /** @var \gromver\platform\news\models\Category $model */
                        return Html::input('text', 'order', $model->ordering, ['class'=>'form-control']);
                    },
                'format' => 'raw',
                'width' => '100px'
            ],
            [
                'header' => Yii::t('gromver.platform', 'Posts'),
                'hAlign' => GridView::ALIGN_CENTER,
                'vAlign' => GridView::ALIGN_MIDDLE,
                'value' => function($model) {
                    /** @var \gromver\platform\news\models\Category $model */
                    return Html::a('('.$model->getPosts()->count().')', ['/news/backend/post/index', 'PostSearch[category_id]' => $model->id], ['data-pjax' => 0]);
                },
                'mergeHeader' => true,
                'format'=>'raw'
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'deleteOptions' => ['data-method'=>'delete']
            ],
        ],
        'responsive' => true,
        'hover' => true,
        'condensed' => true,
        'floatHeader' => true,
        'bordered' => false,
        'panel' => [
            'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '. Html::encode($this->title) . ' </h3>',
            'type' => 'info',
            'before' => Html::a('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('gromver.platform', 'Add'), ['create'], ['class' => 'btn btn-success', 'data-pjax' => '0']),
            'after' =>
                Html::a('<i class="glyphicon glyphicon-sort-by-attributes"></i> ' . Yii::t('gromver.platform', 'Ordering'), ['ordering'], ['class' => 'btn btn-default', 'data-pjax' => '0', 'onclick' => 'processOrdering(this); return false']).' '.
                Html::a('<i class="glyphicon glyphicon-trash"></i> ' . Yii::t('gromver.platform', 'Delete'), ['bulk-delete'], ['class' => 'btn btn-danger', 'data-pjax' => '0', 'onclick' => 'processAction(this); return false']) . ' ' .
                Html::a('<i class="glyphicon glyphicon-repeat"></i> ' . Yii::t('gromver.platform', 'Reset List'), ['index'], ['class' => 'btn btn-info']),
            'showFooter' => false
        ],
    ]) ?>

</div>
<script>
    function processOrdering(el) {
        var $el = $(el),
            $grid = $('#table-grid'),
            selection = $grid.yiiGridView('getSelectedRows'),
            data = {}
        if(!selection.length) {
            alert(<?= json_encode(Yii::t('gromver.platform', 'Select items.')) ?>)
            return
        }
        $.each(selection, function(index, value){
            data[value] = $grid.find('tr[data-key="'+value+'"] input[name="order"]').val()
        })

        $.post($el.attr('href'), {data:data}, function(response){
            $grid.yiiGridView('applyFilter')
        })
    }
    function processAction(el) {
        var $el = $(el),
            $grid = $('#table-grid'),
            selection = $grid.yiiGridView('getSelectedRows')
        if(!selection.length) {
            alert(<?= json_encode(Yii::t('gromver.platform', 'Select items.')) ?>)
            return
        }

        $.post($el.attr('href'), {data:selection}, function(response){
            $grid.yiiGridView('applyFilter')
        })
    }
</script>