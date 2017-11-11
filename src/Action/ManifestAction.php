<?php

namespace Smalot\Docker\Registry\Action;

use GuzzleHttp\Psr7\Uri;

/**
 * Class ManifestAction
 *
 * @package Smalot\Docker\Registry\Action
 */
class ManifestAction implements ActionInterface
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
     * @var string
     */
    protected $tag;

    /**
     * ManifestAction constructor.
     *
     * @param string $host
     * @param string $image
     * @param string $tag
     */
    public function __construct($host, $image, $tag = null)
    {
        if (!preg_match('/^https?:\/\//', $host)) {
            $host = 'https://'.$host;
        }

        $this->host = rtrim($host, '/');
        $this->image = trim($image, '/');
        $this->tag = $tag;
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
        return new Uri($this->host.'/v2/'.$this->image.'/manifests/'.$this->tag);
    }

    /**
     * @inheritDoc
     */
    public function getScope()
    {
        return 'registry:'.$this->image.':pull';
    }

}