<?php
/**
 * Plugin Name: Pyrfekt Cat
 * Description: Change easly the order of displaying posts' categories to show the Yoast's primary category as first. No configuration needed.
 * Version: 1.0
 * Author: Mateusz Kozłowski
 * Author URI: http://pyrfekt.com/
 * License: GPL2
 */

if ( ! function_exists( 'is_plugin_active' ) )
     require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

if ( is_plugin_active( 'wordpress-seo/wp-seo.php' ) || is_plugin_active( 'wordpress-seo-premium/wp-seo-premium.php' ) ) {}
else
{add_action( 'admin_notices', 'yoast_notice' );}

function yoast_notice() {
  ?>
  <div class="notice notice-warning is-dismissible">
      <p><?php _e( 'Please install Yoast SEO, it is required for Pyrfect Cat to work properly!', 'my_plugin_textdomain' ); ?></p>
  </div>
  <?php
}

function pyrfect_cat($categories) {
    
    // Check if yoast exists and get the primary category
    if ($categories && class_exists('WPSEO_Primary_Term') ) {

        // Show the post's 'Primary' category, if this Yoast feature is available, & one is set
        $wpseo_primary_term = new WPSEO_Primary_Term( 'category', get_the_id() );
        $wpseo_primary_term = $wpseo_primary_term->get_primary_term();
        $term = get_term( $wpseo_primary_term );
    
        // If no error is returned, get primary yoast term 
        $primary_cat_term_id = (!is_wp_error($term)) ? $term->term_id : null;

        // Loop all categories
        if($primary_cat_term_id !== null) {
            foreach ($categories as $i => $category) {

                // Move the primary category to the top of the array
                if($category->term_id === $primary_cat_term_id) {

                    $out = array_splice($categories, $i, 1);
                    array_splice($categories, 0, 0, $out);

                    break;
                }
            }
        }
    } 
    return $categories;
}
add_filter( 'get_the_categories', 'pyrfect_cat' );


?>