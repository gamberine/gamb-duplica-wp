<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 * @var bool $blur
 */

$blur = $tplData['blur'];

if ($tplData['adminMessageViewModeSwtich'] && !$blur) {
    $tplMng->render('admin_pages/import/step1/message-view-mode-switch');
}

?>

<div class="dup-pro-import-header" >
    <h2 class="title">
        <b>
            <?php printf(esc_html__("Step %s of 2: Upload Backup", 'gamb-duplica-wp'), '<span class="red">1</span>'); ?>
        </b>
    </h2>
</div>

<div class="dup-import-header-content-wrapper <?php echo ($blur ? 'dup-mock-blur' : ''); ?>" >
    <?php $tplMng->render('admin_pages/import/step1/add-file-area'); ?>
    <?php $tplMng->render('admin_pages/import/step1/packages-list'); ?>
</div>


