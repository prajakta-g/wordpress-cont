<?php
//test
class Wpi_Post_Init {

    public function __construct() {


        add_action('add_meta_boxes', array($this, 'wpi_post_options_add'));
        add_action('save_post', array($this, 'wpi_post_options_save'));
        add_action('wp_enqueue_scripts', array($this, 'wpi_register_plugin_styles'));
        add_filter('the_content', array($this, 'wpi_post_display_contributors'));
    }

    function wpi_post_options_add() {
        add_meta_box('wpi_post-options', 'Contributors', array($this, 'wpi_call_to_post_options'), 'post', 'normal', 'high');
    }

    function wpi_call_to_post_options($post) {

        $custom_meta = get_post_meta($post->ID, '_custom-meta-box', true);
        if (empty($custom_meta)) {
            $custom_meta = array();
        }
        $blogusers = get_users('role=author');
        foreach ($blogusers as $user) {
            ?>
            <br/><input type="checkbox" name="custom-meta-box[]" value="<?php echo $user->ID ?>" <?php echo (in_array("$user->ID", $custom_meta)) ? 'checked="checked"' : ''; ?> />
            <?php
            echo $user->display_name;
        }
    }

    function wpi_post_options_save() {

        global $post;
        // Get our form field
        if (isset($_POST['custom-meta-box'])) {
            $custom = $_POST['custom-meta-box'];
            $old_meta = get_post_meta($post->ID, '_custom-meta-box', true);
            // Update post meta
            if (!empty($old_meta)) {
                update_post_meta($post->ID, '_custom-meta-box', $custom);
            } else {
                add_post_meta($post->ID, '_custom-meta-box', $custom, true);
            }
        }

        if (empty($_POST['custom-meta-box'])) {
            delete_post_meta($post->ID, '_custom-meta-box');
        }
    }

    function wpi_post_display_contributors($content) {
        global $post;
        $custom_meta = get_post_meta($post->ID, '_custom-meta-box', true);
        if (!empty($custom_meta)) {
            $author.= "<div class='contributorbox' ><div class='contributorlabel' >Contributors</div>";
            foreach ($custom_meta as $value) {
                $author.= "<div class='twocol'>";
                $author.= "<a href=\"" . get_bloginfo('url') . "/?author=";
                $author.= $value;
                $author.= "\">";
                $author.= get_avatar($value);
                $author.= "</a>";
                $author.= '<div>';
                $author.= "<a href=\"" . get_bloginfo('url') . "/?author=";
                $author.= $value;
                $author.= "\">";
                $author.=get_the_author_meta('display_name', $value);
                $author.= "</a>";
                $author.= "</div>";
                $author.= "</div>";
            }
            $author.= "</div>";
            $content = $content . $author;
        }

        return $content;
    }

    public function wpi_register_plugin_styles() {
        wp_register_style('wordpress-contributors', plugins_url('wordpress-contributors/css/authorstyle.css'));
        wp_enqueue_style('wordpress-contributors');
    }

}
?>
