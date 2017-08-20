<!DOCTYPE html>
<html <?php echo language_attributes();?> >
<head>
    <?php wp_head(); ?>
<meta name="google-site-verification" content="3osnzL43XjOHN4NPWMMLjBrVYVMZTlFxAMhmv5KoOao" />
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<meta property="og:type" content="article" /> 
<meta property="og:description" content="" /> 
<meta property="og:site_name" content="Utabit" /> 
<meta property="og:locale" content="En_IR" /> 
<meta property="og:article:author" content="utabit.com" /> 
<meta property="og:title" content="Cryptocurrency and Beyond!" /> 
<meta property="og:article:section" content="Iran" /> 
<meta property="og:image" content="http://utabit.com/wp-content/uploads/2017/07/imageedit_1_4883649721.png" /> 
<meta property="og:url" content="http://www.utabit.com/" /> 
<meta property="og:updated_time" content="2017-08-12 10:51:15">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@irblockchain">
<meta name="twitter:title" content="Cryptocurrency and Beyond!">
<meta name="twitter:description" content="Blockchain Technology ">
<meta name="twitter:creator" content="@irblockchain">
<meta name="twitter:domain" content="www.utabit.com">
<meta name="twitter:image" content="http://utabit.com/wp-content/uploads/2017/07/imageedit_1_4883649721.png">
<meta name="twitter:image:alt" content="Cryptocurrency and Beyond!">
</head>

<body <?php body_class(mk_get_body_class(global_get_post_id())); ?> <?php echo get_schema_markup('body'); ?> data-adminbar="<?php echo is_admin_bar_showing() ?>">

	<?php
		// Hook when you need to add content right after body opening tag. to be used in child themes or customisations.
		do_action('theme_after_body_tag_start');
	?>

	<!-- Target for scroll anchors to achieve native browser bahaviour + possible enhancements like smooth scrolling -->
	<div id="top-of-page"></div>

		<div id="mk-boxed-layout">

			<div id="mk-theme-container" <?php echo is_header_transparent('class="trans-header"'); ?>>

				<?php mk_get_header_view('styles', 'header-'.get_header_style());