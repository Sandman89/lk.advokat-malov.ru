<?php

namespace app\components;

use Yii;
use yii\db\ActiveRecord as BaseActiveRecord;

/**
 * Class ActiveRecord
 * переопределен основной класс для удобной разделения видимости дел согласно распределению прав.
 * В методе find будет добавлен дополнительное условия для фильтрации дел и задач.
 *
 * Клиент. Видит только свое дело, его редиректит сразу внутрь его дела. Меню у клиента почти все скрыто, есть только мои дела.
 * Эксперт. Видит свои дела и задачи. Те в которых он является автором или исполнителем.
 * Эксперт - админ. Видит все задачи и дела всех сотрудников.
 * @package app\components
 */
class ActiveRecordWithAccess extends BaseActiveRecord
{
    public static function find()
    {
        if (!Yii::$app->user->isGuest) {
            if (Yii::$app->user->identity->isClient){
                $class = static::className();
                $intersect_table_name = '';
                if ($class == 'app\models\Task') {
                    $intersect_table_name = 'task';
                }
                if ($class == 'app\models\Issues') {
                    $intersect_table_name = 'issues';
                }
                return parent::find()->joinWith(['client client'])->onCondition(
                    ['=',  'client.id', Yii::$app->user->id]
                );
            }
            else{
                if ((Yii::$app->user->identity->isExpert) && (!Yii::$app->user->identity->isAdmin)) {
                $class = static::className();
                $intersect_table_name = '';
                if ($class == 'app\models\Task') {
                    $intersect_table_name = 'task_user';
                }
                if ($class == 'app\models\Issues') {
                    $intersect_table_name = 'issues_user';
                }
                    return parent::find()->joinWith(['assigns'])->onCondition(['or',
                        ['=', $intersect_table_name . '.id_user', Yii::$app->user->id],
                        ['=', 'id_author', Yii::$app->user->id]
                    ]);
                }
                else
                    return parent::find();
            }
        }
        else
            return parent::find();
    }
    //Access to view buttons

    /**
     * Возвращает true если есть доступ к редактированию текущей записи. Может редактировать администратор или автор
     * @return bool
     */
    public function getAccessEdit()
    {
        $out = false;
        if (!Yii::$app->user->isGuest)
            if (((Yii::$app->user->identity->isAdmin) || ($this->id_author == Yii::$app->user->id)) && ($this->status != 'completed'))
                $out = true;
        return $out;
    }

    /**
     * Возвращает true если есть доступ к завершениию текущей записи. Может завершать администратор, автор или исполнитель
     * @return bool
     */
    public function getAccessComplete()
    {
        $out = false;
        $in_assign = false;
        foreach ($this->assigns as $assign) {
            if ($assign->id == Yii::$app->user->id) {
                $in_assign = true;
                break;
            }
        }
        if (!Yii::$app->user->isGuest)
            if (((Yii::$app->user->identity->isAdmin) || ($this->id_author == Yii::$app->user->id) || ($in_assign == true)) && ($this->status != 'completed'))
                $out = true;
        return $out;
    }

    /**
     * Возвращает true если есть доступ к восстановлению текущей записи. Может восстанавливать администратор, автор или исполнитель
     * @return bool
     */
    public function getAccessRestore()
    {
        $out = false;
        $in_assign = false;
        foreach ($this->assigns as $assign) {
            if ($assign->id == Yii::$app->user->id) {
                $in_assign = true;
                break;
            }
        }
        if (!Yii::$app->user->isGuest)
            if (((Yii::$app->user->identity->isAdmin) || ($this->id_author == Yii::$app->user->id) || ($in_assign == true)) && ($this->status == 'completed'))
                $out = true;
        return $out;
    }
}