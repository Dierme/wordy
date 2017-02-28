<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "language".
 *
 * @property int $id
 * @property string $lang_short_name
 * @property string $lang_full_name
 *
 * @property Words[] $words
 */
class Language extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'language';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lang_short_name', 'lang_full_name'], 'required'],
            [['lang_short_name', 'lang_full_name'], 'string', 'max' => 32],
            [['lang_short_name'], 'unique'],
            [['lang_full_name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lang_short_name' => 'Lang Short Name',
            'lang_full_name' => 'Lang Full Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWords()
    {
        return $this->hasMany(Words::className(), ['lang_id' => 'id']);
    }

    public static function findByShortName($shortName)
    {
        return self::find()->where(['lang_short_name'=>$shortName])->one();
    }
}
