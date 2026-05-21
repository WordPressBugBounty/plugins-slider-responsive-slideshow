<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Slider Responsive Premium Shortcode
 *
 * @access    public
 *
 * @return    Create Fontend Slider Output
 */
add_shortcode( 'awl-slider', 'awl_slider_responsive_shortcode' );

function awl_slider_responsive_shortcode( $atts ) {
	// Use shortcode_atts() to safely extract attributes with defaults
	$atts = shortcode_atts(
		array(
			'id' => 0,
		),
		$atts,
		'awl-slider'
	);

	$slider_id = absint( $atts['id'] );

	// Early check: verify the slider post exists and is the correct post type
	if ( ! $slider_id ) {
		return '';
	}
	$slider_post = get_post( $slider_id );
	if ( ! $slider_post || 'slider_responsive' !== $slider_post->post_type ) {
		return '';
	}

	ob_start();
	wp_enqueue_script( 'awl-owl-carousel-js' );
	wp_enqueue_style( 'awl-owl-carousel-css' );
	wp_enqueue_style( 'awl-owl-carousel-theme-css' );
	wp_enqueue_style( 'awl-owl-carousel-transitions-css' );

	$allslidesetting = awl_sr_get_slider_settings( $slider_id );

	$slides       = intval( $allslidesetting['slides'] );
	if ( $slides <= 0 ) {
		$slides = 1;
	}
	$srspeed      = intval( $allslidesetting['srspeed'] );
	if ( $srspeed <= 0 ) {
		$srspeed = 200;
	}
	$autoplay     = ( $allslidesetting['autoplay'] === 'true' ) ? 'true' : 'false';
	$navigation   = ( $allslidesetting['navigation'] === 'true' ) ? 'true' : 'false';
	$navigation_n = ! empty( $allslidesetting['navigation_n'] ) ? strval( $allslidesetting['navigation_n'] ) : 'Next';
	$navigation_p = ! empty( $allslidesetting['navigation_p'] ) ? strval( $allslidesetting['navigation_p'] ) : 'Prev';
	$auto_height  = ( $allslidesetting['auto_height'] === 'true' ) ? 'true' : 'false';
	$touch_slide  = ( $allslidesetting['touch_slide'] === 'true' ) ? 'true' : 'false';
	$show_title   = ( $allslidesetting['show_title'] === 'true' ) ? 'true' : 'false';
	$show_desc    = ( $allslidesetting['show_desc'] === 'true' ) ? 'true' : 'false';
	$show_link    = ( $allslidesetting['show_link'] === 'true' ) ? 'true' : 'false';
	$link_text    = ! empty( $allslidesetting['link_text'] ) ? strval( $allslidesetting['link_text'] ) : 'Visit';
	$link_on      = ( $allslidesetting['link_on'] === 'true' ) ? 'true' : 'false';
	$text_align   = in_array( $allslidesetting['text_align'], array( 'left', 'right', 'center' ), true ) ? $allslidesetting['text_align'] : 'center';
	$custom_css   = isset( $allslidesetting['custom-css'] ) ? strval( $allslidesetting['custom-css'] ) : '';

	// Build dynamic CSS and add via wp_add_inline_style (CSP-safe)
	$inline_css = '#sr-slider-' . intval( $slider_id ) . ' .sr-image { margin: 5px; }' . "\n";
	$inline_css .= '#sr-slider-' . intval( $slider_id ) . ' .sr-image img { display: block; width: 100%; height: auto; }' . "\n";
	$inline_css .= '#sr-slider-' . intval( $slider_id ) . ' .sr-title { text-align: ' . esc_html( $text_align ) . '; font-weight: bolder; }' . "\n";
	$inline_css .= '#sr-slider-' . intval( $slider_id ) . ' .sr-desc { text-align: ' . esc_html( $text_align ) . '; }' . "\n";
	$inline_css .= '#sr-slider-' . intval( $slider_id ) . ' .sr-image .sr-link { text-align: ' . esc_html( $text_align ) . '; }' . "\n";
	if ( ! empty( $custom_css ) ) {
		$inline_css .= $custom_css . "\n";
	}
	wp_add_inline_style( 'awl-owl-carousel-css', $inline_css );

	// Build OwlCarousel JS config and add via wp_add_inline_script (CSP-safe)
	$js_autoplay    = ( $autoplay === 'true' ) ? 'true' : 'false';
	$js_navigation  = ( $navigation === 'true' ) ? 'true' : 'false';
	$js_auto_height = ( $auto_height === 'true' ) ? 'true' : 'false';
	$js_touch_slide = ( $touch_slide === 'true' ) ? 'true' : 'false';

	$carousel_js = 'jQuery(document).ready(function() {' . "\n";
	$carousel_js .= '   jQuery("#sr-slider-' . esc_js( $slider_id ) . '").owlCarousel({' . "\n";
	$carousel_js .= '      items : ' . intval( $slides ) . ',' . "\n";
	$carousel_js .= '      slideSpeed : ' . intval( $srspeed ) . ',' . "\n";
	$carousel_js .= '      paginationSpeed : ' . intval( $srspeed ) . ',' . "\n";
	$carousel_js .= '      rewindSpeed : ' . intval( $srspeed * 2 ) . ',' . "\n";
	$carousel_js .= '      autoPlay : ' . $js_autoplay . ',' . "\n";
	$carousel_js .= '      navigation : ' . $js_navigation . ',' . "\n";
	$carousel_js .= '      navigationText : ["' . esc_js( $navigation_p ) . '","' . esc_js( $navigation_n ) . '"],' . "\n";
	$carousel_js .= '      rewindNav : true,' . "\n";
	$carousel_js .= '      responsive: true,' . "\n";
	$carousel_js .= '      responsiveRefreshRate : 200,' . "\n";
	$carousel_js .= '      responsiveBaseWidth: window,' . "\n";
	$carousel_js .= '      baseClass : "owl-carousel",' . "\n";
	$carousel_js .= '      theme : "owl-theme",' . "\n";
	$carousel_js .= '      autoHeight : ' . $js_auto_height . ',' . "\n";
	$carousel_js .= '      dragBeforeAnimFinish : true,' . "\n";
	$carousel_js .= '      mouseDrag : ' . $js_touch_slide . ',' . "\n";
	$carousel_js .= '      touchDrag : ' . $js_touch_slide . ',' . "\n";
	$carousel_js .= '      transitionStyle : "fade"' . "\n";
	$carousel_js .= '   });' . "\n";
	$carousel_js .= '});' . "\n";
	wp_add_inline_script( 'awl-owl-carousel-js', $carousel_js );

	// Render the slider HTML directly (no redundant WP_Query needed - settings already loaded from postmeta)
	?>
	<div id="sr-slider-<?php echo esc_attr( $slider_id ); ?>">
		<?php
		if ( isset( $allslidesetting['slide-ids'] ) && is_array( $allslidesetting['slide-ids'] ) && count( $allslidesetting['slide-ids'] ) > 0 ) {
			$count = 0;

			foreach ( $allslidesetting['slide-ids'] as $attachment_id ) {
				$attachment_details = get_post( $attachment_id );
				if ( ! $attachment_details || is_wp_error( $attachment_details ) || 'attachment' !== $attachment_details->post_type ) {
					$count++;
					continue;
				}

				$slide_link = isset( $allslidesetting['slide-link'][ $count ] ) ? $allslidesetting['slide-link'][ $count ] : '';
				$large      = wp_get_attachment_image_src( $attachment_id, 'large', true );
				$image_src  = is_array( $large ) ? $large[0] : '';

				$title = isset( $allslidesetting['slide-title'][ $count ] ) ? $allslidesetting['slide-title'][ $count ] : '';
				if ( $title === '' ) {
					$title = $attachment_details->post_title;
				}
				$description = isset( $allslidesetting['slide-desc'][ $count ] ) ? $allslidesetting['slide-desc'][ $count ] : '';
				if ( $description === '' ) {
					$description = $attachment_details->post_content;
				}

				$text = $title;
				?>
				<div class="sr-image">
					<?php if ( $link_on == 'true' && ! empty( $slide_link ) ) { ?>
					<a href="<?php echo esc_url( $slide_link ); ?>" target="_blank">
						<img class="lazyOwl" src="<?php echo esc_url( $image_src ); ?>" alt="<?php echo esc_attr( $text ); ?>">
					</a>
					<?php } else { ?>
						<img class="lazyOwl" src="<?php echo esc_url( $image_src ); ?>" alt="<?php echo esc_attr( $text ); ?>">
					<?php } ?>
					
					<!--Text Below Slide-->
					<?php if ( $show_title == 'true' ) { ?>
					<div class="sr-title"><?php echo esc_html( $title ); ?></div>
					<?php } ?>
					
					<?php if ( $show_desc == 'true' ) { ?>
					<div class="sr-desc"><?php echo esc_html( $description ); ?></div>
					<?php } ?>
					
					<?php if ( $show_link == 'true' && $link_on == 'false' && ! empty( $slide_link ) ) { ?>
					<a href="<?php echo esc_url( $slide_link ); ?>" target="_blank">
						<div class="sr-link"><?php echo esc_html( $link_text ); ?></div>
					</a>
					<?php } ?>
					<!--Text Below Slide-->
				</div>
				<?php
				$count++;
			}// end of attachment foreach
		} else {

			esc_html_e( 'Sorry! No slides added to the slider shortcode yet. Please add few slide into shortcode', 'slider-responsive-slideshow' );
		} // end of if esle of slides avaialble check into slider
		?>
	</div>
	<?php
	return ob_get_clean();
}
