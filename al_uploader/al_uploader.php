<?php

/*
Plugin Name: Alkoweb meta box uploader
Plugin URI: http://alkoweb.ru
Author: Petrozavodsky
Author URI: http://alkoweb.ru
Version: 1.0.0
*/

class  al_uploader
{
    private $this_file;
    private $form_attr_name;
    private $post_types = array();

    function __construct($file)
    {
        $this->post_types = apply_filters('al_uploader_post_types', array('post'));
        $this->this_file = $file;
        $this->form_attr_name = '_al_uploader_upload_field_';
        add_action('admin_init', array($this, 'add_extra_fields'), 1);
        add_action('save_post', array($this, 'fields_update'), 0);
        add_action('admin_enqueue_scripts', array($this, 'upload_script'));
    }

    function upload_script()
    {
        if (!did_action('wp_enqueue_media')) {
            wp_enqueue_media();
        }
        wp_enqueue_script('al_uploader_scripts', plugin_dir_url($this->this_file) . 'admin/js/scripts.js', array('jquery'), null, false);
    }

    function add_extra_fields()
    {
        add_meta_box('al_uploader', 'Загрузка файла', array($this, 'fields_html'), array('post'), 'side', 'low');
    }

    function fields_html($post)
    {
        $field_name='al_thumbnail';

        $post_data = get_post_meta($post->ID, 'al_thumbnail', true);
        ?>
        <p>
            <?php
            if ($post_data == '') {
                echo "<a href='#al_uploader_anchor' data-text='Задать другую миниатюру'>Задать миниатюру</a>";
            } else {
                echo "<a href='#al_uploader_anchor' data-text='Задать миниатюру'>Задать новую миниатюру</a>";
            }
            ?>
        </p>

        <p>
            <input type="text" name="<?php echo $this->form_attr_name; ?>[<?php echo $field_name;?>]"
                   value="<?php echo $post_data[$field_name]; ?>" style="display: none"/>
                <span class="al_uploader-img-wrap">
                    <?php if ($post_data !== '') {
                        $src = wp_get_attachment_image_url($post_data, array(300, 300));
                        echo "<img src='{$src}' style='display: block; width: 100%; height: auto'>";
                    }
                    ?>
                </span>
        </p>

        <input type="hidden" name="<?php echo $this->form_attr_name; ?>_fields_nonce"
               value="<?php echo wp_create_nonce($this->this_file); ?>"/>
        <?php
    }

    function fields_update($post_id)
    {
        if (!wp_verify_nonce($_POST[$this->form_attr_name . '_fields_nonce'], $this->this_file)) {
            return false;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return false;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return false;
        }
        if (!isset($_POST[$this->form_attr_name])) {
            return false;
        };

        $items = array_map('trim', $_POST[$this->form_attr_name]);

        foreach ($items as $key=>$val){
            update_post_meta($post_id, $key, $val);
        }

        return $post_id;
    }
}

function al_uploader_init()
{
    new al_uploader(__FILE__);
}

add_action('plugins_loaded', 'al_uploader_init');