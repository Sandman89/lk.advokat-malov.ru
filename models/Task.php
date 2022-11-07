<?php

namespace app\models;

use app\components\ActiveRecordWithAccess;
use app\components\behaviors\IdAuthorBehavior;
use omnilight\datetime\DateTimeBehavior;
use yii\behaviors\TimestampBehavior;
use Yii;

/**
 * This is the model class for table "task".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $created_at
 * @property int $updated_at
 * @property int $id_issue
 * @property int $assign_id
 * @property string $deadline
 * @property string $status
 * @property string $completed_at
 * @property string $start
 * @property string $end
 * @property bool $isallday
 * @property int $id_author
 * @property int $typetask
 * @property bool $accessEdit
 * @property bool $accessComplete
 * @property bool $accessRestore
 */
class Task extends ActiveRecordWithAccess
{

    const TYPETASK_DUE = 1;
    const TYPETASK_BETWEEN_DATE = 2;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task';
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
            [
                'class' => IdAuthorBehavior::className(),
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['id_author'],
                ],
            ],
            [
                'class' => \voskobovich\linker\LinkerBehavior::className(),
                'relations' => [
                    'assign_id' => [
                        'assigns'
                    ],
                ],
            ],
            'datetime' => [
                'class' => DateTimeBehavior::className(), // Our behavior
                'originalFormat' => ['datetime', 'yyyy-MM-dd HH:mm'],
                //'targetFormat' => ['datetime', 'yyyy-MM-dd HH:mm'],
                'targetFormat' => ['datetime', 'dd.MM.yyyy HH:mm'],
                'attributes' => [
                    'deadline', // List all editable date/time attributes
                    'start',
                    'end'
                ],
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title','assign_id'], 'required'],
            [['start_local'], 'required','when' => function($model) {
                return (!empty($model->end_local));
            },'message' => 'Если есть конец, должно быть и начало'],
            [['end_local'], 'required','when' => function($model) {
                return (!empty($model->start_local));
            },'message' => 'Если есть начало, должен быть и конец'],
            [['description'], 'string'],
            [['isallday'], 'boolean'],
            [['assign_id'], 'each', 'rule' => ['integer']],
            [['created_at', 'updated_at', 'id_issue', 'id_author'], 'integer'],
            [['deadline', 'deadline_local','completed_at','start_local', 'end_local'], 'safe'],
            [['start_local', 'end_local'], 'date', 'format' => 'dd.MM.yyyy HH:mm'],
            [['start_local','end_local','description'], 'validateDate'],
            [['title', 'status'], 'string', 'max' => 255],
            ['status', 'default', 'value' => 'working'],
        ];
    }

    public function validateDate()
    {
        if ($this->start_local > $this->end_local) {
            $this->addError('end_local', '"Дата окончания", не может быть раньше "даты начала"');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название задачи',
            'description' => 'Описание',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'id_issue' => 'Связанное дело',
            'assign_id' => 'Исполнитель',
            'deadline' => 'Крайний срок',
            'deadline_local' => 'Крайний срок',
            'completed_at' => 'Завершено',
            'start' => 'Дата начала',
            'start_local' => 'Дата начала',
            'end' => 'Дата завершения',
            'end_local' => 'Дата завершения',
            'isallday' => 'Весь день',
            'status' => 'Статус',
            'id_author' => 'Id Author',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (empty($this->deadline_local)) {
                $this->deadline =  null;
            }
            if (empty($this->start_local)) {
                $this->start =  null;
            }
            if (empty($this->end_local)) {
                $this->end =  null;
            }
            return true;
        } else {
            return false;
        }
    }
    public function getAssigns()
    {
        return $this->hasMany(\dektrium\user\models\User::className(), ['id' => 'id_user'])
            ->viaTable('{{%task_user}}', ['id_task' => 'id']);
    }
    public function getAuthor()
    {
        return $this->hasOne(\dektrium\user\models\User::className(), ['id' => 'id_author']);
    }
    public function getIssue()
    {
        return $this->hasOne(Issues::className(), ['id' => 'id_issue']);
    }

    public function getStatuslabel(){
        if ($this->status == 'completed')
            return '<span class="label label-danger">Завершено</span>';
        else
            return '';
    }

    /**
     * @return int
     */
    public function getTypetask(){
        if ((($this->start) && ($this->start)) && (empty($this->deadline)))
            return self::TYPETASK_BETWEEN_DATE;
        else
       // if (($this->deadline) && ((empty($this->start)) && (empty($this->end))))
            return self::TYPETASK_DUE;
    }

}
