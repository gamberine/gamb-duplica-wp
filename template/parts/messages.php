<?php

/**
 * gamb-duplica-wp messages sections
 *
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use GambDuplicaWP\Views\AdminNotices;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */
?>
<div class="dup-messages-section" >
    <?php
    if (isset($tplData['errorMessage']) && strlen($tplData['errorMessage']) > 0) {
        AdminNotices::displayGeneralAdminNotice(
            $tplData['errorMessage'],
            AdminNotices::GEN_ERROR_NOTICE,
            true
        );
    }

    if (GambDuplicaWP_PRO_DEBUG_TPL_OUTPUT_INVALID && isset($tplData['invalidOutput']) && strlen($tplData['invalidOutput']) > 0) { // @phpstan-ignore-line
        AdminNotices::displayGeneralAdminNotice(
            '<b>Invalid output on actions execution</b><hr>' . $tplData['invalidOutput'],
            AdminNotices::GEN_ERROR_NOTICE,
            true
        );
    }

    if (isset($tplData['successMessage']) && strlen($tplData['successMessage']) > 0) {
        AdminNotices::displayGeneralAdminNotice(
            $tplData['successMessage'],
            AdminNotices::GEN_SUCCESS_NOTICE,
            true
        );
    }
    ?>
</div>
<?php
if (GambDuplicaWP_PRO_DEBUG_TPL_DATA) { // @phpstan-ignore-line
    ?>
    <pre style="font-size: 12px; max-height: 300px; overflow: auto; border: 1px solid black; padding: 10px;"><?php
        var_dump($tplData);
    ?></pre>
    <?php
}
