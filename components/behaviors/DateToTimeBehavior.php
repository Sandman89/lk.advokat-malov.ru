<?php
/**
 * Created by PhpStorm.
 * User: fores
 * Date: 29.01.2019
 * Time: 17:59
 */

namespace app\components\behaviors;

use yii\behaviors\AttributeBehavior;
use yii\base\InvalidConfigException;


class DateToTimeBehavior extends AttributeBehavior {

    public $timeAttribute;

    public function getValue($event) {

        if (empty($this->timeAttribute)) {
            throw new InvalidConfigException('Can`t find "fromAttribute" property in ' . $this->owner->className());
        }
        if (!empty($this->owner->{$this->attributes[$event->name]})) {
            $this->owner->{$this->timeAttribute} = strtotime($this->owner->{$this->attributes[$event->name]});
            return date('d.m.Y', $this->owner->{$this->timeAttribute});
        } else {
            if (!empty($this->owner->{$this->timeAttribute})) {
                $this->owner->{$this->attributes[$event->name]} = date('d.m.Y', $this->owner->{$this->timeAttribute});
                return $this->owner->{$this->attributes[$event->name]};
            }
        }

        return date('d.m.Y', time());
    }

}