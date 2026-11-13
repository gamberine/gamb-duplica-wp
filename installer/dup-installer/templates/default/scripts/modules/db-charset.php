<?php

/**
 *
 * @package templates/default
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use gamb-duplica-wp\Installer\Core\Params\Descriptors\ParamDescDatabase;
use gamb-duplica-wp\Installer\Core\Params\PrmMng;
use gamb-duplica-wp\Libs\Snap\SnapJson;

$paramsManager = PrmMng::getInstance();
?>
<script>
    const dbCharsetDefaultID = <?php echo SnapJson::jsonEncode($paramsManager->getFormItemId(PrmMng::PARAM_DB_CHARSET)); ?>;
    const dbCollateDefaultID = <?php echo SnapJson::jsonEncode($paramsManager->getFormItemId(PrmMng::PARAM_DB_COLLATE)); ?>;

    $(document).ready(function ()
    {
        $('#' + dbCharsetDefaultID).on('change', function () {
            let collateDefault = $(this).find(':selected').data('collation-default');
            let collations = $(this).find(':selected').data('collations');
            let collateObj = $('#' + dbCollateDefaultID);

            collateObj.empty();
            $("<option></option>")
                    .appendTo(collateObj)
                    .attr('value', '')
                    .text(<?php echo json_encode(ParamDescDatabase::EMPTY_COLLATION_LABEL); ?> + ' [' + collateDefault + ']')
                    .prop('selected', true);

            for (let i = 0; i < collations.length; i++) {
                let label = collations[i] + (collations[i] === collateDefault ? <?php echo json_encode(ParamDescDatabase::DEFAULT_COLLATE_POSTFIX); ?> : '');
                $("<option></option>")
                        .appendTo(collateObj)
                        .attr('value', collations[i])
                        .text(label);
            }
        });
    });
</script>
