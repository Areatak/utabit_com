<?php
if (!defined('THEME_FRAMEWORK')) exit('No direct script access allowed');

/**
 * Helper functions for get views part of the template
 *
 * @author      Bob Ulusoy
 * @copyright   Artbees LTD (c)
 * @link        http://artbees.net
 * @since       Version 5.1
 * @package     artbees
 */



/**
 * Get template parts from views folder
 * @param string    $slug
 * @param string    $name
 * @param boolean   $return
 * @return object
 *
 */
if (!function_exists('mk_get_view')) {
    function mk_get_view($slug, $name = '', $return = false, $view_params = array()) {
        if ($return) {
            ob_start();
            mk_get_template_part('views/' . $slug . '/' . $name, $view_params);
            return ob_get_clean();
        } 
        else {
            mk_get_template_part('views/' . $slug . '/' . $name, $view_params);
        }
    }
}

/**
 * Get header components and put them together. this function passes variables into the file too.
 * @param string    $slug
 * @param string    $name
 * @param boolean   $return
 * @return object
 *
 */
if (!function_exists('mk_get_header_view')) {
    function mk_get_header_view($location, $component, $view_params = array() , $return = false) {
        if ($return) {
            ob_start();
            mk_get_template_part('views/header/' . $location . '/' . $component, $view_params);
            return ob_get_clean();
        } 
        else {
            mk_get_template_part('views/header/' . $location . '/' . $component, $view_params);
        }
    }
}

/**
 * Get template parts from shortcodes folder
 * @param string    $slug
 * @param string    $name
 * @param boolean   $return
 * @return object
 *
 */
if (!function_exists('mk_get_shortcode_view')) {
    function mk_get_shortcode_view($shortcode_name, $name = '', $return = false, $view_params = array()) {
        if ($return) {
            ob_start();
            mk_get_template_part('components/shortcodes/' . $shortcode_name . '/' . $name, $view_params);
            return ob_get_clean();
        } 
        else {
            mk_get_template_part('components/shortcodes/' . $shortcode_name . '/' . $name, $view_params);
        }
    }
}

/**
 * Get template parts from Control Panel
 * @param string    $slug
 * @param string    $name
 * @param boolean   $return
 * @return object
 *
 */
if (!function_exists('mk_get_control_panel_view')) {
    function mk_get_control_panel_view($name = '', $return = false, $view_params = array()) {
        if ($return) {
            ob_start();
            mk_get_template_part('framework/admin/control-panel/views/' . $name, $view_params);
            return ob_get_clean();
        } 
        else {
            mk_get_template_part('framework/admin/control-panel/views/' . $name, $view_params);
        }
    }
}

/**
 * Like get_template_part() put lets you pass args to the template file
 * Args are available in the tempalte as $view_params array
 * @param string filepart
 * @param mixed wp_args style argument list
 *
 * @since 5.0.0
 * @since 5.9.1 Refactored the function to improve performance.
 */
if ( ! function_exists( 'mk_get_template_part' ) ) {
    function mk_get_template_part( $file, $view_params = array() ) {  
        global $post;
        
        // security check for LFI
        if ( is_numeric( strpos( $file,".." ) ) or is_numeric( strpos( $file,"./" ) ) ) {
            // if someone tries to reach parent directories
            echo "Cheating hah?";
            die;
        }

        $child_theme_path = get_stylesheet_directory() . '/' . $file . '.php';
        $parent_theme_path = get_template_directory() . '/' . $file . '.php';

        if ( is_child_theme() ) {
            if ( file_exists( $child_theme_path ) ) {
                $file = $child_theme_path;
            } else {
                $file = $parent_theme_path;
            }
        } else {
            $file = $parent_theme_path;
        }


       $view_params = wp_parse_args( $view_params );

       if ( file_exists( $file ) ) {
            include( $file );
       } else {
            echo "{$file} Does not exist";
       }
    }
}
