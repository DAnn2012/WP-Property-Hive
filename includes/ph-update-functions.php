<?php
/**
 * PropertyHive Updates
 *
 * Functions for updating data during an update.
 *
 * @author      PropertyHive
 * @category    Core
 * @package     PropertyHive/Functions
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Update on_market_change_dates for 1.4.68
 *
 * @return void
 */
function propertyhive_update_1468_on_market_change_dates() {
    global $wpdb;

    $args = array(
        'post_type' => 'property',
        'fields' => 'ids',
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => '_on_market',
                'value' => 'yes'
            ),
            array(
                'key' => '_on_market_change_date',
                'compare' => 'NOT EXISTS'
            )
        ),
        'nopaging' => true,
        'suppress_filters' => true,
    );
    $property_query =  new WP_Query($args);

    if ( $property_query->have_posts() )
    {
        while ( $property_query->have_posts() )
        {
            $property_query->the_post();

            $date_post_written = get_the_date( 'Y-m-d H:i:s' );
            add_post_meta( get_the_ID(), '_on_market_change_date', $date_post_written );
        }
    }

    wp_reset_postdata();
}

/**
 * Update address_concatenated for 1.5.16
 *
 * @return void
 */
function propertyhive_update_1516_address_concatenated() {
    global $wpdb;

    $args = array(
        'post_type' => 'property',
        'fields' => 'ids',
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => '_address_concatenated',
                'compare' => 'NOT EXISTS'
            )
        ),
        'nopaging' => true,
        'suppress_filters' => true,
    );
    $property_query =  new WP_Query($args);

    if ( $property_query->have_posts() )
    {
        while ( $property_query->have_posts() )
        {
            $property_query->the_post();

            $property = new PH_Property( get_the_ID() );

            // Set field of concatenated address
            update_post_meta( get_the_ID(), '_address_concatenated', $property->get_formatted_full_address() );
        }
    }

    $args = array(
        'post_type' => 'contact',
        'fields' => 'ids',
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => '_address_concatenated',
                'compare' => 'NOT EXISTS'
            )
        ),
        'nopaging' => true,
        'suppress_filters' => true,
    );
    $contact_query =  new WP_Query($args);

    if ( $contact_query->have_posts() )
    {
        while ( $contact_query->have_posts() )
        {
            $contact_query->the_post();

            $contact = new PH_Contact( get_the_ID() );

            // Set field of concatenated address
            update_post_meta( get_the_ID(), '_address_concatenated', $contact->get_formatted_full_address() );
        }
    }

    wp_reset_postdata();
}