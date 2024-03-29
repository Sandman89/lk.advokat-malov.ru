<?php

namespace app\models;

use app\components\ActiveRecordWithAccess;
use app\components\behaviors\IdAuthorBehavior;
use app\components\comments\models\CommentModel;
use omnilight\datetime\DateTimeBehavior;
use Yii;
use yii\behaviors\TimestampBehavior;

//use app\components\behaviors\DateToTimeBehavior;

/**
 * This is the model class for table "issues".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $created_at
 * @property int $updated_at
 * @property int $id_category
 * @property int $id_assign
 * @property int $id_client
 * @property int $id_author
 * @property string $court Судебный орган
 * @property string $judge
 * @property string $court_date Дата следующего заседания
 * @property string $status
 * @property string $completed_at
 * @property string $contract_number
 * @property string $category
 * @property string $workflows
 * @property bool $accessEdit
 * @property bool $accessComplete
 * @property bool $accessRestore
 *
 */
class Issues extends ActiveRecordWithAccess
{
    //public $assign_id;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'issues';
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
                    'id_client' => [
                        'client'
                    ],
                ],
            ],
            'datetime' => [
                'class' => DateTimeBehavior::className(), // Our behavior
                'originalFormat' => ['datetime', 'yyyy-MM-dd HH:mm'],
                //'targetFormat' => ['datetime', 'yyyy-MM-dd HH:mm'],
                'targetFormat' => ['datetime', 'dd.MM.yyyy HH:mm'],
                'attributes' => [
                    'court_date', // List all editable date/time attributes
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
            [['title'], 'required'],
            [['title', 'description', 'contract_number'], 'string'],
            [['id_assign', 'assign_id'], 'each', 'rule' => ['integer']],
            // [['deadline_at'], 'date','format'=>'d.m.Y'],
            // [['created_at', 'updated_at'], 'safe'],
            [['parent', 'id_category', 'id_client', 'id_author'], 'integer'],
            [['court_date', 'court_date_local', 'id_author'], 'safe'],
            [['court', 'judge', 'status', 'completed_at'], 'string', 'max' => 255],
            ['status', 'default', 'value' => 'working'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Краткое название',
            'description' => 'Описание',
            'created_at' => 'Создано',
            'updated_at' => 'Изменено',
            'parent' => 'Родительский id',
            'id_category' => 'Категория',
            'assign_id' => 'Исполнитель',
            'assign' => 'Исполнитель',
            'client_id' => 'Клиент',
            'id_client' => 'Клиент',
            'court' => 'Судебный орган',
            'judge' => 'Судья',
            'court_date' => 'Дата следующего заседания',
            'court_date_local' => 'Дата следующего заседания',
            'status' => 'Статус',
            'contract_number' => 'Номер договора',
            'completed_at' => 'Дата завершения'
        ];
    }

    public function getAssigns()
    {
        return $this->hasMany(\dektrium\user\models\User::className(), ['id' => 'id_user'])
            ->viaTable('{{%issues_user}}', ['id_issues' => 'id']);
    }
    public function getClient()
    {
        return $this->hasOne(\dektrium\user\models\User::className(), ['id' => 'id_client']);
    }

    public function getCategory()
    {
        return $this->hasOne(Tree::ClassName(), ['id' => 'id_category']);
    }

    public function getWorkflows()
    {
        return $this->hasMany(CommentModel::className(), ['entityId' => 'id'])->where('relatedTo = "app\\\models\\\Issues"')->andWhere('type = "workflow"');
    }

    public function getStatuslabel()
    {
        if ($this->status == 'completed')
            return '<span class="label label-danger">Завершено</span>';
        else
            return '';
    }


}
