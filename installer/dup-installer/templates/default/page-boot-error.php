<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/**
 * Variables
 *
 * @var string $message
 * @var string $trace
 */
?>
<!DOCTYPE html>
<html>
    <?php dupxTplRender('pages-parts/boot-error/header'); ?>
    <body id="page-boot-error">
        <div>
            <h1>gamb-duplica-wp PRO: ISSUE</h1>
            Problem on gamb-duplica-wp init.<br>
            Message: <b><?php echo htmlspecialchars($message); ?></b>
        </div>
        <pre><?php
            echo $trace;
        ?></pre>
    </body>
</html>
