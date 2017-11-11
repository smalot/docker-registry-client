<?php

namespace Smalot\Docker\Registry\Credential;

/**
 * Class LoginPasswordCredential
 *
 * @package Smalot\Docker\Registry\Credential
 */
class LoginPasswordCredential implements CredentialInterface
{
    /**
     * @var string
     */
    protected $login;

    /**
     * @var string
     */
    protected $password;

    /**
     * LoginPasswordCredential constructor.
     *
     * @param string $login
     * @param string $password
     */
    public function __construct($login, $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    /**
     * @inheritdoc
     */
    public function getEncodedCredential($domain)
    {
        return base64_encode($this->login.':'.$this->password);
    }
}
