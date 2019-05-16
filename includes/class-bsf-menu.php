<?php
/**
 * Admin helper functions.
 *
 * @package ConvertPro
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( ! class_exists( 'Bsf_Menu' ) ) {

	/**
	 * Class Bsf_Menu.
	 */
	class Bsf_Menu extends Cp_V2_Model {


		/**
		 * View actions
		 *
		 * @var view_actions
		 */
		public static $view_actions = array();

		/**
		 * Plugin slug
		 *
		 * @var plugin_slug
		 */
		public static $plugin_slug = 'convert-pro';

		/**
		 * Is top level page
		 *
		 * @var is_top_level_page
		 */
		public static $is_top_level_page = true;

		/**
		 * Default menu position
		 *
		 * @var default_menu_position
		 */
		public static $default_menu_position = 'middle';

		/**
		 * Parent page slug
		 *
		 * @var parent_page_slug
		 */
		public static $parent_page_slug = 'dashboard';

		/**
		 * Current slug
		 *
		 * @var current_slug
		 */
		public static $current_slug = '';

		/**
		 * Cpro multisite flag
		 *
		 * @var cpro_multisite_flag
		 */
		public static $cpro_multisite_flag = 0;

		/**
		 * Constructor
		 */
		function __construct() {
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 99 );
			add_action( 'admin_menu', array( $this, 'add_admin_menu_rename' ), 9999 );
			add_action( 'parent_file', array( $this, 'menu_highlight' ) );

			/* White Label Code Start */

			if ( is_multisite() ) {
				self::$cpro_multisite_flag = 1;
			}

			add_filter( 'bsf_product_name_convertpro', array( $this, 'cpro_plugin_name_atts' ) );

			$atts = esc_attr( 0 == self::$cpro_multisite_flag ? get_option( 'cpro_branding_plugin_author_name' ) : get_site_option( '_cpro_branding_plugin_author_name' ) );

			if ( '' !== $atts && trim( $atts ) ) {
				add_filter( 'bsf_product_author_convertpro', array( $this, 'cpro_author_atts' ) );
			}

			$atts = esc_attr( 0 == self::$cpro_multisite_flag ? get_option( 'cpro_branding_plugin_desc' ) : get_site_option( '_cpro_branding_plugin_desc' ) );

			if ( '' !== $atts && trim( $atts ) ) {
				add_filter( 'bsf_product_description_convertpro', array( $this, 'cpro_description_atts' ) );
			}

			$atts = esc_attr( 0 == self::$cpro_multisite_flag ? get_option( 'cpro_branding_enable_image' ) : get_site_option( '_cpro_branding_enable_image' ) );
			if ( '0' !== $atts ) {
				add_filter( 'bsf_product_icons_convertpro', array( $this, 'cpro_icons_atts' ) );
				add_filter( 'bsf_product_icons_convertpro-addon', array( $this, 'cpro_icons_atts' ) );
			}

			add_filter( 'gettext', array( $this, 'plugin_gettext_convertpro' ) );
			add_filter( 'gettext', array( $this, 'plugin_gettext_convertpro_addon' ) );

			/* White Label Code End */

			add_action( 'bsf_menu_dashboard_action', array( $this, 'dashboard_page' ) );
			add_action( 'bsf_menu_create_new_action', array( $this, 'add_new_popup' ) );

			add_action( 'bsf_menu_general_settings_action', array( $this, 'general_settings_page' ) );
			add_action( 'bsf_menu_license_action', array( $this, 'license_page' ) );
			add_action( 'bsf_menu_add_on_action', array( $this, 'add_on_page' ) );
			add_action( 'wp_ajax_bsf_save_settings', array( $this, 'handle_bsf_save_setttings_action' ) );

			if ( isset( $_REQUEST['page'] ) && strpos( $_REQUEST['page'], CP_PRO_SLUG ) !== false ) {
				add_action( 'admin_footer_text', array( $this, 'cp_admin_footer' ) );
			}

			$actions = array(
				'dashboard'        => array(
					'name' => __( 'Dashboard', 'convertpro' ),
					'link' => false,
				),
				'create-new'       => array(
					'name' => __( 'Create New', 'convertpro' ),
					'link' => false,
				),
				'general-settings' => array(
					'name' => __( 'Settings', 'convertpro' ),
					'link' => false,
				),
			);

			$view_actions = apply_filters( 'bsf_menu_options', $actions );

			self::$view_actions = $view_actions;

			self::$default_menu_position = get_option( 'bsf_menu_position' ) ? esc_attr( get_option( 'bsf_menu_position' ) ) : 'middle';
		}

		/**
		 * Function Name: cp_bsf_extensions_menu.
		 * Function Description: Register Comvertplug Addons installer menu.
		 *
		 * @param string $reg_menu string parameter.
		 */
		function cp_bsf_extensions_menu( $reg_menu ) {

			$reg_menu = get_site_option( 'bsf_installer_menu', $reg_menu );

			$_dir = CP_V2_BASE_DIR;

			$bsf_cp_id = bsf_extract_product_id( $_dir );

			$reg_menu['convertpro'] = array(
				'parent_slug' => self::$plugin_slug,
				'page_title'  => __( 'API Connections', 'convertpro' ),
				'menu_title'  => __( 'API Connections', 'convertpro' ),
				'product_id'  => $bsf_cp_id,
			);

			update_site_option( 'bsf_installer_menu', $reg_menu );

			return $reg_menu;
		}

		/**
		 * Function Name: add_new_popup.
		 * Function Description: add new popup.
		 */
		function add_new_popup() {
			require_once( CP_V2_BASE_DIR . 'admin/create-new.php' );
		}

		/**
		 * Function Name: menu_highlight.
		 * Function Description: menu highlight.
		 *
		 * @param string $parent_file string parameter.
		 */
		function menu_highlight( $parent_file ) {
			global $current_screen;

			$taxonomy = $current_screen->taxonomy;

			if ( CP_CONNECTION_TAXONOMY == $taxonomy ) {
				$parent_file = self::$plugin_slug;
			}

				return $parent_file;
		}

		/**
		 * Add main manu for ConvertPro
		 *
		 * @since 1.0
		 */
		function add_admin_menu() {

			new CP_V2_Tab_Menu( '' );
			$parent_page       = self::$default_menu_position;
			$is_top_level_page = self::$is_top_level_page;

			self::$current_slug = str_replace( '-', '_', self::$parent_page_slug );

			if ( $is_top_level_page ) {
				switch ( $parent_page ) {
					case 'top':
						$position = 3; // position of Dashboard + 1.
						break;
					case 'bottom':
						$position = ( ++$GLOBALS['_wp_last_utility_menu'] );
						break;
					case 'middle':
					default:
						$position = ( ++$GLOBALS['_wp_last_object_menu'] );
						break;
				}

				$main_page = add_menu_page( CPRO_BRANDING_NAME . ' Popups', CPRO_BRANDING_NAME, 'access_cp_pro', self::$plugin_slug, array( $this, 'menu_callback' ), 'div', $position );

				self::$view_actions = apply_filters( 'bsf_menu_options', self::$view_actions );
				$actions            = self::$view_actions;

				foreach ( $actions as $menu_slug => $menu ) {
					if ( $menu_slug !== self::$parent_page_slug ) {
						$callback_function  = 'menu_callback';
						self::$current_slug = $menu_slug;

						$admin_menu_slug = self::$plugin_slug . '-' . $menu_slug;

						if ( false !== $menu['link'] ) {
							$admin_menu_slug = $menu['link'];

							$page = add_submenu_page(
								self::$plugin_slug,
								esc_attr( $menu['name'] ),
								esc_attr( $menu['name'] ),
								'access_cp_pro',
								$admin_menu_slug
							);
						} else {
							$page = add_submenu_page(
								self::$plugin_slug,
								esc_attr( $menu['name'] ),
								esc_attr( $menu['name'] ),
								'access_cp_pro',
								$admin_menu_slug,
								array( $this, $callback_function )
							);
						}
					}
				}
			} else {
				add_submenu_page( $parent_page, 'Popups', CPRO_BRANDING_NAME, 'access_cp_pro', self::$plugin_slug, array( $this, 'menu_callback' ) );
			}
		}

		/**
		 * Function Name: menu_callback.
		 * Function Description: menu callback.
		 */
		function menu_callback() {

			if ( self::$is_top_level_page ) {
				$screen      = get_current_screen();
				$screen_base = $screen->base;

				$curr_slg = sanitize_title( CPRO_BRANDING_NAME );

				$current_slug = str_replace( array( $curr_slg . '_page_', self::$plugin_slug . '-' ), '', $screen_base );

				if ( strpos( $current_slug, 'toplevel_page' ) !== false ) {
					$current_slug = self::$parent_page_slug;
				}
			} else {
				$current_slug = isset( $_GET['action'] ) ? esc_attr( $_GET['action'] ) : self::$current_slug;
			}

			$active_tab   = str_replace( '_', '-', $current_slug );
			$current_slug = str_replace( '-', '_', $current_slug );

			$current_slug = str_replace(
				array(
					'convert_pro_page_',
					'convertpro_page_',
				),
				'',
				$current_slug
			);

			$cp_logo = CP_V2_BASE_URL . 'assets/admin/img/cp-pro-logo.png';

			if ( is_multisite() ) {
				self::$cpro_multisite_flag = 1;
			}

			$cp_custom_image_enabled = esc_attr( ( 0 == self::$cpro_multisite_flag ) ? get_option( 'cpro_branding_enable_image' ) : get_site_option( '_cpro_branding_enable_image' ) );
			$cp_custom_image         = ( 0 == self::$cpro_multisite_flag ) ? get_option( 'cpro_branding_url_image' ) : get_site_option( '_cpro_branding_url_image' );

			?>
			<div class='cp-parent-wrap'>
				<div id="cp-menu-page" class="wrap">
					<div class="cp-flex-center cp-header">
						<div class="cp-about-header cp-logo">
							<?php
							if ( CPRO_BRANDING_NAME != CP_PRO_NAME || ( '0' != $cp_custom_image_enabled && '' != $cp_custom_image_enabled ) ) {

								if ( '0' != $cp_custom_image_enabled && '' != $cp_custom_image_enabled ) {
									if ( defined( 'CPRO_CUSTOM_IMAGE_URL' ) ) {
										$cp_custom_image = CPRO_CUSTOM_IMAGE_URL;
									}
									?>
										<img class="cp-custom-logo" src="<?php echo esc_url( $cp_custom_image ); ?>" align="left">
									<?php } ?>
									<h1><?php echo CPRO_BRANDING_NAME != CP_PRO_NAME ? CPRO_BRANDING_NAME : ''; ?></h1>
							<?php } else { ?>
							<img src="<?php echo esc_url( $cp_logo ); ?>">
							<?php } ?>
						</div>
						<div class="cp-help-links">
							<?php

							$know_base_url   = ( 0 == self::$cpro_multisite_flag ) ? get_option( 'cpro_branding_url_kb' ) : get_site_option( '_cpro_branding_url_kb' );
							$kb_enabled      = esc_attr( ( 0 == self::$cpro_multisite_flag ) ? get_option( 'cpro_branding_enable_kb' ) : get_site_option( '_cpro_branding_enable_kb' ) );
							$support_enabled = esc_attr( ( 0 == self::$cpro_multisite_flag ) ? get_option( 'cpro_branding_enable_support' ) : get_site_option( '_cpro_branding_enable_support' ) );

							$know_base_url = ! $know_base_url ? CP_KNOWLEDGE_BASE_URL . '?utm_source=wp-dashboard&utm_medium=header-link&utm_campaign=knowledge-base' : $know_base_url;

							$support_url = ( 0 == self::$cpro_multisite_flag ) ? get_option( 'cpro_branding_url_support' ) : get_site_option( '_cpro_branding_url_support' );
							$support_url = ! $support_url ? CP_SUPPORT_URL . '?utm_source=wp-dashboard&utm_medium=header-link&utm_campaign=request-support' : $support_url;

							if ( '0' != $kb_enabled ) {
								if ( defined( 'CPRO_CUSTOM_KNOWLEDGE_BASE_URL' ) ) {
									$know_base_url = CPRO_CUSTOM_KNOWLEDGE_BASE_URL;
								}
								?>
								<a rel="noopener" href="<?php echo esc_url( $know_base_url ); ?>" target="_blank">                                
									<span class="cp-link-icon"><i class="dashicons dashicons-book"></i></span><?php _e( 'Knowledge Base', 'convertpro' ); ?>
								</a>
								<?php
							}

							if ( '0' != $support_enabled ) {
								if ( defined( 'CPRO_CUSTOM_SUPPORT_URL' ) ) {
									$support_url = CPRO_CUSTOM_SUPPORT_URL;
								}
								?>
								<a rel="noopener" href="<?php echo esc_url( $support_url ); ?>" target="_blank">
								<span class="cp-link-icon"><i class="dashicons dashicons-admin-users"></i></span><?php _e( 'Request Support', 'convertpro' ); ?>
							</a>

							<?php } ?>
						</div>
					</div>
				</div>

				<div class="cp-main-wrap">
					<?php

					new CP_V2_Tab_Menu( $active_tab );

					do_action( 'bsf_menu_' . $current_slug . '_action' );
					?>
				</div>
			</div>
			<?php
		}

		/**
		 * Function Name: handle_bsf_save_setttings_action.
		 * Function Description: handle bsf save setttings action.
		 */
		function handle_bsf_save_setttings_action() {

			if ( ! current_user_can( 'access_cp_pro' ) ) {
				$data = array(
					'message' => __( 'You are not authorized to perform this action.', 'convertpro' ),
				);
				wp_send_json_error( $data );
			}

			check_ajax_referer( 'cp-update-settings-nonce', 'security' );
			unset( $_POST['action'] );
			$settings           = $_POST['data'];
			$access_roles_found = false;
			$filter             = array();
			$check_branding     = 0;

			foreach ( $settings as $select ) {
				if ( substr( $select['name'], -2 ) == '[]' ) {
					$name = substr( $select['name'], 0, -2 );
					if ( ! array_key_exists( $name, $filter ) ) {
						$filter[ $name ] = array();
					}
					array_push( $filter[ $name ], $select['value'] );
				}
				if ( 'curr_tab' == $select['name'] ) {
					$curr_tab = $select['value'];
				}
			}

			if ( ! empty( $filter ) ) {
				foreach ( $filter as $key => $setting ) {
					update_option( $key, $setting );
				}
			}

			foreach ( $settings as $key => $setting ) {
				if ( 'cp_access_role[]' == $setting['name'] ) {
					$access_roles_found = true;
				}
				if ( 'cp_branding' == $setting['name'] ) {
					$check_branding = 1;
				}
				update_option( $setting['name'], $setting['value'] );
			}

			if ( ! $access_roles_found ) {
				update_option( 'cp_access_role', false );
			}

			// Menu position.
			$position      = esc_attr( get_option( 'bsf_menu_position' ) );
			$menu_position = ! $position ? 'middle' : $position;

			$is_top_level_page = in_array( $menu_position, array( 'top', 'middle', 'bottom' ), true );

			$is_other_plugin = in_array( $menu_position, array( 'index.php', 'edit.php', 'upload.php', 'edit.php?post_type=page', 'edit-comments.php', 'themes.php', 'plugins.php', 'users.php', 'tools.php', 'options-general.php' ), true );
			$flag            = 0;
			// If menu is at top level.
			if ( $is_top_level_page ) {
				$url = admin_url( 'admin.php?page=' . self::$plugin_slug . '-general-settings#advanced' );
			} else {
				if ( strpos( $menu_position, '?' ) !== false ) {
					$query_var = '&page=' . self::$plugin_slug . '&action=general-settings#advanced';
					$flag      = 1;
				} else {
					$query_var = '?page=' . self::$plugin_slug . '&action=general-settings#advanced';
				}
				$url = admin_url( $menu_position . $query_var );

				if ( ! $is_other_plugin && ! $flag ) {
					$url = admin_url( 'admin.php?page=' . self::$plugin_slug . '&action=general-settings#advanced' );
				}
			}

			if ( is_multisite() ) {
				foreach ( $settings as $key => $setting ) {
					if ( 1 == $check_branding ) {
						update_site_option( '_' . $setting['name'], $setting['value'] );
					}
				}
			}

			if ( 'true' == $_POST['has_redirect'] ) {
				$query = array(
					'message'  => 'saved',
					'redirect' => $url,
				);
				wp_send_json_success( $query );
			}

			$query = array(
				'message' => 'saved',
			);
			wp_send_json_success( $query );
		}

		/**
		 * Function Name: general_settings_page.
		 * Function Description: general settings page.
		 */
		function general_settings_page() {
			require_once( CP_V2_BASE_DIR . 'admin/general-settings.php' );
		}

		/**
		 * Function Name: dashboard_page.
		 * Function Description: dashboard page.
		 */
		function dashboard_page() {
			require_once( CP_V2_BASE_DIR . 'admin/insights.php' );
		}

		/**
		 * Function Name: add_admin_menu_rename.
		 * Function Description: add admin menu rename.
		 */
		function add_admin_menu_rename() {
			global $menu, $submenu;
			if ( isset( $submenu[ CP_PRO_SLUG ][0][0] ) ) {
				$submenu[ CP_PRO_SLUG ][0][0] = __( 'Dashboard', 'convertpro' );
			}
		}

		/**
		 * Add footer link for dashboar
		 * Since 1.0.1
		 */
		public static function cp_admin_footer() {

			$current_url       = '//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$current_r_url     = $current_url . '#addons';
			$reset_bundled_url = $current_url . '&remove-bundled-products&redirect=' . urlencode( $current_r_url );
			$author_url        = get_option( 'cpro_branding_plugin_author_url' );
			$author_url        = ! $author_url ? CPRO_AUTHOR_URL : $author_url;
			if ( defined( 'CPRO_CUSTOM_AUTHOR_URL' ) ) {
				$author_url = CPRO_CUSTOM_AUTHOR_URL;
			}
			echo'<div id="wpfooter" role="contentinfo" class="cp_admin_footer">
				        <p id="footer-left" class="alignleft">
				        <span id="footer-thankyou">Thank you for using <a href="' . esc_url( $author_url ) . '" target="_blank" rel="noopener" >' . CPRO_BRANDING_NAME . '</a>.</span>   </p>
				    <p id="footer-upgrade" class="alignright">';
					_e( 'Version', 'convertpro' );
					echo ' ' . CP_V2_VERSION . '</p>';
					echo  '<p id="footer-upgrade" class="alignright cp-bundled-prod">';
					echo  '<a href="' . esc_url( $reset_bundled_url ) . '">' . __( 'Refresh Bundled Products', 'convertpro' ) . '</a></p>
					<div class="clear"></div>
				</div>';
		}

		/**
		 * Redirects to the premium version of MailChimp for WordPress (uses JS)
		 */
		function cp_redirect_to_kb() {

			?>
			<script type="text/javascript">window.location.replace('<?php echo admin_url(); ?>edit.php?page=<?php echo CP_PRO_SLUG; ?>-dashboard&view=knowledge_base'); </script>
																				<?php
		}

		/**
		 * White labels the plugin name.
		 *
		 * @return string
		 */
		function cpro_plugin_name_atts() {

			$atts = sanitize_title( CPRO_BRANDING_NAME );

			return $atts;
		}

		/**
		 * White labels the plugin author.
		 *
		 * @return string
		 */
		function cpro_author_atts() {
			$atts = esc_attr( 0 == self::$cpro_multisite_flag ? get_option( 'cpro_branding_plugin_author_name' ) : get_site_option( '_cpro_branding_plugin_author_name' ) );

			if ( defined( 'CPRO_CUSTOM_AUTHOR_NAME' ) ) {
				$atts = CPRO_CUSTOM_AUTHOR_NAME;
			}
			return $atts;
		}

		/**
		 * White labels the plugin description.
		 *
		 * @return string
		 */
		function cpro_description_atts() {
			$atts = esc_attr( 0 == self::$cpro_multisite_flag ? get_option( 'cpro_branding_plugin_desc' ) : get_site_option( '_cpro_branding_plugin_desc' ) );
			if ( defined( 'CPRO_CUSTOM_DESCRIPTION' ) ) {
				$atts = CPRO_CUSTOM_DESCRIPTION;
			}
			return $atts;
		}

		/**
		 * White labels the plugin image.
		 *
		 * @return array
		 */
		function cpro_icons_atts() {
			$atts = 0 == self::$cpro_multisite_flag ? get_option( 'cpro_branding_url_image' ) : get_site_option( '_cpro_branding_url_image' );
			if ( defined( 'CPRO_CUSTOM_IMAGE_URL' ) ) {
				$atts = CPRO_CUSTOM_IMAGE_URL;
			}
			$icon = array(
				'1x'      => esc_attr( $atts ),
				'2x'      => esc_attr( $atts ),
				'default' => esc_attr( $atts ),
			);
			return $icon;
		}

		/**
		 * White labels the plugin using the gettext filter
		 * to cover areas that we can't access.
		 *
		 * @param string $text Text.
		 * @return string
		 */
		function plugin_gettext_convertpro( $text ) {

			if ( is_admin() && 'Convert Pro' == $text ) {
				$text = CPRO_BRANDING_NAME;
			}

			return $text;
		}

		/**
		 * White labels the plugin using the gettext filter for addon
		 * to cover areas that we can't access.
		 *
		 * @param string $text Text.
		 * @return string
		 */
		function plugin_gettext_convertpro_addon( $text ) {

			if ( is_admin() && 'Convert Pro - Addon' == $text ) {
				$text = CPRO_BRANDING_NAME . ' - Addon';
			}

			return $text;
		}
	}

	new Bsf_Menu;
}
