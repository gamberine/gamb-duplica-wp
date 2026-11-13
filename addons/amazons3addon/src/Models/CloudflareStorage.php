<?php

/**
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace GambDuplicaWP\Addons\AmazonS3Addon\Models;

class CloudflareStorage extends AmazonS3CompatibleStorage
{
    /**
     * Return the storage type
     *
     * @return int
     */
    public static function getSType()
    {
        return 11;
    }

    /**
     * Returns the storage type name.
     *
     * @return string
     */
    public static function getStypeName()
    {
        return __('Cloudflare R2', 'gamb-duplica-wp');
    }

    /**
     * Get storage location string
     *
     * @return string
     */
    public function getLocationString(): string
    {
        return 'https://dash.cloudflare.com/';
    }

    /**
     * Returns the storage location label.
     *
     * @return string The storage location label
     */
    protected function getLocationLabel()
    {
        return __('Dashboard', 'gamb-duplica-wp');
    }

    /**
     * Returns the storage type icon url.
     *
     * @return string The icon url
     */
    protected static function getIconUrl()
    {
        return GambDuplicaWP_PRO_IMG_URL . '/cloudflare.svg';
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
     * Get documentation links
     *
     * @return array<int,array<string,string>>
     */
    protected static function getDocumentationLinks()
    {
        return [
            [
                'label' => __('Overview', 'gamb-duplica-wp'),
                'url'   => 'https://developers.cloudflare.com/r2/',
            ],
            [
                'label' => __('S3 Compatible API', 'gamb-duplica-wp'),
                'url'   => 'https://developers.cloudflare.com/r2/api/s3/api/',
            ],
        ];
    }
}
