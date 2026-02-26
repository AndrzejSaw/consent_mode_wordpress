<?php
/**
 * Script Guard module for RU Consent Mode plugin.
 *
 * Blocks tracking scripts until user provides consent.
 * Converts script tags to placeholders and reactivates them based on consent.
 *
 * @package RUConsentMode\Front
 */

namespace RUConsentMode\Front;

/**
 * ScriptGuard class.
 *
 * Handles script blocking and reactivation based on consent categories.
 */
class ScriptGuard {
	/**
	 * Singleton instance.
	 *
	 * @var ScriptGuard|null
	 */
	private static $instance = null;

	/**
	 * Categories map from settings.
	 *
	 * @var array
	 */
	private $categories_map = [];

	/**
	 * Consent categories priority order.
	 * Higher priority = processed first if handle is in multiple categories.
	 *
	 * Priority order: ads > analytics > functional
	 *
	 * @var array
	 */
	private $category_priority = [
		'ads'        => 3,
		'analytics'  => 2,
		'functional' => 1,
	];

	/**
	 * Get singleton instance.
	 *
	 * @return ScriptGuard
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Private constructor to prevent direct instantiation.
	 */
	private function __construct() {
		$this->load_categories_map();
	}

	/**
	 * Initialize the script guard module.
	 *
	 * @return void
	 */
	public function init() {
		// Hook into script loader to guard scripts.
		add_filter( 'script_loader_tag', [ $this, 'guard' ], 10, 3 );

		// Hook into style loader for CSS (optional, if needed for consistency).
		// add_filter( 'style_loader_tag', [ $this, 'guard_style' ], 10, 4 );
	}

	/**
	 * Load categories map from plugin settings.
	 *
	 * @return void
	 */
	private function load_categories_map() {
		$settings = get_option( 'ru_consent_mode_settings', [] );

		// Default categories map if not set.
		$default_map = [
			'analytics'  => '',
			'ads'        => '',
			'functional' => '',
		];

		$this->categories_map = isset( $settings['categories_map'] )
			? $settings['categories_map']
			: $default_map;
	}

	/**
	 * Guard script tag based on consent category.
	 *
	 * Converts script tags to placeholders if they require consent.
	 *
	 * @param string $tag    The script tag HTML.
	 * @param string $handle Script handle/ID.
	 * @param string $src    Script source URL.
	 * @return string Modified or original script tag.
	 */
	public function guard( $tag, $handle, $src ) {
		// Get consent category for this handle.
		$category = $this->get_handle_category( $handle );

		// If handle is not in any category, return original tag.
		if ( ! $category ) {
			return $tag;
		}

		// Parse script attributes.
		$attributes = $this->parse_script_attributes( $tag );

		// Check if this is an inline script (no src).
		$is_inline = empty( $src );

		if ( $is_inline ) {
			// Handle inline script.
			return $this->guard_inline_script( $tag, $handle, $category, $attributes );
		} else {
			// Handle external script.
			return $this->guard_external_script( $tag, $handle, $src, $category, $attributes );
		}
	}

