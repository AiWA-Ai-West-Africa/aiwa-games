<?php
namespace AiWAMandinkaGames;
/**
 * AiWA Mandinka Games
 *
 * @package           AiWAMandinkaGames
 * @author            Starisian Technologies (Max Barrett)
 * @copyright         Copyright © 2025 Ai West Africa (AiWA). All rights reserved.
 * @license           Proprietary
 *
 * @wordpress-plugin
 * Plugin Name:       AiWA Mandinka Games
 * Plugin URI:        https://github.com/AiWA-Ai-West-Africa/mandinka-games/
 * Description:       A flashcard quiz for learning Mandinka, implemented as a shortcode.
 * Version:           1.1.1
 * Requires at least: 6.4
 * Requires PHP:      8.2
 * Author:            Starisian Technologies (Max Barrett)
 * Author URI:        https://starisian.com/
 * License:           Proprietary
 * License URI:       https://github.com/AiWA-Ai-West-Africa/mandinka-games/LICENSE.md
 * Update URI:        https://github.com/AiWA-Ai-West-Africa/mandinka-games/
 * Text Domain:       mandinka-games
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( version_compare( PHP_VERSION, '8.2', '<' ) ) {
    add_action(
        'admin_notices',
        static function () {
            echo '<div class="notice notice-error"><p>' . esc_html__( 'AiWA Mandinka Games requires PHP 8.2 or higher.', 'mandinka-games' ) . '</p></div>';
        }
    );
    return;
}

define( 'MANDINKA_GAMES_PATH', plugin_dir_path( __FILE__ ) );
define( 'MANDINKA_GAMES_URL', plugin_dir_url( __FILE__ ) );
define( 'MANDINKA_GAMES_VERSION', '1.1.1' );

// load PSR-4 autoloader if available
// require_once __DIR__ . '/vendor/autoload.php';

final class MandinkaGames {
    /**
     * Absolute path to the plugin directory.
     *
     * @var string
     */
    private string $pluginPath;

    /**
     * Base URL to the plugin directory.
     *
     * @var string
     */
    private string $pluginUrl;

    /**
     * Current plugin version.
     *
     * @var string
     */
    private string $version;

    /**
     * Plugin singleton instance.
     *
     * @var MandinkaGames|null
     */
    private static ?MandinkaGames $instance = null;

    /**
     * Flashcards feature instance.
     *
     * @var \AiWAMandinkaGames\src\games\MandinkaFlashcards|null
     */
    public $flashcards = null;

    /**
     * Class constructor.
     */
    public function __construct() {
        $this->pluginPath = MANDINKA_GAMES_PATH;
        $this->pluginUrl  = MANDINKA_GAMES_URL;
        $this->version    = MANDINKA_GAMES_VERSION;

        $this->loadDependencies();

        if ( class_exists( 'AiWAMandinkaGames\\src\\games\\MandinkaFlashcards' ) ) {
            $this->flashcards = new \AiWAMandinkaGames\src\games\MandinkaFlashcards( $this->pluginPath, $this->pluginUrl, $this->version );
            $this->flashcards->init();
        }
    }

    /**
     * Retrieves the plugin instance.
     */
    public static function getInstance(): MandinkaGames {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Loads required class files when not using Composer.
     */
    private function loadDependencies(): void {
        $file = $this->pluginPath . 'src/games/MandinkaFlashcards.php';
        if ( file_exists( $file ) ) {
            require_once $file;
        }
    }

    /**
     * Bootstraps the plugin instance.
     */
    public static function mnkg_run(): void {
        $globalKey = __NAMESPACE__ . '\\MandinkaGames';
        if ( ! isset( $GLOBALS[ $globalKey ] ) || ! $GLOBALS[ $globalKey ] instanceof self ) {
            $GLOBALS[ $globalKey ] = self::getInstance();
        }
    }

    /**
     * Handles plugin activation tasks.
     */
    public static function mnkgActivate(): void {
        // No need to include db-setup.php here directly if DatabaseManager handles it.
        // if ( class_exists( 'MandinkaGames\src\core\DatabaseManager' ) ) {
        //     DatabaseManager::createTables();
        // }
        flush_rewrite_rules();
    }

    /**
     * Handles plugin deactivation tasks.
     */
    public static function mnkgDeactivate(): void {
        // if ( class_exists( 'MandinkaGames\src\core\DatabaseManager' ) ) {
        //     DatabaseManager::handleDeactivation();
        // }
        flush_rewrite_rules();
    }

    /**
     * Handles plugin uninstallation cleanup.
     */
    public static function mnkgUninstall(): void {
        // if ( class_exists( 'MandinkaGames\src\core\DatabaseManager' ) ) {
        //     DatabaseManager::handleUninstall();
        // }
        // Also delete plugin options from wp_options table
    }
}

// Activation, Deactivation, Uninstall hooks remain the same, calling the static methods:
register_activation_hook( __FILE__, array( 'AiWAMandinkaGames\MandinkaGames', 'mnkgActivate' ) );
register_deactivation_hook( __FILE__, array( 'AiWAMandinkaGames\MandinkaGames', 'mnkgDeactivate' ) );
// For uninstall, you'd typically check if the file exists before trying to call a method from it.
// A common pattern is to have a separate uninstall.php file in the plugin root.
// If using register_uninstall_hook:
register_uninstall_hook( __FILE__, array( 'AiWAMandinkaGames\MandinkaGames', 'mnkgUninstall' ) );

// Run the plugin
add_action( 'plugins_loaded', function () {
    \AiWAMandinkaGames\MandinkaGames::mnkg_run();
});
