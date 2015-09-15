<?php

use yii\db\Schema;

class m000012_000000_news_create_tables extends \yii\db\Migration
{
    public function up()
    {
        // category
        $this->createTable('{{%news_category}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'title' => Schema::TYPE_STRING . '(1024)',
            'alias' => Schema::TYPE_STRING,
            'path' => Schema::TYPE_STRING . '(2048)',
            'preview_text' => Schema::TYPE_TEXT,
            'preview_image' => Schema::TYPE_STRING . '(1024)',
            'detail_text' => Schema::TYPE_TEXT,
            'detail_image' => Schema::TYPE_STRING . '(1024)',
            'metakey' => Schema::TYPE_STRING,
            'metadesc' => Schema::TYPE_STRING . '(2048)',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER,
            'published_at' => Schema::TYPE_INTEGER,
            'status' => Schema::TYPE_SMALLINT,
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_by' => Schema::TYPE_INTEGER,
            'lft' => Schema::TYPE_INTEGER,
            'rgt' => Schema::TYPE_INTEGER,
            'level' => Schema::TYPE_SMALLINT . ' UNSIGNED',
            'ordering' => Schema::TYPE_INTEGER . ' UNSIGNED',
            'hits' => Schema::TYPE_BIGINT . ' UNSIGNED',
            'lock' => Schema::TYPE_BIGINT . ' UNSIGNED DEFAULT 1',
        ]);
        $this->createIndex('ParentId_idx', '{{%news_category}}', 'parent_id');
        $this->createIndex('Lft_Rgt_idx', '{{%news_category}}', 'lft, rgt');
        $this->createIndex('Path_idx', '{{%news_category}}', 'path');
        $this->createIndex('Alias_idx', '{{%news_category}}', 'alias');
        $this->createIndex('Status_idx', '{{%news_category}}', 'status');
        //вставляем рутовый элемент
        $this->insert('{{%news_category}}', [
            'status' => 1,
            'title' => 'Root',
            'created_at' => time(),
            'created_by' => 1,
            'lft' => 1,
            'rgt' => 2,
            'level' => 1,
            'ordering' => 1
        ]);

        // post
        $this->createTable('{{%news_post}}', [
            'id' => Schema::TYPE_PK,
            'category_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'title' => Schema::TYPE_STRING . '(1024)',
            'alias' => Schema::TYPE_STRING,
            'preview_text' => Schema::TYPE_TEXT,
            'preview_image' => Schema::TYPE_STRING . '(1024)',
            'detail_text' => Schema::TYPE_TEXT,
            'detail_image' => Schema::TYPE_STRING . '(1024)',
            'metakey' => Schema::TYPE_STRING,
            'metadesc' => Schema::TYPE_STRING . '(2048)',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER,
            'published_at' => Schema::TYPE_INTEGER,
            'status' => Schema::TYPE_SMALLINT,
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_by' => Schema::TYPE_INTEGER,
            'ordering' => Schema::TYPE_INTEGER . ' UNSIGNED',
            'hits' => Schema::TYPE_BIGINT . ' UNSIGNED',
            'lock' => Schema::TYPE_BIGINT . ' UNSIGNED DEFAULT 1',
        ]);
        $this->createIndex('CategoryId_idx', '{{%news_post}}', 'category_id');
        $this->createIndex('Alias_idx', '{{%news_post}}', 'alias');
        $this->createIndex('Status_idx', '{{%news_post}}', 'status');
        $this->addForeignKey('NewsPost_CategoryId_fk', '{{%news_post}}', 'category_id', '{{%news_category}}', 'id', 'CASCADE', 'CASCADE');

        // post viewed
        $this->createTable('{{%news_post_viewed}}', [
            'post_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'viewed_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ]);
        $this->addPrimaryKey('PostId_UserId_pr', '{{%news_post_viewed}}', 'post_id, user_id');
        $this->createIndex('PostId_idx', '{{%news_post_viewed}}', 'post_id');
        $this->createIndex('UserId_idx', '{{%news_post_viewed}}', 'user_id');
        $this->addForeignKey('NewsPostViewed_PostId_fk', '{{%news_post_viewed}}', 'post_id', '{{%news_post}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('NewsPostViewed_UserId_fk', '{{%news_post_viewed}}', 'user_id', '{{%core_user}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%news_post_viewed}}');
        $this->dropTable('{{%news_post}}');
        $this->dropTable('{{%news_category}}');
    }
}