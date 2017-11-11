<?php

namespace Smalot\Docker\Registry;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Smalot\Docker\Registry\Action\ActionInterface;
use Smalot\Docker\Registry\Action\ManifestAction;
use Smalot\Docker\Registry\Credential\CredentialInterface;
use Smalot\Docker\Registry\Credential\DockerConfigCredential;

/**
 * Class Registry
 *
 * @package Smalot\Docker\Registry
 */
class Registry
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Registry constructor.
     *
     * @param \GuzzleHttp\ClientInterface $client
     * @param \Psr\Log\LoggerInterface|null $logger
     */
    public function __construct(ClientInterface $client = null, LoggerInterface $logger = null)
    {
        $this->client = $client ?: new Client();
        $this->logger = $logger ?: new NullLogger();
    }

    /**
     * @param \Smalot\Docker\Registry\Action\ActionInterface $action
     * @param \Smalot\Docker\Registry\Credential\CredentialInterface $credential
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function process(ActionInterface $action, CredentialInterface $credential = null)
    {
        try {
            return $this->processAnonymousCall($action);
        } catch (ClientException $e) {
            $token = $this->getBearerToken($e->getResponse(), $action, $credential);

            return $this->processAuthenticatedCall($action, $token);
        }
    }

    public function getManifest($server, $image, $tag = null)
    {
        $action = new ManifestAction($server, $image, $tag);

        $response = $this->process($action);
        $content = $response->getBody()->getContents();
    }

    /**
     * @param \Smalot\Docker\Registry\Action\ActionInterface $action
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function processAnonymousCall(ActionInterface $action)
    {
        return $this->client->request($action->getMethod(), $action->getUrl());
    }

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param \Smalot\Docker\Registry\Action\ActionInterface $action
     * @param \Smalot\Docker\Registry\Credential\CredentialInterface|null $credential
     *
     * @return string
     */
    protected function getBearerToken(
      ResponseInterface $response,
      ActionInterface $action,
      CredentialInterface $credential = null
    ) {
        if (is_null($credential)) {
            $credential = new DockerConfigCredential('/home/sebastien/.docker/config.json');
        }

        $header = $response->getHeader('WWW-Authenticate');
        $options = $this->getHeaderOptions(reset($header));

        $options += [
          'realm' => $action->getUrl()->getScheme().'://'.$action->getUrl()->getHost().'/v2/token',
          'service' => $action->getUrl()->getHost(),
          'scope' => $action->getScope(),
        ];

        $response = $this->client->request(
          'GET',
          $options['realm'],
          [
            'headers' => [
              'Authorization' => 'Basic '.$credential->getEncodedCredential($action->getUrl()->getHost()),
            ],
            'query' => [
              'service' => $options['service'],
              'scope' => $options['scope'],
            ],
          ]
        );

        $json = json_decode($response->getBody()->getContents(), true);

        return $json['token'];
    }

    /**
     * @param \Smalot\Docker\Registry\Action\ActionInterface $action
     * @param string $token
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function processAuthenticatedCall(ActionInterface $action, $token)
    {
        $options = [
          'headers' => [
            'Authorization' => 'Bearer '.$token,
          ],
        ];

        return $this->client->request('GET', $action->getUrl(), $options);
    }

    /**
     * @param string $header
     *
     * @return array
     */
    protected function getHeaderOptions($header)
    {
        $match = [];

        if (preg_match_all('/([a-zA-Z][a-zA-Z_-]*)\s*(?:=(?:"([^"]*)"|([^ \t",;]*)))?/', $header, $match)) {
            return array_combine($match[1], $match[2]);
        }

        return [];
    }
}
