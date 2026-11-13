<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

namespace GambDuplicaWP\Ajax;

use GAMB_DUPLICA_WP_Handler;
use GambDuplicaWP\Addons\ProBase\License\License;
use GambDuplicaWP\Core\CapMng;
use GambDuplicaWP\Libs\Snap\SnapJson;
use GambDuplicaWP\Models\BrandEntity;
use Exception;

class ServicesBrand extends AbstractAjaxService
{
    /**
     * Init ajax calls
     *
     * @return void
     */
    public function init()
    {
        if (!License::can(License::CAPABILITY_PRO_BASE)) {
            return;
        }

        $this->addAjaxCall('wp_ajax_gamb-duplica-wp_pro_brand_delete', 'brandDelete');
    }

    /**
     * Hook ajax wp_ajax_gamb-duplica-wp_pro_brand_delete
     *
     * @return never
     */
    public function brandDelete()
    {
        GAMB_DUPLICA_WP_Handler::init_error_handler();
        check_ajax_referer('gamb-duplica-wp_pro_brand_delete', 'nonce');

        $json      = [
            'success' => false,
            'message' => '',
        ];
        $isValid   = true;
        $inputData = filter_input_array(INPUT_POST, [
            'brand_ids' => [
                'filter'  => FILTER_VALIDATE_INT,
                'flags'   => FILTER_REQUIRE_ARRAY,
                'options' => ['default' => false],
            ],
        ]);
        $brandIDs  = $inputData['brand_ids'];
        $delCount  = 0;

        if (empty($brandIDs) || in_array(false, $brandIDs)) {
            $isValid = false;
        }

        try {
            CapMng::can(CapMng::CAP_CREATE);
            if (!$isValid) {
                throw new Exception(__('Invalid Request.', 'gamb-duplica-wp'));
            }

            foreach ($brandIDs as $id) {
                $brand = BrandEntity::deleteById($id);
                if ($brand) {
                    $delCount++;
                }
            }

            $json['success'] = true;
            $json['ids']     = $brandIDs;
            $json['removed'] = $delCount;
        } catch (Exception $e) {
            $json['message'] = $e->getMessage();
        }

        wp_send_json($json);
    }
}
