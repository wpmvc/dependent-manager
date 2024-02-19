<?php

namespace WpMvc\DependentManager;

defined( 'ABSPATH' ) || exit;

class Manager {
    protected string $dependency_version;

    protected string $self_version;

    protected string $self_plugin;

    protected string $dependency_plugin;

    public function __construct( string $dependency_version, string $self_version, string $dependency_plugin, string $self_plugin ) {
        $this->dependency_version = $dependency_version;
        $this->self_version       = $self_version;
        $this->self_plugin        = $self_plugin;
        $this->dependency_plugin  = $dependency_plugin;
    }

    public function is_compatible() {
        $is_compatible = version_compare( $this->self_version, $this->dependency_version ) >= 0 ? true : false;

        if ( ! $is_compatible && is_admin() ) {
            add_action( 'admin_notices', [ $this, 'action_admin_notices' ] );
        }

        return $is_compatible;
    }

    /**
     * Prints admin screen notices.
     */
    public function action_admin_notices() : void {
        global $pagenow;

        if ( 'update.php' === $pagenow ) {
            return;
        }
        ?>
        <div class="notice notice-error" style="padding-bottom: 15px;">
            <h2>The <?php echo esc_html( $this->self_plugin )?> plugin is stopped temporarily.</h2>
            <p>The <?php echo esc_html( $this->self_plugin )?> plugin is not compatible with <?php echo esc_html( $this->dependency_plugin )?>. To avoid unexpected errors this plugin stopped temporarily. To enable this plugin, please update with the latest version.</p>
            <a class="button button-primary" href="<?php echo esc_url( admin_url( 'update-core.php?force-check=1' ) )?>">Update <?php echo esc_html( $this->self_plugin )?></a>
        </div>
        <?php
    }
}