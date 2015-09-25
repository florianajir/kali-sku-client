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
 * Class Sku
 *
 * @author florianajir <florian@1001pharmacies.com>
 */
class Sku implements SkuInterface
{
    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $project;

    /**
     * @var string
     */
    protected $foreignType;

    /**
     * @var string
     */
    protected $foreignId;

    /**
     * @var string
     */
    protected $permalink;

    /**
     * @var bool
     */
    protected $active;

    /**
     * {@inheritDoc}
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * {@inheritDoc}
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * {@inheritDoc}
     */
    public function setProject($project)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getForeignType()
    {
        return $this->foreignType;
    }

    /**
     * {@inheritDoc}
     */
    public function setforeignType($foreignType)
    {
        $this->foreignType = $foreignType;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getForeignId()
    {
        return $this->foreignId;
    }

    /**
     * {@inheritDoc}
     */
    public function setForeignId($foreignId)
    {
        $this->foreignId = $foreignId;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getPermalink()
    {
        return $this->permalink;
    }

    /**
     * {@inheritDoc}
     */
    public function setPermalink($permalink)
    {
        $this->permalink = $permalink;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * String representation of object
     *
     * @link http://php.net/manual/en/serializable.serialize.php
     *
     * @return string the json representation of the object
     */
    public function serialize()
    {
        $data = array();

        if (isset($this->code)) {
            $data['code'] = $this->code;
        }

        if (isset($this->foreignId)) {
            $data['id'] = $this->foreignId;
        }

        if (isset($this->foreignType)) {
            $data['type'] = $this->foreignType;
        }

        if (isset($this->project)) {
            $data['project'] = $this->project;
        }

        if (isset($this->permalink)) {
            $data['permalink'] = $this->permalink;
        }

        if (isset($this->active)) {
            $data['active'] = $this->active;
        }

        return json_encode($data);
    }

    /**
     * Constructs the object
     *
     * @link http://php.net/manual/en/serializable.unserialize.php
     *
     * @param string|array $data The string representation of the object.
     *
     * @return self
     */
    public function unserialize($data)
    {
        //if data is a json string, decode in array
        if (is_string($data)) {
            $data = json_decode($data, true);
        }

        if (isset($data['code'])) {
            $this->code = $data['code'];
        }

        if (isset($data['id'])) {
            $this->foreignId = $data['id'];
        }

        if (isset($data['type'])) {
            $this->foreignType = $data['type'];
        }

        if (isset($data['project'])) {
            $this->project = $data['project'];
        }

        if (isset($data['permalink'])) {
            $this->permalink = $data['permalink'];
        }

        if (isset($data['active'])) {
            $this->active = (bool) $data['active'];
        }

        return $this;
    }
}
