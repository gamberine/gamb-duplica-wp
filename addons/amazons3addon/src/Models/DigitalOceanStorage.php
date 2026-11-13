<?php

/**
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace gamb-duplica-wp\Addons\AmazonS3Addon\Models;

class DigitalOceanStorage extends AmazonS3CompatibleStorage
{
    /**
     * Return the storage type
     *
     * @return int
     */
    public static function getSType()
    {
        return 14;
    }

    /**
     * Returns the storage type name.
     *
     * @return string
     */
    public static function getStypeName()
    {
        return __('Digital Ocean Spaces', 'gamb-duplica-wp');
    }

    /**
     * Return true if the region is generated automatically
     *
     * @return bool
     */
    public function isAutofillRegion(): bool
    {
        return true;
    }

    /**
     * Return true if the ACL is supported
     *
     * @return bool
     */
    protected function isACLSupported(): bool
    {
        return false;
    }

    /**
     * Returns the storage type icon url.
     *
     * @return string The icon url
     */
    protected static function getIconUrl()
    {
        return gamb-duplica-wp_PRO_IMG_URL . '/digital-ocean.svg';
    }

    /**
     * Get storage location string
     *
     * @return string
     */
    public function getLocationString()
    {
        return 'https://cloud.digitalocean.com/spaces/' . $this->config['bucket'] . $this->config['storage_folder'];
    }

    /**
     * Get documentation links
     *
     * @return array<int,array<string,string>>
     */
    protected static function getDocumentationLinks()
    {
        return [
            [
                'label' => __('Spaces Object Storage', 'gamb-duplica-wp'),
                'url'   => 'https://docs.digitalocean.com/products/spaces/',
            ],
            [
                'label' => __('Spaces API', 'gamb-duplica-wp'),
                'url'   => 'https://docs.digitalocean.com/reference/api/spaces-api/',
            ],
        ];
    }
}
