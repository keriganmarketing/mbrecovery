<?php // USP Pro - Custom Post Type for USP Posts

if (!defined('ABSPATH')) die();

/*
	Class: Custom Post Type for USP Posts
	Provides various functions for the usp_post post type
*/
if (!class_exists('USP_Pro_Posts')) {
	class USP_Pro_Posts {
		const POST_TYPE = 'USP_Post';
		public function __construct() {
			add_action('init', array(&$this, 'init'));
		}
		public function init() {
			$this->create_post_type();
			$this->create_post_examples();
			add_action('admin_init', array(&$this, 'settings_updated'));
		}
		public static function create_post_type() {
			global $usp_advanced;
			if (isset($usp_advanced['post_type_slug']) && !empty($usp_advanced['post_type_slug'])) $post_slug = $usp_advanced['post_type_slug'];
			if ($usp_advanced['post_type'] == 'usp_post') {
				$capabilities = array(
					'edit_post'              => 'edit_usp_post',
					'read_post'              => 'read_usp_post',
					'delete_post'            => 'delete_usp_post',
					'edit_posts'             => 'edit_usp_posts',
					'publish_posts'          => 'publish_usp_posts',
					'edit_others_posts'      => 'edit_others_usp_posts',
					'read_private_posts'     => 'read_private_usp_posts',
					'delete_posts'           => 'delete_usp_posts',
					'delete_private_posts'   => 'delete_private_usp_posts',
					'delete_published_posts' => 'delete_published_usp_posts',
					'delete_others_posts'    => 'delete_others_usp_posts',
					'edit_private_posts'     => 'edit_private_usp_posts',
					'edit_published_posts'   => 'edit_published_usp_posts',
				);
				$labels = array(
					'name'               => esc_html__('USP Posts',                   'usp-pro'),
					'singular_name'      => esc_html__('USP Post',                    'usp-pro'),
					'add_new'            => esc_html__('Add New',                     'usp-pro'),
					'add_new_item'       => esc_html__('Add New USP Post',            'usp-pro'),
					'edit_item'          => esc_html__('Edit USP Post',               'usp-pro'),
					'new_item'           => esc_html__('New USP Post',                'usp-pro'),
					'view_item'          => esc_html__('View USP Post',               'usp-pro'),
					'all_items'          => esc_html__('All USP Posts',               'usp-pro'),
					'search_items'       => esc_html__('Search USP Posts',            'usp-pro'),
					'not_found'          => esc_html__('No USP Posts found',          'usp-pro'),
					'not_found_in_trash' => esc_html__('No USP Posts found in Trash', 'usp-pro'),
					'parent_item_colon'  => esc_html__('Parent USP Post:',            'usp-pro'),
					'menu_name'          => esc_html__('USP Posts',                   'usp-pro'),
				);
				$args = array(
					'labels'              => $labels,
					'description'         => esc_html__('USP Post Types', 'usp-pro'),
					'public'              => true,
					'show_ui'             => true,
					'show_in_menu'        => true,
					'publicly_queryable'  => true, 
					'exclude_from_search' => false,
					'show_in_nav_menus'   => true,
					'menu_position'       => null,
					'menu_icon'           => 'dashicons-admin-post',
					'hierarchical'        => false,
					'taxonomies'          => array('category', 'post_tag', 'page-category', 'optional'),
					'supports'            => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'page-attributes'),
					'has_archive'         => true,
					'can_export'          => true,
					'rewrite'             => array('slug' => $post_slug, 'with_front' => true),
					'query_var'           => $post_slug,
					//
					'capability_type'     => 'usp_post',
					'map_meta_cap'        => true, 
					'capabilities'        => $capabilities,
				);
				register_post_type(strtolower(self::POST_TYPE), $args);
			}
		}
		public function settings_updated() {
			global $pagenow, $usp_advanced;
			if (is_admin() && $pagenow == 'options-general.php') {
				if (isset($_GET['page']) && $_GET['page'] == 'usp_options') {
					if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
						if (isset($usp_advanced['post_type_role'])) {
							$roles = $usp_advanced['post_type_role'];
							$matches = array();
							$misses = array();
							foreach (get_editable_roles() as $role_name => $role_info) {
								if (in_array($role_name, $roles)) $matches[] = $role_name;
								else $misses[] = $role_name;
							}
							foreach ($matches as $match) $this->add_capability($match);
							foreach ($misses as $miss) $this->remove_capability($miss);
						}
					}
				}
			}
		}
		public function add_capability($role) {
			$role_obj = get_role($role);
			$caps = $this->default_caps();
			foreach ($caps as $cap) $role_obj->add_cap($cap);
		}
		public function remove_capability($role) {
			$role_obj = get_role($role);
			$caps = $this->default_caps();
			foreach ($caps as $cap) $role_obj->remove_cap($cap);
		}
		public static function default_caps() {
			$caps = array(
				'edit_usp_post',
				'read_usp_post',
				'delete_usp_post',
				'edit_usp_posts',
				'publish_usp_posts',
				'edit_others_usp_posts',
				'read_private_usp_posts',
				'delete_usp_posts',
				'delete_private_usp_posts',
				'delete_published_usp_posts',
				'delete_others_usp_posts',
				'edit_private_usp_posts',
				'edit_published_usp_posts'
			);
			return $caps;	
		}
		public function create_post_examples() {
			global $usp_advanced;
			if ($usp_advanced['post_type'] == 'usp_post') {
				if ($usp_advanced['post_demos']) {
					
					$usp_post  = esc_html__('This is an example of a custom USP Post. ', 'usp-pro');
					$usp_post .= esc_html__('To delete this post, disable the option "Auto-generate post demo" (Advanced settings tab) and then delete as normal. ', 'usp-pro');
					$usp_post .= esc_html__('To restore the demo post, re-enable the auto-generate option.', 'usp-pro');
					
					$usp_shortcode  = '<p>'. esc_html__('This demo includes a few of the plugin&rsquo;s shortcodes. Visit this post on the front-end to see it in action!', 'usp-pro') .'</p>'. "\n";
					
					$usp_shortcode .= '<h3>Display user info</h3>'. "\n";
					$usp_shortcode .= '<p>'. esc_html__('Hello,', 'usp-pro') .' [usp_status display="name"]'. esc_html__('! Here is your information:', 'usp-pro') .'</p>'. "\n";
					
					$usp_shortcode .= '<ul>'. "\n";
					$usp_shortcode .= '<li>'. esc_html__('ID:',    'usp-pro') .' [usp_status display="id"]</li>'    . "\n";
					$usp_shortcode .= '<li>'. esc_html__('Role:',  'usp-pro') .' [usp_status display="role"]</li>'  . "\n";
					$usp_shortcode .= '<li>'. esc_html__('Email:', 'usp-pro') .' [usp_status display="email"]</li>' . "\n";
					$usp_shortcode .= '</ul>'. "\n";
					
					$usp_shortcode .= '<h3>'. esc_html__('Display content based on user role', 'usp-pro') .'</h3>'. "\n";
					$usp_shortcode .= '<p>[usp_access cap="switch_themes" deny=""]'. esc_html__('Content for Admins only.', 'usp-pro') .'[/usp_access]</p>'. "\n";
					
					$usp_shortcode .= '<h3>'. esc_html__('Display content for any logged-in user', 'usp-pro') .'</h3>'. "\n";
					$usp_shortcode .= '<p>[usp_member deny="Login required!"]'. esc_html__('Content for logged-in users only.', 'usp-pro') .'[/usp_member]</p>'. "\n";
					
					$usp_shortcode .= '<h3>'. esc_html__('Display content for visitors', 'usp-pro') .'</h3>'. "\n";
					$usp_shortcode .= '<p>[usp_visitor deny="'. esc_html__('Sorry, you are logged in!', 'usp-pro') .'"]'. esc_html__('Content for visitors (not logged in) only.', 'usp-pro') .'[/usp_visitor]</p>'. "\n";
					
					$existing_post = get_page_by_title('USP Post Demo', ARRAY_A, 'usp_post');
					$existing_shortcode = get_page_by_title('USP Shortcode Demo', ARRAY_A, 'usp_post');
					if (!$existing_post) {
						$post_demo = array(
							'comment_status' => 'closed',
							'ping_status'    => 'closed',
							'post_content'   => $usp_post,
							'post_name'      => 'example-post',
							'post_status'    => 'draft',
							'post_title'     => 'USP Post Demo',
							'post_type'      => 'usp_post',
						);
						$post_demo_ID = wp_insert_post($post_demo);
						add_post_meta($post_demo_ID, 'is_submission', '1', true);
					}
					if (!$existing_shortcode) {
						$shortcode_demo = array(
							'comment_status' => 'closed',
							'ping_status'    => 'closed',
							'post_content'   => $usp_shortcode,
							'post_name'      => 'shortcodes',
							'post_status'    => 'draft',
							'post_title'     => 'USP Shortcode Demo',
							'post_type'      => 'usp_post',
						);
						$shortcode_demo_ID = wp_insert_post($shortcode_demo);
						add_post_meta($shortcode_demo_ID, 'is_submission', '1', true);
					}
				}
			}
		}
	}
}



