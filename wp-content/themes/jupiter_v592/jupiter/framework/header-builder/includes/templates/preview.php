<?php
/**
 * For previewing the file under ?header-builder=preview
 *
 * @since 0.1.0
 * @package Header_Builder
 */

/*
Template Name: Header Builder Preview
*/
?>
<?php // @codingStandardsIgnoreStart ?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php _e( 'Home', 'mk_framework' ); ?></title>
		<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <style type="text/css">
      img {
        max-width: 100%;
      }
      a {
        width: 100%;
      }
      .row {
        margin-top: 15px;
        margin-bottom: 15px;
      }

      .header {
        display: none;
      }

      @media screen and (min-width: 992px) {
        .header.desktop {
          display: block;
        }
      }

      @media screen and (max-width: 991px) and (min-width: 481px) {
        .header.tablet {
          display: block;
        }
      }

      @media screen and (max-width: 480px) {
        .header.mobile {
          display: block;
        }
      }
    </style>
		<style type="text/css"><?php $mk_hb->render_style(); ?></style>
	</head>

	<body><?php $mk_hb->render_markup(); ?></body>
</html>
<?php // @codingStandardsIgnoreEnd ?>
