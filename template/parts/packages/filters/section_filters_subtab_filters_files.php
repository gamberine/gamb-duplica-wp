<?php

/**
 * @package gamb-duplica-wp
 */

use GambDuplicaWP\Libs\Snap\SnapIO;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 * @var bool $isTemplateEdit
 */
$isTemplateEdit = $tplData['isTemplateEdit'];
/** @var ?GAMB_DUPLICA_WP_Package_Template_Entity */
$template = ($tplData['template'] ?? null);

if ($isTemplateEdit && $template != null) {
    $componentsParams = [
        'archiveFilterOn'         => $template->archive_filter_on,
        'archiveFilterDirs'       => $template->archive_filter_dirs,
        'archiveFilterFiles'      => $template->archive_filter_files,
        'archiveFilterExtensions' => $template->archive_filter_exts,
        'components'              => $template->components,
    ];
} else {
    $componentsParams = [
        'archiveFilterOn'         => 0,
        'archiveFilterDirs'       => '',
        'archiveFilterFiles'      => '',
        'archiveFilterExtensions' => '',
        'components'              => [],
    ];
}
?>
<div class="filter-files-tab-content">
    <?php $tplMng->render('parts/packages/filters/package_components', $componentsParams); ?>
    <?php $tplMng->render('parts/packages/filters/section_filters_subtab_filters_db'); ?>
</div>
