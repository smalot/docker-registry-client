<?php

namespace Smalot\Docker\Registry\Action;

use GuzzleHttp\Psr7\Uri;

/**
 * Class TagAction
 *
 * @package Smalot\Docker\Registry\Action
 */
class TagAction implements ActionInterface
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $image;

    /**
     * TagAction constructor.
     *
     * @param string $host
     * @param string $image
     */
    public function __construct($host, $image)
    {
        if (!preg_match('/^https?:\/\//', $host)) {
            $host = 'https://'.$host;
        }

        $this->host = rtrim($host, '/');
        $this->image = trim($image, '/');
    }

    /**
     * @inheritDoc
     */
    public function getMethod()
    {
        return 'GET';
    }

    /**
     * @inheritDoc
     */
    public function getUrl()
    {
        return new Uri($this->host.'/v2/'.$this->image.'/tags/list');
    }

    /**
     * @inheritDoc
     */
    public function getScope()
    {
        return 'registry:'.$this->image.':pull';
    }

}