// meta boxes
function usp_custom_meta() {
	global $post;
	
	$post_types = get_post_types(array('public' => true, '_builtin' => false));
	array_push($post_types, 'post', 'page');
	
	$display_box = false;
	$post_meta = get_post_meta($post->ID);
	if (!empty($post_meta)) {
		foreach($post_meta as $key => $value) {
			if (strpos($key, '_usp_') !== false || $key === 'usp-custom-metabox-display') {
				$display_box = true; 
				break;
			}
		}
	}
	if ($display_box) {
		$meta_box_title = apply_filters('usp_meta_box_title', esc_html__('USP Custom Fields', 'usp-pro'));
		foreach ($post_types as $post_type) add_meta_box('usp_meta', $meta_box_title, 'usp_meta_callback', $post_type);
	}
}
add_action('add_meta_boxes', 'usp_custom_meta');

function usp_get_meta_box_fields($post_meta) {
	
	$field_array = array();
	if (is_array($post_meta)) {
		foreach ($post_meta as $key => $value) {
			if ($key === 'usp-custom-metabox-display') {
				if (is_array($value)) {
					foreach ($value as $k => $v) {
						$field_array = array_map('trim', explode(',', $v));
					}
				}
			}
			if (preg_match("/^_usp_([0-9a-z_-]+)?$/i", $key, $match)) {
				if (is_array($value)) {
					foreach ($value as $k => $v) {
						if (!isset($field_array[$key])) $field_array[] = $key; 
					}
				}
			}
		}
	}
	return $field_array;
}

