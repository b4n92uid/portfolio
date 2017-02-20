<?php

require(__DIR__ . '/vendor/autoload.php');

if ( ! class_exists( 'Timber' ) ) {
	add_action( 'admin_notices', function() {
		echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php') ) . '</a></p></div>';
	});

	return;
}

class StarterSite extends TimberSite {

	function __construct() {
    add_theme_support( 'post-formats' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'menus' );

		add_filter( 'timber_context', array( $this, 'add_to_context' ) );
		add_filter( 'get_twig', array( $this, 'add_to_twig' ) );

		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );

    add_shortcode( 'skill', array($this, 'parse_skills') );
    add_shortcode( 'tags', array($this, 'parse_tags') );

    add_action( 'after_setup_theme', array($this, 'setup_theme') );

    parent::__construct();
  }

  function setup_theme() {
    add_image_size("article-thumbs", 150, 100, true);
    add_image_size("article-gallery", 1140, 575, true);

    add_filter('image_size_names_choose', 'custom_sizes');

    function custom_sizes( $sizes ) {
        return array_merge( $sizes, array(
            'article-thumbs' => __( 'Article Thumbnails' ),
            'article-gallery' => __( 'Article Gallery' ),
        ) );
    }
  }

  function parse_tags($attrs, $content) {
		return Timber::fetch("include/tags.twig", array('tags' => explode(',', $content)));
  }

  function parse_skills($attrs) {
		return Timber::fetch("include/skill.twig", $attrs);
  }

  function avatar_url($size) {
    $email = md5( strtolower( trim( get_bloginfo('admin_email') ) ) );
    $avatar = "https://www.gravatar.com/avatar/$email?s=$size";

    return $avatar;
  }

	function register_post_types() {
		//this is where you can register custom post types
	}

  function register_taxonomies() {
    //this is where you can register custom taxonomies
  }

	function get_gallery($post) {
	  $ids = json_decode($post->_gallery);
    $srclist = array();

    foreach($ids as $id) {
      $srclist[] = wp_get_attachment_image_src(intval($id), 'full');
    }

    return $srclist;
	}

	function add_to_context( $context ) {
    $context['me'] = new Timber\User(1);
		$context['site'] = $this;
		return $context;
	}

	function add_to_twig( $twig ) {
		/* this is where you can add your own functions to twig */
    $twig->addFilter( new Twig_SimpleFilter('gallery', array($this, 'get_gallery')) );
		$twig->addExtension( new Twig_Extension_StringLoader() );
		return $twig;
	}

}

new StarterSite();
