<?php

namespace AppBundle\Entity;

/**
 * Url
 */
class Url
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $originalUrl;

    /**
     * @var string
     */
    private $shortTag;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var int
     */
    private $usageCount = 0;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set originalUrl
     *
     * @param string $originalUrl
     *
     * @return Url
     */
    public function setOriginalUrl($originalUrl)
    {
        $this->originalUrl = $originalUrl;

        return $this;
    }

    /**
     * Get originalUrl
     *
     * @return string
     */
    public function getOriginalUrl()
    {
        return $this->originalUrl;
    }

    /**
     * Set shortUrl
     *
     * @param string $shortTag
     *
     * @return Url
     */
    public function setShortTag($shortTag)
    {
        $this->shortTag = $shortTag;

        return $this;
    }

    /**
     * Get shortUrl
     *
     * @return string
     */
    public function getShortTag()
    {
        return $this->shortTag;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Url
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set usagesCount
     *
     * @param integer $usageCount
     *
     * @return Url
     */
    public function setUsageCount($usageCount)
    {
        $this->usageCount = $usageCount;

        return $this;
    }

    /**
     * Get usagesCount
     *
     * @return int
     */
    public function getUsageCount()
    {
        return $this->usageCount;
    }
}
