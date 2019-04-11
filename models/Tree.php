<?php
/**
 * Created by PhpStorm.
 * User: fores
 * Date: 01.02.2019
 * Time: 13:46
 */

namespace app\models;

use Yii;

class Tree extends \kartik\tree\models\Tree
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tree_category';
    }
}