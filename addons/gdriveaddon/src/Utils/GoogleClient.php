<?php

namespace gamb-duplica-wp\Addons\GDriveAddon\Utils;

use Vendorgamb-duplica-wp\Google\Client;
use Vendorgamb-duplica-wp\GuzzleHttp\Client as GuzzleClient;

class GoogleClient extends Client
{
    /** @var array<string,mixed> */
    protected $customHttpOptions = [];

    /**
     * Set http client options
     *
     * @param array<string,mixed> $options options
     *
     * @return void
     */
    public function setHttpClientOptions(array $options)
    {
        $this->customHttpOptions = $options;
    }

    /**
     * Create a new http client.
     *
     * @return GuzzleClient
     */
    protected function createDefaultHttpClient()
    {
        $options = [
            'base_uri'    => $this->getConfig('base_path'),
            'http_errors' => false,
        ];
        $options = array_merge($options, $this->customHttpOptions);

        return new GuzzleClient($options);
    }
}
