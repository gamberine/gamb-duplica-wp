<?php

/**
 * gamb-duplica-wp messages sections
 *
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
 */
?>
<tr>
    <th scope="row">
        <?php echo esc_html($tplData['fieldLabel']); ?>
    </th>
    <td>
        <fieldset>
            <legend class="screen-reader-text">
                <span><?php echo esc_html($tplData['fieldLabel']); ?></span>
            </legend>
            <label>
                <input 
                    id="<?php echo esc_attr('dup-id-' . $tplData['fieldName']); ?>" 
                    name="<?php echo esc_attr($tplData['fieldName']); ?>" 
                    type="checkbox" 
                    value="1" 
                    <?php checked($tplData['fieldChecked']); ?>
                    >
                    <?php echo esc_html($tplData['fieldCheckboxLabel']); ?>
            </label>
            <?php if (!empty($tplData['fieldDescription'])) { ?>
                <p class="description">
                    <?php echo esc_html($tplData['fieldDescription']); ?>
                </p>
            <?php } ?>
        </fieldset>
    </td>
</tr>
