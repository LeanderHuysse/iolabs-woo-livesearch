<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.iolabs.nl
 * @since      1.0.0
 *
 * @package    Iolabs_Woo_Livesearch
 * @subpackage Iolabs_Woo_Livesearch/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Iolabs_Woo_Livesearch
 * @subpackage Iolabs_Woo_Livesearch/admin
 * @author     Leander Huysse <leander@iomedia.nl>
 */
class Iolabs_Woo_Livesearch_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     *
     * @param      string $plugin_name The name of this plugin.
     * @param      string $version     The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    /**
     *
     */
    public function buildProductCache()
    {
        $options = get_option('wools_searchfields');
        $args      = [
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_query' => [
                [
                    'key' => '_stock_status',
                    'value' => 'instock',
                ]
            ]
        ];
        $_products = query_posts($args);

        $_products = array_map(function ($item) use ($options) {
            $res = [];

            foreach($item as $key => $value) {
                if(in_array($key, $options)) {
                    $res[$key] = $value;
                }
            }
            $res['ID'] = $item->ID;

            $meta = get_post_meta($item->ID);

            foreach($meta as $key => $value) {
                if(in_array($key, $options)) {
                    $res[$key] = $value[0];
                }
            }

            return $res;
        }, $_products);

        $_products = json_encode($_products, true);
        $_file     = plugin_dir_path(__FILE__) . 'cache/productCache.json';

        if (file_put_contents($_file, $_products) !== false) {
            header("Location:" . $_POST['referrer'] . "&wools-cache-status=success");
        } else {
            header("Location:" . $_POST['referrer'] . "&wools-cache-status=fail");
        }
    }

    public function addMenuPage()
    {
        add_submenu_page('iolabs', 'Woo LiveSearch', 'Woo LiveSearch', 'manage_options', 'woo-livesearch', [&$this, 'renderMenuPage']);
    }

    public function renderMenuPage()
    {
        $options = get_option('wools_searchfields');
        $keys = $this->getOptions();
        include 'partials/iolabs-woo-livesearch-admin-display.php';
    }

    public function saveOptions()
    {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized user');
        }

        if(isset($_POST['keys'])) {
            update_option('wools_searchfields', $_POST['keys']);
        } else {
            update_option('wools_searchfields', []);
        }
        header("Location:" . $_POST['referrer'] . "&wools-options-status=success");
    }

    public function getOptions()
    {

        $args  = [
            'post_type'      => 'product',
            'posts_per_page' => 1,
        ];
        $query = new WP_Query($args);
        $post  = array_map(function ($item) {
            $meta = get_post_meta($item->ID);
            $meta = array_map(function ($i) {
                return $i[0];
            }, $meta);
            $arr  = (object)array_merge((array)$item, (array)$meta);

            return $arr;
        }, $query->posts);

        $keys = array_keys((array)$post[0]);

        return $keys;
    }

    public function flattenArray($array)
    {
        if (!is_array($array)) {
            return false;
        }
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = array_merge($result, $this->flattenArray($value));
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Iolabs_Woo_Livesearch_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Iolabs_Woo_Livesearch_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/iolabs-woo-livesearch-admin.css', [], $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Iolabs_Woo_Livesearch_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Iolabs_Woo_Livesearch_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/iolabs-woo-livesearch-admin.js', ['jquery'], $this->version,
            false);
    }

}
