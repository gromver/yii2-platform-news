<?php

use yii\db\Schema;

class __m000012_000000_news_init extends \yii\db\Migration
{
    public function up()
    {
        $webConfigPath = Yii::getAlias('@config/core/web.php');
        $webConfig = @include($webConfigPath);
        if (!is_array($webConfig)) {
            $webConfig = [];
        }
        $webConfig = \yii\helpers\ArrayHelper::merge($webConfig, [
            'modules' => [
                'news' => ['class' => 'gromver\platform\news\Module']
            ]
        ]);
        file_put_contents($webConfigPath, var_export($webConfig));

        $consoleConfigPath = Yii::getAlias('@config/core/console.php');
        $consoleConfig = @include($consoleConfigPath);
        if (!is_array($consoleConfig)) {
            $consoleConfig = [];
        }
        $consoleConfig = \yii\helpers\ArrayHelper::merge($consoleConfig, [
            'modules' => [
                'news' => ['class' => 'gromver\platform\news\Module']
            ]
        ]);
        file_put_contents($consoleConfigPath, var_export($consoleConfig));
    }

    public function down()
    {
        $webConfigPath = Yii::getAlias('@config/core/web.php');
        $webConfig = @include($webConfigPath);
        if (!is_array($webConfig)) {
            $webConfig = [];
        }
        if (isset($webConfig['modules']['news'])) {
            unset($webConfig['modules']['news']);
        }
        file_put_contents($webConfigPath, var_export($webConfig));

        $consoleConfigPath = Yii::getAlias('@config/core/console.php');
        $consoleConfig = @include($consoleConfigPath);
        if (!is_array($consoleConfig)) {
            $consoleConfig = [];
        }
        if (isset($consoleConfig['modules']['news'])) {
            unset($consoleConfig['modules']['news']);
        }
        file_put_contents($consoleConfigPath, var_export($consoleConfig));
    }
}