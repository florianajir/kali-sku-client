<?php

namespace Meup\Bundle\KaliClientBundle\Model;

/**
 * Interface SkuInterface
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
interface SkuInterface
{
    /**
     * Get code
     *
     * @return string
     */
    public function getCode();

    /**
     * Set code
     *
     * @param string $code
     *
     * @return self
     */
    public function setCode($code);

    /**
     * Get project
     *
     * @return string
     */
    public function getProject();

    /**
     * Set project
     *
     * @param string $project
     *
     * @return self
     */
    public function setProject($project);

    /**
     * Get foreignerType
     *
     * @return string
     */
    public function getForeignType();

    /**
     * Set foreignType
     *
     * @param string $foreignType
     *
     * @return self
     */
    public function setforeignType($foreignType);

    /**
     * Get foreignId
     *
     * @return string
     */
    public function getForeignId();

    /**
     * Set foreignId
     *
     * @param string $foreignId
     *
     * @return self
     */
    public function setForeignId($foreignId);

    /**
     * @return string
     */
    public function getPermalink();

    /**
     * @param string $permalink
     *
     * @return self
     */
    public function setPermalink($permalink);

    /**
     * @return bool
     */
    public function isActive();
}