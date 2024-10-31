<?php
/*
Plugin Name: Recent Posts Columns Block
Plugin URI: https://tishonator.com/plugins/recent-posts-columns-block
Description: The "Recent Posts Columns Block" plugin is a plugin that introduces a new Gutenberg block. This block enables the presentation of Recent Posts in a columnar layout within your pages, along with the ability to include Pages Block content. It offers customizable features, allowing you to choose whether to display thumbnails, the author, date, or page. This plugin is entirely responsive, ensuring optimal viewing on all devices, and adheres to accessibility standards for inclusive design.
Author: tishonator
Version: 1.0.0
Author URI: http://tishonator.com/
Contributors: tishonator
Text Domain: recent-posts-columns-block
*/

if ( !class_exists('tishonator_tb_RecentPostsColumnsBlockPlugin') ) :

    /**
     * Register the plugin.
     *
     * Display the administration panel, insert JavaScript etc.
     */
    class tishonator_tb_RecentPostsColumnsBlockPlugin {
        
    	/**
    	 * Instance object
    	 *
    	 * @var object
    	 * @see get_instance()
    	 */
    	protected static $instance = NULL;


        /**
         * Constructor
         */
        public function __construct() {}

        /**
         * Setup
         */
        public function setup() {

            add_action( 'init', array(&$this, 'register_scripts') );

            // register a block to display team members
            add_action( 'init', array(&$this, 'register_block') );
        }

        /**
         * Register scripts used to display team members
         */
        public function register_scripts() {

            if ( !is_admin() ) {
                // Recent Posts Columns Block CSS
                wp_register_style('tishonator-bootstrap-css',
                    plugins_url('css/bootstrap.min.css', __FILE__));

                wp_enqueue_style( 'tishonator-bootstrap-css',
                    plugins_url('css/bootstrap.min.css', __FILE__), array( ) );

                wp_register_style('tishonator-recent-posts-columns-block-style',
                    plugins_url('css/recent-posts-columns-block-style.css', __FILE__));

                wp_enqueue_style( 'tishonator-recent-posts-columns-block-style',
                    plugins_url('css/recent-posts-columns-block-style.css', __FILE__), array( ) );
            }
        }

        /*
         * Register Block
         */
        public function register_block() {

            global $pagenow;

            $arrDeps = ($pagenow === 'widgets.php') ?
                array( 'wp-edit-widgets', 'wp-blocks', 'wp-i18n', 'wp-element', )
              : array( 'wp-editor', 'wp-blocks', 'wp-i18n', 'wp-element', );

            // Recent Posts Columns
            wp_register_script(
                'tishonator-recent-posts-columns-block',
                plugins_url('/js/recent-posts-columns-block.js', __FILE__),
                $arrDeps
            );

            register_block_type( 'tishonator/recent-posts-columns', array(
                'editor_script' => 'tishonator-recent-posts-columns-block',
                'render_callback' => array(&$this, 'tishonator_recent_posts_columns_render_callback'),
            ) );
        }

        public function tishonator_recent_posts_columns_render_callback( $attributes, $content ) {
            $count = isset( $attributes['posts'] ) ? count( $attributes['posts'] ) : 3;
            $columnsCount = isset( $attributes['columnsCount'] ) ? absint( $attributes['columnsCount'] ) : 3;
            $excerptLength = isset( $attributes['excerptLength'] ) ? absint( $attributes['excerptLength'] ) : 50;
            $showPages = isset( $attributes['showPages'] ) ? (bool) $attributes['showPages'] : true;
            $displayThumbnails = isset( $attributes['displayThumbnails'] ) ? (bool) $attributes['displayThumbnails'] : true;
            $displayReadMore = isset( $attributes['displayReadMore'] ) ? (bool) $attributes['displayReadMore'] : true;
            $displayAuthor = isset( $attributes['displayAuthor'] ) ? (bool) $attributes['displayAuthor'] : true;
            $displayDate = isset( $attributes['displayDate'] ) ? (bool) $attributes['displayDate'] : true;

            $args = array(
                'post_type'      => 'post',
                'post_status'    => 'publish',
                'orderby'        => 'date',
                'order'          => 'DESC',
                'posts_per_page' => $count,
            );

            if ($showPages) {
                $paged_value = is_front_page() && is_home() ? 'paged' : (is_front_page() ? 'page' : 'paged');
                $args['paged'] = get_query_var($paged_value) ? get_query_var($paged_value) : 1;
            }

            $query = new WP_Query( $args );

            if ( $query->have_posts() ) {
                $output = '<div class="blog-section">
                    <div class="container">
                        <div class="row">';

                while ( $query->have_posts() ) {
                    $query->the_post();
                    $output .= '<div class="col-lg-' . (12 / $columnsCount) . ' col-md-6">
                        <div class="single-blog-box">';
                    if ( $displayThumbnails && has_post_thumbnail() ) {
                        $output .= '<div class="blog-thumb">';
                        $output .= get_the_post_thumbnail();
                        $output .= '</div>';
                    }
                    $output .= '<div class="tishonator-recent-post-meta text-left" style="text-align:left;">';

                    if ($displayAuthor) {
                        $output .= '<a href="#">' . get_the_author() . '</a> ';
                    }

                    // Display date only if displayDate is true
                    if ($displayDate) {
                        $output .= '<span class="tishonator-recent-post-date">' . get_the_date() . '</span>';
                    }

                    $output .= '</div>
                                <div class="tishonator-recent-post-blog-content text-left">
                                    <h2 class="tishonator-recent-post-title text-left" style="text-align:left;"><a href="' . esc_url( get_permalink() ) . '">' . get_the_title() . '</a></h2>
                                    <p>' . wp_trim_words(get_the_excerpt(), $excerptLength, '...') . '</p>';
                    if ($displayReadMore) {
                        $output .= '<div class="tishonator-recent-post-more more-link">
                                        <a href="' . esc_url( get_permalink() ) . '">Read More</a>
                                    </div>';
                    }
                    $output .= '</div>
                            </div>
                        </div>';
                }

                wp_reset_postdata();

                $output .= '</div>
                    </div>
                </div>';

                if ($showPages) {
                    $paged_value = is_front_page() && is_home() ? 'paged' : (is_front_page() ? 'page' : 'paged');
                    $current_page = get_query_var($paged_value) ? get_query_var($paged_value) : 1;
                    $output .= '<div class="pagination-wrap">';
                    $output .= paginate_links(array(
                        'total' => $query->max_num_pages,
                        'prev_text' => '&laquo;',
                        'next_text' => '&raquo;',
                        'current' => $current_page,
                    ));
                    $output .= '</div>';
                }

                return $output;
            } else {
                return '<p>' . __('No posts found.', 'tishonator') . '</p>';
            }
        }

    	/**
    	 * Used to access the instance
         *
         * @return object - class instance
    	 */
    	public static function get_instance() {

    		if ( NULL === self::$instance ) {
                self::$instance = new self();
            }

    		return self::$instance;
    	}
    }

endif; // tishonator_tb_RecentPostsColumnsBlockPlugin

add_action('plugins_loaded',
    array( tishonator_tb_RecentPostsColumnsBlockPlugin::get_instance(), 'setup' ), 10);
