<?php
/**
 * The main template file
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

$context = Timber::get_context();

$templates = array( 'index.twig' );

if(is_home()) {

  $context['skills'] = new Timber\Post('skills');

  $context['articles_list'] = Timber::get_posts(array(
    'category_name' => 'articles',
    'posts_per_page' => -1
  ));

  $context['projects_list'] = Timber::get_posts(array(
    'category_name' => 'projects',
    'posts_per_page' => -1
  ));

  // $context['articles'] = Timber::get_term('articles', 'category');

  // $context['projects'] = Timber::get_term('projects', 'category');

	array_unshift( $templates, 'home.twig' );
}

if(is_search()) {
  $context['posts'] = Timber::get_posts();
  // $context['posts'] = Timber::get_posts(array(
    // 'post_type' => 'post',
  // ));

  array_unshift( $templates, 'search.twig' );
}

Timber::render( $templates, $context );
