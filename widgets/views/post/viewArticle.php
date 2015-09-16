<?php
/**
 * @var $this yii\web\View
 * @var $model \gromver\platform\news\models\Post
 */

use yii\helpers\Html;

\gromver\platform\news\widgets\assets\PostAsset::register($this); ?>

<h1 class="page-title title-article">
    <?= Html::encode($model->title) ?>
</h1>'

<?php if($model->detail_image) echo Html::img($model->detail_image, [
    'class' => 'text-block img-responsive',
]); ?>
<div class="article-detail">
    <?= $model->detail_text ?>
</div>