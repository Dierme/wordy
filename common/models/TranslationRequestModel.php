<?php
/**
 * Created by PhpStorm.
 * User: kalim_000
 * Date: 2/26/2017
 * Time: 7:51 PM
 */

namespace common\models;


use common\handlers\TranslateHandler;
use common\validators\StrtolowerUTF8;
use yii\base\Model;

class TranslationRequestModel extends Model
{
    public $sourceLang;

    public $targetLang;

    public $sourceText;

    public $translationTexts;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['sourceText', 'string'],
            ['translationTexts', 'safe'],
            [
                ['sourceLang', 'targetLang'],
                'exist',
                'skipOnError' => false,
                'targetClass' => Language::className(),
                'targetAttribute' => ['sourceLang' => 'lang_short_name']
            ],
            ['sourceText', StrtolowerUTF8::className()],
            ['translationTexts', StrtolowerUTF8::className()],
        ];
    }

    public function translate()
    {
        if(!$this->validate()){
            return false;
        }

        $handler = new TranslateHandler();
        $handler->setSourceLang($this->sourceLang);
        $handler->setTargetLang($this->targetLang);

        $translation = $handler->handle($this->sourceText);

        return $translation;
    }

}