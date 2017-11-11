<?php

namespace Smalot\Docker\Registry\Action;

/**
 * Interface ActionInterface
 *
 * @package Smalot\Docker\Registry\Action
 */
interface ActionInterface
{
    /**
     * @return string
     */
    public function getMethod();

    /**
     * @return \GuzzleHttp\Psr7\Uri
     */
    public function getUrl();

    /**
     * @return string
     */
    public function getScope();
}
