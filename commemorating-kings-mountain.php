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

// override theme meta
function twentyfifteen_entry_meta() {
    if ( is_sticky() && is_home() && ! is_paged() ) {
        printf( '<span class="sticky-post">%s</span>', __( 'Featured', 'twentyfifteen' ) );
    }

    if ( in_array( get_post_type(), array( 'post', 'attachment' ) ) ) {
        $begin_date = date_create( get_field( 'date', get_the_ID() ) );
        $end_date = ( get_field( 'end_date' ) ? date_create( get_field( 'end_date' ) ) : '' );

        if ( '1' == get_field( 'fuzzy' ) ) {
            $date_string = $begin_date->format( 'F Y' );
        } else {
            if ( $end_date && $begin_date != $end_date ) {
                $date_string = $begin_date->format( 'F j–' ) . $end_date->format( 'j, Y' );
            } else {
                $date_string = $begin_date->format( 'F j, Y' );
            }
        }
        $time_string = sprintf( '<time class="entry-date" datetime="%1$s">%2$s</time>',
            esc_attr( get_field( 'date' ) ),
            $date_string
        );

        printf( '<span class="posted-on"><span class="screen-reader-text">%1$s </span><a href="%2$s" rel="bookmark">%3$s</a></span>',
            'Publication date:',
            esc_url( get_permalink() ),
            $time_string
        );
    }

    if ( 'post' == get_post_type() ) {
        $tags_list = get_the_tag_list( '', _x( ', ', 'Used between list items, there is a space after the comma.', 'twentyfifteen' ) );
        if ( $tags_list ) {
            printf( '<span class="tags-links"><span class="screen-reader-text">%1$s </span>%2$s</span>',
                _x( 'Tags', 'Used before tag names.', 'twentyfifteen' ),
                $tags_list
            );
        }
    }

    if ( is_attachment() && wp_attachment_is_image() ) {
        // Retrieve attachment metadata.
        $metadata = wp_get_attachment_metadata();

        printf( '<span class="full-size-link"><span class="screen-reader-text">%1$s </span><a href="%2$s">%3$s &times; %4$s</a></span>',
            _x( 'Full size', 'Used before full size attachment link.', 'twentyfifteen' ),
            esc_url( wp_get_attachment_url() ),
            $metadata['width'],
            $metadata['height']
        );
    }

    if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
        echo '<span class="comments-link">';
        /* translators: %s: post title */
        comments_popup_link( sprintf( __( 'Leave a comment<span class="screen-reader-text"> on %s</span>', 'twentyfifteen' ), get_the_title() ) );
        echo '</span>';
    }
function ckm_timeline_post_class( $classes ) {
    $classes[] = 'timeline-event';
    return $classes;
}
