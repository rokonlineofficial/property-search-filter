<?php
/**
 * Plugin Name: Property Search Filter By Rok Online
 * Description: A custom search filter for real estate properties.
 * Version: 0.1
 * Author: Rok Online
 * Author URI: https://rokonline.com/
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function psf_search_form_shortcode() {
    ob_start(); 
    
    $current_location  = isset($_GET['prop_loc']) ? esc_attr($_GET['prop_loc']) : '';
    $current_type      = isset($_GET['prop_type']) ? esc_attr($_GET['prop_type']) : '';
    $current_max_price = isset($_GET['max_price']) ? esc_attr($_GET['max_price']) : '';
    
    $chevron_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); color: rgba(255, 255, 255, 0.4); pointer-events: none;"><path d="m6 9 6 6 6-6"></path></svg>';
    $search_icon  = '<svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></svg>';
    ?>
    
    <form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="GET" class="property-search-form">
        <input type="hidden" name="post_type" value="property"> 
        
        <style>
            .property-search-form { display: flex; flex-direction: column; gap: 15px; width: 100%; padding: 0; background: transparent; }
            .filter-field { position: relative; width: 100%; }
            .filter-field svg.left-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #d4af37; width: 20px; height: 20px; pointer-events: none; z-index: 2; }
            
            .property-search-form select, 
            .property-search-form input { 
                width: 100%; 
                padding: 15px 40px 15px 45px; 
                border-radius: 12px; 
                border: 1px solid rgba(255, 255, 255, 0.1); 
                background: rgba(255, 255, 255, 0.05); 
                color: #fff; 
                font-size: 14px; 
                outline: none; 
                box-sizing: border-box; 
                appearance: none; 
            }
            
            /* Dropdown background fix */
            .property-search-form select option { background: #1a1a1a; color: #fff; }

            /* Remove arrows from number input */
            input[type=number]::-webkit-inner-spin-button, 
            input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
            input[type=number] { -moz-appearance: textfield; }

            /* Button Styles */
            .filter-submit-btn { 
                width: 100%; 
                padding: 15px; 
                background: #d4af37; 
                color: #000; 
                border: none; 
                border-radius: 12px; 
                font-weight: 700; 
                cursor: pointer; 
                transition: 0.3s; 
                display: flex; 
                align-items: center; 
                justify-content: center; 
                gap: 8px; 
            }
            .filter-submit-btn:hover { background: #f3cd46; transform: translateY(-3px); }
        </style>

        <div class="filter-field">
            <svg class="left-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
            <?php 
            wp_dropdown_categories(array(
                'show_option_all' => 'Location (City, ZIP, Neighborhood)',
                'taxonomy'        => 'property-location',
                'name'            => 'prop_loc',
                'value_field'     => 'slug',
                'selected'        => $current_location
            )); 
            echo $chevron_icon;
            ?>
        </div>

        <div class="filter-field">
            <svg class="left-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
            <?php 
            wp_dropdown_categories(array(
                'show_option_all' => 'Property Type',
                'taxonomy'        => 'property-type',
                'name'            => 'prop_type',
                'value_field'     => 'slug',
                'selected'        => $current_type
            )); 
            echo $chevron_icon;
            ?>
        </div>

        <div class="filter-field">
            <svg class="left-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            <input type="number" name="max_price" placeholder="Budget Range" value="<?php echo $current_max_price; ?>">
        </div>

        <button type="submit" class="filter-submit-btn">
            <?php echo $search_icon; ?>
            Search Properties
        </button>

        <a href="<?php echo get_post_type_archive_link('property'); ?>" style="display: block; text-align: center; margin-top: 10px; color: #d4af37; text-decoration: none; font-size: 13px;">
            Reset Filters
        </a>

    </form>
    
    <?php
    return ob_get_clean();
}
add_shortcode('property_search_filter', 'psf_search_form_shortcode');

function psf_filter_property_archive_query( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) return;

    if ( $query->is_post_type_archive( 'property' ) || ( isset($_GET['post_type']) && $_GET['post_type'] == 'property' ) ) {
        $meta_query = array('relation' => 'AND');
        $tax_query  = array('relation' => 'AND');

        if ( ! empty( $_GET['prop_loc'] ) ) {
            $tax_query[] = array('taxonomy' => 'property-location', 'field' => 'slug', 'terms' => sanitize_text_field($_GET['prop_loc']));
        }
        if ( ! empty( $_GET['prop_type'] ) ) {
            $tax_query[] = array('taxonomy' => 'property-type', 'field' => 'slug', 'terms' => sanitize_text_field($_GET['prop_type']));
        }
        if ( ! empty( $_GET['max_price'] ) ) {
            $meta_query[] = array('key' => 'price', 'value' => intval($_GET['max_price']), 'compare' => '<=', 'type' => 'NUMERIC');
        }

        if ( count( $tax_query ) > 1 ) $query->set( 'tax_query', $tax_query );
        if ( count( $meta_query ) > 1 ) $query->set( 'meta_query', $meta_query );
    }
}
add_action( 'pre_get_posts', 'psf_filter_property_archive_query' );