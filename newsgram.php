<?php
/**
 * @package Newsgram
 * @version 1.0
 */
/*
Plugin Name: Newsgram
Plugin URI: http://www.pigrecolab.com
Description: Send messages like newsletter to Telegram Channel.
Author: Roberto Bruno
Version: 1.0
Author URI: http://www.pigrecolab.com
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Newsgram.
 *
 * Main Newsgram class initializes the plugin.
 *
 * @class		Newsgram
 * @version		1.0.0
 * @author		Roberto Bruno
 */
class Newsgram {

	/**
	 * Plugin version.
	 *
	 * @since 1.0.0
	 * @var string $version Plugin version number.
	 */
	public $version = '1.0.0';

	/**
	 * Plugin file.
	 *
	 * @since 1.0.0
	 * @var string $file Plugin file path.
	 */
	public $file = __FILE__;



	/**
	 * Instance of Newsgram.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var object $instance The instance of Newsgram.
	 */
	private static $instance;


	/**
	 * Construct.
	 *
	 * Initialize the class and plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

				// Initialize plugin parts
		$this->init();
	}

		/**
	 * init.
	 *
	 * Initialize plugin parts.
	 *
	 * @since 1.0.0
	 */
	public function init() {

         $file = dirname(__DIR__) . '/log.txt'; 

        $open = fopen( $file, "w" ); 
        $write = fputs( $open, 'uff'); 
        fclose( $open );

		// Load textdomain
		load_plugin_textdomain('newsgram',false,dirname( plugin_basename( __FILE__ ) ) . '/languages/');

		add_shortcode( 'telegram_qrcode', array($this,'telegram_qrcode' ));
		add_action( 'init', array($this,'register_newsgram' ));
		add_filter('manage_edit-newsgram_columns', array($this,'add_new_newsgram_columns'));
		add_action('manage-newsgram_posts_custom_column', array($this,'manage_newsgram_columns'), 5, 2);
		add_action('after_setup_theme',array($this,'add_nw_metabox'));
		add_action( 'publish_newsgram', array($this,'send_newsgram'), 10, 2 );
/*    add_action('new_to_publish_newsgram', array($this,'send_newsgram'), 10, 2 );    
  add_action('draft_to_publish_newsgram', array($this,'send_newsgram'), 10, 2 );    
  add_action('pending_to_publish_newsgram', array($this,'send_newsgram'), 10, 2 );*/
		add_action( 'publish_post', array($this,'send_postby_newsgram'), 10, 2 );

	}

		/**
	 * Instance.
	 *
	 * An global instance of the class. Used to retrieve the instance
	 * to use on other files/plugins/themes.
	 *
	 * @since 1.0.0
	 * @return object Instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) :
			self::$instance = new self();
		endif;

		return self::$instance;

	}


public function register_newsgram() {

    $labels = array(
        'name' => __( 'Newsgrams', 'newsgram' ),
        'singular_name' => __( 'Newsgram', 'newsgram' ),
        'add_new' => __( 'Add New', 'newsgram' ),
        'add_new_item' => __( 'Add new newsgram', 'newsgram' ),
        'edit_item' => __( 'Edit newsgram', 'newsgram' ),
        'new_item' => __( 'New newsgram', 'newsgram' ),
        'view_item' => __( 'View newsgram', 'newsgram' ),
        'search_items' => __( 'Search newsgram', 'newsgram' ),
        'not_found' =>  __( 'No newsgram found', 'newsgram' ),
        'not_found_in_trash' => __( 'No newsgram found in Trash', 'newsgram' ),
        'parent_item_colon' => __( 'Parent newsgram:', 'newsgram' ),
        'menu_name' => __( 'Newsgrams', 'newsgram' )
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'description' => __( 'Custom Post Type - newsgram Pages', 'newsgram' ),
        'supports' => array( 'title'),
        'taxonomies' => array( 'newsgram-category' ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 20,
        'menu_icon' => 'dashicons-groups',
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post',
    );

  register_post_type( 'newsgram', $args );
   
}



    //Admin Dashobord Listing newsgram Columns Title
public function add_new_newsgram_columns() {
    $columns['cb'] = '<input type="checkbox" />';
    $columns['title'] = __('Title', 'newsgram');
    $columns['type'] =  __('Type', 'newsgram' );
    $columns['author'] = __('Author', 'newsgram' );
    $columns['date'] = __('Date', 'newsgram');
    return $columns; 
}



//Admin Dashobord Listing newsgram Columns Manage
public function manage_newsgram_columns($columns) {
    global $post;
    switch ($columns) {
    case 'type':
         echo get_post_meta($post->ID,'type',true);
         
    break; 
 

    }
}
 
public function add_nw_metabox() {
    
    if (!is_admin()) return;

    //include the main class file
  require_once("meta-box-class/my-meta-box-class.php");

  /* 
   * prefix of meta keys, optional
   * use underscore (_) at the beginning to make keys hidden, for example $prefix = '_ba_';
   *  you also can make prefix empty to disable it
   * 
   */
  $prefix = 'nwg_';
  /* 
   * configure your meta box
   */
  $config = array(
    'id'             => 'newsgram_meta_box',          // meta box id, unique per meta box
    'title'          => __('Newsgram', 'newsgram'),          // meta box title
    'pages'          => array('newsgram'),      // post types, accept custom post types as well, default is array('post'); optional
    'context'        => 'normal',            // where the meta box appear: normal (default), advanced, side; optional
    'priority'       => 'high',            // order of meta box: high (default), low; optional
    'fields'         => array(),            // list of meta fields (can be added by field arrays)
    'local_images'   => false,          // Use local or hosted images (meta box images for add/remove)
    'use_with_theme' => false          //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
  );

