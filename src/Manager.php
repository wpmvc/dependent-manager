<?php

namespace WpMVC\DependentManager;

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
        <div class="notice notice-error dependency-manager-notice" style="padding-left: 0;">
            <div style="padding: 20px;">
                <div style="display: flex; align-items: flex-start; gap: 16px;">
                    <div style="color: #d63638; flex-shrink: 0; background: rgba(220, 54, 56, 0.1); border-radius: 8px; padding: 12px; display: flex; align-items: center; justify-content: center;">
                        <span class="dashicons dashicons-warning" style="font-size: 22px; width: 22px; height: 22px;"></span>
                    </div>
                    <div style="flex: 1;">
                        <h3 style="margin: 0 0 12px 0;">
                            <?php echo esc_html( $this->dependency_plugin ); ?> <?php echo esc_html( $this->dependency_version ); ?> requires minimum <?php echo esc_html( $this->self_plugin ); ?> version <?php echo esc_html( $this->dependency_version ); ?>
                        </h3>
                        <p style="margin: 0; line-height: 1.6;">
                            Since your current version is not compatible, the <?php echo esc_html( $this->self_plugin ); ?> plugin has been <strong>stopped to avoid unexpected errors.</strong> Please update <?php echo esc_html( $this->self_plugin ); ?> to the latest version to restore functionality.
                        </p>
                        <?php 
                        $show_update_button = ( $pagenow !== 'update-core.php' && $pagenow !== 'plugins.php' );
                        $show_manage_button = ( $pagenow !== 'plugins.php' );
                        $has_buttons        = $show_update_button || $show_manage_button;
                        ?>
                        <?php if ( $has_buttons ) : ?>
                        <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 16px;">
                            <?php if ( $show_update_button ) : ?>
                            <a class="button button-primary dependency-btn" href="<?php echo esc_url( admin_url( 'update-core.php?force-check=1' ) ); ?>">
                                Update <?php echo esc_html( $this->self_plugin ); ?>
                            </a>
                            <?php endif; ?>
                            
                            <?php if ( $show_manage_button ) : ?>
                            <a href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>" class="button button-secondary dependency-btn">
                                Manage Plugins
                            </a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        
                        <style>
                        @media (max-width: 782px) {
                            .dependency-btn {
                                width: 100% !important;
                                text-align: center !important;
                                margin-bottom: 8px !important;
                            }
                            .dependency-btn:last-child {
                                margin-bottom: 0 !important;
                            }
                        }
                        </style>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}