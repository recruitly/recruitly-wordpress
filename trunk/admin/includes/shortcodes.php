<?php
add_shortcode( 'recruitly_jobs', 'recruitly_wordpress_job_listing_shortcode' );
add_shortcode( 'recruitly_job_search', 'recruitly_wordpress_job_search_shortcode' );
add_shortcode( 'recruitly_job_sector_widget', 'recruitly_wordpress_job_sector_widget_shortcode' );
add_shortcode( 'recruitly_job_tag_widget', 'recruitly_wordpress_job_tags_widget_shortcode' );

/**
 * Renders Job Search Form
 */
function recruitly_wordpress_job_search_shortcode($atts) {
    ob_start();
    extract(shortcode_atts( array(
        'target' => '',
        'cssclass' => 'jumbotron'
    ), $atts ));
    get_recruitly_template('job-search-form.php');
    return ob_get_clean();
}

/**
 * Lists all jobs with pagination support
 */
function recruitly_wordpress_job_listing_shortcode() {

    //recruitly_wordpress_insert_post_type();

    global $wp_query;

    $temp = $wp_query;

    if ( get_query_var( 'paged' ) ) {

        $paged = get_query_var( 'paged' );

    } elseif ( get_query_var( 'page' ) ) {

        $paged = get_query_var( 'page' );

    } else {

        $paged = 1;

    }

    $args = array(
        'post_type'      => 'recruitlyjobs',
        'posts_per_page' => 25,
        'meta_key'       => 'uniqueId',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
        'paged'          => $paged
    );

    if ( isset( $_GET['job_search'] ) ) {

        //Escape Output
        $job_type   = htmlspecialchars( $_GET['job_type'] );
        $job_sector = htmlspecialchars( $_GET['job_sector'] );
        $job_city   = htmlspecialchars( $_GET['job_city'] );
        $job_tag    = htmlspecialchars( $_GET['job_tag'] );
        $q          = htmlspecialchars( $_GET['job_search'] );

        if ( $q ) {
            $args['s'] = $q;
        }

        $args['tax_query'] = array( 'relation' => 'AND' );
        //Tag is a meta query
        if($job_tag){
            $args['meta_query'] = array( 'relation' => 'AND' );
            $args['meta_query'][] = array(
                'value' => $job_tag
            );
        }

        //Job Type, Sector and City are Taxonomy Queries
        if ( $job_type ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'jobtype',
                'field'    => 'slug',
                'terms'    => $job_type
            );
        }

        if ( $job_sector ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'jobsector',
                'field'    => 'slug',
                'terms'    => $job_sector
            );
        }

        if ( $job_city ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'jobcity',
                'field'    => 'slug',
                'terms'    => $job_city
            );
        }

    }
    // $wp_query = null;
   
    $wp_query = new WP_Query( $args );
    ob_start();
    get_recruitly_template('job-listing.php');
    wp_reset_postdata(); // wp_reset_query();  // Restore global post data stomped by the_post().
    $wp_query = $temp;
    return ob_get_clean();
}

/**
 * Renders Job Sector Widget
 */
function recruitly_wordpress_job_sector_widget_shortcode($atts) {

    global $wp_query;
    $temp = $wp_query;
    ob_start();

    extract(shortcode_atts( array(
        'target' => '',
        'sector' => '',
        'count' =>10
    ), $atts ));

    $job_sector = htmlspecialchars($sector);
    $job_count = htmlspecialchars($count);
    $job_search_page = htmlspecialchars($target);

    $job_params= array(
        'sector' =>$job_sector,
        'target' =>$job_search_page
    );

    $args = array(
        'post_type'      => 'recruitlyjobs',
        'posts_per_page' => $job_count,
        'meta_key'       => 'jobStatus',
        'meta_value'     => 'OPEN',
        'paged'          => 1
    );
    $args['tax_query'][] = array(
        'taxonomy' => 'jobsector',
        'field'    => 'slug',
        'terms'    => $job_sector
    );

    $wp_query = new WP_Query( $args );
    get_recruitly_template('job-sector-widget.php',$job_params);
    wp_reset_postdata(); // wp_reset_query();  // Restore global post data stomped by the_post().
    $wp_query = $temp;
    return ob_get_clean();
}

/**
 * Renders Jobs by Tags Widget
 */
function recruitly_wordpress_job_tags_widget_shortcode($atts) {

    global $wp_query;
    $temp = $wp_query;
    ob_start();

    extract(shortcode_atts( array(
        'target' => '',
        'tagkey' => '',
        'tagvalue' => '',
        'count' =>10
    ), $atts ));

    $job_tagkey = htmlspecialchars($tagkey);
    $job_tagval = htmlspecialchars($tagvalue);
    $job_count = htmlspecialchars($count);
    $job_search_page = htmlspecialchars($target);

    $job_params= array(
        'tagkey' =>$job_tagkey,
        'tagvalue' =>$job_tagval,
        'target' =>$job_search_page
    );

    $args = array(
        'post_type'      => 'recruitlyjobs',
        'posts_per_page' => $job_count,
        'meta_value' => $job_tagval,
        'paged'          => 1
    );

    $wp_query = new WP_Query( $args );
    get_recruitly_template('job-tag-widget.php',$job_params);
    wp_reset_postdata(); // wp_reset_query();  // Restore global post data stomped by the_post().
    $wp_query = $temp;
    return ob_get_clean();
}
?>