function usp_get_meta_custom_fields($post_meta, $field_array) {
	
	$custom_fields = array();
	foreach ($post_meta as $key => $value) {
		if (in_array($key, $field_array)) {
			if (is_array($value)) {
				foreach ($value as $k => $v) {
					
					if (strpos($key, '_usp_') !== false) {
						$label = str_replace('_usp_', '', $key);
						
					} elseif (strpos($key, 'usp-') !== false) {
						$label = str_replace('usp-', '', $key);
						
					} else {
						$label = $key;
					}
					$label = ucwords(trim(str_replace('_', ' ', $label)));
					$label = ucwords(trim(str_replace('-', ' ', $label)));
					
					$custom_fields[] = array($key, $label, $v);
				}
			}
		}
	}
	return $custom_fields;
}

function usp_meta_callback($post) {
	
	wp_nonce_field(basename(__FILE__), 'usp_meta_nonce');
	
	$post_meta = get_post_meta($post->ID);
	$field_array = usp_get_meta_box_fields($post_meta);
	$custom_fields = usp_get_meta_custom_fields($post_meta, $field_array);
	
	$meta_box_name  = apply_filters('usp_meta_box_name', esc_html__('Name', 'usp-pro'));
	$meta_box_value = apply_filters('usp_meta_box_value', esc_html__('Value', 'usp-pro')); ?>
	
	<table class="usp-meta-boxes">
	<thead><tr><th><?php echo $meta_box_name; ?></th><th><?php echo $meta_box_value; ?></th></tr></thead><tbody>
		
	<?php foreach ($custom_fields as $custom_field) : 
		
		$key   = isset($custom_field[0]) ? $custom_field[0] : esc_html__('undefined', 'usp-pro');
		$label = isset($custom_field[1]) ? $custom_field[1] : esc_html__('undefined', 'usp-pro');
		$value = isset($custom_field[2]) ? $custom_field[2] : esc_html__('undefined', 'usp-pro'); ?>
	
		<tr class="usp-meta-box">
			<td class="usp-meta-name"><label for="<?php echo esc_attr($key); ?>" class="usp-meta-label"><?php echo esc_html($label); ?></label></td>
			<td class="usp-meta-value"><input name="<?php echo esc_attr($key); ?>" id="<?php echo esc_attr($key); ?>" class="usp-meta-input" type="text" value="<?php echo esc_attr($value); ?>" /></td>
		</tr>
	
	<?php endforeach; ?>
	
	</tbody></table>
	
<?php }

function usp_meta_save($post_id) {
	
	$is_autosave = wp_is_post_autosave($post_id);
	$is_revision = wp_is_post_revision($post_id);
	$is_valid_nonce = (isset($_POST['usp_meta_nonce']) && wp_verify_nonce($_POST['usp_meta_nonce'], basename(__FILE__))) ? 'true' : 'false';
	
	if ($is_autosave || $is_revision || !$is_valid_nonce) return;
	
	$custom_fields = array();
	$post_meta = get_post_meta($post_id);
	$field_array = usp_get_meta_box_fields($post_meta);
	
	if (is_array($post_meta)) {
		foreach ($post_meta as $key => $value) {
			if (in_array($key, $field_array)) {
				if (isset($_POST[$key])) {
					$update = update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
				}
			}
		}
	}
}
add_action('save_post', 'usp_meta_save');


