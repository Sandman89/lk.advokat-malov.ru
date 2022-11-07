<?php

namespace app\models;

use Yii;
use yii\helpers\Url;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "fiiemanager".
 *
 * @property int $id
 * @property string $name
 * @property string $original_name
 * @property string $path
 * @property int $id_issue
 * @property int $id_comment
 * @property string $token_comment
 * @property string $ext
 * @property string $type
 * @property int $createdAt
 */
class Filemanager extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'filemanager';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_issue', 'id_comment', 'createdAt', 'updatedAt'], 'integer'],
            [['name'], 'string', 'max' => 250],
            [['path', 'token_comment', 'ext', 'type', 'original_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'updatedAt',
            ],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'original_name' => 'Name',
            'path' => 'Path',
            'id_issue' => 'Id Issue',
            'id_comment' => 'Id Comment',
            'ext' => 'Ext',
            'type' => 'Type',
            'token_comment' => 'Token comment',
            'createdAt' => 'Created At',
            'updatedAt' => 'updated At',

        ];
    }

    /**
     * @param  $commentModel \app\components\comments\models\CommentModel
     */
    public static function SaveConstAttachment($commentModel){
        $files = Filemanager::find()->where(['token_comment' => $commentModel->token_comment, 'id_comment' => null])->all();
        if (count($files) > 0) {
            $root = Yii::getAlias('@webroot');
            preg_match('/[A-Z]/', $commentModel->relatedTo, $char_model, PREG_OFFSET_CAPTURE, 0);
            $newFilePath = '/attachments/' . $commentModel->entityId.'-'.$char_model[0][0] . '_' . self::RandomString($commentModel->entityId);
            foreach ($files as $file) {
                //создаем новую папку для соответствующего дела и переносим туда файл из комментария
                if (!is_dir($root . $newFilePath)) {
                    mkdir($root . $newFilePath, 0755, true);
                }
                rename($root . $file->path, $root . $newFilePath . '/' . $file->name);
                //обновляем путь до файла в таблице файлов
                $file->path = $newFilePath . '/' . $file->name;
                //устанавливаем id коммент для ссылки
                $file->id_comment = $commentModel->id;
                $file->update(false);
            }
        }
    }
    /**
     * @param $attachments
     * @return array
     */
    public function SaveTempAttachments($attachments)
    {
        $files = [];
        $allwoedFiles = ['jpg', 'jpeg', 'gif', 'png', 'doc', 'docx', 'pdf', 'xlsx', 'rar', 'zip', 'xlsx', 'xls', 'txt', 'csv', 'rtf', 'one', 'pptx', 'ppsx', 'pot'];
        if ($_FILES) {
            $tmpname = $_FILES['CommentModel']['tmp_name']['image'];
            $fname = $_FILES['CommentModel']['name']['image'];
            $type = $_FILES['CommentModel']['type']['image'];

            if (!empty($attachments)) {
                if (count($fname) > 0) {
                    //Loop through each file
                    for ($i = 0; $i < count($fname); $i++) {
                        //Get the temp file path
                        $tmpFilePath = $tmpname[$i];
                        //Make sure we have a filepath
                        if ($tmpFilePath != "") {
                            //save the filename
                            $shortname = $fname[$i];
                            $newFileName = \yii\helpers\Inflector::transliterate($shortname, 'Russian-Latin/BGN; Any-Latin; Latin-ASCII; NFD; [:Nonspacing Mark:] Remove; NFC;');
                            $newFileName = preg_replace('/[=\s—–-]+/u', '-', $newFileName);
                            $newFileName = strtolower($newFileName);
                            $ext = substr(strrchr($shortname, '.'), 1);
                            $original_name = substr($shortname, 0, strrpos($shortname, '.'));
                            if (in_array($ext, $allwoedFiles)) {
                                //save the url and the file
                                //Upload the file into the temp dir
                                if (move_uploaded_file($tmpFilePath, 'uploads/attachment/temp/' . $newFileName)) {
                                    $true_type = self::getFileType($type[$i]);
                                    $initialPreviewConfig[] = [
                                        'key' => $i,
                                        'caption' => $original_name . '.' . $ext,
                                        'type' => $true_type,
                                        'originalName' => $newFileName,
                                        'extra' => ['name' => $original_name . '.' . $ext]
                                    ];
                                    $preview[] = Url::base(TRUE) . '/uploads/attachment/temp/' . $newFileName;
                                    $files['initialPreview'] = $preview;
                                    $files['initialPreviewAsData'] = true;
                                    // $files['uploadExtraData'][]['is_post'] = 'new';
                                    $files['initialPreviewConfig'] = $initialPreviewConfig;
                                    $files['shortname'] = $newFileName;
                                    $files['original_name'] = $original_name;
                                    $files['ext'] = $ext;
                                    $files['type'] = $true_type;
                                    $files['path'] = '/uploads/attachment/temp/' . $newFileName;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $files;
    }

    /**
     * Get string type file in _FILES [type]
     * @param $_filetype
     */
    public static function getFileType($_filetype)
    {
        if ((preg_match('/(tiff?|wmf)/', $_filetype)) || (preg_match('/(gif|png|jpe?g)/', $_filetype)))
            return 'image';
        if (preg_match('/(htm|html)/', $_filetype))
            return 'html';
        if ((preg_match('/(word|excel|powerpoint|office)/', $_filetype)) || (preg_match('/(docx?|xlsx?|pptx?|pps|potx?|rtf)/', $_filetype)))
            return 'office';
        if ((preg_match('/(xml|javascript|text)/', $_filetype)) || (preg_match('/(txt|md|csv|nfo|ini|json|php|js|css)/', $_filetype)))
            return 'text';
        if (preg_match('/(pdf)/', $_filetype))
            return 'pdf';
        return 'other';
    }

    /**
     * @param Filemanager $file
     */
    public static function getLinkFileType($file)
    {
        if ($file->type == "office")
            return '<a target="_blank" href="https://view.officeapps.live.com/op/embed.aspx?src=' . Url::base(TRUE) . $file->path . '">Смотреть</a>';
        if (($file->type == "pdf") || ($file->type == "text"))
            return '<a target="_blank" href="https://docs.google.com/viewer?url=' . Url::base(TRUE) . $file->path . '&embedded=true">Смотреть</a>';
        return '';

    }

    /**
     * @return string
     */
    public static function RandomString($number)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < 10; $i++) {
            if (($i + 1) * $number > 52)
                $num_char = 52 - ((($i + 1) * $number) % 52);
            else
                $num_char = 52 - ($i + 1) * $number;
            $randstring .= $characters[$num_char];
        }
        return $randstring;
    }
}
