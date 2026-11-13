<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

defined("ABSPATH") or die("");

?>
<p>
    <?php esc_html_e('The brandable area allows for a loose set of html and custom styling.  Below is a general guide.', 'gamb-duplica-wp'); ?>
</p>
<p>
    - <b><?php esc_html_e('Embed Image:', 'gamb-duplica-wp'); ?></b><br/> &lt;img src="/wp-content/uploads/image.png /&gt;<br/><br/>
    - <b><?php esc_html_e('Text Only:', 'gamb-duplica-wp'); ?></b><br/> <?php esc_html_e('My Installer Name', 'gamb-duplica-wp'); ?><br/><br/>
    - <b><?php esc_html_e('Text & Font-Awesome:', 'gamb-duplica-wp'); ?></b><br/> &lt;i class="fa fa-cube"&gt;&lt;/i&gt;
    <?php esc_html_e('My Company', 'gamb-duplica-wp'); ?>
</p>
<p>
<small>
<?php
wp_kses(
    sprintf(
        _x(
            'Note: %1$sFont-Awesome 4.7%2$s is the referenced library',
            '1: opening anchor tag, 2: closing anchor tag',
            'gamb-duplica-wp'
        ),
        "<a href='http://fontawesome.io/icons/' target='_blank'>",
        "</a>"
    ),
    ['a' => ['href' => []]]
);
?>
</small>
</p>
