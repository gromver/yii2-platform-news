<?php
/**
 * @var $this yii\web\View
 * @var $model string|\gromver\platform\news\models\Category
 */
?>

<h1 class="page-title title-category"><?=\yii\helpers\Html::encode($model->title) ?></h1>

<?php echo \gromver\platform\news\widgets\CategoryList::widget([
    'id' => 'cat-cats',
    'category' => $model,
    'listViewOptions' => [
        'emptyTextOptions' => ['class' => 'hidden']
    ],
    'context' => $this->context->context
]);

echo \gromver\platform\news\widgets\PostList::widget([
    'id' => 'cat-posts',
    'category' => $model,
    'context' => $this->context->context
]);