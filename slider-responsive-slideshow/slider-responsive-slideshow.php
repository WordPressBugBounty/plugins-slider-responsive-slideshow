<?php

/**
@package Slider Responsive Slideshow
Plugin Name: Slider Responsive Slideshow
Plugin URI:  https://awplife.com/wordpress-plugins/slider-responsive-slideshow-premium/
Description: An Easy Simple Responsive Beautiful Powerful CSS & JS Based WordPress Slider Plugin
Version:     1.4.8
Author:      A WP Life
Author URI:  https://awplife.com/
Text Domain: slider-responsive-slideshow
Domain Path: /languages
License:     GPL2

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

		protected $protected_plugin_api;
		protected $ajax_plugin_nonce;

		public function __construct() {
			$this->_constants();
			$this->_hooks();
		}

		protected function _constants() {
			/**
			 * Plugin Version
			 */
			define( 'SR_PLUGIN_VER', '1.4.8' );

			/**
			 * Plugin Text Domain
			 */
			define( 'slider-responsive-slideshow', 'slider-responsive-slideshow' );

			/**
			 * Plugin Name
			 */
			define( 'SR_PLUGIN_NAME', __( 'Slider Responsive', 'slider-responsive-slideshow' ) );

			/**
			 * Plugin Slug
			 */
			define( 'SR_PLUGIN_SLUG', 'slider-responsive-slideshow' );

			/**
			 * Plugin Directory Path
			 */
			define( 'SR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

			/**
			 * Plugin Directory URL
			 */
			define( 'SR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

			/**
			 * Create a key for the .htaccess secure download link.
			 *
			 * @uses    NONCE_KEY     Defined in the WP root config.php
			 */
			define( 'SR_SECURE_KEY', md5( NONCE_KEY ) );

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
			 * add gallery menu item, change menu filter for multisite
			 */
			//add_action( 'admin_menu', array( $this, '_srgallery_menu' ), 101 );

			/**
			 * Create Slider Responsive Custom Post
			 */
			add_action( 'init', array( $this, '_Slider_Responsive' ) );

			/**
			 * Add meta box to custom post
			 */
			 add_action( 'add_meta_boxes', array( $this, '_admin_add_meta_box' ) );

			/**
			 * loaded during admin init
			 */
			add_action( 'admin_init', array( $this, '_admin_add_meta_box' ) );

			add_action( 'wp_ajax_slide_responsive', array( &$this, '_ajax_slide_responsive' ) );

			add_action( 'save_post', array( &$this, '_sr_save_settings' ) );
			/**
			 * Shortcode Compatibility in Text Widgets
			 */
			add_filter( 'widget_text', 'do_shortcode' );

			// add pfg cpt shortcode column - manage_{$post_type}_posts_columns
			add_filter( 'manage_slider_responsive_posts_columns', array( &$this, 'set_slider_responsive_shortcode_column_name' ) );

			// add pfg cpt shortcode column data - manage_{$post_type}_posts_custom_column
			add_action( 'manage_slider_responsive_posts_custom_column', array( &$this, 'custom_slider_responsive_shodrcode_data' ), 10, 2 );

			add_action( 'wp_enqueue_scripts', array( &$this, 'slider_enqueue_scripts_in_header' ) );

		} // end of hook function

		public function slider_enqueue_scripts_in_header() {
			wp_enqueue_script( 'jquery' );
		}

		// Slider Responsive table cpt shortcode column before date columns
		public function set_slider_responsive_shortcode_column_name( $defaults ) {
			$new       = array();
			$shortcode = $columns['slider_responsive_shortcode'];  // save the tags column
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
					echo "<input type='button' class='button button-primary' onclick='return SLIDERRESCopyShortcode" . esc_attr( $post_id ) . "();' readonly value='Copy' style='margin-left:4px;' />";
					echo "<span id='copy-msg-" . esc_attr( $post_id ) . "' class='button button-primary' style='display:none; background-color:#32CD32; color:#FFFFFF; margin-left:4px; border-radius: 4px;'>copied</span>";
					echo '<script>
						function SLIDERRESCopyShortcode' . esc_attr( $post_id ) . "() {
							var copyText = document.getElementById('slider-responsive-shortcode-" . esc_attr( $post_id ) . "');
							copyText.select();
							document.execCommand('copy');
							
							//fade in and out copied message
							jQuery('#copy-msg-" . esc_attr( $post_id ) . "').fadeIn('1000', 'linear');
							jQuery('#copy-msg-" . esc_attr( $post_id ) . "').fadeOut(2500,'swing');
						}
						</script>
					";
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
				'has_archive'         => true,
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
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
			add_meta_box( '1', __( 'Copy Slider Responsive Slideshow Shortcode', 'slider-responsive-slideshow' ), array( &$this, '_srs_shortcode_left_metabox' ), 'slider_responsive', 'side', 'default' );
			add_meta_box( '', __( 'Add Image Slides', 'slider-responsive-slideshow' ), array( &$this, 'sr_upload_multiple_images' ), 'slider_responsive', 'normal', 'default' );
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
			<style>
				.srs-copy {
					position: absolute;
					top: 9px;
					right: 30px;
					font-size: 30px;
					cursor: pointer;
				}
				.ui-sortable-handle > span {
					font-size: 16px !important;
				}
			</style>
			<script>
			jQuery( "#srs-copy-code" ).hide();
			function copyToClipboard(element) {
				var $temp = jQuery("<input>");
				jQuery("body").append($temp);
				$temp.val(jQuery(element).val()).select();
				document.execCommand("copy");
				$temp.remove();
				jQuery( "#SRScopyshortcode" ).select();
				jQuery( "#srs-copy-code" ).fadeIn();
			}
			</script>
			<?php
		}

		public function sr_upload_multiple_images( $post ) {
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_script( 'awl-sr-uploader.js', SR_PLUGIN_URL . 'js/awl-sr-uploader.js', array( 'jquery' ) );
			wp_enqueue_style( 'awl-sr-uploader-css', SR_PLUGIN_URL . 'css/awl-sr-uploader.css' );
			wp_enqueue_media();
			?>
			
			<div class="row">
				<!--Add New Image Button-->
					<div class="file-upload">
						<div class="image-upload-wrap">
							<input class="add-new-slider file-upload-input" id="add-new-slider" name="add-new-slider" value="Upload Image" />
							<div class="drag-text">
								<h3><?php esc_html_e( 'ADD IMAGES', 'responsive-slider-gallery' ); ?></h3>
							</div>
						</div>
					</div>
				</div>
				<div style="clear:left;"></div>
			
			<?php
			require_once 'slider-settings.php';
		} // end of upload multiple image

		public function _sr_ajax_callback_function( $id ) {
			if ( current_user_can( 'manage_options' ) ) {
				// wp_get_attachment_image_src ( int $attachment_id, string|array $size = 'thumbnail', bool $icon = false )
				// thumb, thumbnail, medium, large, post-thumbnail
				$thumbnail  = wp_get_attachment_image_src( $id, 'thumbnail', true );
				$attachment = get_post( $id ); // $id = attachment id
				?>
				<li class="slide">
					<img class="new-slide" src="<?php echo esc_url( $thumbnail[0] ); ?>" alt="" style="height: 150px; width: 98%; border-radius: 8px;">
					<input type="hidden" id="slide-ids[]" name="slide-ids[]" value="<?php echo esc_attr( $id ); ?>" />
					<input type="text" name="slide-title[]" id="slide-title[]" style="width: 98%;" placeholder="Slide Title" value="<?php echo esc_html( get_the_title( $id ) ); ?>">
					<textarea name="slide-desc[]" id="slide-desc[]" placeholder="Slide Description" style="height: 108px; width: 98%;"><?php echo esc_html( $attachment->post_content ); ?></textarea>
					<input type="text" name="slide-link[]" id="slide-link[]" style="width: 98%;" placeholder="Slide Link URL">
					<a class="pw-trash-icon" name="remove-slide" id="remove-slide" href="#"><span class="dashicons dashicons-trash"></span></a>
				</li>
				<?php
			}
		}

		public function _ajax_slide_responsive() {
			if ( current_user_can( 'manage_options' ) ) {
				if (isset( $_POST['sr_add_images_nonce'] ) && wp_verify_nonce( $_POST['sr_add_images_nonce'], 'sr_add_images' ) ) {
					echo esc_attr( $this->_sr_ajax_callback_function ( sanitize_text_field( $_POST['slideId'] ) ) );
					die;
				} else {
					print 'Sorry, your nonce did not verify.';
					exit;
				}
			}
		}

		public function _sr_save_settings( $post_id ) {
			if (current_user_can('manage_options')) {
				if ( isset( $_POST['sr_save_nonce'] ) ) {
					if ( isset( $_POST['sr_save_nonce'] ) && wp_verify_nonce( $_POST['sr_save_nonce'], 'sr_save_settings' ) ) {

						$slides       = sanitize_text_field( $_POST['slides'] );
						$srspeed      = sanitize_text_field( $_POST['srspeed'] );
						$autoplay     = sanitize_text_field( $_POST['autoplay'] );
						$navigation   = sanitize_text_field( $_POST['navigation'] );
						$navigation_n = sanitize_text_field( $_POST['navigation_n'] );
						$navigation_p = sanitize_text_field( $_POST['navigation_p'] );
						$auto_height  = sanitize_text_field( $_POST['auto_height'] );
						$touch_slide  = sanitize_text_field( $_POST['touch_slide'] );
						$show_title   = sanitize_text_field( $_POST['show_title'] );
						$show_desc    = sanitize_text_field( $_POST['show_desc'] );
						$show_link    = sanitize_text_field( $_POST['show_link'] );
						$link_on      = sanitize_text_field( $_POST['link_on'] );
						$link_text    = sanitize_text_field( $_POST['link_text'] );
						$text_align   = sanitize_text_field( $_POST['text_align'] );
						$custom_css   = sanitize_textarea_field( $_POST['custom-css'] );

						$i             = 0;
						$image_ids     = array();
						$image_titles  = array();
						$slide_link    = array();
						$image_descs   = array();
						$image_ids_val = isset( $_POST['slide-ids'] ) ? (array) $_POST['slide-ids'] : array();
						$image_ids_val = array_map( 'sanitize_text_field', $image_ids_val );

						foreach ( $image_ids_val as $image_id ) {
							$image_ids[]    = sanitize_text_field( $_POST['slide-ids'][ $i ] );
							$image_titles[] = sanitize_text_field( $_POST['slide-title'][ $i ] );
							$slide_link[]   = sanitize_text_field( $_POST['slide-link'][ $i ] );
							$image_descs[]  = sanitize_text_field( $_POST['slide-desc'][ $i ] );

							$single_image_update = array(
								'ID'           => $image_id,
								'post_title'   => $image_titles[ $i ],
								'post_content' => $image_descs[ $i ],
							);
							wp_update_post( $single_image_update );
							$i++;
						}

						$allslidesetting = array(
							'slide-ids'    => $image_ids,
							'slide-title'  => $image_titles,
							'slide-link'   => $slide_link,
							'slide-desc'   => $image_descs,
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
						update_post_meta( $post_id, $awl_slider_responsive_shortcode_setting, json_encode( $allslidesetting ) );
					} else {
						print 'Sorry, your nonce did not verify.';
						exit;
					}
				}
			}
		}//end _sr_save_settings()

		/**
		 * Slider Responsive Docs Page
		 * Create doc page to help user to setup plugin
		 *
		 * @access    private
		 * @return    void.
		 */

	} // end of class

	// register sf scripts
	function awplife_sr_register_scripts() {

		// css & JS
		wp_register_script( 'awl-owl-carousel-js', plugin_dir_url( __FILE__ ) . 'js/awl-owl-carousel.js', array( 'jquery' ) );
		wp_register_style( 'awl-owl-carousel-css', plugin_dir_url( __FILE__ ) . 'css/awl-owl-carousel.css' );
		wp_register_style( 'awl-owl-carousel-theme-css', plugin_dir_url( __FILE__ ) . 'css/awl-owl-theme.css' );
		wp_register_style( 'awl-owl-carousel-transitions-css', plugin_dir_url( __FILE__ ) . 'css/awl-owl-transitions.css' );
		// css & JS
	}
		add_action( 'wp_enqueue_scripts', 'awplife_sr_register_scripts' );

	/**
	 * Instantiates the Class
	 *
	 * @global    object    $sr_gallery_object
	 */
	$sr_gallery_object = new Slider_Responsive();
	require_once 'shortcode.php';
} // end of class exists
?>
