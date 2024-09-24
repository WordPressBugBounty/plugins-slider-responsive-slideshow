<?php
// toggle button CSS
wp_enqueue_style( 'awl-toogle-button-css', SR_PLUGIN_URL . 'css/toogle-button.css' );

// css dropdown toggle
wp_enqueue_style( 'nig-admin-bootstrap-css', SR_PLUGIN_URL . 'css/admin-bootstrap.css' );
wp_enqueue_style( 'awl-metabox-css', SR_PLUGIN_URL . 'css/metabox.css' );
wp_enqueue_style( 'awl-font-awesome-css', SR_PLUGIN_URL . 'css/font-awesome.min.css' );

// js
wp_enqueue_script( 'jquery' );
wp_enqueue_script( 'awl-bootstrap-js', SR_PLUGIN_URL . 'js/bootstrap.min.js', array( 'jquery' ), '', true );
?>
<style>

	.col-1, .col-2, .col-3, .col-4, .col-5, .col-6, .col-7, .col-8, .col-9, .col-10, .col-11, .col-12, .col, .col-auto, .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4, .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9, .col-sm-10, .col-sm-11, .col-sm-12, .col-sm, .col-sm-auto, .col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12, .col-md, .col-md-auto, .col-lg-1, .col-lg-2, .col-lg-3, .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8, .col-lg-9, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg, .col-lg-auto, .col-xl-1, .col-xl-2, .col-xl-3, .col-xl-4, .col-xl-5, .col-xl-6, .col-xl-7, .col-xl-8, .col-xl-9, .col-xl-10, .col-xl-11, .col-xl-12, .col-xl, .col-xl-auto {
		float: left;
	}
	.slider_settings {
		font-size: 16px !important;
		padding-left: 6px;
		font: initial;
		margin-top: 5px;
		font-weight: 600;
		padding-left:14px;
	}
	
	/* hide premalink for setting page */
	#comment-link-box, #edit-slug-box {
		display: none;
	}
