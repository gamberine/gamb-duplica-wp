<?php
namespace GambDuplicaWP;
defined('ABSPATH') || exit;

/** Políticas da distribuição independente, sem serviços comerciais configurados. */
final class ProjectPolicy
{
    public static function register()
    {
        add_filter('pre_http_request', [self::class, 'guardUnconfiguredServices'], 10, 3);
        add_action('admin_notices', [self::class, 'zipNotice']);
        add_action('admin_enqueue_scripts', [self::class, 'adminAssets'], 100);
    }

    public static function adminAssets($hook)
    {
        if (strpos((string) $hook, 'gamb-duplica-wp') !== false) {
            wp_enqueue_style('gamb-duplica-wp-project', GambDuplicaWP_PRO_PLUGIN_URL . 'assets/css/project.css', ['gamb-duplica-wp-main'], GambDuplicaWP_PRO_VERSION);
        }
    }

    public static function guardUnconfiguredServices($response, $arguments, $url)
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        if ($host === 'gamb-duplica-wp.com' || substr($host, -strlen('.gamb-duplica-wp.com')) === '.gamb-duplica-wp.com') {
            return new \WP_Error('gamb_service_unconfigured', 'Este serviço remoto não está configurado para a distribuição Gamb Duplica WP.');
        }
        return $response;
    }

    public static function zipNotice()
    {
        if (!current_user_can('manage_options') || class_exists('ZipArchive')) { return; }
        $page = isset($_GET['page']) ? sanitize_key(wp_unslash($_GET['page'])) : '';
        if (strpos($page, 'gamb-duplica-wp') !== 0) { return; }
        if (\GambDuplicaWP\Utils\ZipArchiveExtended::isPhpZipAvailable()) { echo '<div class="notice notice-info"><p>'.esc_html('Gamb Duplica WP: ZIP portátil disponível pelo PclZip do WordPress, sem habilitar ZipArchive. AES e ZIP64 continuam exigindo a extensão nativa; DAF é um formato distinto de ZIP.').'</p></div>'; return; }
        echo '<div class="notice notice-warning"><p>' . esc_html('Gamb Duplica WP: a extensão ZIP do PHP está desabilitada. Para gerar arquivos .zip pelo mecanismo PHP, habilite ZipArchive no servidor. O formato .daf é um formato próprio de recuperação e não é um arquivo ZIP.') . '</p></div>';
    }
}
