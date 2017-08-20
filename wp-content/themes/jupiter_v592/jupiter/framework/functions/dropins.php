<?php
/**
 * Manage dropins for the Jupiter framework.
 *
 * @author Dominique Mariano <dominique@artbees.net>
 *
 * @since 5.7
 */

/**
 * Returns dropins recognized by Jupiter.
 *
 * Simply add new entries to the dropins array below, for your relatively
 * "stand-alone" projects, instead of modifying the functions.php file every
 * now and then.
 *
 * @since 5.7
 *
 * @return array Key is file name. The value is an array, with the first value the
 * purpose of the drop-in and the second value the name of the constant that must be
 * true for the drop-in to be used, or true if no constant is required. The third
 * value is the array of files to load, in case "key file" cannot be found.
 */
function _mk_get_framework_dropins() {
    $dropins = array(
        'framework/header-builder/hb-load.php' => array(
            __( 'Easy to use drag-and-drop header builder for Artbees themes.' ),
            'ARTBEES_HEADER_BUILDER',
            array(
                'framework/header-builder/src/hb-load.php',
                'framework/header-builder/build/hb-load.php',
            )
        ),
    );

    return $dropins;
}

$dropins = _mk_get_framework_dropins();
foreach( $dropins as $dropin => $info ) {
    if ( file_exists( THEME_DIR . '/' . $dropin ) ) {
        /*
         * $info[1] can either refer to a contant name or be a Boolean value.
         *
         * Use Boolean true if no constant is required to enable the dropin.
         * Otherwise, pass the name of the constant that has to be defined in
         * functions.php
         */
        if ( defined( $info[1] ) || true === $info[1] ) {
            include_once( THEME_DIR . '/' . $dropin );
        }
    } elseif ( isset( $info[2] ) && is_array( $info[2] ) ) {
        $lookup = $info[2];

        foreach( $lookup as $fallback ) {
            if ( file_exists( THEME_DIR . '/' . $fallback ) ) {
                include_once( THEME_DIR . '/' . $fallback );
                break;
            }
        }
    }
}
