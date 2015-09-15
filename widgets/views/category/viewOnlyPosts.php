<?php
/**
 * @var $this yii\web\View
 * @var $model string|\gromver\platform\news\models\Category
 */ ?>

<h1 class="page-title title-category"><?=\yii\helpers\Html::encode($model->title)?></h1>

<?= \gromver\platform\news\widgets\PostList::widget([
    'id' => 'cat-posts',
    'category' => $model,
    'layout' => 'post/listDefault',
    'context' => $this->context->context
]) ?>