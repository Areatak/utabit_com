<?php
global $wp_embed;
$phpinfo =  pathinfo( __FILE__ );
$path = $phpinfo['dirname'];
include( $path . '/config.php' );

$max_width = (!empty($max_width) && $max_width > 0) ? ('style="max-width:'. $max_width.'px"') : '';
$poster_image = (!empty($poster_image)) ? (' poster="'. $poster_image.'"') : '';

?>

<div class="wpb_video_widget <?php echo get_viewport_animation_class($animation).$el_class; ?>">
	<div class="wpb_wrapper" <?php echo $max_width; ?>>

		<?php mk_get_view('global', 'shortcode-heading', false, ['title' => $title]); ?>

		<div class="video-container" <?php echo get_schema_markup('video'); ?>>
			<?php if($host == 'self_hosted'){ ?>

					<video<?php echo $poster_image; ?> preload="auto" loop="true" autoplay="true">

						<?php if ( !empty( $mp4 ) ) { ?>

							<source type="video/mp4" src="<?php echo $mp4; ?>" />

						<?php } if ( !empty( $webm ) ) { ?>

							<source type="video/webm" src="<?php echo $webm; ?>" />

						<?php } ?>
					</video>

			<?php } else{
			
			 	echo $wp_embed->run_shortcode( '[embed width="1140" height="641"]'.$link.'[/embed]' ); 
			
			}?>
		</div>
	</div>
</div>
