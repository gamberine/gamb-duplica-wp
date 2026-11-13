<?php

/**
 * @package   gamb-duplica-wp
 * @copyright (c) 2022, Snap Creek LLC
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/**
 * Variables
 *
 * @var string $currentSkipMode
 */
?>
<div 
    class="dynamic-sub-note dynamic-sub-note-<?php echo GAMB_DUPLICA_WP_Extraction::FILTER_NONE .
    ($currentSkipMode == GAMB_DUPLICA_WP_Extraction::FILTER_NONE ? '' : ' no-display'); ?>"
>
    All Files in the archive are going to be extracted.
</div>
<div 
    class="dynamic-sub-note dynamic-sub-note-<?php echo GAMB_DUPLICA_WP_Extraction::FILTER_SKIP_WP_CORE .
    ($currentSkipMode == GAMB_DUPLICA_WP_Extraction::FILTER_SKIP_WP_CORE ? '' : ' no-display'); ?>"
>
    When this option is chosen the WordPress core files, if any, are not modified. They are not deleted and/or extracted.
</div>
<div 
    class="dynamic-sub-note dynamic-sub-note-<?php echo GAMB_DUPLICA_WP_Extraction::FILTER_SKIP_CORE_PLUG_THEMES .
        ($currentSkipMode == GAMB_DUPLICA_WP_Extraction::FILTER_SKIP_CORE_PLUG_THEMES ? '' : ' no-display'); ?>"
>
    When this option is chosen the WordPress core files, if any, are not modified. They are not deleted and/or extracted. </br>
    Also, if a plugin (theme) exists on BOTH the host and and the archive, the contents of the host plugin (theme) are going to be kept.
</div>
<div 
    class="dynamic-sub-note dynamic-sub-note-<?php echo GAMB_DUPLICA_WP_Extraction::FILTER_ONLY_MEDIA_PLUG_THEMES .
        ($currentSkipMode == GAMB_DUPLICA_WP_Extraction::FILTER_ONLY_MEDIA_PLUG_THEMES ? '' : ' no-display'); ?>"
>
    When this option is chosen only the "uploads" folder and plugins (themes) that don't exist on the host are going to be extracted.
</div>
