<?php

/**
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace GambDuplicaWP\Addons\AmazonS3Addon\Models;

class WasabiStorage extends AmazonS3CompatibleStorage
{
    /**
     * Return the storage type
     *
     * @return int
     */
    public static function getSType()
    {
        return 10;
    }

    /**
     * Returns the storage type name.
     *
     * @return string
     */
    public static function getStypeName()
    {
        return __('Wasabi', 'gamb-duplica-wp');
    }

    /**
     * Returns the storage type icon url.
     *
     * @return string The icon url
     */
    protected static function getIconUrl()
    {
        return GambDuplicaWP_PRO_IMG_URL . '/wasabi.svg';
    }

    /**
     * Return true if the endpoint is generated automatically
     *
     * @return bool
     */
    public function isAutofillEndpoint(): bool
    {
        return true;
    }

    /**
     * Get storage location string
     *
     * @return string
     */
    public function getLocationURL(): string
    {
        return 'https://console.wasabisys.com/file_manager/';
    }

    /**
     * Returns the storage location label.
     *
     * @return string The storage location label
     */
    protected function getLocationLabel()
    {
        return __('Bucket List', 'gamb-duplica-wp');
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
                'label' => __('Wasabi Academy', 'gamb-duplica-wp'),
                'url'   => 'https://docs.wasabi.com/',
            ],
            [
                'label' => __('S3 Compatible API', 'gamb-duplica-wp'),
                'url'   => 'https://docs.wasabi.com/docs/wasabi-api',
            ],
        ];
    }
}
