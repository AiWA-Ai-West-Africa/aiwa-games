<?php
namespace AiWAMandinkaGames\src\games;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handles front-end Mandinka flashcard functionality.
 */
class MandinkaFlashcards {

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
     * Style handle for flashcard assets.
     *
     * @var string
     */
    private string $styleHandle = 'mnkgFlashcardsStyle';

    /**
     * Script handle for flashcard assets.
     *
     * @var string
     */
    private string $scriptHandle = 'mnkgFlashcardsScript';

    /**
     * Constructor.
     */
    public function __construct( string $pluginPath, string $pluginUrl, string $version ) {
        $this->pluginPath = $pluginPath;
        $this->pluginUrl  = $pluginUrl;
        $this->version    = $version;
    }

    /**
     * Initializes hooks for the flashcard feature.
     */
    public function init(): void {
        $this->registerHooks();
    }

    /**
     * Registers WordPress hooks.
     */
    private function registerHooks(): void {
        add_shortcode( 'mandinka_flashcards', array( $this, 'renderFlashcardQuizShortcode' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'registerAndLocalizeAssets' ) );
    }

    /**
     * Registers scripts/styles and localizes data.
     */
    public function registerAndLocalizeAssets(): void {
        wp_register_style(
            $this->styleHandle,
            $this->pluginUrl . 'assets/css/mandinka-flashcards-styles.css',
            array(),
            $this->version
        );

        wp_register_script(
            $this->scriptHandle,
            $this->pluginUrl . 'assets/js/flashcards/mandinka-flashcards.js',
            array( 'wp-element', 'wp-i18n', 'wp-api-fetch' ),
            $this->version,
            true
        );

        wp_localize_script(
            $this->scriptHandle,
            'aiwaMandinkaGames',
            array(
                'dictionary_url' => $this->pluginUrl . 'dictionary/mandinka-dictionary.json',
                'ajax_url'       => admin_url( 'admin-ajax.php' ),
                'restApiBaseUrl' => rest_url( 'mandinka-games/v1/' ),
                'nonce'          => wp_create_nonce( 'wp_rest' ),
            )
        );
    }

    /**
     * Shortcode function to display the quiz.
     */
    public function renderFlashcardQuizShortcode( array $atts ): string {
        wp_enqueue_style( $this->styleHandle );
        wp_enqueue_script( $this->scriptHandle );

        $file = $this->pluginPath . 'templates/flashcards/mandinka-flashcards-game-ui.php';
        if ( file_exists( $file ) ) {
            ob_start();
            include $file;
            return ob_get_clean();
        }
        return '<!-- ' . esc_html__( 'Mandinka Flashcards UI template file not found', 'mandinka-games' ) . ' -->';
    }
}

