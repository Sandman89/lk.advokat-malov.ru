<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Issues]].
 *
 * @see Issues
 */
class IssuesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Issues[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Issues|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
