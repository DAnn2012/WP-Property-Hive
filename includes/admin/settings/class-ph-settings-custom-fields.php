<?php
/**
 * PropertyHive Custom Fields Settings
 *
 * @author      PropertyHive
 * @category    Admin
 * @package     PropertyHive/Admin
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'PH_Settings_Custom_Fields' ) ) :

/**
 * PH_Settings_General
 */
class PH_Settings_Custom_Fields extends PH_Settings_Page {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->id    = 'customfields';
        $this->label = __( 'Custom Fields', 'propertyhive' );

        add_filter( 'propertyhive_settings_tabs_array', array( $this, 'add_settings_page' ), 15 );
        add_action( 'propertyhive_sections_' . $this->id, array( $this, 'output_sections' ) );
        add_action( 'propertyhive_settings_' . $this->id, array( $this, 'output' ) );
        add_action( 'propertyhive_settings_save_' . $this->id, array( $this, 'save' ) );
    }
    
    /**
     * Get sections
     *
     * @return array
     */
    public function get_sections() {
        $sections = array(
            ''         => __( 'Custom Fields', 'propertyhive' )
        );
        
        // Residential Custom Fields
        $sections[ 'availability' ] = __( 'Availabilities', 'propertyhive' );
        add_action( 'propertyhive_admin_field_custom_fields_availability', array( $this, 'custom_fields_availability_setting' ) );

        $sections[ 'property-type' ] = __( 'Property Types', 'propertyhive' );
        add_action( 'propertyhive_admin_field_custom_fields_property_type', array( $this, 'custom_fields_property_type_setting' ) );
        
        $sections[ 'location' ] = __( 'Locations', 'propertyhive' );
        add_action( 'propertyhive_admin_field_custom_fields_location', array( $this, 'custom_fields_location_setting' ) );
        
        $sections[ 'parking' ] = __( 'Parking', 'propertyhive' );
        add_action( 'propertyhive_admin_field_custom_fields_parking', array( $this, 'custom_fields_parking_setting' ) );
        
        $sections[ 'outside-space' ] = __( 'Outside Spaces', 'propertyhive' );
        add_action( 'propertyhive_admin_field_custom_fields_outside_space', array( $this, 'custom_fields_outside_space_setting' ) );
        
        // Residential Sales Custom Fields
        $sections[ 'price-qualifier' ] = __( 'Price Qualifiers', 'propertyhive' );
        add_action( 'propertyhive_admin_field_custom_fields_price_qualifier', array( $this, 'custom_fields_price_qualifier_setting' ) );
        
        $sections[ 'sale-by' ] = __( 'Sale By', 'propertyhive' );
        add_action( 'propertyhive_admin_field_custom_fields_sale_by', array( $this, 'custom_fields_sale_by_setting' ) );
        
        $sections[ 'tenure' ] = __( 'Tenures', 'propertyhive' );
        add_action( 'propertyhive_admin_field_custom_fields_tenure', array( $this, 'custom_fields_tenure_setting' ) );
        
        // Residential Lettings Custom Fields
        $sections[ 'furnished' ] = __( 'Furnished', 'propertyhive' );
        add_action( 'propertyhive_admin_field_custom_fields_furnished', array( $this, 'custom_fields_furnished_setting' ) );

        return $sections;
    }
    
    /**
     * Get settings array
     *
     * @return array
     */
    public function get_settings() {

        global $hide_save_button;
        
        $hide_save_button = true;

        $html = '';
        
        $sections = $this->get_sections();
        
        //$sections = array_shift($sections); // Remove 'Custom Fields' from list of custom fields sections
        $i = 0;
        foreach ($sections as $key => $value)
        {
            if ($i > 0)
            {
                $html .= '<p><a href="' . admin_url( 'admin.php?page=ph-settings&tab=customfields&section=' . $key ) . '">' . $value . '</a></p>';
            }
            ++$i;
        }

        return apply_filters( 'propertyhive_custom_fields_settings', array(

            array( 'title' => __( 'Custom Fields', 'propertyhive' ), 'type' => 'title', 'desc' => '', 'id' => 'custom_field_options' ),
            
            array(
                'type'      => 'html',
                'title'     => __( 'Custom Fields', 'propertyhive' ),
                'html'      => $html
            ),
            
            array( 'type' => 'sectionend', 'id' => 'custom_field_options'),
            
        ));
    }

    /**
     * Output the settings
     */
    public function output() {
        global $current_section;

        if ( $current_section ) {
            
            if (isset($_REQUEST['id'])) // we're either adding or editing
            {
                $current_id = empty( $_REQUEST['id'] ) ? '' : sanitize_title( $_REQUEST['id'] );
                
                switch ($current_section)
                {
                    case "availability": { $settings = $this->get_custom_fields_availability_setting(); break; }
                    case "availability-delete": { $settings = $this->get_custom_fields_delete($current_id, 'availability', __( 'Availability', 'propertyhive' )); break; }
                    case "property-type": { $settings = $this->get_custom_fields_property_type_setting(); break; }
                    case "property-type-delete": { $settings = $this->get_custom_fields_delete($current_id, 'property_type', __( 'Property Type', 'propertyhive' )); break; }
                    case "location": { $settings = $this->get_custom_fields_location_setting(); break; }
                    case "location-delete": { $settings = $this->get_custom_fields_delete($current_id, 'location', __( 'Location', 'propertyhive' )); break; }
                    case "parking": { $settings = $this->get_custom_fields_parking_setting(); break; }
                    case "parking-delete": { $settings = $this->get_custom_fields_delete($current_id, 'parking', __( 'Parking', 'propertyhive' )); break; }
                    case "outside-space": { $settings = $this->get_custom_fields_outside_space_setting(); break; }
                    case "outside-space-delete": { $settings = $this->get_custom_fields_delete($current_id, 'outside_space', __( 'Outside Space', 'propertyhive' )); break; }
                    
                    case "price-qualifier": { $settings = $this->get_custom_fields_price_qualifier_setting(); break; }
                    case "price-qualifier-delete": { $settings = $this->get_custom_fields_delete($current_id, 'price_qualifier', __( 'Price Qualifier', 'propertyhive' )); break; }
                    case "sale-by": { $settings = $this->get_custom_fields_sale_by_setting(); break; }
                    case "sale-by-delete": { $settings = $this->get_custom_fields_delete($current_id, 'sale_by', __( 'Sale By', 'propertyhive' )); break; }
                    case "tenure": { $settings = $this->get_custom_fields_tenure_setting(); break; }
                    case "tenure-delete": { $settings = $this->get_custom_fields_delete($current_id, 'tenure', __( 'Tenure', 'propertyhive' )); break; }
                    
                    case "furnished": { $settings = $this->get_custom_fields_furnished_setting(); break; }
                    case "furnished-delete": { $settings = $this->get_custom_fields_delete($current_id, 'furnished', __( 'Furnished', 'propertyhive' )); break; }
                    
                    default: { echo 'UNKNOWN CUSTOM FIELD'; }
                }
                
                PH_Admin_Settings::output_fields( $settings );
            }
            else
            {
                global $hide_save_button;
        
                $hide_save_button = true;
        
                // The main custom field screen listing them in a table
                $settings = $this->get_custom_fields_setting($current_section);
            
                PH_Admin_Settings::output_fields( $settings );
            }
            
        } else {
            $settings = $this->get_settings();

            PH_Admin_Settings::output_fields( $settings );
        }
    }

    /**
     * Output custom fields settings.
     *
     * @access public
     * @return void
     */
    public function get_custom_fields_setting($current_section) {
        
        $sections = $this->get_sections();
        
        return apply_filters( 'propertyhive_custom_fields_' . $current_section . '_settings', array(

            array( 'title' => $sections[$current_section], 'type' => 'title', 'desc' => '', 'id' => 'custom_fields_' . $current_section . '_options' ),
            
            array(
                'type'      => 'custom_fields_' . str_replace("-", "_", $current_section),
            ),
            
            array( 'type' => 'sectionend', 'id' => 'custom_fields_' . $current_section . '_options'),
            
        ));
        
    }

    /**
     * Output list of availabilities
     *
     * @access public
     * @return void
     */
    public function custom_fields_availability_setting() {
        global $post;
    ?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                &nbsp;
            </th>
            <td class="forminp forminp-button">
                <a href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=availability&id=' ); ?>" class="button alignright"><?php echo __( 'Add New Availability', 'propertyhive' ); ?></a>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row" class="titledesc"><?php _e( 'Availability Options', 'propertyhive' ) ?></th>
            <td class="forminp">
                <table class="ph_customfields widefat" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="type"><?php _e( 'Availability', 'propertyhive' ); ?></th>
                            <th class="settings">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $args = array(
                            'hide_empty' => false,
                            'parent' => 0
                        );
                        $terms = get_terms( 'availability', $args );
                        
                        if ( !empty( $terms ) && !is_wp_error( $terms ) )
                        {
                            foreach ($terms as $term)
                            { 
                        ?>
                        <tr>
                            <td class="type"><?php echo $term->name; ?></td>
                            <td class="settings">
                                <a class="button" href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=availability&id=' . $term->term_id ); ?>"><?php echo __( 'Edit', 'propertyhive' ); ?></a>
                                <a class="button" href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=availability-delete&id=' . $term->term_id ); ?>"><?php echo __( 'Delete', 'propertyhive' ); ?></a>
                            </td>
                        </tr>
                        <?php
                            }
                        }
                        else
                        {
                        ?>
                        <tr>
                            <td><?php echo __( 'No availability options found', 'propertyhive' ); ?></td>
                            <td class="settings">
                                
                            </td>
                        </tr>
                        <?php
                        }
                    ?>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row" class="titledesc">
                &nbsp;
            </th>
            <td class="forminp forminp-button">
                <a href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=availability&id=' ); ?>" class="button alignright"><?php echo __( 'Add New Availability', 'propertyhive' ); ?></a>
            </td>
        </tr>
    <?php
    }
    
    /**
     * Output list of property types
     *
     * @access public
     * @return void
     */
    public function custom_fields_property_type_setting() {
        global $post;
    ?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                &nbsp;
            </th>
            <td class="forminp forminp-button">
                <a href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=property-type&id=' ); ?>" class="button alignright"><?php echo __( 'Add New Property Type', 'propertyhive' ); ?></a>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row" class="titledesc"><?php _e( 'Property Types', 'propertyhive' ) ?></th>
            <td class="forminp">
                <table class="ph_customfields widefat" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="type"><?php _e( 'Property Type', 'propertyhive' ); ?></th>
                            <th class="settings">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $args = array(
                            'hide_empty' => false,
                            'parent' => 0
                        );
                        $terms = get_terms( 'property_type', $args );
                        
                        if ( !empty( $terms ) && !is_wp_error( $terms ) )
                        {
                            foreach ($terms as $term)
                            {
                                $args = array(
                                    'hide_empty' => false,
                                    'parent' => $term->term_id
                                );
                                $subterms = get_terms( 'property_type', $args );
                        ?>
                        <tr>
                            <td class="type"><?php echo $term->name; ?></td>
                            <td class="settings">
                                <a class="button" href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=property-type&id=' . $term->term_id ); ?>"><?php echo __( 'Edit', 'propertyhive' ); ?></a>
                                <?php if ( empty( $subterms ) ) { ?>
                                <a class="button" href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=property-type-delete&id=' . $term->term_id ); ?>"><?php echo __( 'Delete', 'propertyhive' ); ?></a>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php
                                if ( !empty( $subterms ) && !is_wp_error( $subterms ) )
                                {
                                    foreach ($subterms as $term)
                                    {
                                        ?>
                                        <tr>
                                            <td class="type subtype">&nbsp;&nbsp;&nbsp;- <?php echo $term->name; ?></td>
                                            <td class="settings">
                                                <a class="button" href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=property-type&id=' . $term->term_id ); ?>"><?php echo __( 'Edit', 'propertyhive' ); ?></a>
                                                <a class="button" href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=property-type-delete&id=' . $term->term_id ); ?>"><?php echo __( 'Delete', 'propertyhive' ); ?></a>
                                            </td>
                                        </tr>
                                        <?php   
                                    }
                                }
                        ?>
                        <?php
                            }
                        }
                        else
                        {
                        ?>
                        <tr>
                            <td><?php echo __( 'No property types found', 'propertyhive' ); ?></td>
                            <td class="settings">
                                
                            </td>
                        </tr>
                        <?php
                        }
                    ?>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row" class="titledesc">
                &nbsp;
            </th>
            <td class="forminp forminp-button">
                <a href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=property-type&id=' ); ?>" class="button alignright"><?php echo __( 'Add New Property Type', 'propertyhive' ); ?></a>
            </td>
        </tr>
    <?php
    }

    /**
     * Output list of locations
     *
     * @access public
     * @return void
     */
    public function custom_fields_location_setting() {
        global $post;
    ?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                &nbsp;
            </th>
            <td class="forminp forminp-button">
                <a href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=location&id=' ); ?>" class="button alignright"><?php echo __( 'Add New Location', 'propertyhive' ); ?></a>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row" class="titledesc"><?php _e( 'Locations', 'propertyhive' ) ?></th>
            <td class="forminp">
                <table class="ph_customfields widefat" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="type"><?php _e( 'Location', 'propertyhive' ); ?></th>
                            <th class="settings">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $args = array(
                            'hide_empty' => false,
                            'parent' => 0
                        );
                        $terms = get_terms( 'location', $args );
                        
                        if ( !empty( $terms ) && !is_wp_error( $terms ) )
                        {
                            foreach ($terms as $term)
                            {
                                $args = array(
                                    'hide_empty' => false,
                                    'parent' => $term->term_id
                                );
                                $subterms = get_terms( 'location', $args );
                        ?>
                        <tr>
                            <td class="type"><?php echo $term->name; ?></td>
                            <td class="settings">
                                <a class="button" href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=location&id=' . $term->term_id ); ?>"><?php echo __( 'Edit', 'propertyhive' ); ?></a>
                                <?php if ( empty( $subterms ) ) { ?>
                                <a class="button" href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=location-delete&id=' . $term->term_id ); ?>"><?php echo __( 'Delete', 'propertyhive' ); ?></a>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php
                                if ( !empty( $subterms ) && !is_wp_error( $subterms ) )
                                {
                                    foreach ($subterms as $term)
                                    {
                                        $args = array(
                                            'hide_empty' => false,
                                            'parent' => $term->term_id
                                        );
                                        $subsubterms = get_terms( 'location', $args );
                                        ?>
                                        <tr>
                                            <td class="type subtype">&nbsp;&nbsp;&nbsp;- <?php echo $term->name; ?></td>
                                            <td class="settings">
                                                <a class="button" href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=location&id=' . $term->term_id ); ?>"><?php echo __( 'Edit', 'propertyhive' ); ?></a>
                                                <?php if ( empty( $subsubterms ) ) { ?>
                                                <a class="button" href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=location-delete&id=' . $term->term_id ); ?>"><?php echo __( 'Delete', 'propertyhive' ); ?></a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php
                                        if ( !empty( $subsubterms ) && !is_wp_error( $subsubterms ) )
                                        {
                                            foreach ($subsubterms as $term)
                                            {
                                                ?>
                                                <tr>
                                                    <td class="type subtype">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <?php echo $term->name; ?></td>
                                                    <td class="settings">
                                                        <a class="button" href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=location&id=' . $term->term_id ); ?>"><?php echo __( 'Edit', 'propertyhive' ); ?></a>
                                                        <a class="button" href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=location-delete&id=' . $term->term_id ); ?>"><?php echo __( 'Delete', 'propertyhive' ); ?></a>
                                                    </td>
                                                </tr>
                                                <?php   
                                            }
                                        }
                                    }
                                }
                        ?>
                        <?php
                            }
                        }
                        else
                        {
                        ?>
                        <tr>
                            <td><?php echo __( 'No locations found', 'propertyhive' ); ?></td>
                            <td class="settings">
                                
                            </td>
                        </tr>
                        <?php
                        }
                    ?>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row" class="titledesc">
                &nbsp;
            </th>
            <td class="forminp forminp-button">
                <a href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=location&id=' ); ?>" class="button alignright"><?php echo __( 'Add New Location', 'propertyhive' ); ?></a>
            </td>
        </tr>
    <?php
    }
    
    /**
     * Output list of parking
     *
     * @access public
     * @return void
     */
    public function custom_fields_parking_setting() {
        global $post;
    ?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                &nbsp;
            </th>
            <td class="forminp forminp-button">
                <a href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=parking&id=' ); ?>" class="button alignright"><?php echo __( 'Add New Parking', 'propertyhive' ); ?></a>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row" class="titledesc"><?php _e( 'Parking Options', 'propertyhive' ) ?></th>
            <td class="forminp">
                <table class="ph_customfields widefat" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="type"><?php _e( 'Parking', 'propertyhive' ); ?></th>
                            <th class="settings">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $args = array(
                            'hide_empty' => false,
                            'parent' => 0
                        );
                        $terms = get_terms( 'parking', $args );
                        
                        if ( !empty( $terms ) && !is_wp_error( $terms ) )
                        {
                            foreach ($terms as $term)
                            { 
                        ?>
                        <tr>
                            <td class="type"><?php echo $term->name; ?></td>
                            <td class="settings">
                                <a class="button" href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=parking&id=' . $term->term_id ); ?>"><?php echo __( 'Edit', 'propertyhive' ); ?></a>
                                <a class="button" href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=parking-delete&id=' . $term->term_id ); ?>"><?php echo __( 'Delete', 'propertyhive' ); ?></a>
                            </td>
                        </tr>
                        <?php
                            }
                        }
                        else
                        {
                        ?>
                        <tr>
                            <td><?php echo __( 'No parking options found', 'propertyhive' ); ?></td>
                            <td class="settings">
                                
                            </td>
                        </tr>
                        <?php
                        }
                    ?>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row" class="titledesc">
                &nbsp;
            </th>
            <td class="forminp forminp-button">
                <a href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=parking&id=' ); ?>" class="button alignright"><?php echo __( 'Add New Parking', 'propertyhive' ); ?></a>
            </td>
        </tr>
    <?php
    }
    
    /**
     * Output list of outside spaces
     *
     * @access public
     * @return void
     */
    public function custom_fields_outside_space_setting() {
        global $post;
    ?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                &nbsp;
            </th>
            <td class="forminp forminp-button">
                <a href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=outside-space&id=' ); ?>" class="button alignright"><?php echo __( 'Add New Outside Space', 'propertyhive' ); ?></a>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row" class="titledesc"><?php _e( 'Outside Spaces', 'propertyhive' ) ?></th>
            <td class="forminp">
                <table class="ph_customfields widefat" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="type"><?php _e( 'Outside Space', 'propertyhive' ); ?></th>
                            <th class="settings">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $args = array(
                            'hide_empty' => false,
                            'parent' => 0
                        );
                        $terms = get_terms( 'outside_space', $args );
                        
                        if ( !empty( $terms ) && !is_wp_error( $terms ) )
                        {
                            foreach ($terms as $term)
                            { 
                        ?>
                        <tr>
                            <td class="type"><?php echo $term->name; ?></td>
                            <td class="settings">
                                <a class="button" href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=outside-space&id=' . $term->term_id ); ?>"><?php echo __( 'Edit', 'propertyhive' ); ?></a>
                                <a class="button" href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=outside-space-delete&id=' . $term->term_id ); ?>"><?php echo __( 'Delete', 'propertyhive' ); ?></a>
                            </td>
                        </tr>
                        <?php
                            }
                        }
                        else
                        {
                        ?>
                        <tr>
                            <td><?php echo __( 'No outside spaces found', 'propertyhive' ); ?></td>
                            <td class="settings">
                                
                            </td>
                        </tr>
                        <?php
                        }
                    ?>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row" class="titledesc">
                &nbsp;
            </th>
            <td class="forminp forminp-button">
                <a href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=outside-space&id=' ); ?>" class="button alignright"><?php echo __( 'Add New Outside Space', 'propertyhive' ); ?></a>
            </td>
        </tr>
    <?php
    }

    /**
     * Output list of price qualifiers
     *
     * @access public
     * @return void
     */
    public function custom_fields_price_qualifier_setting() {
        global $post;
    ?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                &nbsp;
            </th>
            <td class="forminp forminp-button">
                <a href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=price-qualifier&id=' ); ?>" class="button alignright"><?php echo __( 'Add New Price Qualifier', 'propertyhive' ); ?></a>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row" class="titledesc"><?php _e( 'Price Qualifiers', 'propertyhive' ) ?></th>
            <td class="forminp">
                <table class="ph_customfields widefat" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="type"><?php _e( 'Price Qualifier', 'propertyhive' ); ?></th>
                            <th class="settings">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $args = array(
                            'hide_empty' => false,
                            'parent' => 0
                        );
                        $terms = get_terms( 'price_qualifier', $args );
                        
                        if ( !empty( $terms ) && !is_wp_error( $terms ) )
                        {
                            foreach ($terms as $term)
                            { 
                        ?>
                        <tr>
                            <td class="type"><?php echo $term->name; ?></td>
                            <td class="settings">
                                <a class="button" href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=price-qualifier&id=' . $term->term_id ); ?>"><?php echo __( 'Edit', 'propertyhive' ); ?></a>
                                <a class="button" href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=price-qualifier-delete&id=' . $term->term_id ); ?>"><?php echo __( 'Delete', 'propertyhive' ); ?></a>
                            </td>
                        </tr>
                        <?php
                            }
                        }
                        else
                        {
                        ?>
                        <tr>
                            <td><?php echo __( 'No price qualifiers found', 'propertyhive' ); ?></td>
                            <td class="settings">
                                
                            </td>
                        </tr>
                        <?php
                        }
                    ?>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row" class="titledesc">
                &nbsp;
            </th>
            <td class="forminp forminp-button">
                <a href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=price-qualifier&id=' ); ?>" class="button alignright"><?php echo __( 'Add New Price Qualifier', 'propertyhive' ); ?></a>
            </td>
        </tr>
    <?php
    }

    /**
     * Output list of sale by options
     *
     * @access public
     * @return void
     */
    public function custom_fields_sale_by_setting() {
        global $post;
    ?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                &nbsp;
            </th>
            <td class="forminp forminp-button">
                <a href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=sale-by&id=' ); ?>" class="button alignright"><?php echo __( 'Add New Sale By', 'propertyhive' ); ?></a>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row" class="titledesc"><?php _e( 'Sale By Options', 'propertyhive' ) ?></th>
            <td class="forminp">
                <table class="ph_customfields widefat" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="type"><?php _e( 'Sale By', 'propertyhive' ); ?></th>
                            <th class="settings">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $args = array(
                            'hide_empty' => false,
                            'parent' => 0
                        );
                        $terms = get_terms( 'sale_by', $args );
                        
                        if ( !empty( $terms ) && !is_wp_error( $terms ) )
                        {
                            foreach ($terms as $term)
                            { 
                        ?>
                        <tr>
                            <td class="type"><?php echo $term->name; ?></td>
                            <td class="settings">
                                <a class="button" href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=sale-by&id=' . $term->term_id ); ?>"><?php echo __( 'Edit', 'propertyhive' ); ?></a>
                                <a class="button" href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=sale-by-delete&id=' . $term->term_id ); ?>"><?php echo __( 'Delete', 'propertyhive' ); ?></a>
                            </td>
                        </tr>
                        <?php
                            }
                        }
                        else
                        {
                        ?>
                        <tr>
                            <td><?php echo __( 'No sale by options found', 'propertyhive' ); ?></td>
                            <td class="settings">
                                
                            </td>
                        </tr>
                        <?php
                        }
                    ?>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row" class="titledesc">
                &nbsp;
            </th>
            <td class="forminp forminp-button">
                <a href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=sale-by&id=' ); ?>" class="button alignright"><?php echo __( 'Add New Sale By', 'propertyhive' ); ?></a>
            </td>
        </tr>
    <?php
    }

    /**
     * Output list of tenure options
     *
     * @access public
     * @return void
     */
    public function custom_fields_tenure_setting() {
        global $post;
    ?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                &nbsp;
            </th>
            <td class="forminp forminp-button">
                <a href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=tenure&id=' ); ?>" class="button alignright"><?php echo __( 'Add New Tenure', 'propertyhive' ); ?></a>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row" class="titledesc"><?php _e( 'Tenures', 'propertyhive' ) ?></th>
            <td class="forminp">
                <table class="ph_customfields widefat" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="type"><?php _e( 'Tenure', 'propertyhive' ); ?></th>
                            <th class="settings">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $args = array(
                            'hide_empty' => false,
                            'parent' => 0
                        );
                        $terms = get_terms( 'tenure', $args );
                        
                        if ( !empty( $terms ) && !is_wp_error( $terms ) )
                        {
                            foreach ($terms as $term)
                            { 
                        ?>
                        <tr>
                            <td class="type"><?php echo $term->name; ?></td>
                            <td class="settings">
                                <a class="button" href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=tenure&id=' . $term->term_id ); ?>"><?php echo __( 'Edit', 'propertyhive' ); ?></a>
                                <a class="button" href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=tenure-delete&id=' . $term->term_id ); ?>"><?php echo __( 'Delete', 'propertyhive' ); ?></a>
                            </td>
                        </tr>
                        <?php
                            }
                        }
                        else
                        {
                        ?>
                        <tr>
                            <td><?php echo __( 'No tenure found', 'propertyhive' ); ?></td>
                            <td class="settings">
                                
                            </td>
                        </tr>
                        <?php
                        }
                    ?>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row" class="titledesc">
                &nbsp;
            </th>
            <td class="forminp forminp-button">
                <a href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=tenure&id=' ); ?>" class="button alignright"><?php echo __( 'Add New Tenure', 'propertyhive' ); ?></a>
            </td>
        </tr>
    <?php
    }

    /**
     * Output list of furnished options
     *
     * @access public
     * @return void
     */
    public function custom_fields_furnished_setting() {
        global $post;
    ?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                &nbsp;
            </th>
            <td class="forminp forminp-button">
                <a href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=furnished&id=' ); ?>" class="button alignright"><?php echo __( 'Add New Furnished Option', 'propertyhive' ); ?></a>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row" class="titledesc"><?php _e( 'Furnished Options', 'propertyhive' ) ?></th>
            <td class="forminp">
                <table class="ph_customfields widefat" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="type"><?php _e( 'Furnished', 'propertyhive' ); ?></th>
                            <th class="settings">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $args = array(
                            'hide_empty' => false,
                            'parent' => 0
                        );
                        $terms = get_terms( 'furnished', $args );
                        
                        if ( !empty( $terms ) && !is_wp_error( $terms ) )
                        {
                            foreach ($terms as $term)
                            { 
                        ?>
                        <tr>
                            <td class="type"><?php echo $term->name; ?></td>
                            <td class="settings">
                                <a class="button" href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=furnished&id=' . $term->term_id ); ?>"><?php echo __( 'Edit', 'propertyhive' ); ?></a>
                                <a class="button" href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=furnished-delete&id=' . $term->term_id ); ?>"><?php echo __( 'Delete', 'propertyhive' ); ?></a>
                            </td>
                        </tr>
                        <?php
                            }
                        }
                        else
                        {
                        ?>
                        <tr>
                            <td><?php echo __( 'No furnished options found', 'propertyhive' ); ?></td>
                            <td class="settings">
                                
                            </td>
                        </tr>
                        <?php
                        }
                    ?>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row" class="titledesc">
                &nbsp;
            </th>
            <td class="forminp forminp-button">
                <a href="<?php echo admin_url( 'admin.php?page=ph-settings&tab=customfields&section=furnished&id=' ); ?>" class="button alignright"><?php echo __( 'Add New Furnished Option', 'propertyhive' ); ?></a>
            </td>
        </tr>
    <?php
    }

    /**
     * Show availability add/edit options
     *
     * @access public
     * @return string
     */
    public function get_custom_fields_availability_setting()
    {
        $current_id = empty( $_REQUEST['id'] ) ? '' : sanitize_title( $_REQUEST['id'] );
        
        $taxonomy = 'availability';
        $term_name = '';
        if ($current_id != '')
        {
            $term = get_term( $current_id, $taxonomy );
            $term_name = $term->name;
        }

        $args = array(

            array( 'title' => __( ( $current_id == '' ? 'Add New Availability Option' : 'Edit Availability' ), 'propertyhive' ), 'type' => 'title', 'desc' => '', 'id' => 'custom_field_availability_settings' ),
            
            array(
                'title' => __( 'Availability', 'propertyhive' ),
                'id'        => 'availability_name',
                'default'   => $term_name,
                'type'      => 'text',
                'desc_tip'  =>  false,
            ),
            
            array(
                'type'      => 'hidden',
                'id'        => 'taxonomy',
                'default'     => $taxonomy
            ),
            
            array( 'type' => 'sectionend', 'id' => 'custom_field_availability_settings' )
            
        );
        
        return apply_filters( 'propertyhive_custom_field_availability_settings', $args );
    }

    /**
     * Show property type add/edit options
     *
     * @access public
     * @return string
     */
    public function get_custom_fields_property_type_setting()
    {
        $current_id = empty( $_REQUEST['id'] ) ? '' : sanitize_title( $_REQUEST['id'] );
        
        $taxonomy = 'property_type';
        $term_name = '';
        $term_parent = '';
        if ($current_id != '')
        {
            $term = get_term( $current_id, $taxonomy );
            $term_name = $term->name;
            $term_parent = $term->parent;
        }
        
        $existing_terms = array('' => '(' . __ ( 'no parent', 'propertyhive') . ')');
        
        $args = array(
            'hide_empty' => false,
            'parent' => 0,
            'exclude' => array($current_id)
        );
        $terms = get_terms( 'property_type', $args );
        if ( !empty( $terms ) && !is_wp_error( $terms ) )
        {
            foreach ($terms as $term)
            {
                $existing_terms[$term->term_id] = $term->name;
            }
        }
        
        $args = array(

            array( 'title' => __( ( $current_id == '' ? 'Add New Property Type' : 'Edit Property Type' ), 'propertyhive' ), 'type' => 'title', 'desc' => '', 'id' => 'custom_field_property_type_settings' ),
            
            array(
                'title' => __( 'Property Type', 'propertyhive' ),
                'id'        => 'property_type_name',
                'default'   => $term_name,
                'type'      => 'text',
                'desc_tip'  =>  false,
            ),
            
            array(
                'title' => __( 'Parent', 'propertyhive' ),
                'id'        => 'parent_property_type_id',
                'default'   => $term_parent,
                'options'   => $existing_terms,
                'type'      => 'select',
                'desc_tip'  =>  false,
                'desc'      => ''
            ),
            
            array(
                'type'      => 'hidden',
                'id'        => 'taxonomy',
                'default'     => $taxonomy
            ),
            
            array( 'type' => 'sectionend', 'id' => 'custom_field_property_type_settings' )
            
        );
        
        return apply_filters( 'propertyhive_custom_field_property_type_settings', $args );
    }

    /**
     * Show location add/edit options
     *
     * @access public
     * @return string
     */
    public function get_custom_fields_location_setting()
    {
        $current_id = empty( $_REQUEST['id'] ) ? '' : sanitize_title( $_REQUEST['id'] );
        
        $taxonomy = 'location';
        $term_name = '';
        $term_parent = '';
        if ($current_id != '')
        {
            $term = get_term( $current_id, $taxonomy );
            $term_name = $term->name;
            $term_parent = $term->parent;
        }
        
        $existing_terms = array('' => '(' . __ ( 'no parent', 'propertyhive') . ')');
        
        $args = array(
            'hide_empty' => false,
            'parent' => 0,
            'exclude' => array($current_id)
        );
        $terms = get_terms( $taxonomy, $args );
        if ( !empty( $terms ) && !is_wp_error( $terms ) )
        {
            foreach ($terms as $term)
            {
                $existing_terms[$term->term_id] = '- '.$term->name;
                
                $args = array(
                    'hide_empty' => false,
                    'parent' => $term->term_id,
                    'exclude' => array($current_id)
                );
                $terms = get_terms( $taxonomy, $args );
                if ( !empty( $terms ) && !is_wp_error( $terms ) )
                {
                    foreach ($terms as $term)
                    {
                        $existing_terms[$term->term_id] = '- - '.$term->name;
                    }
                }
            }
        }
        
        $args = array(

            array( 'title' => __( ( $current_id == '' ? 'Add New Location' : 'Edit Location' ), 'propertyhive' ), 'type' => 'title', 'desc' => '', 'id' => 'custom_field_location_settings' ),
            
            array(
                'title' => __( 'Location', 'propertyhive' ),
                'id'        => 'location_name',
                'default'   => $term_name,
                'type'      => 'text',
                'desc_tip'  =>  false,
            ),
            
            array(
                'title' => __( 'Parent', 'propertyhive' ),
                'id'        => 'parent_location_id',
                'default'   => $term_parent,
                'options'   => $existing_terms,
                'type'      => 'select',
                'desc_tip'  =>  false,
                'desc'      => ''
            ),
            
            array(
                'type'      => 'hidden',
                'id'        => 'taxonomy',
                'default'     => $taxonomy
            ),
            
            array( 'type' => 'sectionend', 'id' => 'custom_field_location_settings' )
            
        );
        
        return apply_filters( 'propertyhive_custom_field_location_settings', $args );
    }
    
    /**
     * Show parking add/edit options
     *
     * @access public
     * @return string
     */
    public function get_custom_fields_parking_setting()
    {
        $current_id = empty( $_REQUEST['id'] ) ? '' : sanitize_title( $_REQUEST['id'] );
        
        $taxonomy = 'parking';
        $term_name = '';
        if ($current_id != '')
        {
            $term = get_term( $current_id, $taxonomy );
            $term_name = $term->name;
        }

        $args = array(

            array( 'title' => __( ( $current_id == '' ? 'Add New Parking Option' : 'Edit Parking' ), 'propertyhive' ), 'type' => 'title', 'desc' => '', 'id' => 'custom_field_parking_settings' ),
            
            array(
                'title' => __( 'Parking', 'propertyhive' ),
                'id'        => 'parking_name',
                'default'   => $term_name,
                'type'      => 'text',
                'desc_tip'  =>  false,
            ),
            
            array(
                'type'      => 'hidden',
                'id'        => 'taxonomy',
                'default'     => $taxonomy
            ),
            
            array( 'type' => 'sectionend', 'id' => 'custom_field_parking_settings' )
            
        );
        
        return apply_filters( 'propertyhive_custom_field_parking_settings', $args );
    }
    
    /**
     * Show outside space add/edit options
     *
     * @access public
     * @return string
     */
    public function get_custom_fields_outside_space_setting()
    {
        $current_id = empty( $_REQUEST['id'] ) ? '' : sanitize_title( $_REQUEST['id'] );
        
        $taxonomy = 'outside_space';
        $term_name = '';
        if ($current_id != '')
        {
            $term = get_term( $current_id, $taxonomy );
            $term_name = $term->name;
        }

        $args = array(

            array( 'title' => __( ( $current_id == '' ? 'Add New Outside Space' : 'Edit Outside Space' ), 'propertyhive' ), 'type' => 'title', 'desc' => '', 'id' => 'custom_field_outside_space_settings' ),
            
            array(
                'title' => __( 'Outside Space', 'propertyhive' ),
                'id'        => 'outside_space_name',
                'default'   => $term_name,
                'type'      => 'text',
                'desc_tip'  =>  false,
            ),
            
            array(
                'type'      => 'hidden',
                'id'        => 'taxonomy',
                'default'     => $taxonomy
            ),
            
            array( 'type' => 'sectionend', 'id' => 'custom_field_outside_space_settings' )
            
        );
        
        return apply_filters( 'propertyhive_custom_field_outside_space_settings', $args );
    }

    /**
     * Show outside space delete options
     *
     * @access public
     * @return string
     */
    public function get_custom_fields_delete($current_id, $taxonomy, $taxonomy_name)
    {
        global $save_button_text;
        
        $save_button_text = __( 'Delete', 'propertyhive' );
        
        //$taxonomy = 'outside_space';
        //$taxonomy_name = __( 'Outside Space', 'propertyhive' );
        
        if ( isset($_POST['confirm_removal']) && $_POST['confirm_removal'] == 1 )
        {
            // A term has just been deleted
            global $hide_save_button, $show_cancel_button, $cancel_button_href;
            
            $hide_save_button = TRUE;
            $show_cancel_button = TRUE;
            $cancel_button_href = admin_url( 'admin.php?page=ph-settings&tab=customfields&section=' . str_replace("_", "-", $taxonomy) );
            
            $args = array();
                    
            $args[] = array( 'title' => __( 'Successfully Deleted', 'propertyhive' ) . ' ' . $taxonomy_name, 'type' => 'title', 'desc' => '', 'id' => 'custom_field_' . $taxonomy . '_delete' );
            
            $args[] = array(
                'title'     => __( 'Term Deleted', 'propertyhive' ),
                'id'        => '',
                'html'      => $taxonomy_name . __(' deleted successfully', 'propertyhive' ) . ' <a href="' . admin_url( 'admin.php?page=ph-settings&tab=customfields&section=' . str_replace("_", "-", $taxonomy) ) . '">' . __( 'Go Back', 'propertyhive' ) . '</a>',
                'type'      => 'html',
                'desc_tip'  =>  false,
            );
            
            $args[] = array( 'type' => 'sectionend', 'id' => 'custom_field_' . $taxonomy . '_delete' );
        }
        else
        {
            $term_name = '';
            if ($current_id == '')
            {
                die("ID not passed");
            }
            else
            {
                $term = get_term( $current_id, $taxonomy );
                
                if ( is_null($term) || is_wp_error($term) )
                {
                    die("Invalid term trying to be deleted");
                }
                else
                {
                    $term_name = $term->name;
                    
                    $args = array();
                    
                    $args[] = array( 'title' => __( 'Delete', 'propertyhive' ) . ' ' . $taxonomy_name . ': ' . $term_name, 'type' => 'title', 'desc' => '', 'id' => 'custom_field_' . $taxonomy . '_delete' );
                    
                    // Get number of properties assigned to this term
                    $query_args = array(
                        'post_type' => 'property',
                        'nopaging' => true,
                        'post_status' => array( 'pending', 'auto-draft', 'draft', 'private', 'publish', 'future', 'trash' ),
                        'tax_query' => array(
                            array(
                                'taxonomy' => $taxonomy,
                                'field'    => 'id',
                                'terms'    => $current_id,
                            ),
                        ),
                    );
                    $property_query = new WP_Query( $query_args );
                    
                    $num_properties = $property_query->found_posts;
                    
                    // Get number of applicants assigned to this term (future)
                    
                    if ($num_properties > 0)
                    {
                        $alternative_terms = array();
                        
                        $alternative_terms['none'] = '-- ' . __( 'Don\'t Reassign', 'propertyhive' ) . ' --';
                        
                        $term_args = array(
                            'hide_empty' => false,
                            'exclude' => array($current_id),
                            'parent' => 0
                        );
                        $terms = get_terms( $taxonomy, $term_args );
                        
                        if ( !empty( $terms ) && !is_wp_error( $terms ) )
                        {
                            foreach ($terms as $term)
                            {
                                $alternative_terms[$term->term_id] = $term->name;
                            }
                        } 
                        
                        // There are properties assigned to this term
                        $args[] = array(
                            'title' => __( 'Re-assign to', 'propertyhive' ),
                            'id'        => 'reassign_to',
                            'default'   => '',
                            'options'   => $alternative_terms,
                            'type'      => 'select',
                            'desc_tip'  =>  false,
                            'desc'      => __( 'There are properties that have this term assigned to them. Which, if any, term should they be reassigned to?' , 'propertyhive' )
                        );
                    }
                    
                    $args[] = array(
                            'title' => __( 'Confirm removal?', 'propertyhive' ),
                            'id'        => 'confirm_removal',
                            'type'      => 'checkbox',
                            'desc_tip'  =>  false,
                        );
                        
                    $args[] = array(
                            'type'      => 'hidden',
                            'id'        => 'taxonomy',
                            'default'     => $taxonomy
                        );
                        
                    $args[] = array( 'type' => 'sectionend', 'id' => 'custom_field_' . $taxonomy . '_delete' );
                }
            }
        }

        return apply_filters( 'propertyhive_custom_field_' . $taxonomy . '_delete', $args );
    }

    /**
     * Show price qualifier add/edit options
     *
     * @access public
     * @return string
     */
    public function get_custom_fields_price_qualifier_setting()
    {
        $current_id = empty( $_REQUEST['id'] ) ? '' : sanitize_title( $_REQUEST['id'] );
        
        $taxonomy = 'price_qualifier';
        $term_name = '';
        if ($current_id != '')
        {
            $term = get_term( $current_id, $taxonomy );
            $term_name = $term->name;
        }

        $args = array(

            array( 'title' => __( ( $current_id == '' ? 'Add New Price Qualifier' : 'Edit Price Qualifier' ), 'propertyhive' ), 'type' => 'title', 'desc' => '', 'id' => 'custom_field_price_qualifier_settings' ),
            
            array(
                'title' => __( 'Price Qualifier', 'propertyhive' ),
                'id'        => 'price_qualifier_name',
                'default'   => $term_name,
                'type'      => 'text',
                'desc_tip'  =>  false,
            ),
            
            array(
                'type'      => 'hidden',
                'id'        => 'taxonomy',
                'default'     => $taxonomy
            ),
            
            array( 'type' => 'sectionend', 'id' => 'custom_field_price_qualifier_settings' )
            
        );
        
        return apply_filters( 'propertyhive_custom_field_price_qualifier_settings', $args );
    }

    /**
     * Show sale by add/edit options
     *
     * @access public
     * @return string
     */
    public function get_custom_fields_sale_by_setting()
    {
        $current_id = empty( $_REQUEST['id'] ) ? '' : sanitize_title( $_REQUEST['id'] );
        
        $taxonomy = 'sale_by';
        $term_name = '';
        if ($current_id != '')
        {
            $term = get_term( $current_id, $taxonomy );
            $term_name = $term->name;
        }

        $args = array(

            array( 'title' => __( ( $current_id == '' ? 'Add New Sale By' : 'Edit Sale By' ), 'propertyhive' ), 'type' => 'title', 'desc' => '', 'id' => 'custom_field_sale_by_settings' ),
            
            array(
                'title' => __( 'Sale By', 'propertyhive' ),
                'id'        => 'sale_by_name',
                'default'   => $term_name,
                'type'      => 'text',
                'desc_tip'  =>  false,
            ),
            
            array(
                'type'      => 'hidden',
                'id'        => 'taxonomy',
                'default'     => $taxonomy
            ),
            
            array( 'type' => 'sectionend', 'id' => 'custom_field_sale_by_settings' )
            
        );
        
        return apply_filters( 'propertyhive_custom_field_sale_by_settings', $args );
    }

    /**
     * Show tenure add/edit options
     *
     * @access public
     * @return string
     */
    public function get_custom_fields_tenure_setting()
    {
        $current_id = empty( $_REQUEST['id'] ) ? '' : sanitize_title( $_REQUEST['id'] );
        
        $taxonomy = 'tenure';
        $term_name = '';
        if ($current_id != '')
        {
            $term = get_term( $current_id, $taxonomy );
            $term_name = $term->name;
        }

        $args = array(

            array( 'title' => __( ( $current_id == '' ? 'Add New Tenure' : 'Edit Tenure' ), 'propertyhive' ), 'type' => 'title', 'desc' => '', 'id' => 'custom_field_tenure_settings' ),
            
            array(
                'title' => __( 'Tenure', 'propertyhive' ),
                'id'        => 'tenure_name',
                'default'   => $term_name,
                'type'      => 'text',
                'desc_tip'  =>  false,
            ),
            
            array(
                'type'      => 'hidden',
                'id'        => 'taxonomy',
                'default'     => $taxonomy
            ),
            
            array( 'type' => 'sectionend', 'id' => 'custom_field_tenure_settings' )
            
        );
        
        return apply_filters( 'propertyhive_custom_field_tenure_settings', $args );
    }

    /**
     * Show furnished add/edit options
     *
     * @access public
     * @return string
     */
    public function get_custom_fields_furnished_setting()
    {
        $current_id = empty( $_REQUEST['id'] ) ? '' : sanitize_title( $_REQUEST['id'] );
        
        $taxonomy = 'furnished';
        $term_name = '';
        if ($current_id != '')
        {
            $term = get_term( $current_id, $taxonomy );
            $term_name = $term->name;
        }

        $args = array(

            array( 'title' => __( ( $current_id == '' ? 'Add New Furnished' : 'Edit Furnished' ), 'propertyhive' ), 'type' => 'title', 'desc' => '', 'id' => 'custom_field_furnished_settings' ),
            
            array(
                'title' => __( 'Furnished', 'propertyhive' ),
                'id'        => 'furnished_name',
                'default'   => $term_name,
                'type'      => 'text',
                'desc_tip'  =>  false,
            ),
            
            array(
                'type'      => 'hidden',
                'id'        => 'taxonomy',
                'default'     => $taxonomy
            ),
            
            array( 'type' => 'sectionend', 'id' => 'custom_field_furnished_settings' )
            
        );
        
        return apply_filters( 'propertyhive_custom_field_furnished_settings', $args );
    }

    /**
     * Save settings
     */
    public function save() {
        global $current_section, $post;

        if ( $current_section != '' ) 
        {
            if (isset($_REQUEST['id'])) // we're either adding or editing
            {
                $current_id = empty( $_REQUEST['id'] ) ? '' : sanitize_title( $_REQUEST['id'] );
                
                switch ($current_section)
                {
                    // With heirarchy
                    case "property-type":
                    case "location":
                    {
                        // TODO: Validate (check for blank fields)
                        
                        if ($current_id == '')
                        {
                            // Adding new term
                            
                            // TODO: Check term doesn't exist already
                            
                            wp_insert_term(
                                $_POST[$_POST['taxonomy'] . '_name'], // the term 
                                $_POST['taxonomy'], // the taxonomy
                                array(
                                    'parent' => $_POST['parent_' . $_POST['taxonomy'] . '_id']
                                )
                            );
                            
                            // TODO: Check for errors returned from wp_insert_term()
                        }
                        else
                        {
                            // Editing term
                            wp_update_term($current_id, $_POST['taxonomy'], array(
                                'name' => $_POST[$_POST['taxonomy'].'_name'],
                                 'parent' => $_POST['parent_' . $_POST['taxonomy'] . '_id']
                            ));
                            
                            // TODO: Check for errors returned from wp_update_term()
                        }
                        break;
                    }
                    // Without heirarchy
                    case "availability":
                    case "outside-space":
                    case "parking":
                    case "price-qualifier":
                    case "sale-by":
                    case "tenure":
                    case "furnished":
                    {
                        // TODO: Validate (check for blank fields)
                        
                        if ($current_id == '')
                        {
                            // Adding new term
                            
                            // TODO: Check term doesn't exist already
                            
                            wp_insert_term(
                                $_POST[$_POST['taxonomy'] . '_name'], // the term 
                                $_POST['taxonomy'] // the taxonomy
                            );
                            
                            // TODO: Check for errors returned from wp_insert_term()
                        }
                        else
                        {
                            // Editing term
                            wp_update_term($current_id, $_POST['taxonomy'], array(
                                'name' => $_POST[$_POST['taxonomy'] . '_name']
                            ));
                            
                            // TODO: Check for errors returned from wp_update_term()
                        }
                        break;
                    }
                    case "availability-delete":
                    case "property-type-delete":
                    case "location-delete":
                    case "parking-delete":
                    case "price-qualifier-delete":
                    case "sale-by-delete":
                    case "tenure-delete":
                    case "furnished-delete":
                    {
                        if ( isset($_POST['confirm_removal']) && $_POST['confirm_removal'] == '1' )
                        {
                            // Get number of properties assigned to this term
                            $query_args = array(
                                'post_type' => 'property',
                                'nopaging' => true,
                                'post_status' => array( 'pending', 'auto-draft', 'draft', 'private', 'publish', 'future', 'trash' ),
                                'tax_query' => array(
                                    array(
                                        'taxonomy' => $_POST['taxonomy'],
                                        'field'    => 'id',
                                        'terms'    => $current_id,
                                    ),
                                ),
                            );
                            $property_query = new WP_Query( $query_args );
                            
                            if ( $property_query->have_posts() )
                            {
                                while ( $property_query->have_posts() )
                                {
                                    $property_query->the_post();
                                    
                                    wp_remove_object_terms( $post->ID, $current_id, $_POST['taxonomy'] );
                                    
                                    // Re-assign to another term
                                    if ( isset($_POST['reassign_to']) && ! empty( $_POST['reassign_to'] ) && $_POST['reassign_to'] != 'none' )
                                    {
                                        $new_id = $_POST['reassign_to'];
                                        
                                        wp_set_post_terms( $post->ID, $new_id, $_POST['taxonomy'], TRUE );
                                        
                                        // TODO: Check for WP_ERROR
                                    }
                                }
                            }
                            
                            wp_reset_postdata();
                            
                            wp_delete_term( $current_id, $_POST['taxonomy'] );
                        }

                        break;
                    }
                    default: { echo 'UNKNOWN CUSTOM FIELD'; }
                }
            }
            else
            {
                // Nothing to save. Should always be an id set when editing custom fields.
                // Even blank ids dictate something is being added
            }
        }
        else
        {
            // Nothing to save. Should always be a section when editing custom fields
        }
    }
}

endif;

return new PH_Settings_Custom_Fields();