<?php

/**
 *
 * @package templates/default
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use GambDuplicaWP\Installer\Core\Security;
use GambDuplicaWP\Installer\Core\Params\PrmMng;
use GambDuplicaWP\Installer\Utils\SecureCsrf;
use GambDuplicaWP\Libs\Snap\SnapJson;

$nextStepPrams = [
    PrmMng::PARAM_CTRL_ACTION => 'ctrl-step4',
    Security::CTRL_TOKEN      => SecureCsrf::generate('ctrl-step4'),
];
?>
<script>
    DUPX.deployStep1 = function () {
        DUPX.oneStepDeploy($('#s1-input-form'), <?php echo SnapJson::jsonEncode($nextStepPrams); ?>);
    };
</script>
