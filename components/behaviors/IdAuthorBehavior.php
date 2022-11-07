<?php
/**
 * Created by PhpStorm.
 * User: fores
 * Date: 30.05.2019
 * Time: 17:19
 */

namespace app\components\behaviors;

use yii\base\InvalidCallException;
use yii\db\BaseActiveRecord;
use yii\behaviors\AttributeBehavior;
use \yii;

class IdAuthorBehavior extends AttributeBehavior
{
    public $id_author = 'id_author';
    public $value;
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => [$this->id_author]
            ];
        }
    }

    protected function getValue($event)
    {
        if ($this->value === null) {
            if (!Yii::$app->user->isGuest)
            {
                return  Yii::$app->user->identity->id;
            }

        }

        return parent::getValue($event);
    }
}