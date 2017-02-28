<?php

namespace frontend\models;

use common\exceptions\DataValidationException;
use common\validators\StrtolowerUTF8;
use Yii;
use yii\behaviors\TimestampBehavior;
use common\models\Language;

/**
 * This is the model class for table "words".
 *
 * @property int $id
 * @property int $lang_id
 * @property string $text
 * @property string $created_at
 *
 * @property Translation[] $translations
 * @property Translation[] $translations0
 * @property Language $lang
 */
class Words extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'words';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => null,
                'value' => function () {
                    return date('Y-m-d');
                },
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lang_id', 'text'], 'required'],
            [['lang_id'], 'integer'],
            [['created_at'], 'safe'],
            ['text', StrtolowerUTF8::className()],
            [['text'], 'string', 'max' => 255],
            [['text'], 'unique'],
            [
                ['lang_id'],
                'exist',
                'skipOnError' => false,
                'targetClass' => Language::className(),
                'targetAttribute' => ['lang_id' => 'id']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lang_id' => 'Language',
            'text' => 'Text',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
//    public function getTranslations()
//    {
//        return $this->hasMany(Translation::className(), ['translation_word_id' => 'id']);
//    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslations()
    {
        return $this->hasMany(Translation::className(), ['word_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLang()
    {
        return $this->hasOne(Language::className(), ['id' => 'lang_id']);
    }

    /**
     * @param $text
     * @param $lang
     * @return Words
     * @throws DataValidationException
     */
    public static function saveWord($text, $lang)
    {
        if (self::isTextExists($text)) {
            return Words::findByText($text);
        }

        $langModel = Language::findByShortName($lang);

        if (!$langModel) {
            throw new DataValidationException('Specified language not found');
        }

        $model = new Words();
        $model->setAttribute('text', $text);
        $model->setAttribute('lang_id', $langModel->id);
        $model->save();
        return $model;
    }

    public static function findByText($text)
    {
        return self::find()->where(['text' => $text])->one();
    }

    public static function isTextExists($text)
    {
        return self::find()->where(['text' => $text])->exists();
    }

}
