<?php

namespace AppBundle\Service;

use AppBundle\Entity\Url;
use AppBundle\Exception\HostUnavailableException;
use AppBundle\Exception\ShortNameUsedException;
use Doctrine\ORM\EntityManager;
use Goutte\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Client as GuzzleClient;

class ShortenerService
{
    const HTTP_TIMEOUT = 10;
    const DEFAULT_LENGTH = 5;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * ShortenerService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Url $url
     *
     * @throws \DomainException
     */
    public function generate(Url $url)
    {
        $this->checkUrl($url);
        if (null === $url->getShortTag()) {
            $this->generateShortUrl($url);
        } else {
            $url->setShortTag(strtoupper($url->getShortTag()));
            $existsUrl = $this
                ->entityManager
                ->getRepository(Url::class)
                ->findOneBy(['shortTag' => $url->getShortTag()])
            ;
            if (null!== $existsUrl) {
                throw new ShortNameUsedException('Short url already used');
            }
        }
    }

    /**
     * @param Url $url
     *
     * @throws \DomainException
     */
    private function checkUrl(Url $url)
    {
        $client = new Client();
        $guzzleClient = new GuzzleClient([
            'timeout' => static::HTTP_TIMEOUT,
        ]);
        $client->setClient($guzzleClient);
        try {
            $guzzleClient->request('GET', $url->getOriginalUrl());
        } catch (ConnectException $e) {
            throw new HostUnavailableException(sprintf('Url "%s" is not available', $url->getOriginalUrl()));
        } catch (ClientException $e) {
            throw new HostUnavailableException('Url return response code '. $e->getCode());
        }
    }

    private function generateShortUrl(Url $url)
    {
        $shortUrl = strtoupper(substr(md5(uniqid($url->getOriginalUrl(), true)), 0, static::DEFAULT_LENGTH));
        $url->setShortTag($shortUrl);
    }
}
