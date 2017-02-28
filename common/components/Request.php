<?php
/**
 * Created by PhpStorm.
 * User: kalim_000
 * Date: 2/26/2017
 * Time: 8:43 PM
 */

namespace common\components;

class Request
{
    /**
     * Performs http queries
     * @var mixed
     */
    public $client;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * Query method
     * @var
     */
    private $method;

    /**
     * Query options
     * @var array
     */
    private $options;

    /**
     * Query resulting response
     * @var object
     */
    private $response;

    /**
     * Request constructor.
     * @param $baseUrl
     */
    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl;

        $this->client = $this->resolveClient();

        $this->options = ['query' => []];
    }

    /**
     * @param $paramName string
     * @param $value int|string|bool
     */
    public function withQueryParam($paramName, $value)
    {
        $this->options['query'][$paramName] = $value;
    }

    /**
     * @param $method
     */
    public function useMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Performs call via $this->client class
     * @return object
     */
    public function query()
    {
        $this->response = $this->client->request(
            $this->method,
            $this->baseUrl,
            $this->options
        );

        return $this->response;
    }

    /**
     * Resolves HTTP client dependency
     * @return mixed
     */
    private function resolveClient()
    {
        return new \Yii::$app->params['http-client']();
    }
}