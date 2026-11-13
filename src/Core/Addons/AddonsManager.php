<?php

/**
 * Class that collects the functions of initial checks on the requirements to run the plugin
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace GambDuplicaWP\Core\Addons;

final class AddonsManager
{
    /** @var ?self */
    private static $instance;
    /** @var AbstractAddonCore[] */
    private $addons = [];
    /** @var AbstractAddonCore[] */
    private $enabledAddons = [];
    /** @var object */
    private $check;

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
        $this->addons = self::getAddonListFromFolder();
        $checkString  = pack("H*", \GambDuplicaWP\Core\Bootstrap::getAddsHash());
        $this->check  = json_decode($checkString);
    }

    /**
     *
     * @return void
     */
    public function inizializeAddons()
    {
        if (!is_array($this->check->r) || !is_array($this->check->fd)) {
            throw new \Exception('Addons initialization error');
        }

        foreach ($this->addons as $addon) {
            if (!in_array($addon->getSlug(), $this->check->fd) && $addon->canEnable() && $addon->hasDependencies()) {
                $this->enabledAddons[$addon->getSlug()] = $addon;
                $addon->init();
            }
        }

        do_action('gamb-duplica-wp_addons_loaded');
    }

    /**
     *
     * @return boolean
     */
    public function isAddonsReady()
    {
        return (count(array_diff($this->check->r, array_keys($this->enabledAddons))) === 0);
    }

    /**
     * Get list of availables addons
     *
     * @return string[]
     */
    public function getAvailableAddons(): array
    {
        $result = [];
        foreach ($this->addons as $addon) {
            $result[] = $addon->getSlug();
        }

        return $result;
    }

    /**
     *
     * @return AbstractAddonCore[]
     */
    public function getEnabledAddons()
    {
        return $this->enabledAddons;
    }

    /**
     * Returns true if the addon is enabled
     *
     * @param string $slug addon slug
     *
     * @return boolean
     */
    public function isAddonEnabled($slug)
    {
        return isset($this->enabledAddons[$slug]);
    }

    /**
     * return addons folder
     *
     * @return string
     */
    public static function getAddonsPath()
    {
        return GambDuplicaWP____PATH . '/addons';
    }

    /**
     *
     * @return AbstractAddonCore[]
     */
    private static function getAddonListFromFolder(): array
    {
        $addonList = [];

        $checkDir = trailingslashit(self::getAddonsPath());

        if (!is_dir($checkDir)) {
            return [];
        }

        if (($dh = opendir($checkDir)) == false) {
            return [];
        }

        while (($elem = readdir($dh)) !== false) {
            if ($elem === '.' || $elem === '..') {
                continue;
            }

            $fullPath = $checkDir . $elem;

            if (!is_dir($fullPath)) {
                continue;
            }

            $addonMainFile  = false;
            $addonMainClass = '';

            if (($addonDh       = opendir($fullPath)) == false) {
                continue;
            }

            while (($addonElem = readdir($addonDh)) !== false) {
                if ($addonElem === '.' || $addonElem === '..') {
                    continue;
                }
                $info = pathinfo($fullPath . '/' . $addonElem);

                if (strcasecmp($elem, $info['filename']) === 0) {
                    $addonMainFile  = $checkDir . $elem . '/' . $addonElem;
                    $addonMainClass = '\\GambDuplicaWP\\Addons\\' . $info['filename'] . '\\' . $info['filename'];
                    break;
                }
            }

            if (empty($addonMainFile)) {
                continue;
            }

            try {
                if (!is_subclass_of($addonMainClass, AbstractAddonCore::class)) {
                    \GAMB_DUPLICA_WP_Log::trace(
                        'Addon main file ' . $addonMainFile . ' don\'t contain a main class ' .
                        $addonMainClass . 'that extend AbstractAddonCore'
                    );
                    continue;
                }
            } catch (\Exception $e) {
                \GAMB_DUPLICA_WP_Log::trace('Addon file ' . $addonMainFile . ' exists but not countain addon main core class, Exception: ' . $e->getMessage());
                continue;
            } catch (\Error $e) {
                \GAMB_DUPLICA_WP_Log::trace('Addon file ' . $addonMainFile . ' exists but generate an error, Exception: ' . $e->getMessage());
                continue;
            }

            $addonObj                        = $addonMainClass::getInstance();
            $addonList[$addonObj->getSlug()] = $addonObj;
        }
        closedir($dh);

        return $addonList;
    }
}
