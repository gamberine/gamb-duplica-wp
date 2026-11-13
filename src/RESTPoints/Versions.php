<?php

/**
 * REST point to get gamb-duplica-wp and WordPress versions
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace GambDuplicaWP\RESTPoints;

use GambDuplicaWP\Core\CapMng;

class Versions extends \GambDuplicaWP\Core\REST\AbstractRESTPoint
{
    /**
     * return REST point route string
     *
     * @return string
     */
    protected function getRoute(): string
    {
        return '/versions';
    }

    /**
     *
     * @param \WP_REST_Request $request      request data
     * @param mixed[]          $responseBase response base data
     *
     * @return \WP_REST_Response
     */
    protected function respond(\WP_REST_Request $request, $responseBase) // @phpstan-ignore missingType.generics
    {
        global $wp_version;

        $response = $responseBase;

        $response['wp']  = $wp_version;
        $response['dup'] = GambDuplicaWP_PRO_VERSION;

        return new \WP_REST_Response($response, 200);
    }

    /**
     *
     * @param \WP_REST_Request $request request data
     *
     * @return \WP_Error|boolean
     */
    public function permission(\WP_REST_Request $request) // @phpstan-ignore missingType.generics
    {
        $nonce = $request->get_header('X-WP-Nonce') ?: $request->get_param('_wpnonce');
        if (!CapMng::can(CapMng::CAP_BASIC, false) || !wp_verify_nonce((string) $nonce, 'wp_rest')) {
            return new \WP_Error('rest_forbidden', esc_html__('You cannot execute this action.', 'gamb-duplica-wp'), ['status' => is_user_logged_in() ? 403 : 401]);
        }
        return true;
    }
}
