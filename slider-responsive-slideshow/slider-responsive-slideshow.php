<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Plugin Name:       Slider Responsive Slideshow
 * Plugin URI:        https://awplife.com/wordpress-plugins/slider-responsive-slideshow-premium/
 * Description:       An Easy Simple Responsive Beautiful Powerful CSS & JS Based WordPress Slider Plugin
 * Version:           1.6.1
 * Requires at least: 5.4
 * Requires PHP:      7.2
 * Author:            A WP Life
 * Author URI:        https://profiles.wordpress.org/awordpresslife
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       slider-responsive-slideshow
 * Domain Path:       /languages
 * License:           GPL2
 
Slider Responsive Slideshow is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Slider Responsive Slideshow is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Slider Responsive Slideshow. If not, see https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html.
 */

if ( ! class_exists( 'Slider_Responsive' ) ) {

	class Slider_Responsive {

		public function __construct() {
			$this->_constants();
			$this->_hooks();
		}

		protected function _constants() {
			/**
			 * Plugin Version
			 */
			define( 'AWLSR_PLUGIN_VER', '1.6.1' );

			/**
			 * Plugin Name
			 */
			define( 'AWLSR_PLUGIN_NAME', 'Slider Responsive' );

			/**
			 * Plugin Slug
			 */
			define( 'AWLSR_PLUGIN_SLUG', 'slider-responsive-slideshow' );

			/**
			 * Plugin Directory Path
			 */
			define( 'AWLSR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

			/**
			 * Plugin Directory URL
			 */
			define( 'AWLSR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

			/**
			 * Create a key for the .htaccess secure download link.
			 *
			 * @uses    NONCE_KEY     Defined in the WP root config.php
			 */
			define( 'AWLSR_SECURE_KEY', md5( NONCE_KEY ) );

		} // end of constructor function

		/**
		 * Setup the default filters and actions
		 *
		 * @uses      add_action()  To add various actions
		 * @access    private
		 * @return    void
		 */
		protected function _hooks() {

			/**
			 * Load text domain
			 */
			add_action( 'plugins_loaded', array( $this, '_load_textdomain' ) );

			/**
			 * Create Slider Responsive Custom Post
			 */
			add_action( 'init', array( $this, '_Slider_Responsive' ) );

			/**
			 * Add meta box to custom post
			 */
			 add_action( 'add_meta_boxes', array( $this, '_admin_add_meta_box' ) );



			add_action( 'wp_ajax_slide_responsive', array( $this, '_ajax_slide_responsive' ) );

			add_action( 'save_post', array( $this, '_sr_save_settings' ) );
			/**
			 * Shortcode Compatibility in Text Widgets
			 */
			add_filter( 'widget_text', 'do_shortcode' );

			// add pfg cpt shortcode column - manage_{$post_type}_posts_columns
			add_filter( 'manage_slider_responsive_posts_columns', array( $this, 'set_slider_responsive_shortcode_column_name' ) );

			// add pfg cpt shortcode column data - manage_{$post_type}_posts_custom_column
			add_action( 'manage_slider_responsive_posts_custom_column', array( $this, 'custom_slider_responsive_shodrcode_data' ), 10, 2 );

			/**
			 * Admin scripts and styles (restricted to slider_responsive CPT screens)
			 */
			add_action( 'admin_enqueue_scripts', array( $this, '_admin_enqueue_assets' ) );

		} // end of hook function

		/**
		 * Enqueue admin assets only on slider_responsive CPT screens.
		 */
		public function _admin_enqueue_assets( $hook ) {
			$screen = get_current_screen();
			if ( ! $screen || 'slider_responsive' !== $screen->post_type ) {
				return;
			}

			// WordPress media uploader assets
			wp_enqueue_media();
			wp_enqueue_script( 'media-upload' );

			// Plugin specific styles
			wp_enqueue_style( 'awl-toogle-button-css', AWLSR_PLUGIN_URL . 'css/toogle-button.css', array(), AWLSR_PLUGIN_VER );
			wp_enqueue_style( 'nig-admin-bootstrap-css', AWLSR_PLUGIN_URL . 'css/admin-bootstrap.css', array(), AWLSR_PLUGIN_VER );
			wp_enqueue_style( 'awl-metabox-css', AWLSR_PLUGIN_URL . 'css/metabox.css', array(), AWLSR_PLUGIN_VER );
			wp_enqueue_style( 'awl-font-awesome-css', AWLSR_PLUGIN_URL . 'css/font-awesome.min.css', array(), AWLSR_PLUGIN_VER );
			wp_enqueue_style( 'awl-sr-uploader-css', AWLSR_PLUGIN_URL . 'css/awl-sr-uploader.css', array(), AWLSR_PLUGIN_VER );

			// jQuery dependencies
			wp_enqueue_script( 'jquery' );

			// Bootstrap JS
			wp_enqueue_script( 'awl-bootstrap-js', AWLSR_PLUGIN_URL . 'js/bootstrap.min.js', array( 'jquery' ), AWLSR_PLUGIN_VER, true );

			// Slide uploader and sortable JS
			wp_enqueue_script( 'awl-sr-uploader-js', AWLSR_PLUGIN_URL . 'js/awl-sr-uploader.js', array( 'jquery', 'jquery-ui-sortable' ), AWLSR_PLUGIN_VER, true );

			// Admin helper JS (tabs, toggles, clipboard, range sliders, pulse)
			wp_enqueue_script( 'awl-sr-admin-js', AWLSR_PLUGIN_URL . 'js/awl-sr-admin.js', array( 'jquery' ), AWLSR_PLUGIN_VER, true );
		}

		// Slider Responsive table cpt shortcode column before date columns
		public function set_slider_responsive_shortcode_column_name( $defaults ) {
			$new       = array();
			unset( $defaults['tags'] );   // remove it from the columns list

			foreach ( $defaults as $key => $value ) {
				if ( $key == 'date' ) {  // when we find the date column
					$new['slider_responsive_shortcode'] = __( 'Shortcode', 'slider-responsive-slideshow' );  // put the tags column before it
				}
				$new[ $key ] = $value;
			}
			return $new;
		}

		// Slider Responsive cpt shortcode column data
		public function custom_slider_responsive_shodrcode_data( $column, $post_id ) {
			switch ( $column ) {
				case 'slider_responsive_shortcode':
					echo "<input type='text' class='button button-primary' id='slider-responsive-shortcode-" . esc_attr( $post_id ) . "' value='[awl-slider id=" . esc_attr( $post_id ) . "]' style='font-weight:bold; background-color:#32373C; color:#FFFFFF; text-align:center;' />";
					echo "<input type='button' class='button button-primary sr-copy-shortcode-btn' data-post-id='" . esc_attr( $post_id ) . "' value='Copy' style='margin-left:4px;' />";
					echo "<span id='copy-msg-" . esc_attr( $post_id ) . "' class='button button-primary' style='display:none; background-color:#32CD32; color:#FFFFFF; margin-left:4px; border-radius: 4px;'>copied</span>";
				break;
			}
		}

		/**
		 * Loads the text domain.
		 *
		 * @return    void
		 * @access    private
		 */
		public function _load_textdomain() {
			load_plugin_textdomain( 'slider-responsive-slideshow', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Slider Responsive Custom Post
		 * Create gallery post type in admin dashboard.
		 *
		 * @access    private
		 * @return    void      Return custom post type.
		 */
		public function _Slider_Responsive() {
			$labels = array(
				'name'               => _x( 'Slider Responsive Slideshow', 'Post Type General Name', 'slider-responsive-slideshow' ),
				'singular_name'      => _x( 'Slider Responsive Slideshow', 'Post Type Singular Name', 'slider-responsive-slideshow' ),
				'menu_name'          => __( 'Slider Responsive Slideshow', 'slider-responsive-slideshow' ),
				'name_admin_bar'     => __( 'Slider Responsive Slideshow', 'slider-responsive-slideshow' ),
				'parent_item_colon'  => __( 'Parent Item:', 'slider-responsive-slideshow' ),
				'all_items'          => __( 'All Slider', 'slider-responsive-slideshow' ),
				'add_new_item'       => __( 'Add New Slider', 'slider-responsive-slideshow' ),
				'add_new'            => __( 'Add New Slider', 'slider-responsive-slideshow' ),
				'new_item'           => __( 'New Slider', 'slider-responsive-slideshow' ),
				'edit_item'          => __( 'Edit Slider', 'slider-responsive-slideshow' ),
				'update_item'        => __( 'Update Slider', 'slider-responsive-slideshow' ),
				'search_items'       => __( 'Search Slider', 'slider-responsive-slideshow' ),
				'not_found'          => __( 'Slider Not found', 'slider-responsive-slideshow' ),
				'not_found_in_trash' => __( 'Slider Not found in Trash', 'slider-responsive-slideshow' ),
			);
			$args   = array(
				'label'               => __( 'Slider', 'slider-responsive-slideshow' ),
				'description'         => __( 'Custom Post Type For Slider', 'slider-responsive-slideshow' ),
				'labels'              => $labels,
				'supports'            => array( 'title' ),
				'taxonomies'          => array(),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'menu_position'       => 65,
				'menu_icon'           => 'dashicons-images-alt2',
				'show_in_admin_bar'   => true,
				'show_in_nav_menus'   => true,
				'can_export'          => true,
				'has_archive'         => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'capability_type'     => 'page',
			);
			register_post_type( 'slider_responsive', $args );

		} // end of post type function

		/**
		 * Adds Meta Boxes
		 *
		 * @access    private
		 * @return    void
		 */
		public function _admin_add_meta_box() {
			// Syntax: add_meta_box( $id, $title, $callback, $screen, $context, $priority, $callback_args );
			add_meta_box( 'awl_sr_shortcode_metabox', __( 'Copy Slider Responsive Slideshow Shortcode', 'slider-responsive-slideshow' ), array( $this, '_srs_shortcode_left_metabox' ), 'slider_responsive', 'side', 'default' );
			add_meta_box( 'awl_sr_slides_metabox', __( 'Add Image Slides', 'slider-responsive-slideshow' ), array( $this, 'sr_upload_multiple_images' ), 'slider_responsive', 'normal', 'default' );
		}

		// image gallery copy shortcode meta box under publish button
		public function _srs_shortcode_left_metabox( $post ) { 
			$post_id = esc_attr($post->ID);
			?>
			<p class="input-text-wrap">
				<input type="text" name="SRScopyshortcode" id="SRScopyshortcode" value="<?php echo "[awl-slider id=".$post_id."]"; ?>" readonly style="height: 90px; text-align: center; width:100%;  font-size: 20px; border: 2px dashed;">
				<p id="srs-copy-code"><?php esc_html_e( 'Shortcode copied to clipboard!', 'slider-responsive-slideshow' ); ?></p>
				<p style="margin-top: 10px"><?php esc_html_e( 'Copy & Embed shotcode into any Page/ Post / Text Widget to display Slider.', 'slider-responsive-slideshow' ); ?></p>
			</p>
			<span onclick="copyToClipboard('#SRScopyshortcode')" class="srs-copy dashicons dashicons-clipboard"></span>
			<?php
		}

		public function sr_upload_multiple_images( $post ) {
			?>
			
			<div class="row">
				<!--Add New Image Button-->
					<div class="file-upload">
						<div class="image-upload-wrap">
							<input class="add-new-slider file-upload-input" type="button" id="add-new-slider" name="add-new-slider" value="Upload Image" />
							<div class="drag-text">
								<h3><?php esc_html_e( 'ADD IMAGES', 'slider-responsive-slideshow' ); ?></h3>
							</div>
						</div>
					</div>
				</div>
				<div style="clear:left;"></div>
			
			<?php
			require_once 'slider-settings.php';
		} // end of upload multiple image

		public function _sr_ajax_callback_function( $id ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( array( 'message' => 'Insufficient permissions.' ) );
			}

			$id = absint( $id );
			if ( ! $id || 'attachment' !== get_post_type( $id ) ) {
				wp_send_json_error( array( 'message' => 'Invalid attachment ID.' ) );
			}

			// wp_get_attachment_image_src ( int $attachment_id, string|array $size = 'thumbnail', bool $icon = false )
			$thumbnail  = wp_get_attachment_image_src( $id, 'thumbnail', true );
			$attachment = get_post( $id ); // $id = attachment id
			?>
			<li class="slide">
				<img class="new-slide" src="<?php echo esc_url( $thumbnail[0] ); ?>" alt="">
				<input type="hidden" name="slide-ids[]" value="<?php echo esc_attr( $id ); ?>" />
				<input type="text" name="slide-title[]" style="width: 98%;" placeholder="Slide Title" value="<?php echo esc_attr( get_the_title( $id ) ); ?>">
				<textarea name="slide-desc[]" placeholder="Slide Description" style="height: 108px; width: 98%;"><?php echo esc_textarea( $attachment->post_content ); ?></textarea>
				<input type="text" name="slide-link[]" style="width: 98%;" placeholder="Slide Link URL">
				<a class="pw-trash-icon" href="#"><span class="dashicons dashicons-trash"></span></a>
			</li>
			<?php
		}


		public function _ajax_slide_responsive() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( array( 'message' => 'Insufficient permissions.' ) );
			}

			if ( ! isset( $_POST['sr_add_images_nonce'] ) || ! wp_verify_nonce( $_POST['sr_add_images_nonce'], 'sr_add_images' ) ) {
				wp_send_json_error( array( 'message' => 'Nonce verification failed.' ) );
			}

			$slide_id = isset( $_POST['slideId'] ) ? absint( $_POST['slideId'] ) : 0;
			if ( ! $slide_id ) {
				wp_send_json_error( array( 'message' => 'Missing slide ID.' ) );
			}

			// let the callback print the <li> markup directly
			$this->_sr_ajax_callback_function( $slide_id );
			wp_die(); // always use wp_die() in AJAX handlers
		}


		public function _sr_save_settings( $post_id ) {
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			if ( wp_is_post_revision( $post_id ) ) {
				return;
			}
			if ( 'slider_responsive' !== get_post_type( $post_id ) ) {
				return;
			}
			if ( ! isset( $_POST['sr_save_nonce'] ) ) {
				return;
			}
			if ( ! wp_verify_nonce( $_POST['sr_save_nonce'], 'sr_save_settings' ) ) {
				print 'Sorry, your nonce did not verify.';
				exit;
			}
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			if ( current_user_can( 'manage_options' ) ) {
				$slides       = isset( $_POST['slides'] ) ? sanitize_text_field( $_POST['slides'] ) : '1';
				$srspeed      = isset( $_POST['srspeed'] ) ? sanitize_text_field( $_POST['srspeed'] ) : '200';
				$autoplay     = isset( $_POST['autoplay'] ) ? sanitize_text_field( $_POST['autoplay'] ) : 'true';
				$navigation   = isset( $_POST['navigation'] ) ? sanitize_text_field( $_POST['navigation'] ) : 'false';
				$navigation_n = isset( $_POST['navigation_n'] ) ? sanitize_text_field( $_POST['navigation_n'] ) : 'Next';
				$navigation_p = isset( $_POST['navigation_p'] ) ? sanitize_text_field( $_POST['navigation_p'] ) : 'Prev';
				$auto_height  = isset( $_POST['auto_height'] ) ? sanitize_text_field( $_POST['auto_height'] ) : 'false';
				$touch_slide  = isset( $_POST['touch_slide'] ) ? sanitize_text_field( $_POST['touch_slide'] ) : 'true';
				$show_title   = isset( $_POST['show_title'] ) ? sanitize_text_field( $_POST['show_title'] ) : 'false';
				$show_desc    = isset( $_POST['show_desc'] ) ? sanitize_text_field( $_POST['show_desc'] ) : 'false';
				$show_link    = isset( $_POST['show_link'] ) ? sanitize_text_field( $_POST['show_link'] ) : 'false';
				$link_on      = isset( $_POST['link_on'] ) ? sanitize_text_field( $_POST['link_on'] ) : 'false';
				$link_text    = isset( $_POST['link_text'] ) ? sanitize_text_field( $_POST['link_text'] ) : 'Visit';
				$text_align   = isset( $_POST['text_align'] ) ? sanitize_text_field( $_POST['text_align'] ) : 'center';
				$custom_css   = isset( $_POST['custom-css'] ) ? wp_strip_all_tags( $_POST['custom-css'] ) : '';

				$i             = 0;
				$image_ids     = array();
				$image_titles  = array();
				$slide_link    = array();
				$image_descs   = array();
				$image_ids_val = isset( $_POST['slide-ids'] ) ? (array) $_POST['slide-ids'] : array();
				$image_ids_val = array_map( 'sanitize_text_field', $image_ids_val );

				foreach ( $image_ids_val as $image_id ) {
					$image_ids[]    = sanitize_text_field( $image_id );
					$image_titles[] = isset( $_POST['slide-title'][ $i ] ) ? sanitize_text_field( $_POST['slide-title'][ $i ] ) : '';
					$slide_link[]   = isset( $_POST['slide-link'][ $i ] ) ? sanitize_text_field( $_POST['slide-link'][ $i ] ) : '';
					$image_descs[]  = isset( $_POST['slide-desc'][ $i ] ) ? sanitize_textarea_field( $_POST['slide-desc'][ $i ] ) : '';
					$i++;
				}

				$allslidesetting = array(
					'slide-ids'    => $image_ids,
					'slide-title'  => $image_titles,
					'slide-desc'   => $image_descs,
					'slide-link'   => $slide_link,
					'slides'       => $slides,
					'srspeed'      => $srspeed,
					'autoplay'     => $autoplay,
					'navigation'   => $navigation,
					'navigation_n' => $navigation_n,
					'navigation_p' => $navigation_p,
					'auto_height'  => $auto_height,
					'touch_slide'  => $touch_slide,
					'show_title'   => $show_title,
					'show_desc'    => $show_desc,
					'show_link'    => $show_link,
					'link_on'      => $link_on,
					'link_text'    => $link_text,
					'text_align'   => $text_align,
					'custom-css'   => $custom_css,
				);

				$awl_slider_responsive_shortcode_setting = 'awl_sr_settings_' . $post_id;
				update_post_meta( $post_id, $awl_slider_responsive_shortcode_setting, $allslidesetting );
			}
		}//end _sr_save_settings()

	} // end of class

	/**
	 * Centralized settings retrieval function with backward compatibility logic.
	 *
	 * @param int $post_id Post ID of the slider.
	 * @return array Merged settings array.
	 */
	function awl_sr_get_slider_settings( $post_id ) {
		$post_id = absint( $post_id );
		if ( ! $post_id ) {
			return array();
		}

		$meta_key = 'awl_sr_settings_' . $post_id;
		$raw_data = get_post_meta( $post_id, $meta_key, true );

		$settings = array();

		if ( is_array( $raw_data ) ) {
			$settings = $raw_data;
		} elseif ( is_string( $raw_data ) && ! empty( $raw_data ) ) {
			// Attempt raw JSON string check
			$json_decoded = json_decode( $raw_data, true );
			if ( json_last_error() === JSON_ERROR_NONE && is_array( $json_decoded ) ) {
				$settings = $json_decoded;
			} else {
				// Attempt legacy base64 decoding
				$base64_decoded = base64_decode( $raw_data, true );
				if ( $base64_decoded !== false ) {
					$json_decoded_b64 = json_decode( $base64_decoded, true );
					if ( json_last_error() === JSON_ERROR_NONE && is_array( $json_decoded_b64 ) ) {
						$settings = $json_decoded_b64;
					} elseif ( is_serialized( $base64_decoded ) ) {
						$unserialized = @unserialize( $base64_decoded );
						if ( is_array( $unserialized ) ) {
							$settings = $unserialized;
						}
					}
				}

				// If it wasn't valid base64 or failed to yield an array, try raw serialize
				if ( empty( $settings ) && is_serialized( $raw_data ) ) {
					$unserialized = @unserialize( $raw_data );
					if ( is_array( $unserialized ) ) {
						$settings = $unserialized;
					}
				}
			}
		}

		$defaults = array(
			'slide-ids'    => array(),
			'slide-title'  => array(),
			'slide-desc'   => array(),
			'slide-link'   => array(),
			'slides'       => '1',
			'srspeed'      => '200',
			'autoplay'     => 'true',
			'navigation'   => 'false',
			'navigation_n' => 'Next',
			'navigation_p' => 'Prev',
			'auto_height'  => 'false',
			'touch_slide'  => 'true',
			'show_title'   => 'false',
			'show_desc'    => 'false',
			'show_link'    => 'false',
			'link_on'      => 'false',
			'link_text'    => 'Visit',
			'text_align'   => 'center',
			'custom-css'   => '',
		);

		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		return wp_parse_args( $settings, $defaults );
	}

	// register sf scripts
	function awplife_sr_register_scripts() {
		$suffix     = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '-min';
		$plugin_url = defined( 'AWLSR_PLUGIN_URL' ) ? AWLSR_PLUGIN_URL : plugin_dir_url( __FILE__ );
		$js_url     = $plugin_url . 'js/awl-owl-carousel' . $suffix . '.js';
		$ver        = defined( 'AWLSR_PLUGIN_VER' ) ? AWLSR_PLUGIN_VER : '1.6.1';

		// css & JS
		wp_register_script( 'awl-owl-carousel-js', $js_url, array( 'jquery' ), $ver, true );
		wp_register_style( 'awl-owl-carousel-css', $plugin_url . 'css/awl-owl-carousel.css', array(), $ver );
		wp_register_style( 'awl-owl-carousel-theme-css', $plugin_url . 'css/awl-owl-theme.css', array(), $ver );
		wp_register_style( 'awl-owl-carousel-transitions-css', $plugin_url . 'css/awl-owl-transitions.css', array(), $ver );
		// css & JS
	}
	add_action( 'wp_enqueue_scripts', 'awplife_sr_register_scripts' );

	/**
	 * Instantiates the Class
	 *
	 * @global    object    $awl_sr_gallery_object
	 */
	$awl_sr_gallery_object = new Slider_Responsive();
	require_once 'shortcode.php';
} // end of class exists
