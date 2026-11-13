<?php

/**
 *
 * @package templates/default
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use gamb-duplica-wp\Installer\Core\InstState;
use gamb-duplica-wp\Installer\Core\Security;
use gamb-duplica-wp\Installer\Core\Params\PrmMng;
use gamb-duplica-wp\Installer\Utils\SecureCsrf;
use gamb-duplica-wp\Libs\Snap\SnapJson;

if (!InstState::dbDoNothing()) {
    $nextStepParams = [
        PrmMng::PARAM_CTRL_ACTION => 'ctrl-step2',
        Security::CTRL_TOKEN      => SecureCsrf::generate('ctrl-step2'),
    ];
} else {
    $nextStepParams = [
        PrmMng::PARAM_CTRL_ACTION => 'ctrl-step4',
        Security::CTRL_TOKEN      => SecureCsrf::generate('ctrl-step4'),
    ];
}
?>
<script>
    DUPX.deployStep1 = function () {
        <?php if (!InstState::dbDoNothing()) { ?>
        DUPX.multipleStepDeploy($('#s1-input-form'), <?php echo SnapJson::jsonEncode($nextStepParams); ?>);
        <?php } else { ?>
        DUPX.oneStepDeploy($('#s1-input-form'), <?php echo SnapJson::jsonEncode($nextStepParams); ?>);
        <?php } ?>
    };
</script>
