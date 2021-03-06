<?php

class Wpmf_duplicate_file {

    function __construct() {
        add_action('wp_enqueue_media', array($this, "wpmf_enqueue_admin_scripts"));
        add_action('wp_ajax_wpmf_duplicate_file', array($this, 'wpmf_duplicate_file'));
    }
    
    /* includes styles and some scripts */
    function wpmf_enqueue_admin_scripts() {
        wp_enqueue_script('duplicate-image');
        wp_enqueue_style('duplicate-style', plugins_url('assets/css/style_duplicate_file.css', dirname(__FILE__)), array(), WPMF_VERSION);
    }
    
    /* Ajax duplicate attachment */
    public function wpmf_duplicate_file() {
        if (isset($_POST['id'])) {
            $post = get_post($_POST['id']);
            if (empty($post))
                wp_send_json(array('status' => false, 'message' => __('This post is not exists', 'wpmf')));
            $terms_parent = wp_get_object_terms( $post->ID , WPMF_TAXO, array('orderby' => 'name', 'order' => 'ASC', 'fields' => 'ids') );
            $alt_post = get_post_meta($_POST['id'], '_wp_attachment_image_alt',true);
            $file_path = get_attached_file($_POST['id']);
            if (!file_exists($file_path))
                wp_send_json(array('status' => false, 'message' => __('File is not exists', 'wpmf')));
            $infos_url = pathinfo($post->guid);
            $mime_type = get_post_mime_type($_POST['id']);
            $infos_path = pathinfo($file_path);
            $name = $infos_path['basename'];
            $content = @file_get_contents($file_path);
            $filename = wp_unique_filename($infos_path['dirname'], $name);
            $upload = file_put_contents($infos_path['dirname'] . '/' . $filename, $content);
            if ($upload) {
                $attachment = array(
                    'guid' => $infos_url['dirname'] . '/' . $filename,
                    'post_mime_type' => $mime_type,
                    'post_title' => $post->post_title,
                    'post_content' => $post->post_content,
                    'post_excerpt' => $post->post_excerpt,
                    'post_status' => 'inherit'
                );

                // insert attachment
                $attach_id = wp_insert_attachment($attachment, $infos_path['dirname'] . '/' . $filename);
                $attach_data = wp_generate_attachment_metadata($attach_id, $infos_path['dirname'] . '/' . $filename);
                wp_update_attachment_metadata($attach_id, $attach_data);
                update_post_meta($attach_id, '_wp_attachment_image_alt', $alt_post);

                // set term
                if (!empty($terms_parent)) {
                    foreach ($terms_parent as $term_id) {
                        wp_set_object_terms($attach_id, $term_id, WPMF_TAXO, true);
                    }
                }
                wp_send_json(array('status' => true, 'message' => __('Duplicated file ', 'wpmf') . $name));
            }
            wp_send_json(array('status' => false, 'message' => __('Error duplicated file', 'wpmf')));
        }
    }

}
