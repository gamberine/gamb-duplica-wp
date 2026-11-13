<?php

/**
 * @package gamb-duplica-wp
 */

use gamb-duplica-wp\Installer\Package\ArchiveDescriptor;
use gamb-duplica-wp\Package\SettingsUtils;

defined("ABSPATH") or die("");

/**
 * Variables
 *
 * @var \gamb-duplica-wp\Core\Controllers\ControllersManager $ctrlMng
 * @var \gamb-duplica-wp\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

$secureOn   = ($tplData['secureOn'] ?? ArchiveDescriptor::SECURE_MODE_NONE);
$securePass = ($tplData['securePass'] ?? '');

$unavaliableMessage = '';
$encryptAvaliable   = SettingsUtils::isArchiveEncryptionAvailable($unavaliableMessage);

?>
<div class="archive-setup-tab" >
    <div class="dup-package-hdr-1">
        <?php esc_html_e('Security', 'gamb-duplica-wp'); ?>
    </div>

    <div class="dup-form-item margin-bottom-1">
        <label class="lbl-larger" >
            <?php esc_html_e('Mode', 'gamb-duplica-wp') ?>:&nbsp;
            <i class="fa-solid fa-question-circle fa-sm dark-gray-color"
                data-tooltip-title="<?php esc_attr_e('Security', 'gamb-duplica-wp'); ?>"
                data-tooltip="<?php $tplMng->renderEscAttr('admin_pages/packages/setup/security-tooltip-content'); ?>">
            </i>
        </label>
        <div class="input">
            <span class="secure-on-input-wrapper">
                <label class="inline-display margin-right-1" >
                    <input 
                        type="radio" 
                        name="secure-on" 
                        class="margin-0"
                        id="secure-on-none" 
                        onclick="DupPro.EnableInstallerPassword()" 
                        required
                        value="<?php echo (int) ArchiveDescriptor::SECURE_MODE_NONE; ?>"
                        <?php checked($secureOn, ArchiveDescriptor::SECURE_MODE_NONE); ?>
                        data-parsley-multiple="secure-on-mltiple-error"
                        data-parsley-errors-container="#secure-on-parsely-error"
                    >
                    <?php esc_html_e('None', 'gamb-duplica-wp') ?>
                </label>
                <label class="inline-display margin-right-1" >
                    <input 
                        type="radio" 
                        name="secure-on" 
                        class="margin-0"
                        id="secure-on-inst-pwd" 
                        value="<?php echo (int) ArchiveDescriptor::SECURE_MODE_INST_PWD; ?>"
                        <?php checked($secureOn, ArchiveDescriptor::SECURE_MODE_INST_PWD); ?>
                        onclick="DupPro.EnableInstallerPassword()" 
                        data-parsley-multiple="secure-on-mltiple-error"
                    >
                    <?php esc_html_e('Installer password', 'gamb-duplica-wp') ?>
                </label>
                <label class="inline-display" >
                    <input 
                        type="radio" 
                        name="secure-on" 
                        class="margin-0"
                        id="secure-on-arc-encrypt" 
                        value="<?php echo (int) ArchiveDescriptor::SECURE_MODE_ARC_ENCRYPT; ?>"
                        <?php echo ($encryptAvaliable ? checked($secureOn, ArchiveDescriptor::SECURE_MODE_ARC_ENCRYPT, false) : ''); ?>
                        onclick="DupPro.EnableInstallerPassword()" 
                        <?php disabled(!$encryptAvaliable); ?>
                        data-parsley-multiple="secure-on-mltiple-error"
                    >
                    <?php esc_html_e('Archive encryption', 'gamb-duplica-wp') ?>
                </label>
            </span>
            <div id="secure-on-parsely-error"></div>
        </div>
    </div>
    <div class="dup-form-item">
        <label class="lbl-larger" >
            <?php esc_html_e('Password', 'gamb-duplica-wp') ?>:
        </label>
        <div class="input">
            <span class="dup-password-toggle width-xlarge"> 
                <input 
                    id="secure-pass" 
                    type="password" 
                    name="secure-pass" 
                    required="required"
                    size="50"
                    maxlength="150"
                    value="<?php echo esc_attr($securePass); ?>" 
                >
                <button type="button" >
                    <i class="fas fa-eye fa-sm"></i>
                </button>
            </span>
            <div class="input dup-tabs-opts-help-secure-pass">
                <?php
                    esc_html_e(
                        'Caution: Passwords are case-sensitive and if lost cannot be recovered.  Please keep passwords in a safe place!',
                        'gamb-duplica-wp'
                    );
                    echo '<br/>';
                    esc_html_e(
                        'If this password is lost then a new archive file will need to be created.',
                        'gamb-duplica-wp'
                    );
                    ?>
            </div>
        </div>
    </div>

    <?php if (!$encryptAvaliable) { ?>
        <div class="dup-form-item">
            <span class="title">
                &nbsp;
            </span>
            <span class="input dup-tabs-opts-notice">
                <i>
                <i class="fas fa-exclamation-triangle fa-xs"></i>
                <?php
                    echo esc_html__("The security mode 'Archive encryption' option above is currently disabled on this server.", 'gamb-duplica-wp') . '<br>'
                        . esc_html($unavaliableMessage);
                ?>
                </i>
            </span>
        </div>
    <?php } ?>

</div>

<script>
    jQuery(function($) {
        DupPro.EnableInstallerPassword = function () {
            let $button = $('#secure-btn');
            let secureOnVal = $('.secure-on-input-wrapper input:checked').val();

            if (secureOnVal == <?php echo json_encode(ArchiveDescriptor::SECURE_MODE_NONE); ?>) {
                $('#dpro-install-secure-lock-icon').hide();
                $('#secure-pass').removeAttr('required');
                $('#secure-pass').attr('readonly', true);
                $button.prop('disabled', true);
            } else {
                $('#dpro-install-secure-lock-icon').show();
                $('#secure-pass').attr('readonly', false);
                $('#secure-pass').attr('required', 'true').focus();
                $button.prop('disabled', false); 
            }
        };

        $('#secure-on-none').parsley().on('field:error', function() {
            $('.archive-setup-tab button').trigger('click');
            $('html,body').animate({scrollTop: $(".archive-setup-tab").offset().top - 30},'slow');
        });
    });
</script>
