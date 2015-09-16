<?php
/**
 * @var $this yii\web\View
 * @var $model \gromver\platform\news\models\Post
 */

use yii\helpers\Html;

\gromver\platform\news\widgets\assets\PostAsset::register($this); ?>

<h1 class="page-title title-issue"><?= Html::encode($model->title) ?></h1>

<div class="issue-bar">
    <small class="issue-published"><?= Yii::$app->formatter->asDatetime($model->published_at) ?></small>
    <small class="issue-separator">|</small>
    <?php foreach ($model->tags as $tag) {
        /** @var $tag \gromver\platform\core\modules\tag\models\Tag */
        echo Html::a($tag->title, ['/news/frontend/post/tag', 'tag_id' => $tag->id, 'tag_alias' => $tag->alias, 'category_id' => $model->category_id], ['class' => 'issue-tag badge']);
    } ?>
</div>

<?php if($model->detail_image) {
    echo Html::img($model->detail_image, [
        'class' => 'text-block img-responsive',
    ]);
} ?>

<div class="issue-detail"><?= $model->detail_text ?></div>