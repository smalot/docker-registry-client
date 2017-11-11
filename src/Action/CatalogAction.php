<?php

namespace Smalot\Docker\Registry\Action;

use GuzzleHttp\Psr7\Uri;

/**
 * Class CatalogAction
 *
 * @package Smalot\Docker\Registry\Action
 */
class CatalogAction implements ActionInterface
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $folder;

    /**
     * CatalogAction constructor.
     *
     * @param string $host
     * @param string $folder
     */
    public function __construct($host, $folder = null)
    {
        if (!preg_match('/^https?:\/\//', $host)) {
            $host = 'https://'.$host;
        }

        $this->host = rtrim($host, '/');
        $this->folder = $folder;
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
        return new Uri($this->host.'/v2/'.($this->folder ? $this->folder.'/' : '').'_catalog');
    }

    /**
     * @inheritDoc
     */
    public function getScope()
    {
        return 'registry:catalog:*';
    }

}