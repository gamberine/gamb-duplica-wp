<?php

defined("ABSPATH") or die("");

/**
 * Settings > Packages, shell zip message
 *
 * Variables
 *
 * @var gamb-duplica-wp\Core\Views\TplMng $tplMng
 * @var array<string, mixed> $tplData
 * @var bool $hasShellZip
 */
$hasShellZip = $tplData['hasShellZip'];

if ($hasShellZip) {
    esc_html_e('The "Shell Zip" mode allows gamb-duplica-wp to use the server\'s internal zip command.', 'gamb-duplica-wp');
    ?>
<br/>
    <?php
    esc_html_e('When available this mode is recommended over the PHP "ZipArchive" mode.', 'gamb-duplica-wp');
} else {
    $scanPath = GAMB_DUPLICA_WP_Archive::getScanPaths();
    if (count($scanPath) > 1) {
        ?>

<i style='color:maroon'>
        <i class='fa fa-exclamation-triangle'></i>
        <?php esc_html_e("This server is not configured for the Shell Zip engine - please use a different engine mode.", 'gamb-duplica-wp'); ?>
</i>
    <?php } else { ?>
<i style='color:maroon'>
        <i class='fa fa-exclamation-triangle'></i>
        <?php esc_html_e("This server is not configured for the Shell Zip engine - please use a different engine mode.", 'gamb-duplica-wp'); ?>
        <br/>
        <?php
        printf(
            esc_html_x(
                'Shell Zip is %1$srecommended%2$s when available. ',
                '%1$s and %2$s are html anchor tags or link',
                'gamb-duplica-wp'
            ),
            '<a href="' . esc_url(gamb-duplica-wp_PRO_gamb-duplica-wp_DOCS_URL . 'how-to-work-with-the-different-zip-engines') . '" target="_blank">',
            '</a> '
        );
        printf(
            esc_html_x(
                'For a list of supported hosting providers %1$sclick here%2$s.',
                '%1$s and %2$s are html anchor tags or link',
                'gamb-duplica-wp'
            ),
            '<a href="' . esc_url(gamb-duplica-wp_PRO_gamb-duplica-wp_DOCS_URL . 'what-host-providers-are-recommended-for-gamb-duplica-wp/') . '" target="_blank">',
            '</a> '
        );
        ?>
</i>
        <?php
        // Show possible solutions for some linux setups
        $problem_fixes = GAMB_DUPLICA_WP_Zip_U::getShellExecZipProblems();
        if (count($problem_fixes) > 0 && ((strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN'))) {
            $shell_tooltip  = ' ';
            $shell_tooltip .= __("To make 'Shell Zip' available, ask your host to:", 'gamb-duplica-wp');
            echo '<br/>';
            $i = 1;
            foreach ($problem_fixes as $problem_fix) {
                $shell_tooltip .= "{$i}. {$problem_fix->fix}<br/>";
                $i++;
            }
            $shell_tooltip .= '<br/>';
            echo wp_kses($shell_tooltip, ['br' => []]);
        }
    }
}
?>
