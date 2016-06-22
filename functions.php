<?php
/**
 * UW-Madison functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * The first function, uwmadison_setup(), sets up the theme by registering support
 * for various features in WordPress, such as post thumbnails, navigation menus, and the like.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * We can remove the parent theme's hook only after it is attached, which means we need to
 * wait until setting up the child theme:
 *
 * <code>
 * add_action( 'after_setup_theme', 'my_child_theme_setup' );
 * function my_child_theme_setup() {
 *     // We are providing our own filter for excerpt_length (or using the unfiltered value)
 *     remove_filter( 'excerpt_length', 'uwmadison_excerpt_length' );
 *     ...
 * }
 * </code>
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package WordPress
 * @subpackage UW_Madison
 * @since UW-Madison 1.0
 */

/**
 * Tell WordPress to run uwmadison_setup() when the 'after_setup_theme' hook is run.
 */
add_action( 'after_setup_theme', 'uwmadison_setup' );

if ( ! function_exists( 'uwmadison_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override uwmadison_setup() in a child theme, add your own uwmadison_setup to your child theme's
 * functions.php file.
 *
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_editor_style() To style the visual editor.
 * @uses add_theme_support() To add support for post thumbnails, automatic feed links, custom headers
 * 	and backgrounds, and post formats.
 * @uses register_nav_menus() To add support for navigation menus.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since UW-Madison 1.0
 */
function uwmadison_setup() {

	/* Make UW-Madison available for translation.
	 * Translations can be added to the /languages/ directory.
	 * If you're building a theme based on UW-Madison, use a find and replace
	 * to change 'uwmadison' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'uwmadison', get_template_directory() . '/languages' );

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// Load up our theme options page and related code.
	require( get_template_directory() . '/inc/theme-options.php' );

	// Grab UW-Madison's Ephemera widget.
	require( get_template_directory() . '/inc/widgets.php' );

	// Add default posts and comments RSS feed links to <head>.
	add_theme_support( 'automatic-feed-links' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menu( 'main_menu', __( 'Main Menu', 'uwmadison' ) );
	register_nav_menu( 'utility_menu', __( 'Utility Menu', 'uwmadison' ) );

	// Add support for a variety of post formats
	add_theme_support( 'post-formats', array( 'aside', 'link', 'gallery', 'status', 'quote', 'image' ) );

	$theme_options = uwmadison_get_theme_options();

	// This theme uses Featured Images (also known as post thumbnails) for per-post/per-page Custom Header images
	add_theme_support( 'post-thumbnails' );

}
endif; // uwmadison_setup

if ( ! function_exists( 'uwmadison_excerpt_length' ) ) :
	/**
	 * Sets the post excerpt length to 40 words.
	 *
	 * To override this length in a child theme, remove the filter and add your own
	 * function tied to the excerpt_length filter hook.
	 */
	function uwmadison_excerpt_length( $length ) {
		return 40;
	}
endif;
add_filter( 'excerpt_length', 'uwmadison_excerpt_length' );

if ( ! function_exists( 'uwmadison_continue_reading_link' ) ) :
	/**
	 * Returns a "Continue Reading" link for excerpts
	 */
	function uwmadison_continue_reading_link() {
		return ' <a href="'. esc_url( get_permalink() ) . '">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'uwmadison' ) . '</a>';
	}
endif;

if ( ! function_exists( 'uwmadison_auto_excerpt_more' ) ) :
	/**
	 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and uwmadison_continue_reading_link().
	 *
	 * To override this in a child theme, remove the filter and add your own
	 * function tied to the excerpt_more filter hook.
	 */
	function uwmadison_auto_excerpt_more( $more ) {
		return ' &hellip;' . uwmadison_continue_reading_link();
	}
endif;
add_filter( 'excerpt_more', 'uwmadison_auto_excerpt_more' );

if ( ! function_exists( 'uwmadison_custom_excerpt_more' ) ) :
	/**
	 * Adds a pretty "Continue Reading" link to custom post excerpts.
	 *
	 * To override this link in a child theme, remove the filter and add your own
	 * function tied to the get_the_excerpt filter hook.
	 */
	function uwmadison_custom_excerpt_more( $output ) {
		if ( has_excerpt() && ! is_attachment() ) {
			$output .= uwmadison_continue_reading_link();
		}
		return $output;
	}
endif;
add_filter( 'get_the_excerpt', 'uwmadison_custom_excerpt_more' );

if ( ! function_exists( 'uwmadison_page_menu_args' ) ) :
	/**
	 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
	 */
	function uwmadison_page_menu_args( $args ) {
		$args['show_home'] = true;
		return $args;
	}
endif;
add_filter( 'wp_page_menu_args', 'uwmadison_page_menu_args' );

if ( ! function_exists( 'UW_Madison_Ephemera_Widget' ) ) :
	/**
	 * Register our sidebars and widgetized areas. Also register the default Epherma widget.
	 *
	 * @since UW-Madison 1.0
	 */
	function uwmadison_widgets_init() {

		register_widget( 'UW_Madison_Ephemera_Widget' );

		register_sidebar( array(
			'name' => __( 'Main Sidebar', 'uwmadison' ),
			'id' => 'sidebar-1',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => "</aside>",
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		) );

		register_sidebar( array(
			'name' => __( 'Showcase Sidebar', 'uwmadison' ),
			'id' => 'sidebar-2',
			'description' => __( 'The sidebar for the optional Showcase Template', 'uwmadison' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => "</aside>",
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		) );

		register_sidebar( array(
			'name' => __( 'Footer Area One', 'uwmadison' ),
			'id' => 'sidebar-3',
			'description' => __( 'An optional widget area for your site footer', 'uwmadison' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => "</aside>",
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		) );

		register_sidebar( array(
			'name' => __( 'Footer Area Two', 'uwmadison' ),
			'id' => 'sidebar-4',
			'description' => __( 'An optional widget area for your site footer', 'uwmadison' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => "</aside>",
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		) );

		register_sidebar( array(
			'name' => __( 'Footer Area Three', 'uwmadison' ),
			'id' => 'sidebar-5',
			'description' => __( 'An optional widget area for your site footer', 'uwmadison' ),
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget' => "</aside>",
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		) );
	}
endif;
add_action( 'widgets_init', 'uwmadison_widgets_init' );

if ( ! function_exists( 'uwmadison_content_nav' ) ) :
	/**
	 * Display navigation to next/previous pages when applicable
	 */
	function uwmadison_content_nav( $nav_id ) {
		global $wp_query;

		if ( $wp_query->max_num_pages > 1 ) : ?>
			<nav id="<?php echo $nav_id; ?>">
				<h3 class="assistive-text"><?php _e( 'Post navigation', 'uwmadison' ); ?></h3>
				<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'uwmadison' ) ); ?></div>
				<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'uwmadison' ) ); ?></div>
			</nav><!-- #nav-above -->
		<?php endif;
	}
endif; // uwmadison_content_nav

/**
 * Return the URL for the first link found in the post content.
 *
 * @since UW-Madison 1.0
 * @return string|bool URL or false when no link is present.
 */
function uwmadison_url_grabber() {
	if ( ! preg_match( '/<a\s[^>]*?href=[\'"](.+?)[\'"]/is', get_the_content(), $matches ) )
		return false;

	return esc_url_raw( $matches[1] );
}

if ( ! function_exists( 'uwmadison_footer_sidebar_class' ) ) :
	/**
	 * Count the number of footer sidebars to enable dynamic classes for the footer
	 */
	function uwmadison_footer_sidebar_class() {
		$count = 0;

		if ( is_active_sidebar( 'sidebar-3' ) )
			$count++;

		if ( is_active_sidebar( 'sidebar-4' ) )
			$count++;

		if ( is_active_sidebar( 'sidebar-5' ) )
			$count++;

		$class = '';

		switch ( $count ) {
			case '1':
				$class = 'one';
				break;
			case '2':
				$class = 'two';
				break;
			case '3':
				$class = 'three';
				break;
		}

		if ( $class )
			echo 'class="' . $class . '"';
	}
endif;

if ( ! function_exists( 'uwmadison_comment' ) ) :
	/**
	 * Template for comments and pingbacks.
	 *
	 * To override this walker in a child theme without modifying the comments template
	 * simply create your own uwmadison_comment(), and that function will be used instead.
	 *
	 * Used as a callback by wp_list_comments() for displaying the comments.
	 *
	 * @since UW-Madison 1.0
	 */
	function uwmadison_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		switch ( $comment->comment_type ) :
			case 'pingback' :
			case 'trackback' :
		?>
		<li class="post pingback">
			<p><?php _e( 'Pingback:', 'uwmadison' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit', 'uwmadison' ), '<span class="edit-link">', '</span>' ); ?></p>
		<?php
				break;
			default :
		?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
			<article id="comment-<?php comment_ID(); ?>" class="comment">
				<footer class="comment-meta">
					<div class="comment-author vcard">
						<?php
							$avatar_size = 68;
							if ( '0' != $comment->comment_parent )
								$avatar_size = 39;

							echo get_avatar( $comment, $avatar_size );

							/* translators: 1: comment author, 2: date and time */
							printf( __( '%1$s on %2$s <span class="says">said:</span>', 'uwmadison' ),
								sprintf( '<span class="fn">%s</span>', get_comment_author_link() ),
								sprintf( '<a href="%1$s"><time pubdate datetime="%2$s">%3$s</time></a>',
									esc_url( get_comment_link( $comment->comment_ID ) ),
									get_comment_time( 'c' ),
									/* translators: 1: date, 2: time */
									sprintf( __( '%1$s at %2$s', 'uwmadison' ), get_comment_date(), get_comment_time() )
								)
							);
						?>

						<?php edit_comment_link( __( 'Edit', 'uwmadison' ), '<span class="edit-link">', '</span>' ); ?>
					</div><!-- .comment-author .vcard -->

					<?php if ( $comment->comment_approved == '0' ) : ?>
						<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'uwmadison' ); ?></em>
						<br />
					<?php endif; ?>

				</footer>

				<div class="comment-content"><?php comment_text(); ?></div>

				<div class="reply">
					<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply <span>&darr;</span>', 'uwmadison' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
				</div><!-- .reply -->
			</article><!-- #comment-## -->

		<?php
				break;
		endswitch;
	}
endif; // ends check for uwmadison_comment()

if ( ! function_exists( 'uwmadison_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 * Create your own uwmadison_posted_on to override in a child theme
	 *
	 * @since UW-Madison 1.0
	 */
	function uwmadison_posted_on() {
		printf( __( '<span class="sep">Posted on </span><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s" pubdate>%4$s</time></a><span class="by-author"> <span class="sep"> by </span> <span class="author vcard"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span></span>', 'uwmadison' ),
			esc_url( get_permalink() ),
			esc_attr( get_the_time() ),
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_attr( sprintf( __( 'View all posts by %s', 'uwmadison' ), get_the_author() ) ),
			get_the_author()
		);
	}
endif;

if ( ! function_exists( 'uwmadison_body_classes' ) ) :
	/**
	 * Adds two classes to the array of body classes.
	 * The first is if the site has only had one author with published posts.
	 * The second is if a singular post being displayed
	 *
	 * @since UW-Madison 1.0
	 */
	function uwmadison_body_classes( $classes ) {

		if ( function_exists( 'is_multi_author' ) && ! is_multi_author() )
			$classes[] = 'single-author';

		if ( is_singular() && ! is_home() && ! is_page_template( 'showcase.php' ) && ! is_page_template( 'sidebar-page.php' ) )
			$classes[] = 'singular';

		return $classes;
	}
endif;
add_filter( 'body_class', 'uwmadison_body_classes' );

if ( ! function_exists( 'uwmadison_javascripts' ) ) :
	/**
	 * Enqueue uwmadison javascripts
	 */
	function uwmadison_javascripts() {
		// Load js/uwmadison.js, requiring jQuery
		// The last TRUE will load this in the footer
		wp_enqueue_script( 'uwmadison-theme', get_bloginfo('template_url').'/js/uwmadison.js', array('jquery'), '1.0.4', TRUE );
	}
endif;
add_action('wp_enqueue_scripts', 'uwmadison_javascripts');

if ( ! function_exists( 'output_uw_wordpress_banner' ) ) :
/**
  * DEFAULT: uses Site Title setting
	* 
  * FOR HIGH-PROFILE SITE: High-profile, public Websites (e.g. )college, school or 
  * dvisional websites) should use a Friz Quadrata type graphic for th site title 
  * in order to more closelt align with the university's official visual identity
	* guidelines.
	*
  * 1) Email web@uc.wisc.edu if you need help creating a Friz Quadrata type graphic
  * for your unit. Let us know that it is for use in the UW-Madison WordPress theme.
  *
  * 2) Place your title graphic in your theme files. 
  * If using a child theme (recommended), just place your banner title image
  * in your child theme at images/logo_title.png
  *
  * If not using a child them, replace images/logo_title.png with your version
  *	leaving the fileame the same.
  *
  * 3) Copy the function below to your child theme and uncomment line 420  and comment line 422
*/
function output_uw_wordpress_banner() {
  //default theme banner image
  $image_dir_path = is_child_theme() ? get_bloginfo("stylesheet_directory") : get_bloginfo("template_directory");

  // $img_html = '<img alt="' . get_bloginfo("name") . '" src="' . $image_dir_path . '/images/logo_title.png">';

  $img_html = get_bloginfo( 'name' );

  echo $img_html;
}
endif;


