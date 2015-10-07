<?php
/**
 * This file is part of the Meup Kali Client Bundle.
 *
 * (c) 1001pharmacies <http://github.com/1001pharmacies/kali-client>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
    public function setForeignType($foreignType);

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

    /**
     * String representation of object
     *
     * @link http://php.net/manual/en/serializable.serialize.php
     *
     * @return string the json representation of the object
     */
    public function serialize();

    /**
     * Constructs the object
     *
     * @link http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string|array $data The string representation of the object.
     *
     * @return self
     */
    public function unserialize($data);
}
