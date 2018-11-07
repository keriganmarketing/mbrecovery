<?php // USP Posts - Single - Theme Template

/*
	This is a bare-bones template file that you can copy and paste into your theme
	When included, this file will enable display of the "usp_post" post type, single-post view
	It is provided to give you a starting point for integrating custom post types into your theme
	Once you see how it works, you can customize it to fit your theme and do just about anything
	To see it work, publish a USP Form, include this file, and visit @ http://example.com/usp_post/new-post/
*/

get_header();

if (have_posts()) : while(have_posts()) : the_post();

	the_title();
	echo '<div class="entry-content">';
	the_content();
	echo '</div>';

endwhile; 
endif;

get_footer();