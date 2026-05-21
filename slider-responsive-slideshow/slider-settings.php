<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$post_id         = absint( $post->ID );
$allslidesetting = awl_sr_get_slider_settings( $post_id );

$slides       = $allslidesetting['slides'];
$srspeed      = $allslidesetting['srspeed'];
$autoplay     = $allslidesetting['autoplay'];
$auto_height  = $allslidesetting['auto_height'];
$touch_slide  = $allslidesetting['touch_slide'];
$navigation   = $allslidesetting['navigation'];
$navigation_n = $allslidesetting['navigation_n'];
$navigation_p = $allslidesetting['navigation_p'];
$show_title   = $allslidesetting['show_title'];
$show_desc    = $allslidesetting['show_desc'];
$show_link    = $allslidesetting['show_link'];
$link_on      = $allslidesetting['link_on'];
$link_text    = $allslidesetting['link_text'];
$text_align   = $allslidesetting['text_align'];
$custom_css   = $allslidesetting['custom-css'];
?>

	<div class="row">
		<div class="col-lg-12 bhoechie-tab-container">
			<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 bhoechie-tab-menu">
				<div class="list-group">
					<a href="#" class="list-group-item active text-center">
						<span class="dashicons dashicons-editor-table"></span><br/><?php esc_html_e( 'Add Images', 'slider-responsive-slideshow' ); ?>
					</a>
					<a href="#" class="list-group-item text-center">
						<span class="dashicons dashicons-admin-generic"></span><br/><?php esc_html_e( 'Configure', 'slider-responsive-slideshow' ); ?>
					</a>
					<a href="#" class="list-group-item text-center">
						<span class="dashicons dashicons-admin-appearance"></span><br/><?php esc_html_e( 'Auto Play', 'slider-responsive-slideshow' ); ?>
					</a>
					<a href="#" class="list-group-item text-center">
						<span class="dashicons dashicons-leftright"></span><br/><?php esc_html_e( 'Navigation Settings', 'slider-responsive-slideshow' ); ?>
					</a>
					<a href="#" class="list-group-item text-center">
						<span class="dashicons dashicons-admin-customizer"></span><br/><?php esc_html_e( 'Title & Description Settings', 'slider-responsive-slideshow' ); ?>
					</a>
					<a href="#" class="list-group-item text-center">
						<span class="dashicons dashicons-admin-comments"></span><br/><?php esc_html_e( 'Custom CSS', 'slider-responsive-slideshow' ); ?>
					</a>
					<a href="#" class="list-group-item text-center">
						<span class="dashicons dashicons-cart"></span><br/><?php esc_html_e( 'Upgrade To Pro', 'slider-responsive-slideshow' ); ?>
					</a>
					
				</div>
			</div>
			<div class="col-lg-10 col-md-10 col-sm-10 col-xs-10 bhoechie-tab">
				<div class="bhoechie-tab-content active">
					<h1><?php esc_html_e( 'Add Images', 'slider-responsive-slideshow' ); ?></h1>
					<?php wp_nonce_field( 'sr_add_images', 'sr_add_images_nonce' ); ?>
					<hr>
					<div id="slider-gallery">
						<input type="button" id="remove-all-slides" name="remove-all-slides" class="button button-large" rel="" value="<?php esc_html_e( 'Delete All Slide', 'slider-responsive-slideshow' ); ?>">
						<ul id="remove-slides" class="sbox">
							<?php
							if ( isset( $allslidesetting['slide-ids'] ) && is_array( $allslidesetting['slide-ids'] ) ) {
								$count = 0;
								foreach ( $allslidesetting['slide-ids'] as $id ) {
									$attachment = get_post( $id );
									if ( ! $attachment || 'attachment' !== $attachment->post_type ) {
										$count++;
										continue;
									}
									$thumbnail  = wp_get_attachment_image_src( $id, 'thumbnail', true );
									$slide_link = isset( $allslidesetting['slide-link'][ $count ] ) ? $allslidesetting['slide-link'][ $count ] : '';
									
									$slide_title = isset( $allslidesetting['slide-title'][ $count ] ) ? $allslidesetting['slide-title'][ $count ] : '';
									if ( $slide_title === '' ) {
										$slide_title = get_the_title( $id );
									}
									$slide_desc = isset( $allslidesetting['slide-desc'][ $count ] ) ? $allslidesetting['slide-desc'][ $count ] : '';
									if ( $slide_desc === '' ) {
										$slide_desc = $attachment->post_content;
									}
									?>
								<li class="slide">
									<img class="new-slide" src="<?php echo esc_url( $thumbnail[0] ); ?>" alt="">
									<input type="hidden" name="slide-ids[]" value="<?php echo esc_attr( $id ); ?>" />
									<!-- Slide Title, Caption, Alt Text, Description-->
									<input type="text" name="slide-title[]" style="width: 98%;" placeholder="Slide Title" value="<?php echo esc_attr( $slide_title ); ?>">
									<textarea name="slide-desc[]" placeholder="Slide Description" style="height: 108px; width: 98%;"><?php echo esc_textarea( $slide_desc ); ?></textarea>
									<input type="text" name="slide-link[]" style="width: 98%;" placeholder="Slide Link URL" value="<?php echo esc_url( $slide_link ); ?>">
									<a class="pw-trash-icon" href="#"><span class="dashicons dashicons-trash"></span></a>
								</li>
									<?php
									$count++;
								} // end of foreach
							} //end of if
							?>
						</ul>
					</div>
				</div>
				<div class="bhoechie-tab-content">
					<h1><?php esc_html_e( 'Configure settings', 'slider-responsive-slideshow' ); ?></h1>
					<hr>
					<div class="col-md-4">
						<div class="ma_field_discription">
							<h6><?php esc_html_e( 'Slides', 'slider-responsive-slideshow' ); ?></h6>
							<p><?php esc_html_e( 'Set numbers of slider you want to display at a time like 1, 2, 3, 4, 10', 'slider-responsive-slideshow' ); ?></p> 
						</div>
					</div>
					<div class="col-md-8">
						<div class="ma_field p-4 switch-field em_size_field">
							<div class="range-slider">	
								<input id="slides" name="slides" class="range-slider__range" type="range" value="<?php echo esc_attr( $slides ); ?>" min="1" step="1" max="10">
								<span class="range-slider__value">0</span>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="ma_field_discription">
							<h6><?php esc_html_e( 'Slide Speed', 'slider-responsive-slideshow' ); ?></h6>
							<p><?php esc_html_e( 'Set slide transition speed in milliseconds like 200, 400, 500, 700, 1000', 'slider-responsive-slideshow' ); ?></p> 
						</div>
					</div>
					<div class="col-md-8">
						<div class="ma_field p-4 switch-field em_size_field">
							<div class="range-slider">
								<input id="srspeed" name="srspeed" class="range-slider__range" type="range" value="<?php echo esc_attr( $srspeed ); ?>" min="10" step="10" max="1000">
								<span class="range-slider__value">0</span>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="ma_field_discription">
							<h6><?php esc_html_e( 'Slide Margin', 'slider-responsive-slideshow' ); ?></h6>
							<p><?php esc_html_e( 'Buy Premium Version To Get This Feature', 'slider-responsive-slideshow' ); ?><a href="https://awplife.com/account/signup/slider-responsive-slideshow" target="_blank"> <em>Buy Now</em></a></p> 
						</div>
					</div>
					<div class="col-md-8">
						<div class="ma_field p-4 switch-field em_size_field">
							<div class="range-slider">
								<input id="" name="" class="range-slider__range" type="range" value="" min="10" step="10" max="1000">
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="ma_field_discription">
							<h6><?php esc_html_e( 'Slide Transitions', 'slider-responsive-slideshow' ); ?></h6>
							<p><?php esc_html_e( 'Buy Premium Version To Get This Feature', 'slider-responsive-slideshow' ); ?><a href="https://awplife.com/account/signup/slider-responsive-slideshow" target="_blank"> <em>Buy Now</em></a></p> 
						</div>
					</div>
					<div class="col-md-8">
						<div class="ma_field p-4 switch-field em_size_field">
							<div class="range-slider">
								<input id="" name="" class="range-slider__range" type="range" value="" min="10" step="10" max="1000">
							</div>
						</div>
					</div>
				</div>
				
				<div class="bhoechie-tab-content">
					<h1><?php esc_html_e( 'Auto Play &  Slider settings', 'slider-responsive-slideshow' ); ?></h1>
					<hr>
					<div class="col-md-4">
						<div class="ma_field_discription">
							<h6><?php esc_html_e( 'Auto Play', 'slider-responsive-slideshow' ); ?></h6>
							<p><?php esc_html_e( 'Set auto play to slides automatically', 'slider-responsive-slideshow' ); ?></p> 
						</div>
					</div>
					<div class="col-md-8">
						<div class="ma_field p-4 switch-field em_size_field">
							<input type="radio" name="autoplay" id="autoplay1" value="true" <?php checked( $autoplay, 'true' ); ?>>
							<label for="autoplay1"><?php esc_html_e( 'Yes', 'slider-responsive-slideshow' ); ?></label>
							<input type="radio" name="autoplay" id="autoplay2" value="false" <?php checked( $autoplay, 'false' ); ?>>
							<label for="autoplay2"><?php esc_html_e( 'No', 'slider-responsive-slideshow' ); ?></label>
						</div>
					</div>
					<div class="col-md-4">
						<div class="ma_field_discription">
							<h6><?php esc_html_e( 'Auto Height', 'slider-responsive-slideshow' ); ?></h6>
							<p><?php esc_html_e( 'Set Set slider auto height', 'slider-responsive-slideshow' ); ?></p> 
						</div>
					</div>
					<div class="col-md-8">
						<div class="ma_field p-4 switch-field em_size_field">
							<input type="radio" name="auto_height" id="auto_height1" value="true" <?php checked( $auto_height, 'true' ); ?>>
							<label for="auto_height1"><?php esc_html_e( 'Yes', 'slider-responsive-slideshow' ); ?></label>
							<input type="radio" name="auto_height" id="auto_height2" value="false" <?php checked( $auto_height, 'false' ); ?>>
							<label for="auto_height2"><?php esc_html_e( 'No', 'slider-responsive-slideshow' ); ?></label>
						</div>
					</div>
					<div class="col-md-4">
						<div class="ma_field_discription">
							<h6><?php esc_html_e( 'Touch Slide', 'slider-responsive-slideshow' ); ?></h6>
							<p><?php esc_html_e( 'Set touch slide to slides using mouse drag event', 'slider-responsive-slideshow' ); ?></p> 
						</div>
					</div>
					<div class="col-md-8">
						<div class="ma_field p-4 switch-field em_size_field">
							<input type="radio" name="touch_slide" id="touch_slide1" value="true" <?php checked( $touch_slide, 'true' ); ?>>
							<label for="touch_slide1"><?php esc_html_e( 'Enable', 'slider-responsive-slideshow' ); ?></label>
							<input type="radio" name="touch_slide" id="touch_slide2" value="false" <?php checked( $touch_slide, 'false' ); ?>>
							<label for="touch_slide2"><?php esc_html_e( 'Disable', 'slider-responsive-slideshow' ); ?></label>
						</div>
					</div>
				</div>
				
				<div class="bhoechie-tab-content">
					<h1><?php esc_html_e( 'Slider Navigation Settings', 'slider-responsive-slideshow' ); ?></h1>
					<hr>
					<div class="col-md-4">
						<div class="ma_field_discription">
							<h6><?php esc_html_e( 'Slider Navigation', 'slider-responsive-slideshow' ); ?></h6>
							<p><?php esc_html_e( 'Set auto play to slides automatically', 'slider-responsive-slideshow' ); ?></p> 
						</div>
					</div>
					<div class="col-md-8">
						<div class="ma_field p-4 switch-field em_size_field">
							<input type="radio" name="navigation" id="navigation1" value="true" <?php checked( $navigation, 'true' ); ?>>
							<label for="navigation1"><?php esc_html_e( 'Yes', 'slider-responsive-slideshow' ); ?></label>
							<input type="radio" name="navigation" id="navigation2" value="false" <?php checked( $navigation, 'false' ); ?>>
							<label for="navigation2"><?php esc_html_e( 'No', 'slider-responsive-slideshow' ); ?></label>
						</div>
					</div>
					<div class="nav_show_hide">
						<div class="col-md-4">
							<div class="ma_field_discription">
								<h6><?php esc_html_e( ' Navigation Text For - Next Button', 'slider-responsive-slideshow' ); ?></h6>
								<p><?php esc_html_e( 'Set navigation next button text', 'slider-responsive-slideshow' ); ?></p> 
							</div>
						</div>
						<div class="col-md-8">
							<div class="ma_field p-4 em_size_field">
								<input type="text" name="navigation_n" id="navigation_n" value="<?php echo esc_attr( $navigation_n ); ?>">
							</div>
						</div>
						<div class="col-md-4">
							<div class="ma_field_discription">
								<h6><?php esc_html_e( ' Navigation Text For - Previous Button', 'slider-responsive-slideshow' ); ?></h6>
								<p><?php esc_html_e( 'Set navigation previous button text', 'slider-responsive-slideshow' ); ?></p> 
							</div>
						</div>
						<div class="col-md-8">
							<div class="ma_field p-4 em_size_field">
								<input type="text" name="navigation_p" id="navigation_p" value="<?php echo esc_attr( $navigation_p ); ?>">
							</div>
						</div>
					</div>
				</div>
				
				<div class="bhoechie-tab-content">
					<h1><?php esc_html_e( 'Title & Description Settings', 'slider-responsive-slideshow' ); ?></h1>
					<hr>
					<div class="col-md-4">
						<div class="ma_field_discription">
							<h6><?php esc_html_e( 'Show Slide Title Text', 'slider-responsive-slideshow' ); ?></h6>
							<p><?php esc_html_e( 'Set yes or no to display or hide slide title text', 'slider-responsive-slideshow' ); ?></p> 
						</div>
					</div>
					<div class="col-md-8">
						<div class="ma_field p-4 switch-field em_size_field">
							<input type="radio" name="show_title" id="show_title1" value="true" <?php checked( $show_title, 'true' ); ?>>
							<label for="show_title1"><?php esc_html_e( 'Yes', 'slider-responsive-slideshow' ); ?></label>
							<input type="radio" name="show_title" id="show_title2" value="false" <?php checked( $show_title, 'false' ); ?>>
							<label for="show_title2"><?php esc_html_e( 'No', 'slider-responsive-slideshow' ); ?></label>
						</div>
					</div>
					<div class="col-md-4">
						<div class="ma_field_discription">
							<h6><?php esc_html_e( 'Show Slide Description Text', 'slider-responsive-slideshow' ); ?></h6>
							<p><?php esc_html_e( 'Set yes or no to display or hide slide title description', 'slider-responsive-slideshow' ); ?></p> 
						</div>
					</div>
					<div class="col-md-8">
						<div class="ma_field p-4 switch-field em_size_field">
							<input type="radio" name="show_desc" id="show_desc1" value="true" <?php checked( $show_desc, 'true' ); ?>>
							<label for="show_desc1"><?php esc_html_e( 'Yes', 'slider-responsive-slideshow' ); ?></label>
							<input type="radio" name="show_desc" id="show_desc2" value="false" <?php checked( $show_desc, 'false' ); ?>>
							<label for="show_desc2"><?php esc_html_e( 'No', 'slider-responsive-slideshow' ); ?></label>
						</div>
					</div>
					<div class="col-md-4">
						<div class="ma_field_discription">
							<h6><?php esc_html_e( 'Show Slide Link', 'slider-responsive-slideshow' ); ?></h6>
							<p><?php esc_html_e( 'Set yes or no to display or hide slide link', 'slider-responsive-slideshow' ); ?></p> 
						</div>
					</div>
					<div class="col-md-8">
						<div class="ma_field p-4 switch-field em_size_field">
							<input type="radio" name="show_link" id="show_link1" value="true" <?php checked( $show_link, 'true' ); ?>>
							<label for="show_link1"><?php esc_html_e( 'Yes', 'slider-responsive-slideshow' ); ?></label>
							<input type="radio" name="show_link" id="show_link2" value="false" <?php checked( $show_link, 'false' ); ?>>
							<label for="show_link2"><?php esc_html_e( 'No', 'slider-responsive-slideshow' ); ?></label>
						</div>
					</div>
					<div class="link_show_hide">
						<div class="col-md-4">
							<div class="ma_field_discription">
								<h6><?php esc_html_e( 'Set Link On', 'slider-responsive-slideshow' ); ?></h6>
								<p><?php esc_html_e( 'Set link url on slide or on custom text link', 'slider-responsive-slideshow' ); ?></p> 
							</div>
						</div>
						<div class="col-md-8">
							<div class="ma_field p-4 switch-field em_size_field">
								<input type="radio" name="link_on" id="link_on1" value="true" <?php checked( $link_on, 'true' ); ?>>
								<label for="link_on1"><?php esc_html_e( 'On Slide', 'slider-responsive-slideshow' ); ?></label>
								<input type="radio" name="link_on" id="link_on2" value="false" <?php checked( $link_on, 'false' ); ?>>
								<label for="link_on2"><?php esc_html_e( 'On Custom Text', 'slider-responsive-slideshow' ); ?></label>
							</div>
						</div>
						<div class="col-md-4">
							<div class="ma_field_discription">
								<h6><?php esc_html_e( 'Custom Slide Link Text', 'slider-responsive-slideshow' ); ?></h6>
								<p><?php esc_html_e( 'Set custom text for slide link text', 'slider-responsive-slideshow' ); ?></p> 
							</div>
						</div>
						<div class="col-md-8">
							<div class="ma_field p-4">
								<input type="text" name="link_text" id="link_text" value="<?php echo esc_attr( $link_text ); ?>" >
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="ma_field_discription">
							<h6><?php esc_html_e( 'Slider All Text Alignment - Title, Description, Link', 'slider-responsive-slideshow' ); ?></h6>
							<p><?php esc_html_e( 'Set text alignment below slides like left, right or center', 'slider-responsive-slideshow' ); ?></p> 
						</div>
					</div>
					<div class="col-md-8">
						<div class="ma_field p-4 switch-field em_size_field">
							<input type="radio" name="text_align" id="text_align1" value="left" <?php checked( $text_align, 'left' ); ?>>
							<label for="text_align1"><?php esc_html_e( 'Left', 'slider-responsive-slideshow' ); ?></label>
							<input type="radio" name="text_align" id="text_align2" value="center" <?php checked( $text_align, 'center' ); ?>>
							<label for="text_align2"><?php esc_html_e( 'Center', 'slider-responsive-slideshow' ); ?></label>
							<input type="radio" name="text_align" id="text_align3" value="right" <?php checked( $text_align, 'right' ); ?>>
							<label for="text_align3"><?php esc_html_e( 'Right', 'slider-responsive-slideshow' ); ?></label>
						</div>
					</div>
				</div>
				
				<div class="bhoechie-tab-content">
					<h1><?php esc_html_e( 'Custom CSS', 'slider-responsive-slideshow' ); ?></h1>
					<hr>
					<div class="col-md-4">
						<div class="ma_field_discription">
							<h6><?php esc_html_e( 'Type Custum CSS', 'slider-responsive-slideshow' ); ?></h6>
							<p><?php esc_html_e( 'Apply own css on image gallery and dont use style tag', 'slider-responsive-slideshow' ); ?></p> 
						</div>
					</div>
					<div class="col-md-8">
						<div class="ma_field p-2 switch-field em_size_field">
							<textarea name="custom-css" id="custom-css" style="width: 100%; height: 120px;" placeholder="Type direct CSS code here. Don't use <style>...</style> tag."><?php echo esc_textarea( $custom_css ); ?></textarea>
						</div>
					</div>
				</div>
				<div class="bhoechie-tab-content">
					<h1><?php esc_html_e( 'Upgrade To Pro', 'slider-responsive-slideshow' ); ?></h1>
					<hr>
					<!--Grid-->
					<div class="" style="padding-left: 10px;">
						<p class="ms-title"><?php esc_html_e( 'Upgrade To Premium For Unlock More Features & Settings', 'slider-responsive-slideshow' ); ?></p>
					</div>

					<div class="">
						<h1><strong><?php esc_html_e( 'Offer:', 'slider-responsive-slideshow' ); ?></strong> <?php esc_html_e( 'Upgrade To Premium Just In Half Price', 'slider-responsive-slideshow' ); ?> <strike>$25</strike> <strong>$19</strong></h1>
						<br>
						<a href="<?php echo esc_url( 'https://awplife.com/demo/slider-responsive-slideshow-premium/' ); ?>" target="_blank" class="button button-primary button-hero load-customize hide-if-no-customize"><?php esc_html_e( 'Check Premium Version Live Demo', 'slider-responsive-slideshow' ); ?></a>
						<a href="<?php echo esc_url( 'https://awplife.com/wordpress-plugins/slider-responsive-slideshow-wordpress-plugin/' ); ?>" target="_blank" class="button button-primary button-hero load-customize hide-if-no-customize"><?php esc_html_e( 'Buy Premium Version', 'slider-responsive-slideshow' ); ?></a>
					</div>
				</div>
			</div>
		</div>
	</div>
<input type="hidden" name="sr-settings" id="sr-settings" value="sr-save-settings">
<?php
	wp_nonce_field( 'sr_save_settings', 'sr_save_nonce' );
