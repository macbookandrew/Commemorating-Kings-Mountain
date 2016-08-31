<?php
/**
 * The default template for displaying content
 *
 * Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true"><?php the_post_thumbnail( 'timeline-square-small' ); ?></a>

    <div class="content">
        <header class="entry-header">
            <?php
                the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );

                $begin_date = date_create( get_field( 'date' ) );
                $end_date = ( get_field( 'end_date' ) ? date_create( get_field( 'end_date' ) ) : '' );

                if ( '1' == get_field( 'fuzzy_date' ) ) {
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

                printf( '<p class="posted-on"><span class="screen-reader-text">%1$s </span>%2$s</p>',
                    'Publication date:',
                    $time_string
                );
            ?>
        </header><!-- .entry-header -->

        <div class="entry-content">
            <?php the_excerpt(); ?>
        </div><!-- .entry-content -->
    </div>

</article><!-- #post-## -->