	/**
	 * Guard external script (with src attribute).
	 *
	 * @param string $tag        Original script tag.
	 * @param string $handle     Script handle.
	 * @param string $src        Script source URL.
	 * @param string $category   Consent category (analytics|ads|functional).
	 * @param array  $attributes Parsed attributes.
	 * @return string Guarded script tag.
	 */
	private function guard_external_script( $tag, $handle, $src, $category, $attributes ) {
		// Build placeholder script tag.
		$placeholder  = '<script type="text/plain"';
		$placeholder .= ' data-rcm-consent="' . esc_attr( $category ) . '"';
		$placeholder .= ' data-src="' . esc_url( $src ) . '"';
		$placeholder .= ' id="rcm-' . esc_attr( $handle ) . '"';

		// Preserve async attribute.
		if ( isset( $attributes['async'] ) ) {
			$placeholder .= ' data-async="1"';
		}

		// Preserve defer attribute.
		if ( isset( $attributes['defer'] ) ) {
			$placeholder .= ' data-defer="1"';
		}

		// Preserve type attribute (if not the default).
		if ( isset( $attributes['type'] ) && 'text/javascript' !== $attributes['type'] ) {
			$placeholder .= ' data-original-type="' . esc_attr( $attributes['type'] ) . '"';
		}

		// Preserve crossorigin attribute.
		if ( isset( $attributes['crossorigin'] ) ) {
			$placeholder .= ' data-crossorigin="' . esc_attr( $attributes['crossorigin'] ) . '"';
		}

		// Preserve integrity attribute.
		if ( isset( $attributes['integrity'] ) ) {
			$placeholder .= ' data-integrity="' . esc_attr( $attributes['integrity'] ) . '"';
		}

		// Preserve nonce attribute.
		if ( isset( $attributes['nonce'] ) ) {
			$placeholder .= ' data-nonce="' . esc_attr( $attributes['nonce'] ) . '"';
		}

		$placeholder .= '></script>';

		return $placeholder;
	}

	/**
	 * Guard inline script (without src attribute).
	 *
	 * @param string $tag        Original script tag.
	 * @param string $handle     Script handle.
	 * @param string $category   Consent category.
	 * @param array  $attributes Parsed attributes.
	 * @return string Guarded script tag.
	 */
	private function guard_inline_script( $tag, $handle, $category, $attributes ) {
		// Extract inline script content.
		$script_content = $this->extract_inline_script( $tag );

		if ( empty( $script_content ) ) {
			// No content found, return original tag.
			return $tag;
		}

		// Build placeholder script tag with content inside.
		$placeholder  = '<script type="text/plain"';
		$placeholder .= ' data-rcm-consent="' . esc_attr( $category ) . '"';
		$placeholder .= ' id="rcm-inline-' . esc_attr( $handle ) . '"';

		// Preserve type attribute (if not the default).
		if ( isset( $attributes['type'] ) && 'text/javascript' !== $attributes['type'] ) {
			$placeholder .= ' data-original-type="' . esc_attr( $attributes['type'] ) . '"';
		}

		$placeholder .= '>';
		$placeholder .= $script_content; // Content is already in the tag, no need to escape here.
		$placeholder .= '</script>';

		return $placeholder;
	}

	/**
	 * Get consent category for a script handle.
	 *
	 * If handle is in multiple categories, returns the highest priority category.
	 * Priority order: ads > analytics > functional
	 *
	 * @param string $handle Script handle.
	 * @return string|false Category name or false if not found.
	 */
	private function get_handle_category( $handle ) {
		$found_categories = [];

		// Check each category.
		foreach ( $this->categories_map as $category => $handles_csv ) {
			if ( empty( $handles_csv ) ) {
				continue;
			}

			// Parse CSV handles.
			$handles = $this->parse_csv_handles( $handles_csv );

			// Check if current handle is in this category.
			if ( in_array( $handle, $handles, true ) ) {
				$found_categories[ $category ] = $this->category_priority[ $category ] ?? 0;
			}
		}

		// If not found in any category, return false.
		if ( empty( $found_categories ) ) {
			return false;
		}

		// If found in multiple categories, return highest priority.
		arsort( $found_categories ); // Sort by priority (descending).
		reset( $found_categories );

		return key( $found_categories );
	}

	/**
	 * Parse CSV handles string into array.
	 *
	 * @param string $csv_string Comma-separated handles.
	 * @return array Array of trimmed handles.
	 */
	private function parse_csv_handles( $csv_string ) {
		// Split by comma.
		$handles = explode( ',', $csv_string );

		// Trim whitespace from each handle.
		$handles = array_map( 'trim', $handles );

		// Remove empty values.
		$handles = array_filter( $handles );

		return $handles;
	}

