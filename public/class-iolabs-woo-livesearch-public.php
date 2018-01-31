<?php

use Foolz\SphinxQL\Drivers\Mysqli;
use Foolz\SphinxQL\SphinxQL;

require_once(dirname(ABSPATH) . '/../params.php');

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.iolabs.nl
 * @since      1.0.0
 *
 * @package    Iolabs_Woo_Livesearch
 * @subpackage Iolabs_Woo_Livesearch/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Iolabs_Woo_Livesearch
 * @subpackage Iolabs_Woo_Livesearch/public
 * @author     Leander Huysse <leander@iomedia.nl>
 */
class Iolabs_Woo_Livesearch_Public {

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
     * @param      string $plugin_name The name of the plugin.
     * @param      string $version     The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->index_name = INDEX_NAME;
        $this->db_name = DB_NAME;
        $this->db_user = DB_USER;
        $this->db_pass = DB_PASSWORD;
        $this->db_host = DB_HOST;
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        add_action('wp_ajax_nopriv_list_items', [$this, 'getResults']);
        add_action('wp_ajax_list_items', [$this, 'getResults']);
    }

    /**
     * @param $name
     *
     * @return \mysqli
     */
    public function connect($name): \mysqli {

        $connect = new \mysqli();
        $connect->connect($this->db_host, $this->db_user, $this->db_pass);
        $connect->select_db($name);
        if (!$connect) {
            die('Couldn\'t connect to database');
        } else {
            return $connect;
        }
    }


    /**
     * @param string $term
     *
     * @return string
     */
    public function getSearchType(string $term): string {
        if(is_numeric($term)) {
            return 'sku';
        } else {
            return 'normal';
        }
    }

    /**
     *
     */
    public function getResults() {

        $searchTerm = strtolower(sanitize_text_field($_POST['searchTerm']));
        if(($cache = get_transient($searchTerm)) && IS_DEVELOPMENT !== true) {
            wp_send_json_success($cache);
        } else {
            if($this->getSearchType($searchTerm) === 'sku') {
                $result = $this->searchForSku($searchTerm);
            } else {
                $result = $this->searchForProduct($searchTerm);
            }

            if($result) {
                $count = $result['products']->num_rows;
                $products = $this->getProducts($result['products']->fetch_all(MYSQLI_ASSOC));

                $data = ['products' => $products, 'count' => $count];

                set_transient($searchTerm, $data, 300);
                wp_send_json_success($data);
            }
            wp_send_json_error();
        }
    }

    /**
     * @param $sku
     *
     * @return bool|mixed
     */
    public function searchForSku($sku) {
        $conn = $this->connect($this->index_name);
        $conn->set_charset("utf8");
        $query = "
            SELECT product_id, product_title, product_author, product_sku, product_price, product_content FROM product_index 
            WHERE 
            product_sku="
            .$sku." LIMIT 1
        ";
        $result = $conn->query($query);
        if($result->num_rows !== 0) {
            return $result;
        }
        return false;
    }

    /**
     * @param $term
     *
     * @return bool|mixed
     */
    public function searchForProduct($term, $offset = 0) {
        $conn = $this->connect($this->index_name);
        $conn->set_charset("utf8");
        $query = "
            SELECT SQL_CALC_FOUND_ROWS
                idx.product_id,
                MATCH(idx.product_title, idx.product_author, idx.product_content, idx.product_note, idx.product_heading,
                      idx.product_categories) against('{$term}' IN BOOLEAN MODE) AS relevance,
                MATCH(idx.product_author) against('{$term}*' IN BOOLEAN MODE)     AS author_relevance,
                MATCH(idx.product_categories) against('{$term}' IN BOOLEAN MODE) AS category_relevance
            FROM product_index AS idx
            WHERE MATCH(idx.product_title, idx.product_author, idx.product_content, idx.product_note, idx.product_heading,
                        idx.product_categories) against('{$term}*' IN BOOLEAN MODE)
            GROUP BY idx.product_id
            ORDER BY relevance + (author_relevance * 5) + (category_relevance * 3) DESC
            LIMIT {$offset}, 12
        ";

        $result = $conn->query($query);

        $count = $conn->query("SELECT FOUND_ROWS()");
        $count = $count->fetch_row();

        $data = [];
        $data['products'] = $result;
        $data['count'] = $count;

        if($result->num_rows !== 0) {
            return $data;
        }
        return false;
    }

    /**
     * @param     $result
     * @param int $count
     *
     * @return array
     */
    public function getProducts($result): array {
        $products = array_map(
            function ($product) {
                $post = get_post($product['product_id']);
                $meta = get_post_meta($product['product_id']);

                $m = [];
                foreach($meta as $key => $value) {
                    $m[$key] = $value[0];
                }
                $post->custom = $m;
                $post->author_name   = truncate($meta['author_name'][0], '40');
                $post->permalink     = get_permalink($product['product_id']);
                $post->image         = file_get_contents("https://cdn.iolabs.nl/image/" . ltrim
                    ($meta['_sku'][0], '0') . "/featured/150");
                $post->post_title    = truncate($post->post_title, '60');
                return $post;
            }, $result
        );

        return $products;
    }


    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

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
        $now = time('s');
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/iolabs-woo-livesearch-public.css?v=' . $now, [], $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

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
        wp_enqueue_script(
            'underscorejs', 'https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js', [],
            $this->version, true
        );

        wp_register_script(
            $this->plugin_name, plugin_dir_url(__FILE__) . 'js/iolabs-woo-livesearch-public.js', ['jquery', 'underscorejs'],
            $this->version, true
        );
        wp_localize_script(
            $this->plugin_name, 'ioWooSearch', [
            'url'       => plugin_dir_url(dirname(__FILE__)),
            'ajax_root' => admin_url('admin-ajax.php'),
        ]
        );
        wp_enqueue_script($this->plugin_name);
    }

}
