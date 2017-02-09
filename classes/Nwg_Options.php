<?php
class Nwg_Options
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;



    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );

        //add_filter( 'pre_update_option_nwg_options', array( $this,'add_nwg_vars'), 10, 1 );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
    // This page will be under "Settings"
        add_options_page(
            __('Newsgram settings','newsgram'), 
            __('Newsgram settings','newsgram'), 
            'manage_options', 
            'nwg-settings-admin', 
            array( $this, 'create_admin_page' )
        );

    }
/**
     * Options page callback
     */
    public function create_admin_page()   {

        // Set class property
        $this->options = get_option( 'nwg_options' );
        $dir = plugin_dir_path( __DIR__ );
        wp_enqueue_script( 'tooltip', '/wp-content/plugins/newsgram/js/tooltip.js',array('jquery-ui-tooltip'),null,true );
        wp_enqueue_style('nwgadmin-css', plugins_url('newsgram/css/nwg-admin-style.css'), false, null);

        ?>
        <div class="wrap" style="width: 400px;">
            <h2><?php _e( 'Newsgram Settings', 'newsgram' ) ?></h2>
           <?php if ( ! isset( $_REQUEST['settings-updated'] ) )
              $_REQUEST['settings-updated'] = false; ?>
 
 
          <?php if ( false !== $_REQUEST['settings-updated'] ) : ?>
               <div class="updated fade"><p><strong><?php _e( 'Newsgram Options saved!', 'newsgram' ); ?></strong></p></div>
          <?php endif; ?>
           
           
            <div id="poststuff">
                <div id="post-body">
                    <div id="post-body-content">

                            <?php
     $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'nwg-settings';
        ?>
         
        <h2 class="nav-tab-wrapper">
            <a href="?page=nwg-settings-admin&tab=nwg-settings" class="nav-tab <?php echo $active_tab == 'nwg-settings' ? 'nav-tab-active' : ''; ?>"><?php _e('General') ?></a>
            <a href="?page=nwg-settings-admin&tab=HELP" class="nav-tab <?php echo $active_tab == 'HELP' ? 'nav-tab-active' : ''; ?>"><?php _e('HELP') ?></a>
        </h2>

                         <form method="post" action="options.php">
                            <?php
                                // This prints out all hidden setting fields
                                
                                 if( $active_tab == 'nwg-settings' ) {
                                    settings_fields( 'nwg_options' );
                                 do_settings_sections( 'nwg-setting-admin' );
                                 load_template( $dir.'admin-parts/tab-footer.php');

                             } else {
                                 settings_fields( 'nwg_help' );
                                do_settings_sections( 'nwg-help-admin' );
                            }
                                submit_button();
                            ?>
                        </form>
                    </div>
                </div> 
            </div>
          </div><?php load_template( $dir.'admin-parts/sidebar.php'); ?>
        <?php

    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting('nwg_options', 'nwg_options', array( $this, 'sanitize' ));

        add_settings_section(
            'settings_section', // ID
            __('Newsgram settings','newsgram'), // Title
            array( $this, 'nwg_info' ), // Callback
            'nwg-setting-admin' // Page
        ); 

        add_settings_section(
            'HELP_section', // ID
            __('Newsgram settings','newsgram'), // Title
            array( $this, 'nwg_help' ), // Callback
            'nwg-help-admin' // Page
        ); 

        add_settings_field(
            'channel_name', // ID
            __( 'Channel name (without @)', 'newsgram' ), // Title 
            array( $this, 'channel_name_callback' ), // Callback
            'nwg-setting-admin', // Page
            'settings_section' // Section           
        );      

        add_settings_field(
            'bot_id', 
           __( 'Telegram Channel BOT ID', 'newsgram' ),
            array( $this, 'bot_id_callback' ), 
            'nwg-setting-admin', 
            'settings_section'
        ); 
        add_settings_field(
            'nwg_sendaut', 
            __( 'Send automatically post to channel', 'newsgram' ), 
            array( $this, 'nwg_sendaut_callback' ), 
            'nwg-setting-admin', 
            'settings_section'
        );   
        add_settings_field(
            'nwg_intro', 
            __( 'Intro text ', 'newsgram' ), 
            array( $this, 'nwg_intro_callback' ), 
            'nwg-setting-admin', 
            'settings_section'
        );  
       
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['channel_name'] ) )  $new_input['channel_name'] = sanitize_text_field( $input['channel_name'] );
        if( isset( $input['bot_id'] ) )  $new_input['bot_id'] = sanitize_text_field( $input['bot_id'] );
        if( isset( $input['nwg_intro'] ) )  $new_input['nwg_intro'] = sanitize_text_field( $input['nwg_intro'] );

       return array_merge($input, $new_input);
    }

    /** 
     * Print the Section text
     */
    public function nwg_info()
    {
        _e('Insert your information about channel name and BOT','newsgram');
    }

        public function nwg_help()
    {

        $dir = plugin_dir_path( __DIR__ );
         load_template( $dir.'admin-parts/help-channel.php'); 
    }
    /** 
     * Get the settings option array and print one of its values
     */
    public function channel_name_callback()
    {
        printf(
            '<input type="text" id="channel_name" name="nwg_options[channel_name]" value="%s" /> ',
            isset( $this->options['channel_name'] ) ? esc_attr( $this->options['channel_name']) : ''
        );
        $this->tooltip('Name of the channel you chose on Telegram');
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function bot_id_callback()
    {
        printf(
            '<input type="text" id="bot_id" name="nwg_options[bot_id]" value="%s" />',
            isset( $this->options['bot_id'] ) ? esc_attr( $this->options['bot_id']) : ''
        );
        $this->tooltip('BOT ID: You can get your BOT ID on Telegram channel');
    }

   public function nwg_sendaut_callback()
    {
        printf(
             '<label><input type="checkbox" name="nwg_sendaut" id="nwg_sendaut" value="1"%s>%s</label>', isset( $this->options['nwg_sendaut'] ) ? ' checked' : '', __( 'Send automatically post to channel', 'newsgram' )

        );
         $this->tooltip('Activate the automatic post sending');
    }
  public function nwg_intro_callback()
    {
        printf(
            '<textarea id="nwg_intro" name="nwg_options[nwg_intro]" cols="30" rows="5">%s</textarea><br /><span class="description">%s</span>',
            isset( $this->options['nwg_intro'] ) ? esc_attr( $this->options['nwg_intro']) : '',
            __( 'Intro text for the message', 'newsgram' )
        );
         $this->tooltip('The message which is before all of your post sent to the channel');
    }

  public function tooltip($text)
  {
     printf(
            '<span class="setting-tooltip dashicons dashicons-editor-help sizeup" title="%s"></span>', $text
        );

  }

}