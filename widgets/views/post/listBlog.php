<?php
/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $itemLayout string
 * @var $category null|\gromver\platform\news\models\Category
 * @var $listViewOptions array
 */
use kartik\icons\Icon;

Icon::map($this, Icon::EL);
\gromver\platform\news\widgets\assets\PostAsset::register($this); ?>

<div class="row">
    <div class="col-sm-4 pull-right">
        <?= \gromver\platform\news\widgets\Calendar::widget([
            'id' => 'news-calendar',
            'categoryId' => $category ? $category->id : null,
            'year' => date('Y'),
            'month' => date('m'),
            'day' => date('d'),
        ]) ?>

        <h3><?= Yii::t('gromver.platform', 'Tags') ?></h3>

        <?= \gromver\platform\core\modules\tag\widgets\TagPostCloud::widget([
            'id' => 'posts-tags',
            'categoryId' => $category ? $category->id : null,
            'context' => $this->context->context
        ]) ?>
    </div>
    <div class="col-sm-8">
        <?= \yii\helpers\Html::a(Icon::show('rss', [], Icon::EL), $category ? ['/news/frontend/post/rss', 'category_id' => $category->id] : ['/news/post/rss'], ['class' => 'btn btn-warning btn-xs pull-right']) ?>

        <?= \yii\widgets\ListView::widget(array_merge([
            'dataProvider' => $dataProvider,
            'itemView' => $itemLayout,
            'summary' => '',
            'viewParams' => [
                'postListWidget' => $this->context
            ]
        ], $listViewOptions)) ?>
    </div>
</div>
