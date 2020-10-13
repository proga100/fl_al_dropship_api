<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.flance.info
 * @since      1.1.4
 *
 * @package    fl_api_aliexpress_dropship Pro
 * @subpackage fl_api_aliexpress_dropship/admin
 */

use AliexApi\Tests\AliexIOTest;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    fl_api_aliexpress_dropship
 * @subpackage fl_api_aliexpress_dropship/admin
 * @author     Rusty <tutyou1972@gmail.com>
 */
class fl_api_aliexpress_dropship
{

    /**
     * The ID of this plugin.
     *
     * @since    1.1.4
     * @access   private
     * @var      string $Flance_wamp The ID of this plugin.
     */
    private $Flance_wamp;

    /**
     * The version of this plugin.
     *
     * @since    1.1.4
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $Flance_wamp The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.1.4
     */
    public function __construct()
    {
        add_filter('fl_api_import_aliexpress_product_search_results', array($this, 'fl_api_import_aliexpress_product_search_results'));
    }

    public static function fl_api_aliexpress_categories()
    {

        $affiliate_cat_id = $_REQUEST['affiliate_cat_id'];
        if (empty($affiliate_cat_id)) $affiliate_cat_id = $_SESSION['affiliate_cat_id'];
        $file = __DIR__ . "/ali_categories.json";
        if (file_exists(__DIR__ . "/ali_categories.json")) {
            $string = file_get_contents($file);

            $json_a = json_decode($string, true);
            $html = '<select name="affiliate_cat_id">';

            foreach ($json_a["categories"] as $categorie) {
                if ($affiliate_cat_id == $categorie["id"]) {
                    $selected = 'selected';
                } else {
                    $selected = '';
                }

                if ($categorie["level"] == 1) {
                    $html .= '<option ' . $selected . '  value="' . $categorie["id"] . '">' . $categorie["name"] . '</option>';
                } elseif ($categorie["level"] > 1) {

                    $html .= '<option ' . $selected . '  value="' . $categorie["id"] . '">-' . $categorie["name"] . '</option>';
                }
            }
            $html .= '</select>';

            return $html;
        }
    }

    public static function fl_api_amp_admin_settings_get_product_cats()
    {
        $product_cat = get_terms('product_cat', 'hide_empty=0');
        $product_cat_option = (array)get_option('flance_amp_product_cat');
        if (in_array(-1, $product_cat_option) || empty($product_cat_option)):
            echo '<option value="-1" selected>All Products</option>';
            foreach ($product_cat as $product_cat_key => $product_cat_value) {
                echo '<option value=' . $product_cat_value->term_id . '>' . $product_cat_value->name . '</option>';
            }
        else:
            echo '<option value="-1">All Products</option>';
            foreach ($product_cat as $product_cat_key => $product_cat_value) {
                if (in_array($product_cat_value->term_id, $product_cat_option)) {
                    echo '<option value=' . $product_cat_value->term_id . ' selected>' . $product_cat_value->name . '</option>';
                } else {
                    echo '<option value=' . $product_cat_value->term_id . '>' . $product_cat_value->name . '</option>';
                }
            }
        endif;
    }

    // Admin Menu Page Calling function.

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.1.4
     */
    public function enqueue_styles()
    {

        wp_enqueue_style('products-admin', plugin_dir_url(__FILE__) . 'css/fl-api-add-multiple-products-admin.css', array(), $this->version, 'all', true);
    }

    // Admin Setting Registration

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.1.4
     */
    public function enqueue_scripts()
    {

        $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
        // wp_enqueue_script('woocommerce-chosen-js', $woocommerce->plugin_url() . '/assets/js/select2/select2' . $suffix . '.js', array('jquery'), null, true);

    }

    // Admin Settings Page Function

