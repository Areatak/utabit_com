<?php
vc_map(array(
    "name" => __("Video Player", "mk_framework") ,
    "base" => "vc_video",
    'icon' => 'icon-mk-video-player vc_mk_element-icon',
    'description' => __('Youtube, Vimeo,..', 'mk_framework') ,
    "category" => __('Social', 'mk_framework') ,
    "params" => array(
        array(
            "type" => "textfield",
            "heading" => __("Shortcode Title", "mk_framework") ,
            "param_name" => "title",
            "value" => "",
            "description" => __("", "mk_framework")
        ) ,
        array(
            "type" => "range",
            "heading" => __("Video Max Width", "mk_framework") ,
            "param_name" => "max_width",
            "value" => "0",
            "min" => "100",
            "max" => "2000",
            "step" => "1",
            "unit" => 'px',
            "description" => __("If set zero, the video will expand to the parent container width", "mk_framework")
        ) ,
        array(
            "heading" => __("Video Type", 'mk_framework') ,
            "description" => __("", 'mk_framework') ,
            "param_name" => "host",
            "value" => array(
                __("Social Hosted (Youtube, Vimeo,..)", 'mk_framework') => "social_hosted",
                __("Self Hosted", 'mk_framework') => "self_hosted",
            ) ,
            "type" => "dropdown"
        ) ,
        array(
            "type" => "upload",
            "heading" => __("MP4 Format", "mk_framework") ,
            "param_name" => "mp4",
            "value" => "",
            "description" => __("Compatibility for Safari, IE9", "mk_framework") ,
            "dependency" => array(
                'element' => "host",
                'value' => array(
                    'self_hosted'
                )
            )
        ) ,
        array(
            "type" => "upload",
            "heading" => __("WebM Format", "mk_framework") ,
            "param_name" => "webm",
            "value" => "",
            "description" => __("Compatibility for Firefox4, Opera, and Chrome", "mk_framework") ,
            "dependency" => array(
                'element' => "host",
                'value' => array(
                    'self_hosted'
                )
            )
        ) ,
        array(
            "type" => "upload",
            "heading" => __("Video Preview image (and fallback image)", "mk_framework") ,
            "param_name" => "poster_image",
            "value" => "",
            "description" => __("This Image will shown until video load. in case of video is not supported or did not load the image will remain as fallback.", "mk_framework") ,
            "dependency" => array(
                'element' => "host",
                'value' => array(
                    'self_hosted'
                )
            )
        ) ,
        array(
            "type" => "textfield",
            "heading" => __("Video link", "mk_framework") ,
            "param_name" => "link",
            "value" => "",
            "description" => __('Link to the video. For YouTube HD videos add this snippet at the of a link "&vq=1080" (video quality set to 1080p). More about supported formats at <a href="http://codex.wordpress.org/Embeds#Okay.2C_So_What_Sites_Can_I_Embed_From.3F" target="_blank">WordPress codex page</a>.', "mk_framework"),
            "dependency" => array(
                'element' => "host",
                'value' => array(
                    'social_hosted'
                )
            )
        ) ,
        $add_css_animations,
        array(
            "type" => "textfield",
            "heading" => __("Extra class name", "mk_framework") ,
            "param_name" => "el_class",
            "value" => "",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your CSS file.", "mk_framework")
        )
    )
));
