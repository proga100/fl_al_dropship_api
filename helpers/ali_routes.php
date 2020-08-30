<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.flance.info
 * @since             1.0.0
 * @package           Flance_aliexpress_dropship
 *
 * @wordpress-plugin
 * Plugin Name:       Flance Aliexpress Dropship Api
 * Description:       Creates api for aliexpress.com
 *
 * The component uses  the Aliexpress official providers APIs.
 * Version:           1.0.0
 * Author:            Rusty
 * Author URI:        http://www.flance.info
 * Text Domain:       flance_aliexpress_dropship_api
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

class Ali_Api_Endpoints extends WP_REST_Controller {
    
    /**
     * Register the routes for the objects of the controller.
     */
    public function register_routes() {
        $version = '1';
        $namespace = 'Ali_Api_Endpoints/v' . $version;
        $base = 'route';
      
        register_rest_route( $namespace, '/' . $base . '/(?P<id>[\d]+)', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_item' ),
                'args'                => array(
                    'context' => array(
                        'default' => 'view',
                    ),
                ),
            )
        ) );
       
    }
    

    /**
     * Get one item from the collection
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function get_item( $request ) {
        //get parameters from request
        $params = $request->get_params();
        $item = array();//do a query, call another class, etc
        $data = $this->prepare_item_for_response( $item, $request );
        
        //return a response or error based on some conditional
        if ( 1 == 1 ) {
            return new WP_REST_Response( $data, 200 );
        } else {
            return new WP_Error( 'code', __( 'message', 'text-domain' ) );
        }
    }
    
  
    /**
     * Check if a given request has access to get a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function get_item_permissions_check( $request ) {
        return $this->get_items_permissions_check( $request );
    }
    
   
    /**
     * Prepare the item for create or update operation
     *
     * @param WP_REST_Request $request Request object
     * @return WP_Error|object $prepared_item
     */
    protected function prepare_item_for_database( $request ) {
        return array();
    }
    
    /**
     * Prepare the item for the REST response
     *
     * @param mixed $item WordPress representation of the item.
     * @param WP_REST_Request $request Request object.
     * @return mixed
     */
    public function prepare_item_for_response( $item, $request ) {
        return array();
    }
    
    /**
     * Get the query params for collections
     *
     * @return array
     */
    public function get_collection_params() {
        return array(
            'page'     => array(
                'description'       => 'Current page of the collection.',
                'type'              => 'integer',
                'default'           => 1,
                'sanitize_callback' => 'absint',
            ),
            'per_page' => array(
                'description'       => 'Maximum number of items to be returned in result set.',
                'type'              => 'integer',
                'default'           => 10,
                'sanitize_callback' => 'absint',
            ),
            'search'   => array(
                'description'       => 'Limit results to those matching a string.',
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
            ),
        );
    }
}