    public function fl_api_amp_admin_menu_page()
    {

        //create new top-level menu
        add_menu_page(
            'Flance Aliexpress Dropship Pro Settings',
            'Flance Aliexpress Dropdship Dashboard',
            'administrator',
            'fl-api-add-aliexpress-dropship',
            array($this, 'flance_amp_admin_dashboard_page'),
            'dashicons-cart',
            10
        );
        //call register settings public function
        add_action('admin_init', array($this, 'register_fl_api_amp_settings'));
        add_submenu_page(
            'fl-api-add-aliexpress-dropship',
            'Aliexpress Product Search',
            'Aliexpress Product Search',
            'administrator',
            'fl-api-amp-admin-search-page',
            array($this, 'fl_api_import_aliexpress_product_search'),

            15);

        add_submenu_page(
            'fl-api-add-aliexpress-dropship',
            'Aliexpress Import list',
            'Import list',
            'administrator',
            'fl-api-import-aliexpress-dropship',
            array($this, 'fl_api_import_aliexpress_dropship'),

            20);
        add_submenu_page(
            'fl-api-add-aliexpress-dropship',
            'Settings',
            'Settings',
            'administrator',
            'fl-api-amp-admin-settings-page',
            array($this, 'flance_amp_admin_settings_page'),
            30);
    }

    // Admin Dashboard Page Function

    public function register_fl_api_amp_settings()
    {
        //register our settings
        register_setting('fl-api-amp-settings-group', 'flance_amp_product_cat');
        register_setting('fl-api-amp-settings-group', 'aliexpress_key');
        register_setting('fl-api-amp-settings-group', 'tracking_id');
        register_setting('fl-api-amp-settings-group', 'language');
        register_setting('fl-api-amp-settings-group', 'currency');
    }

    public function fl_api_import_aliexpress_product_search()
    {
        $res = $this->fl_api_import_aliexpress_product_search_input_values();
        if ($_GET['task'] == 'search' || $_GET['task'] == '') {
            $results = $this->fl_api_import_aliexpress_product_search_results($res);

            //  echo "<pre>";        print_r ($results);exit;

            $product_id = $res['product_id'];
            if ($product_id == '') {
                foreach ($results->result->products as $product) {

                    $ids[] = $product->productId; // get aliexpress product ids from results

                }
            } elseif ($product_id != '') {
                $ids[] = $results->result->productId;
                $results->result->products[] = $results->result;
            }

            $data = get_results_ae($ids);

            $total_results = $results->result->totalResults;
        }

        if ($_GET['task'] == 'import_add') {
            $pks = $_POST['cid'];

            $message = json_encode(array('message' => $pks, 'result' => 1));

            echo $message;
            exit;
        }
    }

    // Admin Import list  Page Function

    public function fl_api_import_aliexpress_product_search_input_values()
    {

        $var['keyword'] = $_GET['keyword'];
        $var['min_price'] = $_GET['min_price'];
        $var['max_price'] = $_GET['max_price'];
        $var['min_score'] = $_GET['min_score'];
        $var['woo_cat'] = $_GET['woo_cat'];
        $var['affiliate_cat_id'] = $_GET['affiliate_cat_id'];
        $var['product_id'] = $_REQUEST['product_id'];
        $var['limit'] = $_REQUEST['limit'];
        $var['limitstart'] = $_REQUEST['limitstart'];

        return $var;
    }

    public function fl_api_import_aliexpress_product_search_results($res)
    {
        if (!empty($res['directionTable'])) {
            $sort = $res['directionTable'];
            if ($sort == 'asc') $sort = 'orignalPriceUp';
            if ($sort == 'desc') $sort = 'orignalPriceDown';
        } else {

            $sort = NUll;
        }


        $keyword = $res['keyword'];
        $product_id = $res['product_id'];

        if (!empty($res['limitstart'])) {
            $pageNo = $res['limitstart'];
        } else {

            $pageNo = 1;
        }
        $currency = $res['vir_currency'];

        $endCreditScore = $res['max_score'];
        $startCreditScore = $res['min_score'];
        $originalPriceFrom = $res['min_price'];
        $originalPriceTo = $res['max_price'];

        if (!empty($res['limit'])) {
            $pageSize = $res['limit'];
        } else {

            $res['limit'] = $pageSize = 5;
        }

        $category_id = $res['affiliate_cat_id'];

        $comparams['ali_api'] = get_option('aliexpress_key');
        $comparams['tracking_id'] = get_option('tracking_id');

        $Ali = new AliexIOTest;
        $Ali->comparams = $comparams;
        if ($product_id == '') $aliexpress_json = $Ali->testAliexIO($keyword, $pageNo, $pageSize, $sort, $originalPriceFrom, $originalPriceTo, $startCreditScore, $endCreditScore, $currency, $category_id);
        if ($product_id != '') $aliexpress_json = $Ali->testGetProductDetail($product_id, $currency);
        $data = json_decode($aliexpress_json);
        return $data;
    }

    public function escape($val)
    {
        return $val;
    }
}
