<?php
/**
 * This file is part of the Kali-client Project
 *
 * (c) 1001pharmacies <http://github.com/1001pharmacies/Kali-client>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Meup\Bundle\KaliClientBundle\Provider;

use Exception;
use Psr\Log\LoggerInterface;

/**
 * Class KaliAuthenticatorInterface
 *
 * @author Florian Ajir <florian@1001pharmacies.com>
 */
interface KaliAuthenticatorInterface
{
    /**
     * Set a logger to log authenticate errors
     *
     * @param LoggerInterface $logger
     *
     * @return self
     */
    public function setLogger($logger);

    /**
     * Authentication method
     *
     * @throws Exception if failed to retrieve access token
     */
    public function authenticate();

    /**
     * Authentification token getter
     *
     * @return string
     */
    public function getToken();
}
