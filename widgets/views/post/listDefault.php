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
\gromver\platform\news\widgets\assets\PostAsset::register($this);

echo \yii\helpers\Html::a(Icon::show('rss', [], Icon::EL), $category ? ['/news/frontend/post/rss', 'category_id' => $category->id] : ['/news/frontend/post/rss'], ['class' => 'btn btn-warning btn-xs pull-right']);

echo \yii\widgets\ListView::widget(array_merge([
    'dataProvider' => $dataProvider,
    'itemView' => $itemLayout,
    'summary' => '',
    'viewParams' => [
        'postListWidget' => $this->context
    ]
], $this->context->listViewOptions));