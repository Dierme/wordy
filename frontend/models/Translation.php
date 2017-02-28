<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "translation".
 *
 * @property int $id
 * @property int $word_id
 * @property int $translation_word_id
 *
 * @property Words $word
 * @property Words $translationWord
 */
class Translation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'translation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['word_id', 'translation_word_id'], 'required'],
            [['word_id', 'translation_word_id'], 'integer'],
            [['word_id'], 'exist', 'skipOnError' => false, 'targetClass' => Words::className(), 'targetAttribute' => ['word_id' => 'id']],
            [['translation_word_id'], 'exist', 'skipOnError' => false, 'targetClass' => Words::className(), 'targetAttribute' => ['translation_word_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'word_id' => 'Word ID',
            'translation_word_id' => 'Translation Word ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWord()
    {
        return $this->hasOne(Words::className(), ['id' => 'word_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTranslationWord()
    {
        return $this->hasOne(Words::className(), ['id' => 'translation_word_id']);
    }

    public static function saveTranslation($wordId, $trWordId)
    {
        $trModel = new Translation();
        $trModel->setAttribute('word_id', $wordId);
        $trModel->setAttribute('translation_word_id', $trWordId);
        $trModel->save();
        return $trModel;
    }
}
