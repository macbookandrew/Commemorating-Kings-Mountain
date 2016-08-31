<?php
/*
 * Plugin Name: Commemorating Kings Mountain
 * Plugin URI: https://github.com/macbookandrew/commemorating-kings-mountain/
 * Description: Provides shortcodes for displaying historical information on a timeline
 * Version: 1.0.0
 * Author: AndrewRMinion Design
 * Author URI: https://andrewrminion.com
 * GitHub Plugin URI: https://github.com/macbookandrew/commemorating-kings-mountain/
 */

if (!defined('ABSPATH')) {
    exit;
}

// register custom image sizes
add_image_size( 'timeline-square-small', 200, 200, true );
add_image_size( 'timeline-square-medium', 400, 400, true );
add_image_size( 'timeline-square-large', 600, 600, true );
add_image_size( 'timeline-square-xlarge', 900, 900, true );

// add custom image sizes to backend
add_filter( 'image_size_names_choose', 'ckm_custom_sizes' );
function ckm_custom_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'timeline-square-small'     => 'Timeline (S)',
        'timeline-square-medium'    => 'Timeline (M)',
        'timeline-square-large'     => 'Timeline (L)',
        'timeline-square-xlarge'    => 'Timeline (XL)',
    ));
}

// register style
add_action( 'wp_enqueue_scripts', 'ckm_styles' );
function ckm_styles() {
    wp_register_style( 'ckm', plugins_url( 'css/ckm.min.css', __FILE__ ) );
}

// timeline shortcode
add_shortcode( 'ckm_timeline', 'ckm_timeline' );
function ckm_timeline() {
    $shortcode_content = '<section class="timeline">';
    $categories = get_categories();
    wp_enqueue_style( 'ckm' );
    add_filter( 'post_class', 'ckm_timeline_post_class' );

    foreach ( $categories as $category ) {
        $shortcode_content .= '<article class="timeline-major-event category category-' . $category->term_id . ' category-' .$category->slug . '">';
        $shortcode_content .= '<h2 class="entry-title">' . $category->name . '</h2>';
        $shortcode_content .= ( ( property_exists( $category, 'description' ) && isset( $category->description ) ) ? '<div class="taxonomy-description">' . apply_filters( 'the_content', $category->description . '</div>' ) : '' );

        // get all posts from this category
        $posts_args = array(
            'posts_per_page'    => -1,
            'category'          => $category->term_id,
            'order'             => 'ASC',
            'orderby'           => 'meta_value',
            'meta_key'          => 'date',
        );
        $posts_query = new WP_Query( $posts_args );

        if ( $posts_query->have_posts() ) {
            $shortcode_content .= '<section class="timeline-minor-events">';
            while ( $posts_query->have_posts() ) {
                $posts_query->the_post();
                ob_start();
                include( 'inc/content-excerpt.php' );
                $shortcode_content .= ob_get_clean();
            }
            $shortcode_content .= '</section><!-- .timeline-minor-events -->';
        }
        wp_reset_postdata();

        $shortcode_content .= '</article><!-- .timeline-major-event -->';
    }

    $shortcode_content .= '</section><!-- .timeline -->';
    return $shortcode_content;
}

function ckm_timeline_post_class( $classes ) {
    $classes[] = 'timeline-event';
    return $classes;
}
