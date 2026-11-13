<?php

/**
 * custom hosting manager
 * singleton class
 *
 * Standard: PSR-2
 *
 * @package SC\DUPX\HOST
 * @link    http://www.php-fig.org/psr/psr-2/
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

require_once(GambDuplicaWP____PATH . '/classes/host/interface.host.php');
require_once(GambDuplicaWP____PATH . '/classes/host/class.godaddy.host.php');
require_once(GambDuplicaWP____PATH . '/classes/host/class.wpengine.host.php');
require_once(GambDuplicaWP____PATH . '/classes/host/class.cloudways.host.php');
require_once(GambDuplicaWP____PATH . '/classes/host/class.wordpresscom.host.php');
require_once(GambDuplicaWP____PATH . '/classes/host/class.liquidweb.host.php');
require_once(GambDuplicaWP____PATH . '/classes/host/class.pantheon.host.php');
require_once(GambDuplicaWP____PATH . '/classes/host/class.flywheel.host.php');

class GAMB_DUPLICA_WP_Custom_Host_Manager
{
    const HOST_GODADDY      = 'godaddy';
    const HOST_WPENGINE     = 'wpengine';
    const HOST_CLOUDWAYS    = 'cloudways';
    const HOST_WORDPRESSCOM = 'wordpresscom';
    const HOST_LIQUIDWEB    = 'liquidweb';
    const HOST_PANTHEON     = 'pantheon';
    const HOST_FLYWHEEL     = 'flywheel';

    /** @var ?self */
    protected static $instance;
    /** @var bool */
    private $initialized = false;
    /** @var GAMB_DUPLICA_WP_Host_interface[] */
    private $customHostings = [];
    /** @var string[] */
    private $activeHostings = [];

    /**
     *
     * @return self
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Class constructor
     */
    private function __construct()
    {
        $this->customHostings[GAMB_DUPLICA_WP_WPEngine_Host::getIdentifier()]     = new GAMB_DUPLICA_WP_WPEngine_Host();
        $this->customHostings[GAMB_DUPLICA_WP_Cloudways_Host::getIdentifier()]    = new GAMB_DUPLICA_WP_Cloudways_Host();
        $this->customHostings[GAMB_DUPLICA_WP_GoDaddy_Host::getIdentifier()]      = new GAMB_DUPLICA_WP_GoDaddy_Host();
        $this->customHostings[GAMB_DUPLICA_WP_WordpressCom_Host::getIdentifier()] = new GAMB_DUPLICA_WP_WordpressCom_Host();
        $this->customHostings[GAMB_DUPLICA_WP_Liquidweb_Host::getIdentifier()]    = new GAMB_DUPLICA_WP_Liquidweb_Host();
        $this->customHostings[GAMB_DUPLICA_WP_Pantheon_Host::getIdentifier()]     = new GAMB_DUPLICA_WP_Pantheon_Host();
        $this->customHostings[GAMB_DUPLICA_WP_Flywheel_Host::getIdentifier()]     = new GAMB_DUPLICA_WP_Flywheel_Host();
    }

    /**
     * Initialize custom hostings
     *
     * @return bool
     */
    public function init(): bool
    {
        if ($this->initialized) {
            return true;
        }
        foreach ($this->customHostings as $cHost) {
            if (!($cHost instanceof GAMB_DUPLICA_WP_Host_interface)) {
                throw new Exception('Host must implement GAMB_DUPLICA_WP_Host_interface');
            }
            if ($cHost->isHosting()) {
                $this->activeHostings[] = $cHost->getIdentifier();
                $cHost->init();
            }
        }
        $this->initialized = true;
        return true;
    }

    /**
     * Return active hostings
     *
     * @return string[]
     */
    public function getActiveHostings()
    {
        return $this->activeHostings;
    }

    /**
     * Returns true if the current site is hosted on identified host
     *
     * @param string $identifier Host identifier
     *
     * @return bool
     */
    public function isHosting($identifier)
    {
        return in_array($identifier, $this->activeHostings);
    }

    /**
     * Returns true if the current site is hosted on a managed host
     *
     * @return bool
     */
    public function isManaged(): bool
    {
        if ($this->isHosting(self::HOST_WORDPRESSCOM)) {
            return true;
        }

        if ($this->isHosting(self::HOST_GODADDY)) {
            return true;
        }

        if ($this->isHosting(self::HOST_WPENGINE)) {
            return true;
        }

        if ($this->isHosting(self::HOST_CLOUDWAYS)) {
            return true;
        }

        if ($this->isHosting(self::HOST_LIQUIDWEB)) {
            return true;
        }

        if ($this->isHosting(self::HOST_PANTHEON)) {
            return true;
        }

        if ($this->isHosting(self::HOST_FLYWHEEL)) {
            return true;
        }

        return false;
    }

    /**
     * Get hostg object
     *
     * @param string $identifier Host identifier
     *
     * @return GAMB_DUPLICA_WP_Host_interface|false
     */
    public function getHosting($identifier)
    {
        if ($this->isHosting($identifier)) {
            return $this->customHostings[$identifier];
        } else {
            return false;
        }
    }
}
