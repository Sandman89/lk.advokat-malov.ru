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
            [['path', 'token_comment', 'ext', 'type','original_name'], 'string', 'max' => 255],
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
     * @param $attachments
     * @return array
     */
    public function SaveTempAttachments($attachments)
    {
        $files = [];
        $allwoedFiles = ['jpg','jpeg', 'gif', 'png', 'doc', 'docx', 'pdf', 'xlsx', 'rar', 'zip', 'xlsx', 'xls', 'txt', 'csv', 'rtf', 'one', 'pptx', 'ppsx', 'pot'];
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
                            $size = $attachments['CommentModel']['size']['image'][$i];
                            $ext = substr(strrchr($shortname, '.'), 1);
                            $original_name = substr($shortname,0, strrpos($shortname, '.'));
                            if (in_array($ext, $allwoedFiles)) {
                                //save the url and the file
                                //Upload the file into the temp dir
                                if (move_uploaded_file($tmpFilePath, 'uploads/attachment/temp/' . $newFileName)) {
                                    $true_type = self::getFileType($type[$i]);
                                    $initialPreviewConfig[] = [
                                        'key'=>$i,
                                        'caption'=>$original_name.'.'.$ext,
                                        'type'=>$true_type,
                                        'size'=>(($size/1000)),
                                        'originalName'=>$newFileName
                                        ];
                                    $preview[] =  Url::base(TRUE) . '/uploads/attachment/temp/' . $newFileName;
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
        if ((preg_match('/(tiff?|wmf)/', $_filetype))||(preg_match('/(gif|png|jpe?g)/', $_filetype)))
            return 'image';
        if (preg_match('/(htm|html)/', $_filetype))
            return 'html';
        if ((preg_match('/(word|excel|powerpoint|office)/', $_filetype))||(preg_match('/(docx?|xlsx?|pptx?|pps|potx?|rtf)/', $_filetype)))
            return 'office';
        if ((preg_match('/(xml|javascript|text)/', $_filetype))||(preg_match('/(txt|md|csv|nfo|ini|json|php|js|css)/', $_filetype)))
            return 'text';
        if (preg_match('/(pdf)/', $_filetype))
            return 'pdf';
        return 'other';
    }

    /**
     * @param Filemanager $file
     */
    public static function getLinkFileType($file){
        if ($file->type == "office")
            return;
    }
}
