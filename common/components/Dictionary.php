<?php
/**
 * Created by PhpStorm.
 * User: kalim_000
 * Date: 2/26/2017
 * Time: 9:37 PM
 */

namespace common\components;

use common\exceptions\DictionaryException;

class Dictionary extends Request
{
    /**
     * Key to access dictionaryApi
     * @var string
     */
    private $apiKey;

    /**
     * Base url of the dictionary
     * @var string
     */
    public $dictionaryUrl;

    /**
     * Dictionary constructor.
     * @throws DictionaryException
     */
    public function __construct()
    {

        if (empty(\Yii::$app->params['dictionary']['apiKey']) || empty(\Yii::$app->params['dictionary']['baseUrl'])) {
            throw new DictionaryException('Required dictionary params are missing');
        }

        $this->apiKey = \Yii::$app->params['dictionary']['apiKey'];

        parent::__construct(\Yii::$app->params['dictionary']['baseUrl']);
    }

    /**
     * Makes request to the dictionary to translate a word
     * @return mixed|array
     */
    public function translate()
    {
        $this->withQueryParam('key', $this->apiKey);
        $this->useMethod('GET');
        $response = $this->query();

        //TODO: Create some kind of ResponseParser
        $data = json_decode($response->getBody()->getContents());

        return $data;
    }
}