</style>

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
							$post_id = esc_attr($post->ID);
							
							if (!function_exists('is_sr_serialized')) {
								function is_sr_serialized($str) {
									return ($str == serialize(false) || @unserialize($str) !== false);
								}
							}

							// Retrieve the base64 encoded data
							$encodedData = get_post_meta($post_id, 'awl_sr_settings_' . $post_id, true);

							// Decode the base64 encoded data
							$decodedData = base64_decode($encodedData);

							// Check if the data is serialized
							if (is_sr_serialized($decodedData)) {
								
								// The data is serialized, so unserialize it
								$allslidesetting = unserialize($decodedData);
								// Optionally, convert the unserialized data to JSON and save it back in base64 encoding for future access
								// This step is optional but recommended to transition your data format
								
								$jsonEncodedData = json_encode($allslidesetting);
								update_post_meta($post_id, 'awl_sr_settings_' . $post_id, $jsonEncodedData);
								
								// Now, to use the newly saved format, fetch and decode again
								$encodedData = get_post_meta($post_id, 'awl_sr_settings_' . $post_id, true);
								$allslidesetting = json_decode(($encodedData), true);
								
							} else {
								// Assume the data is in JSON format
								 $jsonData = get_post_meta($post_id, 'awl_sr_settings_' . $post_id, true);
								// Decode the JSON string into an associative array
								$allslidesetting = json_decode($jsonData, true); // Ensure true is passed to get an associative array
							}

							if ( isset( $allslidesetting['slide-ids'] ) ) {
								$count = 0;
								foreach ( $allslidesetting['slide-ids'] as $id ) {
									$thumbnail  = wp_get_attachment_image_src( $id, 'thumbnail', true );
									$attachment = get_post( $id );
									$slide_link = $allslidesetting['slide-link'][ $count ];
									?>
								<li class="slide">
									<img class="new-slide" src="<?php echo esc_url( $thumbnail[0] ); ?>" alt="" style="height: 150px; width: 98%; border-radius: 8px;">
									<input type="hidden" id="slide-ids[]" name="slide-ids[]" value="<?php echo esc_attr( $id ); ?>" />
									<!-- Slide Title, Caption, Alt Text, Description-->
									<input type="text" name="slide-title[]" id="slide-title[]" style="width: 98%;"  placeholder="Slide Title" value="<?php echo esc_html( get_the_title( $id ) ); ?>">
									<textarea name="slide-desc[]" id="slide-desc[]" placeholder="Slide Description" style="height: 108px; width: 98%;"><?php echo esc_html( $attachment->post_content ); ?></textarea>
									<input type="text" name="slide-link[]" id="slide-link[]" style="width: 98%;" placeholder="Slide Link URL" value="<?php echo esc_url( $slide_link ); ?>">
									<a class="pw-trash-icon" name="remove-slide" id="remove-slide" href="#"><span class="dashicons dashicons-trash"></span></a>
								</li>
									<?php
									$count++; } // end of foreach
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
								<?php
								if ( isset( $allslidesetting['slides'] ) ) {
									$slides = $allslidesetting['slides'];
								} else {
									$slides = '1';
								}
								?>
								<input id="slides" name="slides" class="range-slider__range" type="range" value="<?php echo esc_html( $slides ); ?>" min="1" step="1" max="10">
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
								<?php
								if ( isset( $allslidesetting['srspeed'] ) ) {
									$srspeed = $allslidesetting['srspeed'];
								} else {
									$srspeed = '200';
								}
								?>
								<input id="srspeed" name="srspeed" class="range-slider__range" type="range" value="<?php echo esc_html( $srspeed ); ?>" min="10" step="10" max="1000">
								<span class="range-slider__value">0</span>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="ma_field_discription">
							<h6><?php esc_html_e( 'Slide Margin', 'slider-responsive-slideshow' ); ?></h6>
							<p><?php esc_html_e( 'Buy Premium Version To Get This Feature', 'slider-responsive-slideshow' ); ?><a href="http://awplife.com/account/signup/slider-responsive-slideshow" target="_blank"> <em>Buy Now</em></a></p> 
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
							<p><?php esc_html_e( 'Buy Premium Version To Get This Feature', 'slider-responsive-slideshow' ); ?><a href="http://awplife.com/account/signup/slider-responsive-slideshow" target="_blank"> <em>Buy Now</em></a></p> 
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
							<?php
							if ( isset( $allslidesetting['autoplay'] ) ) {
								$autoplay = $allslidesetting['autoplay'];
							} else {
								$autoplay = 'true';
							}
							?>
							<input type="radio" name="autoplay" id="autoplay1" value="true" 
							<?php
							if ( $autoplay == 'true' ) {
								echo 'checked=checked';}
							?>
							>
							<label for="autoplay1"><?php esc_html_e( 'Yes', 'slider-responsive-slideshow' ); ?></label>
							<input type="radio" name="autoplay" id="autoplay2" value="false" 
							<?php
							if ( $autoplay == 'false' ) {
								echo 'checked=checked';}
							?>
							>
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
							<?php
							if ( isset( $allslidesetting['auto_height'] ) ) {
								$auto_height = $allslidesetting['auto_height'];
							} else {
								$auto_height = 'false';
							}
							?>
							<input type="radio" name="auto_height" id="auto_height1" value="true" 
							<?php
							if ( $auto_height == 'true' ) {
								echo 'checked=checked';}
							?>
							>
							<label for="auto_height1"><?php esc_html_e( 'Yes', 'slider-responsive-slideshow' ); ?></label>
							<input type="radio" name="auto_height" id="auto_height2" value="false" 
							<?php
							if ( $auto_height == 'false' ) {
								echo 'checked=checked';}
							?>
							>
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
							<?php
							if ( isset( $allslidesetting['touch_slide'] ) ) {
								$touch_slide = $allslidesetting['touch_slide'];
							} else {
								$touch_slide = 'true';
							}
							?>
							<input type="radio" name="touch_slide" id="touch_slide1" value="true" 
							<?php
							if ( $touch_slide == 'true' ) {
								echo 'checked=checked';}
							?>
							>
							<label for="touch_slide1"><?php esc_html_e( 'Enable', 'slider-responsive-slideshow' ); ?></label>
							<input type="radio" name="touch_slide" id="touch_slide2" value="false" 
							<?php
							if ( $touch_slide == 'false' ) {
								echo 'checked=checked';}
							?>
							>
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
							<?php
							if ( isset( $allslidesetting['navigation'] ) ) {
								$navigation = $allslidesetting['navigation'];
							} else {
								$navigation = 'false';
							}
							?>
							<input type="radio" name="navigation" id="navigation1" value="true" 
							<?php
							if ( $navigation == 'true' ) {
								echo 'checked=checked';}
							?>
							>
							<label for="navigation1"><?php esc_html_e( 'Yes', 'slider-responsive-slideshow' ); ?></label>
							<input type="radio" name="navigation" id="navigation2" value="false" 
							<?php
							if ( $navigation == 'false' ) {
								echo 'checked=checked';}
							?>
							>
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
								<?php
								if ( isset( $allslidesetting['navigation_n'] ) ) {
									$navigation_n = $allslidesetting['navigation_n'];
								} else {
									$navigation_n = 'Next';
								}
								?>
								<input type="text" name="navigation_n" id="navigation_n" value="<?php echo esc_html( $navigation_n ); ?>">
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
								<?php
								if ( isset( $allslidesetting['navigation_p'] ) ) {
									$navigation_p = $allslidesetting['navigation_p'];
								} else {
									$navigation_p = 'Prev';
								}
								?>
								<input type="text" name="navigation_p" id="navigation_p" value="<?php echo esc_html( $navigation_p ); ?>">
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
							<?php
							if ( isset( $allslidesetting['show_title'] ) ) {
								$show_title = $allslidesetting['show_title'];
							} else {
								$show_title = 'false';
							}
							?>
							<input type="radio" name="show_title" id="show_title1" value="true" 
							<?php
							if ( $show_title == 'true' ) {
								echo 'checked=checked';}
							?>
							>
							<label for="show_title1"><?php esc_html_e( 'Yes', 'slider-responsive-slideshow' ); ?></label>
							<input type="radio" name="show_title" id="show_title2" value="false" 
							<?php
							if ( $show_title == 'false' ) {
								echo 'checked=checked';}
							?>
							>
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
							<?php
							if ( isset( $allslidesetting['show_desc'] ) ) {
								$show_desc = $allslidesetting['show_desc'];
							} else {
								$show_desc = 'false';
							}
							?>
							<input type="radio" name="show_desc" id="show_desc1" value="true" 
							<?php
							if ( $show_desc == 'true' ) {
								echo 'checked=checked';}
							?>
							>
							<label for="show_desc1"><?php esc_html_e( 'Yes', 'slider-responsive-slideshow' ); ?></label>
							<input type="radio" name="show_desc" id="show_desc2" value="false" 
							<?php
							if ( $show_desc == 'false' ) {
								echo 'checked=checked';}
							?>
							>
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
							<?php
							if ( isset( $allslidesetting['show_link'] ) ) {
								$show_link = $allslidesetting['show_link'];
							} else {
								$show_link = 'false';
							}
							?>
							<input type="radio" name="show_link" id="show_link1" value="true" 
							<?php
							if ( $show_link == 'true' ) {
								echo 'checked=checked';}
							?>
							>
							<label for="show_link1"><?php esc_html_e( 'Yes', 'slider-responsive-slideshow' ); ?></label>
							<input type="radio" name="show_link" id="show_link2" value="false" 
							<?php
							if ( $show_link == 'false' ) {
								echo 'checked=checked';}
							?>
							>
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
								<?php
								if ( isset( $allslidesetting['link_on'] ) ) {
									$link_on = $allslidesetting['link_on'];
								} else {
									$link_on = 'false';
								}
								?>
								<input type="radio" name="link_on" id="link_on1" value="true" 
								<?php
								if ( $link_on == 'true' ) {
									echo 'checked=checked';}
								?>
								>
								<label for="link_on1"><?php esc_html_e( 'On Slide', 'slider-responsive-slideshow' ); ?></label>
								<input type="radio" name="link_on" id="link_on2" value="false" 
								<?php
								if ( $link_on == 'false' ) {
									echo 'checked=checked';}
								?>
								>
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
								<?php
								if ( isset( $allslidesetting['link_text'] ) ) {
									$link_text = $allslidesetting['link_text'];
								} else {
									$link_text = 'Visit';
								}
								?>
								<input type="text" name="link_text" id="link_text" value="<?php echo esc_html( $link_text ); ?>" >
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
							<?php
							if ( isset( $allslidesetting['text_align'] ) ) {
								$text_align = $allslidesetting['text_align'];
							} else {
								$text_align = 'center';
							}
							?>
							<input type="radio" name="text_align" id="text_align1" value="left" 
							<?php
							if ( $text_align == 'left' ) {
								echo 'checked=checked';}
							?>
							>
							<label for="text_align1"><?php esc_html_e( 'Left', 'slider-responsive-slideshow' ); ?></label>
							<input type="radio" name="text_align" id="text_align2" value="center" 
							<?php
							if ( $text_align == 'center' ) {
								echo 'checked=checked';}
							?>
							>
							<label for="text_align2"><?php esc_html_e( 'Center', 'slider-responsive-slideshow' ); ?></label>
							<input type="radio" name="text_align" id="text_align3" value="right" 
							<?php
							if ( $text_align == 'right' ) {
								echo 'checked=checked';}
							?>
							>
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
							<?php
							if ( isset( $allslidesetting['custom-css'] ) ) {
								$custom_css = $allslidesetting['custom-css'];
							} else {
								$custom_css = '';
							}
							?>
							<textarea name="custom-css" id="custom-css" style="width: 100%; height: 120px;" placeholder="Type direct CSS code here. Don't use <style>...</style> tag."><?php echo esc_html_e($custom_css); ?></textarea>
						</div>
					</div>
				</div>
				<div class="bhoechie-tab-content">
					<h1><?php esc_html_e( 'Upgrade To Pro', 'responsive-slider-gallery' ); ?></h1>
					<hr>
					<!--Grid-->
					<div class="" style="padding-left: 10px;">
						<p class="ms-title"><?php esc_html_e( 'Upgrade To Premium For Unloack More Features & Settings', 'responsive-slider-gallery' ); ?></p>
					</div>

					<div class="">
						<h1><strong><?php esc_html_e( 'Offer:', 'responsive-slider-gallery' ); ?></strong> <?php esc_html_e( 'Upgrade To Premium Just In Half Price', 'responsive-slider-gallery' ); ?> <strike>$25</strike> <strong>$19</strong></h1>
						<br>
						<a href="<?php echo esc_url( 'https://awplife.com/demo/slider-responsive-slideshow-premium/' ); ?>" target="_blank" class="button button-primary button-hero load-customize hide-if-no-customize"><?php esc_html_e( 'Check Premium Version Live Demo', 'responsive-slider-gallery' ); ?></a>
						<a href="<?php echo esc_url( 'https://awplife.com/wordpress-plugins/slider-responsive-slideshow-wordpress-plugin/' ); ?>" target="_blank" class="button button-primary button-hero load-customize hide-if-no-customize"><?php esc_html_e( 'Buy Premium Version', 'responsive-slider-gallery' ); ?></a>
					</div>
				</div>
			</div>
		</div>
	</div>
<input type="hidden" name="sr-settings" id="sr-settings" value="sr-save-settings">
<?php
	// syntax: wp_nonce_field( 'name_of_my_action', 'name_of_nonce_field' );
	wp_nonce_field( 'sr_save_settings', 'sr_save_nonce' );
?>

<!-- Return to Top -->
<a href="javascript:" id="return-to-top"><i class="fa fa-chevron-up"></i></a>

<hr>
<script>

// ===== Scroll to Top ==== 
jQuery(window).scroll(function() {
	if (jQuery(this).scrollTop() >= 50) {        // If page is scrolled more than 50px
		jQuery('#return-to-top').fadeIn(200);    // Fade in the arrow
	} else {
		jQuery('#return-to-top').fadeOut(200);   // Else fade out the arrow
	}
});
jQuery('#return-to-top').click(function() {      // When arrow is clicked
	jQuery('body,html').animate({
		scrollTop : 0                       // Scroll to top of body
	}, 500);
});

//show and hide settings Start............
	// Link Setting start
	var link_show_hide_settings = jQuery('input[name="show_link"]:checked').val();
		//on change to enable & disable  Link Setting
		if(link_show_hide_settings == "true") {
			jQuery('.link_show_hide').show();
		}
		if(link_show_hide_settings == "false") {
			jQuery('.link_show_hide').hide();
		}

		//on change to enable & disable  Link Setting
		jQuery(document).ready(function() {
			jQuery('input[name="show_link"]').change(function(){
				var link_show_hide_settings = jQuery('input[name="show_link"]:checked').val();
				if(link_show_hide_settings == "true") {
					jQuery('.link_show_hide').show();
				}
				if(link_show_hide_settings == "false") {
					jQuery('.link_show_hide').hide();
				}
			});
		});
	//  Link Setting End
		// navigation settings start
		var nav_show_hide_settings = jQuery('input[name="navigation"]:checked').val();
			//on change to enable & disable navigation Setting
			if(nav_show_hide_settings == "true") {
				jQuery('.nav_show_hide').show();
			}
			if(nav_show_hide_settings == "false") {
				jQuery('.nav_show_hide').hide();
			}

			//on change to enable & disable navigation Setting
			jQuery(document).ready(function() {
				jQuery('input[name="navigation"]').change(function(){
					var nav_show_hide_settings = jQuery('input[name="navigation"]:checked').val();
					if(nav_show_hide_settings == "true") {
						jQuery('.nav_show_hide').show();
					}
					if(nav_show_hide_settings == "false") {
						jQuery('.nav_show_hide').hide();
					}
				});
			});
		// navigation settings End
		
		// Auto play settings start
		var ap_show_hide_settings = jQuery('input[name="autoplay"]:checked').val();
			//on change to enable & disable Auto play
			if(ap_show_hide_settings == "true") {
				jQuery('.ap_show_hide').show();
			}
			if(ap_show_hide_settings == "false") {
				jQuery('.ap_show_hide').hide();
			}

			//on change to enable & disable Auto play
			jQuery(document).ready(function() {
				jQuery('input[name="autoplay"]').change(function(){
					var ap_show_hide_settings = jQuery('input[name="autoplay"]:checked').val();
					if(ap_show_hide_settings == "true") {
						jQuery('.ap_show_hide').show();
					}
					if(ap_show_hide_settings == "false") {
						jQuery('.ap_show_hide').hide();
					}
				});
			});
	//show and hide settings End......

//dropdown toggle on change effect
jQuery(document).ready(function() {
	//accordion icon
	jQuery(function() {
		function toggleSign(e) {
			jQuery(e.target)
			.prev('.panel-heading')
			.find('i')
			.toggleClass('fa fa-chevron-down fa fa-chevron-up');
		}
		jQuery('#accordion').on('hidden.bs.collapse', toggleSign);
		jQuery('#accordion').on('shown.bs.collapse', toggleSign);

		});
	});

//range slider
	var rangeSlider = function(){
	  var slider = jQuery('.range-slider'),
		  range = jQuery('.range-slider__range'),
		  value = jQuery('.range-slider__value');
		
	  slider.each(function(){

		value.each(function(){
		  var value = jQuery(this).prev().attr('value');
		  jQuery(this).html(value);
		});

		range.on('input', function(){
		  jQuery(this).next(value).html(this.value);
		});
	  });
	};
	rangeSlider();		
	
// start pulse on page load
	function pulseEff() {
	   jQuery('#shortcode').fadeOut(600).fadeIn(600);
	};
	var Interval;
	Interval = setInterval(pulseEff,1500);

	// stop pulse
	function pulseOff() {
		clearInterval(Interval);
	}
	// start pulse
	function pulseStart() {
		Interval = setInterval(pulseEff,1500);
	}
	// tab
	jQuery("div.bhoechie-tab-menu>div.list-group>a").click(function(e) {
		e.preventDefault();
		jQuery(this).siblings('a.active').removeClass("active");
		jQuery(this).addClass("active");
		var index = jQuery(this).index();
		jQuery("div.bhoechie-tab>div.bhoechie-tab-content").removeClass("active");
		jQuery("div.bhoechie-tab>div.bhoechie-tab-content").eq(index).addClass("active");
	});
</script>
