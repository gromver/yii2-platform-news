<?php
/**
 * @link https://github.com/gromver/yii2-platform-news.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-news/blob/master/LICENSE
 * @package yii2-platform-news
 * @version 1.0.0
 */

namespace gromver\platform\news\rules;


use yii\rbac\Item;
use Yii;

/**
 * Class PostAuthorRule
 * @package yii2-platform-news
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class PostAuthorRule extends \yii\rbac\Rule
{
    public $name = 'isPostAuthor';

    /**
     * @param string|integer $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return boolean a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        return isset($params['post']) ? $params['post']->created_by == $user : false;
    }
} 