    /*
   * Initiate your meta box
   */
  $my_meta =  new AT_Meta_Box($config);
  
  /*
   * Add fields to your meta box
   */
  

  $my_meta->addRadio($prefix.'type',array('text'=>__('Text','newsgram'),'image'=>__('Image','newsgram'),'post'=>__('Post','newsgram')),array('name'=> __('Newsgram type','newsgram')));
  
  $my_meta->addJSCondition('cond1',array(
    'fieldname'   => $prefix.'type',
      'fieldvalue'     => 'image',
      'visible_fields' => 'gramimg'
      )
  );
  $my_meta->addImage('gramimg',array('name'=> 'Image', 'visible' => false));

    $my_meta->addJSCondition('cond2',array(
    'fieldname'   => $prefix.'type',
      'fieldvalue'     => 'text',
      'visible_fields' => 'gramtext'
      )
  );
  $my_meta->addWysiwyg('gramtext',array('name'=> 'Text', 'visible' => false));

   $my_meta->addJSCondition('cond3',array(
    'fieldname'   => $prefix.'type',
      'fieldvalue'     => 'post',
      'visible_fields' => 'grampost'
      )
  );
  $my_meta->addPosts('grampost',array(),array('name'=> 'Post', 'visible' => false));


  /*
   * Don't Forget to Close up the meta box Declaration 
   */
  //Finish Meta Box Declaration 
  $my_meta->Finish();


}


public function send_newsgram( $ID, $post ) {



        $opt=get_option('nwg_options');

        require_once dirname( __FILE__ ) . '/classes/Telegram.php';
        if (($opt['bot_id']=='') || ($opt['channel_name']=='')) {
            wp_die("You should set the correct values for channel and BOT: please contact special-themes.com if you haven't got these values" );
        }
        $bot_id =$opt['bot_id'];
        $telchan='@'.$opt['channel_name'];
        // Instances the class
        $telegram = new Telegram($bot_id);

        $name = get_bloginfo();
        $message='';
        $img='';
        $type=(isset($_POST['nwg_type'])) ? $_POST['nwg_type'] : get_post_meta($ID,'nwg_type',true);


        switch ($type) {
            case 'text':
        
                            // Take text from the message
                $text=(isset($_POST['gramtext'])) ? $_POST['gramtext'] : get_post_meta($ID,'gramtext',true);
                $content = array('chat_id' => $telchan, 'text' => $text, 'parse_mode' => 'HTML');
                //wp_die(var_dump($content));
                $telegram->sendMessage($content);

        
            break; 

            case 'image':
                 $img=(isset($_POST['gramimg'])) ? $_POST['gramimg'] : get_post_meta($ID,'gramimg',true);
                 $image_id=$img['id'];

                 // retrieve the thumbnail size of our image
                $image_thumb = wp_get_attachment_image_src($image_id, 'medium');
                $pieces = explode("wp-content/", $image_thumb[0]);
                 $upfile = realpath("../wp-content/".$pieces[1]);
                    $img = curl_file_create( $upfile,'image/png'); 
                $content = array('chat_id' => $telchan, 'photo' => $img );
                //wp_die(var_dump($content));
                $telegram->sendPhoto($content);
                 
            break; 
 
        case 'post':

                                        // Take intro text from the message
                $intro='';
                $pst=(isset($_POST['grampost'])) ? $_POST['grampost'] : get_post_meta($ID,'gram_post',true);

                $pst=$intro.' '.get_permalink($pst);
                $content = array('chat_id' => $telchan, 'text' => $pst, 'parse_mode' => 'HTML');
               // wp_die(var_dump($content));
                $telegram->sendMessage($content);

                 
            break;

        }
      
}


public function send_postby_newsgram( $ID, $post ) {

      $opt=get_option('nwg_options');


        if ('on' != $opt['nwg_sendaut'] ) return;

        require_once dirname( __FILE__ ) . '/classes/Telegram.php';
        if (($opt['bot_id']=='') || ($opt['channel_name']=='')) {
            wp_die("You should set the correct values for channel and BOT or disable the sending of newsgram on post publishing: please contact special-themes.com if you haven't got these values" );
        }
        $bot_id =$opt['bot_id'];
        $telchan='@'.$opt['channel_name'];
        // Instances the class
        $telegram = new Telegram($bot_id);

        $name = get_bloginfo();

                                // Take intro text from the message
        $intro=$opt['nwg_intro'];
        $pst=$intro.' '.get_permalink($ID);
        $content = array('chat_id' => $telchan, 'text' => $pst, 'parse_mode' => 'HTML');
        $telegram->sendMessage($content);

               
          
}


  /*-----------------------------------------------------------------------------------*/
/* TELEGRAM SHORTCODE */
/*-----------------------------------------------------------------------------------*/

public function telegram_qrcode( $atts, $content = null ) {

	$dir = plugin_dir_path( __FILE__ );

  $shrt = '<img src="'.$dir.'/images/telegram-qrcode.png" class="img-responsive" />' ;


    return $shrt;
  }



}


/**
 * The main function responsible for returning the Newsgram object.
 *
 * Use this function like you would a global variable, except without needing to declare the global.
 *
 * Example: <?php Newsgram()->method_name(); ?>
 *
 * @since 1.0.0
 *
 * @return object Newsgram class object.
 */
if ( ! function_exists( 'Newsgram' ) ) :

 	function Newsgram() {

		if( is_admin() ) {
			require_once ("classes/Nwg_Options.php");
			$wdpopt = new Nwg_Options();
		}

    	return Newsgram::instance();
	}

endif;

Newsgram();

?>