	/**
	 * Parse script tag attributes.
	 *
	 * @param string $tag Script tag HTML.
	 * @return array Associative array of attributes.
	 */
	private function parse_script_attributes( $tag ) {
		$attributes = [];

		// Match async attribute.
		if ( preg_match( '/\sasync[\s>\/]/i', $tag ) ) {
			$attributes['async'] = true;
		}

		// Match defer attribute.
		if ( preg_match( '/\sdefer[\s>\/]/i', $tag ) ) {
			$attributes['defer'] = true;
		}

		// Match type attribute.
		if ( preg_match( '/\stype=["\']([^"\']+)["\']/i', $tag, $matches ) ) {
			$attributes['type'] = $matches[1];
		}

		// Match crossorigin attribute.
		if ( preg_match( '/\scrossorigin=["\']([^"\']+)["\']/i', $tag, $matches ) ) {
			$attributes['crossorigin'] = $matches[1];
		}

		// Match integrity attribute.
		if ( preg_match( '/\sintegrity=["\']([^"\']+)["\']/i', $tag, $matches ) ) {
			$attributes['integrity'] = $matches[1];
		}

		// Match nonce attribute.
		if ( preg_match( '/\snonce=["\']([^"\']+)["\']/i', $tag, $matches ) ) {
			$attributes['nonce'] = $matches[1];
		}

		return $attributes;
	}

	/**
	 * Extract inline script content from script tag.
	 *
	 * @param string $tag Script tag HTML.
	 * @return string Script content (without script tags).
	 */
	private function extract_inline_script( $tag ) {
		// Match content between <script> and </script>.
		if ( preg_match( '/<script[^>]*>(.*?)<\/script>/is', $tag, $matches ) ) {
			return $matches[1];
		}

		return '';
	}

	/**
	 * Check if a script handle should be guarded.
	 *
	 * @param string $handle Script handle.
	 * @return bool True if should be guarded.
	 */
	public function should_guard( $handle ) {
		return false !== $this->get_handle_category( $handle );
	}

	/**
	 * Get all handles for a specific category.
	 *
	 * @param string $category Category name (analytics|ads|functional).
	 * @return array Array of handles.
	 */
	public function get_category_handles( $category ) {
		if ( ! isset( $this->categories_map[ $category ] ) ) {
			return [];
		}

		return $this->parse_csv_handles( $this->categories_map[ $category ] );
	}

	/**
	 * Add handle to a category.
	 *
	 * @param string $handle   Script handle.
	 * @param string $category Category name.
	 * @return bool Success status.
	 */
	public function add_handle_to_category( $handle, $category ) {
		if ( ! isset( $this->categories_map[ $category ] ) ) {
			return false;
		}

		$handles = $this->get_category_handles( $category );

		if ( in_array( $handle, $handles, true ) ) {
			return true; // Already exists.
		}

		$handles[] = $handle;

		$this->categories_map[ $category ] = implode( ', ', $handles );

		return $this->save_categories_map();
	}

	/**
	 * Remove handle from a category.
	 *
	 * @param string $handle   Script handle.
	 * @param string $category Category name.
	 * @return bool Success status.
	 */
	public function remove_handle_from_category( $handle, $category ) {
		if ( ! isset( $this->categories_map[ $category ] ) ) {
			return false;
		}

		$handles = $this->get_category_handles( $category );
		$handles = array_diff( $handles, [ $handle ] );

		$this->categories_map[ $category ] = implode( ', ', $handles );

		return $this->save_categories_map();
	}

	/**
	 * Save categories map to settings.
	 *
	 * @return bool Success status.
	 */
	private function save_categories_map() {
		$settings                    = get_option( 'ru_consent_mode_settings', [] );
		$settings['categories_map']  = $this->categories_map;

		return update_option( 'ru_consent_mode_settings', $settings );
	}

	/**
	 * Get categories map.
	 *
	 * @return array Categories map.
	 */
	public function get_categories_map() {
		return $this->categories_map;
	}
}
