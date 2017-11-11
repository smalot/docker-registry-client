<?php

namespace Smalot\Docker\Registry\Credential;

/**
 * Interface CredentialInterface
 *
 * @package Smalot\Docker\Registry\Credential
 */
interface CredentialInterface
{
    /**
     * @param string $domain
     *
     * @return string
     */
    public function getEncodedCredential($domain);
}
