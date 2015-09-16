<?php
/**
 * @var $this yii\web\View
 * @var $model \gromver\platform\news\models\Category
 * @var $key string
 * @var $index integer
 * @var $widget \yii\widgets\ListView
 */

use yii\helpers\Html;

echo '<h3>' . Html::a(Html::encode($model->title), $model->getFrontendViewLink()) . '</h3>';

if($model->preview_image) echo Html::img($model->preview_image, [
    'class' => 'pull-left',
    'style' => 'max-width: 200px; margin-right: 15px;'
]);

echo Html::tag('div', $model->preview_text);

echo '<div class="clearfix"></div>';
