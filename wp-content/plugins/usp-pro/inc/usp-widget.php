<?php // USP Pro - Form Widget

if (!defined('ABSPATH')) die();

/*
	Class: USP Form
	Displays USP Form as widget
	@ https://codex.wordpress.org/Widgets_API
*/
if (!class_exists('USP_Form_Widget')) :

class USP_Form_Widget extends WP_Widget {
	
	public function __construct() {
		
		$args = array('classname' => 'usppro', 'description' => esc_html__('Display any USP Form as a widget', 'usp-pro'));
		parent::__construct('usp_form_widget', esc_html__('USP Form Widget', 'usp-pro'), $args);
		
	}
	
	public function widget($args, $instance) {
		
		extract($args);
		
		$id    = $instance['id'];
		$class = $instance['class'];
		$title = $instance['title'];
		
		$postID  = trim($instance['postID']);
		$postIDs = explode(',', $postID);
		
		$usp_args = array('id' => $id, 'class' => $class, 'title' => $title, 'widget' => true);
		
		if (!empty($postID)) {
			
			foreach ($postIDs as $ID) {
				$ID = trim($ID);
				if (is_single($ID) || is_page($ID)) {
					echo usp_form($usp_args);
				}
			}
			
		} else {
			
			echo usp_form($usp_args);
		}
		
	}
	
	public function update($new_instance, $old_instance) {
		
		$instance = $old_instance;
		
		$instance['id']     = $new_instance['id'];
		$instance['class']  = $new_instance['class'];
		$instance['postID'] = $new_instance['postID'];
		$instance['title']  = strip_tags($new_instance['title']);
		
		return $instance;
		
	}
	
	public function form($instance) {
		
		if ($instance) {
			
			$instance = wp_parse_args((array) $instance, array('title' => ''));
			$id       = esc_attr($instance['id']);
			$class    = esc_attr($instance['class']);
			$postID   = esc_attr($instance['postID']);
			$title    = strip_tags($instance['title']);
			
		} else {
			
			$id     = '';
			$class  = '';
			$postID = '';
			$title  = '';
			
		} ?>
		
		<p>
			<strong><?php esc_html_e('Widget Title', 'usp-pro'); ?></strong><br />
			<input name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /><br />
			<small><label for="<?php echo $this->get_field_id('title'); ?>">
				<?php esc_html_e('Enter an optional title for this widget (useful when using multiple form widgets). Displayed only on the widget panel here in the Admin Area.', 'usp-pro'); ?>
			</label></small>
		</p>
		<p>
			<strong><?php esc_html_e('USP Form ID', 'usp-pro'); ?></strong><br />
			<input name="<?php echo $this->get_field_name('id'); ?>" id="<?php echo $this->get_field_id('id'); ?>" type="text" value="<?php echo esc_attr($id); ?>" /><br />
			<small><label for="<?php echo $this->get_field_id('id'); ?>">
				<?php esc_html_e('Enter the ID of a published USP Form. Make sure the form is published and not saved as a draft or pending. Check the form&rsquo;s shortcode for its ID. (Required)', 'usp-pro'); ?>
			</label></small>
		</p>
		<p>
			<strong><?php esc_html_e('Custom CSS Classes', 'usp-pro'); ?></strong><br />
			<input name="<?php echo $this->get_field_name('class'); ?>" id="<?php echo $this->get_field_id('class'); ?>" type="text" value="<?php echo esc_attr($class); ?>" /><br />
			<small><label for="<?php echo $this->get_field_id('class'); ?>">
				<?php esc_html_e('Optional custom classes for the form. Use commas to separate each class, like so: class1,class2,class3', 'usp-pro'); ?>
			</label></small>
		</p>
		<p>
			<strong><?php esc_html_e('Limit Form to Post/Page', 'usp-pro'); ?></strong><br />
			<input name="<?php echo $this->get_field_name('postID'); ?>" id="<?php echo $this->get_field_id('postID'); ?>" type="text" value="<?php echo esc_attr($postID); ?>" /><br />
			<small><label for="<?php echo $this->get_field_id('postID'); ?>">
				<?php esc_html_e('Enter the ID of a specific post or page; leave blank to display on all posts/pages. Separate multiple IDs with a comma.', 'usp-pro'); ?>
			</label></small>
		</p>
		
<?php }

}

function usp_pro_register_form_widget() { register_widget('USP_Form_Widget'); }
add_action('widgets_init', 'usp_pro_register_form_widget');

endif;
