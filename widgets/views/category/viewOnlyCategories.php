<?php
/**
 * @var $this yii\web\View
 * @var $model string|\gromver\platform\news\models\Category
 */ ?>

<h1 class="page-title title-category"><?=\yii\helpers\Html::encode($model->title)?></h1>

<?= \gromver\platform\news\widgets\CategoryList::widget([
    'id' => 'cat-cats',
    'category' => $model,
    'context' => $this->context->context
]) ?>