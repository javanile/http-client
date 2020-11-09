<?php

/**
 * File description.
 *
 * PHP version 5
 *
 * @category   WebServiceClient
 *
 * @author     Francesco Bianco
 * @copyright
 * @license    MIT
 */

namespace Javanile\VtigerClient;

class Logger
{
    /**
     *
     */
    protected $tag;

    /**
     * @var array|false|string
     */
    protected $file;

    /**
     * @var bool
     */
    protected $active;

    /**
     * Logger constructor.
     *
     * @param $args
     */
    public function __construct($args)
    {
        $this->file = getenv('VT_CLIENT_LOG_FILE');
        $this->active = $this->file ? true : false;

        $this->setTag(isset($args['username']) ? $args['username'] : null, $args['endpoint']);
    }

    /**
     * @param $object
     * @param $trace
     * @return mixed
     */
    public function log($object, $trace)
    {
        if ($this->active && $this->file) {
            $this->appendLogLine($object, $trace);
        }

        return $object;
    }

    /**
     *
     */
    protected function appendLogLine($object, $trace)
    {
        $line = date('Y-m-d H:i:s');
        $line .= ' '.json_encode($object, JSON_UNESCAPED_SLASHES);
        $line .= "\n";

        file_put_contents($this->file, $line, FILE_APPEND);
    }

    /**
     * @param $method
     * @param $request
     * @param $response
     *
     * @return mixed
     */
    public function request($method, $request, $response)
    {
        if ($this->active) {
            $line = date('Y-m-d H:i:s').' '.$this->tag.' ';
            $line .= empty($response['success']) ? '[ERROR]' : '[INFO]';
            $line .= ' '.$method.' '.json_encode($request, JSON_UNESCAPED_SLASHES);
            $line .= ' '.json_encode($response, JSON_UNESCAPED_SLASHES);
            $line .= "\n";

            file_put_contents($this->file, $line, FILE_APPEND);
        }

        return $response;
    }

    /**
     * @param $username
     * @param $endpoint
     */
    public function setTag($username, $endpoint)
    {
        $host = parse_url($endpoint, PHP_URL_HOST);

        $this->tag = $username ? $username.'@'.$host : $host;
    }
}