<?php
/**
 * Created by PhpStorm.
 * User: kalim_000
 * Date: 2/28/2017
 * Time: 7:42 AM
 */

namespace common\handlers;

use frontend\models\Translation;
use frontend\models\Words;
use common\exceptions\UnexpectedResponseFormat;
use common\exceptions\TranslationException;

class TranslateHandler
{
    /**
     * Text of the word to be translated
     * @var string
     */
    public $text;

    /**
     * Short language name of the $text
     * @var string
     */
    public $sourceLang;

    /**
     * Short name of the lang to be translated to
     * @var string
     */
    public $targetLang;

    /**
     * Dictionary object
     * @var
     */
    private $dictionary;

    /**
     * TranslateHandler constructor.
     */
    public function __construct()
    {
        $dictionaryClass = $this->resolveDictionary();

        $this->dictionary = new $dictionaryClass();
    }

    /**
     * @param $text
     * @return array
     * @throws TranslationException
     */
    public function handle($text)
    {
        $this->text = $text;

        if (empty($this->targetLang) || empty($this->sourceLang)) {
            throw new TranslationException('targetLang and sourceLang are not set');
        }

        $lang = $this->sourceLang . '-' . $this->targetLang;
        $this->dictionary->withQueryParam('lang', $lang);
        $this->dictionary->withQueryParam('text', $this->text);
        $response = $this->dictionary->translate();

        $translation = $this->parseApiResponse($response);
        $sourceTextModel = Words::saveWord($this->text, $this->sourceLang);

        if (empty($sourceTextModel->id)) {
            throw new TranslationException('Failed to save source text as word');
        }

        foreach ($translation as $word) {

            $trSavingRequired = !Words::isTextExists($word);

            $trModel = Words::saveWord($word, $this->targetLang);

            if (empty($trModel->id)) {
                throw new TranslationException('Failed to save translation text as word');
            }
            if ($trSavingRequired) {
                Translation::saveTranslation($sourceTextModel->id, $trModel->id);
            }
        }

        return [
            'text' => $this->text,
            'translation' => $translation
        ];
    }

    private function parseApiResponse($apiResponse)
    {
        $parsed = [];

        if (!isset($apiResponse->def)) {
            throw new UnexpectedResponseFormat('Unexpected response format encountered');
        }

        foreach ($apiResponse->def as $definition) {

            if (!isset($definition->tr)) {
                throw new UnexpectedResponseFormat('Unexpected response format encountered');
            }

            foreach ($definition->tr as $translation) {
                $parsed[] = $translation->text;

                if (!empty($translation->syn)) {
                    foreach ($translation->syn as $synonym) {
                        $parsed[] = $synonym->text;
                    }
                }
            }
        }
        return $parsed;
    }

    public function setSourceLang($lang)
    {
        $this->sourceLang = $lang;

        return $this;
    }

    public function setTargetLang($lang)
    {
        $this->targetLang = $lang;

        return $this;
    }

    private function resolveDictionary()
    {
        return new \Yii::$app->params['dictionaryClass'];
    }
}