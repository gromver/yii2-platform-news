<?php

class m000012_000003_news_create_upload_dirs extends \yii\db\Migration
{
    public function up()
    {
        // Create folders for media manager
        $uploadRoot = Yii::getAlias('@app/web/upload');
        foreach (['posts', 'categories'] as $folder) {
            $path = $uploadRoot . '/' . $folder;
            if (!file_exists($path)) {
                echo "mkdir('$path', 0777)...";
                if (mkdir($path, 0777, true)) {
                    echo "done.\n";
                } else {
                    echo "failed.\n";
                }
            }
        }
    }
}