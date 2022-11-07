<?php
/**
 * Created by PhpStorm.
 * User: fores
 * Date: 09.03.2019
 * Time: 1:11
 */

namespace app\components\comments\models;

use app\models\Filemanager;
use paulzi\adjacencyList\AdjacencyListBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii2mod\behaviors\PurifyBehavior;
use yii2mod\moderation\enums\Status;
use omnilight\datetime\DateTimeBehavior;

/**
 * Class CommentModel
 *
 * @property int $id
 * @property string $entity
 * @property int $entityId
 * @property string $content
 * @property int $parentId
 * @property int $level
 * @property int $createdBy
 * @property int $updatedBy
 * @property string $relatedTo
 * @property string $url
 * @property int $status
 * @property int $createdAt
 * @property int $customcreatedAt
 * @property int $updatedAt
 * @property string $token_comment
 * @property string $title
 * @property string $type
 *
 * @method ActiveRecord makeRoot()
 * @method ActiveRecord appendTo($node)
 * @method ActiveQuery getDescendants()
 * @method AdjacencyListBehavior deleteWithChildren()
 */
class CommentModel extends ActiveRecord
{

    public $image;
    public $customcreatedAt;
    public static $workflow_title = ['Заключен договор',
        'Подготовлено исковое заявление',
        'Заявление подано',
        'Предварительное слушание',
        'Разработана стратегия защиты',
        'Произведен анализ судебной практики',
        'Подготовлено возражение',
        'Осуществлена поездка',
        'Подготовлены судебные прения',
        'Подготовлено выступление',
        'Досудебное урегулирование',
        'Судебное заседание',
        'Звонок',
        'Встреча',
        'Кассационная жалоба',
        'Апелляционная жалоба',
        'Ходатайство'];
    /**
     * @var null|array|ActiveRecord[] comment children
     */
    protected $children;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%comment}}';
    }

    /*  public function scenarios()
      {
          $scenarios = parent::scenarios();
          $scenarios['workflow'] = ['content', 'title','createdAt_local','image','relatedTo','entityId','parentId','url'];
          $scenarios['complete'] = ['content','createdAt_local','image','relatedTo','entityId','parentId','url'];
          return $scenarios;
      }*/
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //[['entity', 'entityId'], 'required'],
            ['content', 'required', 'message' => Yii::t('yii2mod.comments', 'Comment cannot be blank.'), 'except' => ['workflow', 'complete']],
            [['title', 'createdAt_local'], 'required', 'on' => ['workflow']],
            ['type', 'default', 'value' => 'workflow', 'on' => ['workflow']],
            [['content', 'entity', 'relatedTo', 'url', 'token_comment', 'title', 'type'], 'string'],
            ['status', 'default', 'value' => Status::APPROVED],
            ['status', 'in', 'range' => Status::getConstantsByName()],
            [['image', 'createdAt', 'customcreatedAt', 'createdAt_local', 'url', 'token_comment','content'], 'safe'],
            [['image'], 'file', 'extensions' => 'jpg,jpeg, gif, png, doc, docx, pdf, xlsx, rar, zip, xlsx, xls, txt, csv, rtf, one, pptx, ppsx, pot'],
            [['image'], 'file', 'maxSize' => '10000000'],
            ['level', 'default', 'value' => 1],
            ['parentId', 'validateParentID'],
            [['entityId', 'parentId', 'status', 'level'], 'integer'],
        ];
    }

    /**
     * @params $attribute
     */
    public function validateParentID($attribute)
    {
        if ($this->{$attribute} !== null) {
            $parentCommentExist = static::find()
                ->andWhere([
                    'id' => $this->{$attribute},
                    'entityId' => $this->entityId,
                ])
                ->exists();

            if (!$parentCommentExist) {
                $this->addError('content', Yii::t('yii2mod.comments', 'Oops, something went wrong. Please try again later.'));
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'blameable' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'createdBy',
                'updatedByAttribute' => 'updatedBy',
            ],
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'updatedAt',
            ],
            'purify' => [
                'class' => PurifyBehavior::class,
                'attributes' => ['content'],
                'config' => [
                    'HTML.SafeIframe' => true,
                    'URI.SafeIframeRegexp' => '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%',
                    'AutoFormat.Linkify' => true,
                    'HTML.TargetBlank' => true,
                    'HTML.Allowed' => 'a[href], iframe[src|width|height|frameborder], img[src]',
                ],
            ],
            'adjacencyList' => [
                'class' => AdjacencyListBehavior::class,
                'parentAttribute' => 'parentId',
                'sortable' => false,
            ],
            'datetime' => [
                'class' => DateTimeBehavior::className(), // Our behavior
                'originalFormat' => ['datetime', 'yyyy-MM-dd HH:mm'],
                'targetFormat' => ['datetime', 'dd.MM.yyyy HH:mm'],
                'attributes' => [
                    'createdAt', // List all editable date/time attributes
                ],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('yii2mod.comments', 'ID'),
            'content' => Yii::t('yii2mod.comments', 'Content'),
            'entity' => Yii::t('yii2mod.comments', 'Entity'),
            'entityId' => Yii::t('yii2mod.comments', 'Entity ID'),
            'parentId' => Yii::t('yii2mod.comments', 'Parent ID'),
            'status' => Yii::t('yii2mod.comments', 'Status'),
            'level' => Yii::t('yii2mod.comments', 'Level'),
            'createdBy' => Yii::t('yii2mod.comments', 'Created by'),
            'updatedBy' => Yii::t('yii2mod.comments', 'Updated by'),
            'relatedTo' => Yii::t('yii2mod.comments', 'Related to'),
            'url' => Yii::t('yii2mod.comments', 'Url'),
            'createdAt' => Yii::t('yii2mod.comments', 'Created date'),
            'createdAt_local' => Yii::t('yii2mod.comments', 'Created date'),
            'updatedAt' => Yii::t('yii2mod.comments', 'Updated date'),
            'title' => 'Название события',
            'type' => 'Тип комментария',
            'token_comment' => 'Токен для связи файлов с комментариями',
            'customcreatedAt' => 'Дата события'
        ];
    }


    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            //если коммент - это Рабочий процесс, то его дата находится в поле createdAt_local и ее надо перевести
            if (!empty($this->createdAt_local)) {
                $this->createdAt = strtotime($this->createdAt_local);
            }
            if ($insert) {
                if ($this->parentId > 0) {
                    $parentNodeLevel = static::find()->select('level')->where(['id' => $this->parentId])->scalar();
                    $this->level += $parentNodeLevel;
                }
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        //меняем статус у задачи если комментарий о завершении задачи
        if ($this->type == 'complete') {
            $table_name = '';
            $model_name = $this->relatedTo;
            if ($model_name == 'app\models\Task'){
                $table_name = 'task';
            }
            if ($model_name == 'app\models\Issues'){
                $table_name = 'issues';
            }
            $class_name = $model_name;
            $model = new $class_name();
            $find_record = $model->findOne([$table_name.'.id' => $this->entityId]);
            $find_record->status = 'completed';
            //todo сделать время локальным для разных пользователей. Сейчас работает установка серверного времени.
            $find_record->completed_at = date('Y-m-d H:i:s',time());
            $find_record->save();
        }
    }

    /**
     * @return bool
     */
    public function saveComment()
    {
        if ($this->validate()) {
            if (empty($this->parentId)) {
                return $this->makeRoot()->save();
            } else {
                $parentComment = static::findOne(['id' => $this->parentId]);
                return $this->appendTo($parentComment)->save();
            }
        }

        return false;
    }

    /**
     * Author relation
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(Yii::$app->getUser()->identityClass, ['id' => 'createdBy']);
    }

    /**
     * Get comments tree.
     *
     * @param string $entity
     * @param string $entityId
     * @param null $maxLevel
     *
     * @return array|ActiveRecord[]
     */
    public static function getTree($entityId, $maxLevel = null, $orderby, $relatedTo)
    {
        $query = static::find()
            ->alias('c')
            ->andWhere([
                'c.entityId' => $entityId,
            ])
            ->andWhere([
                'c.relatedTo' => $relatedTo,
            ])
            ->orderBy(['c.parentId' => SORT_DESC, 'c.createdAt' => $orderby])
            ->with(['author']);

        if ($maxLevel > 0) {
            $query->andWhere(['<=', 'c.level', $maxLevel]);
        }

        $models = $query->all();

        if (!empty($models)) {
            $models = static::buildTree($models);
        }

        return $models;
    }

    /**
     * Build comments tree.
     *
     * @param array $data comments list
     * @param int $rootID
     *
     * @return array|ActiveRecord[]
     */
    protected static function buildTree(&$data, $rootID = 0)
    {
        $tree = [];

        foreach ($data as $id => $node) {
            if ($node->parentId == $rootID) {
                unset($data[$id]);
                $node->children = self::buildTree($data, $node->id);
                $tree[] = $node;
            }
        }

        return $tree;
    }

    /**
     * @return array|null|ActiveRecord[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param $value
     */
    public function setChildren($value)
    {
        $this->children = $value;
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return !empty($this->children);
    }

    /**
     * @param $created_by
     * @return bool
     */
    public function isOwner()
    {
        if ($this->author->id == Yii::$app->user->id)
            return true;
        else
            return false;
    }

    /**
     * @return string
     */
    public function getPostedDate()
    {
        return Yii::$app->formatter->asDatetime($this->createdAt, 'php:d.m.Y в H:i');
    }

    /**
     * @return mixed
     */
    public function getAuthorName()
    {
        if ($this->author->hasMethod('getUsername')) {
            return $this->author->getUsername();
        }
        return $this->author->username;
    }

    /**
     * @return string
     */
    public function getContent()
    {   $label = ($this->type == "complete") ? '<span class="label label-danger">Заключение</span> ':'';
        return  $label . nl2br($this->content);
    }


    /**
     * @return ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany(Filemanager::className(), ['id_comment' => 'id']);
    }

    /**
     * Get avatar of the user
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->author->getImageSrc('small_');
    }

    /**
     * Get list of all authors
     *
     * @return array
     */
    public static function getAuthors()
    {
        $query = static::find()
            ->alias('c')
            ->select(['c.createdBy', 'a.username'])
            ->joinWith('author a')
            ->groupBy(['c.createdBy', 'a.username'])
            ->orderBy('a.username')
            ->asArray()
            ->all();

        return ArrayHelper::map($query, 'createdBy', 'author.username');
    }

    /**
     * @return int
     */
    public function getCommentsCount()
    {
        return (int)static::find()
            ->andWhere(['entityId' => $this->entityId])
            ->count();
    }

    /**
     * @return string
     */
    public function getAnchorUrl()
    {
        return "#comment-{$this->id}";
    }

    /**
     * @return null|string
     */
    public function getViewUrl()
    {
        if (!empty($this->url)) {
            return $this->url . $this->getAnchorUrl();
        }

        return null;
    }

    /**
     * Возвращает в переменную complete_with_no_comment false если завершить дело или задачу без сохранение автокомментария нельзя
     *
     *
     *
     * public function getAccessCompleteTaskWithNoComent(){
     * $count_comment_current_user = (int)static::find()
     * ->andWhere(['relatedTo' => $this->relatedTo])
     * ->andWhere(['createdBy' => Yii::$app->user->id])
     * ->count();
     * if ($count_comment_current_user > 0)
     * return false;
     * else
     * return true;
     * }
     */
}
