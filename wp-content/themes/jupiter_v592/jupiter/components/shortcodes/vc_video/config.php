<?php
extract(shortcode_atts(array(
    'title'     => '',
    'max_width' => 0,
    'host'      => 'social_hosted',
    'poster_image' => '',
    'mp4'       => '',
    'webm'      => '',
    'link'      => '',
    'el_class'  => '',
    'animation' => '',
), $atts));

Mk_Static_Files::addAssets('vc_video');
