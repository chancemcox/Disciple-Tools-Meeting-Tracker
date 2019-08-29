<?php

class DT_Meetings{
    private static $_instance = null;
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()

    public function __construct() {
        add_action( 'after_setup_theme', [ $this, 'after_setup_theme' ], 100 );
        add_action( 'p2p_init', [ $this, 'p2p_init' ] );
        add_action( 'dt_details_additional_section', [ $this, 'dt_details_additional_section' ], 10, 2 );

        add_filter( 'dt_custom_fields_settings', [ $this, 'dt_custom_fields_settings' ], 10, 2 );
        add_filter( 'dt_details_additional_section_ids', [ $this, 'dt_details_additional_section_ids' ], 10, 2 );
    }


    public function after_setup_theme(){
        if ( class_exists( 'Disciple_Tools_Post_Type_Template' )) {
            new Disciple_Tools_Post_Type_Template( "meetings", 'Meetings', 'Meetings' );
        }
    }

    public function dt_custom_fields_settings( $fields, $post_type ){
        if ( $post_type === 'meetings' ){
            $fields['contact_count'] = [
                'name' => "Number of Attendees",
                'type' => 'text',
                'default' => '',
                'show_in_table' => true
            ];
            $fields['group_count'] = [
                'name' => "Number of groups",
                'type' => 'text',
                'default' => '',
                'show_in_table' => true
            ];
            $fields['contacts'] = [
                'name' => "Contacts Who Attended",
                'type' => 'connection',
                "p2p_direction" => "from",
                "p2p_key" => "meetings_to_contacts",
                'p2p_listing' => 'contacts'
            ];
            $fields['date'] = [
              'name' => "Date",
              'type' => 'date',
              'default' => '',
              'show_in_table' => true
            ];
            $fields['groups'] = [
                'name' => "Groups",
                'type' => 'connection',
                "p2p_direction" => "from",
                "p2p_key" => "meetings_to_groups",
                'p2p_listing' => 'groups'
            ];
        }
        if ( $post_type === 'groups' ){
            $fields['meetings'] = [
                'name' => "Meetings",
                'type' => 'connection',
                "p2p_direction" => "to",
                "p2p_key" => "meetings_to_groups",
                'p2p_listing' => 'meetings'
            ];
        }
        if ( $post_type === 'contacts' ){
            $fields['meetings'] = [
                'name' => "Meetings",
                'type' => 'connection',
                "p2p_direction" => "to",
                "p2p_key" => "meetings_to_contacts",
                'p2p_listing' => 'meetings'
            ];
        }
        return $fields;
    }

    public function p2p_init(){
        p2p_register_connection_type([
            'name' => 'meetings_to_contacts',
            'from' => 'meetings',
            'to' => 'contacts'
        ]);
        p2p_register_connection_type([
            'name' => 'meetings_to_groups',
            'from' => 'meetings',
            'to' => 'groups'
        ]);
    }

    public function dt_details_additional_section_ids( $sections, $post_type = "" ){
        if ( $post_type === "meetings"){
            $sections[] = "meeting_date";
            $sections[] = 'contacts';
            $sections[] = 'groups';

        }

        if ( $post_type === 'contacts' || $post_type === 'groups' ){
            $sections[] = 'meetings';
        }
        return $sections;
    }


    // function dt_declare_section_date_id( $sections, $post_type = "" ){
    //
    //     //check if we are on a contact
    //
    // }



    public function dt_details_additional_section( $section, $post_type ){
      if ($section == "meeting_date"  && $post_type === "meetings"){
        $post_type = get_post_type();
        $post_settings = apply_filters( "dt_get_post_type_settings", [], $post_type );
        $dt_post = DT_Posts::get_post( $post_type, get_the_ID() );
      ?>
          <!-- need you own css? -->
          <style type="text/css">
              .required-style-example {
                  color: red
              }
          </style>

          <label class="section-header">
              <?php esc_html_e( 'Date', 'disciple_tools' )?>
          </label>
          <!-- <div class="section-subheader">
              <?php esc_html_e( 'Date', 'disciple_tools' )?> <span class="required-style-example">*</span>
          </div> -->

          <?php render_field_for_display( 'date', $post_settings["fields"], $dt_post ) ?>
      <?php
      }
        if ($section == "contacts" && $post_type === "meetings"){
            $post_type = get_post_type();
            $post_settings = apply_filters( "dt_get_post_type_settings", [], $post_type );
            $dt_post = DT_Posts::get_post( $post_type, get_the_ID() );
            ?>

            <label class="section-header">
                <?php esc_html_e( 'Attendees', 'disciple_tools' )?>
            </label>
            <!-- <?php render_field_for_display( 'date', $post_settings["fields"], $dt_post ) ?> -->
            <?php render_field_for_display( 'contact_count', $post_settings["fields"], $dt_post ) ?>

            <?php render_field_for_display( 'contacts', $post_settings["fields"], $dt_post ) ?>

        <?php }

        if ($section == "groups" && $post_type === "meetings"){
            $post_type = get_post_type();
            $post_settings = apply_filters( "dt_get_post_type_settings", [], $post_type );
            $dt_post = DT_Posts::get_post( $post_type, get_the_ID() );
            ?>

            <label class="section-header">
                <?php esc_html_e( 'Groups', 'disciple_tools' )?>
            </label>

            <?php render_field_for_display( 'group_count', $post_settings["fields"], $dt_post ) ?>

            <?php render_field_for_display( 'groups', $post_settings["fields"], $dt_post ) ?>

        <?php }

        if ($section == "meetings" && $post_type === "contacts"){
            $post_type = get_post_type();
            $post_settings = apply_filters( "dt_get_post_type_settings", [], $post_type );
            $dt_post = DT_Posts::get_post( $post_type, get_the_ID() );
            ?>

            <label class="section-header">
                <?php esc_html_e( 'Meetings', 'disciple_tools' )?>
            </label>

            <?php render_field_for_display( 'meetings', $post_settings["fields"], $dt_post ) ?>

        <?php }

        if ($section == "meetings" && $post_type === "groups"){
            $post_type = get_post_type();
            $post_settings = apply_filters( "dt_get_post_type_settings", [], $post_type );
            $dt_post = DT_Posts::get_post( $post_type, get_the_ID() );
            ?>

            <label class="section-header">
                <?php esc_html_e( 'Meetings', 'disciple_tools' )?>
            </label>

            <?php render_field_for_display( 'meetings', $post_settings["fields"], $dt_post ) ?>

        <?php }
    }
}
