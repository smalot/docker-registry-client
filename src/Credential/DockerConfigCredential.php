<?php

namespace Smalot\Docker\Registry\Credential;

/**
 * Class DockerConfigCredential
 *
 * @package Smalot\Docker\Registry\Credential
 */
class DockerConfigCredential implements CredentialInterface
{
    /**
     * @var string
     */
    protected $file;

    /**
     * DockerConfigCredential constructor.
     *
     * @param string $file
     *
     * @throws \Exception
     */
    public function __construct($file = null)
    {
        if (is_null($file)) {
            if ($home = getenv('HOME')) {
                $file = $home.'/.docker/config.json';
            } else {
                throw new \Exception('Unable to detect config file.');
            }
        }

        $this->file = $file;
    }

    /**
     * @inheritdoc
     */
    public function getEncodedCredential($domain)
    {
        if (!file_exists($this->file)) {
            throw new \Exception('File does not exist.');
        }

        $content = file_get_contents($this->file);
        $config = json_decode($content, true);

        if (isset($config['auths'][$domain]['auth'])) {
            return $config['auths'][$domain]['auth'];
        } elseif (isset($config['auths']['https://'.$domain]['auth'])) {
            return $config['auths']['https://'.$domain]['auth'];
        }

        return false;
    }
}
