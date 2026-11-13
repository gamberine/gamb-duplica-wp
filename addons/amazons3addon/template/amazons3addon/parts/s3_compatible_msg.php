<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

use GambDuplicaWP\Models\Storages\AbstractStorageEntity;
use GambDuplicaWP\Addons\AmazonS3Addon\Models\AmazonS3CompatibleStorage;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 * @var AbstractStorageEntity $storage
 */
$storage = $tplData["storage"];
?>

<p> 
    <?php
    esc_html_e(
        'The Amazon S3 compatible storage option allows you to connect to any object storage that is compatible with the S3 API.',
        'gamb-duplica-wp'
    );
    ?>
</p>
<p>
    <?php
    printf(
        esc_attr_x(
            'Examples of compatible providers are %s.',
            '%s is a comma seperated list of providers',
            'gamb-duplica-wp'
        ),
        '<b>' . esc_html(implode(', ', AmazonS3CompatibleStorage::getCompatibleProviders())) . '</b>'
    );
    ?>
</p>
