<?php
/*
Plugin Name: Super Simple Social Tags
Description: Super lightweight plugin to add social media meta tags for Facebook and Twitter. Adds tags to posts.
Author: Beefy Software
Author URI: https://beefysoftware.com/
Version: 1.0

License: GNU General Public License v2.0 (or later)
License URI: http://www.opensource.org/licenses/gpl-license.php
*/

/*
###################################################################################################
Changelog:

2018-08-30 Initial version

*/

// ########################################### //
// Add Facebook meta to language_attributes

function ssst_add_og_lang($output) {
	if(is_single() )
	{
	$output .= ' prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#"';
	}
	else
	{
	$output .= ' prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#"';
	}
	return $output;
}

add_filter('language_attributes', 'ssst_add_og_lang');

// Add Open Graph and Twitter meta tags to <head>

function ssst_add_og_tags() {
	if (is_single() && has_post_thumbnail()) {
		global $post;
		if(get_the_post_thumbnail($post->ID, 'thumbnail')) {
			$thumbnail_id = get_post_thumbnail_id($post->ID);
			$thumbnail_object = get_post($thumbnail_id);
			$image = $thumbnail_object->guid;
		}
		//$description = get_bloginfo('description');
		$description = ssst_custom_excerpt( $post->post_content, $post->post_excerpt );
		$description = strip_tags($description);
		$description = str_replace("\"", "'", $description);
?>
<meta property="og:site_name" content="<?php echo get_bloginfo('name'); ?>" />
<meta property="og:type" content="article" />
<meta property="og:title" content="<?php the_title(); ?>" />
<meta property="og:description" content="<?php echo $description ?>" />
<meta property="og:image" content="<?php echo $image ?>" />
<meta property="og:url" content="<?php the_permalink(); ?>" />
<meta name="twitter:card" content="summary_large_image">
<?php
}}
add_action('wp_head', 'ssst_add_og_tags');

// Override excerpt_length and excerpt_more

function ssst_custom_excerpt($text, $excerpt){

    if ($excerpt) return $excerpt;

    $text = strip_shortcodes( $text );

    $text = apply_filters('the_content', $text);
    $text = str_replace(']]>', ']]&gt;', $text);
    $text = strip_tags($text);
    $excerpt_length = apply_filters('excerpt_length', 55);
    $excerpt_more = apply_filters('excerpt_more', ' ' . '...');
    $words = preg_split("/[\n
	 ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
    if ( count($words) > $excerpt_length ) {
            array_pop($words);
            $text = implode(' ', $words);
            $text = $text . $excerpt_more;
    } else {
            $text = implode(' ', $words);
    }

    return apply_filters('wp_trim_excerpt', $text, $excerpt);
}

// END OF FILE
