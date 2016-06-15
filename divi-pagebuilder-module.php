<?php
/**
 * Custom Divi Page-Builder Modules
 * @author Svetlana cadros.eu
 *
 * WooCommerce Product Categories Gallery
 *
*/



/**
 * Include Categories option output
 */
function custom_et_builder_categories_shop_option( $args = array() ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return '';
	}

	$defaults = apply_filters( 'et_builder_include_categories_shop_defaults', array (
		'use_terms' => true,
		'term_name' => 'product_cat',
	) );

	$args = wp_parse_args( $args, $defaults );

	$output = "\t" . "<% var et_pb_include_categories_shop_temp = typeof et_pb_include_categories !== 'undefined' ? et_pb_include_categories.split( ',' ) : []; %>" . "\n";

	$cats_array = $args['use_terms'] ? get_terms( $args['term_name'] ) : get_categories( apply_filters( 'et_builder_get_categories_shop_args', 'hide_empty=0' ) );


	foreach ( $cats_array as $category ) {
		
		$contains = sprintf(
			'<%%= _.contains( et_pb_include_categories_shop_temp, "%1$s" ) ? checked="checked" : "" %%>',
			esc_html( $category->term_id )
		);

		$output .= sprintf(
			'%4$s<label><input type="checkbox" name="et_pb_include_categories" value="%1$s"%3$s> %2$s</label><br/>',
			esc_attr( $category->term_id ),
			esc_html( $category->name ),
			$contains,
			"\n\t\t\t\t\t"
		);
	}
	return apply_filters( 'custom_et_builder_shop_categories_option', $output );
}



/**
 * Shop Categories Module
 * 
 * @var @class
 * @see Divi/includes/builder/main-modules.php
 */
class ET_Builder_Module_Shop_Cats extends ET_Builder_Module {
	function init() {
		$this->name = esc_html__( 'Shop categories', 'et_builder' );
		$this->slug = 'et_pb_shop_cats';

		$this->whitelisted_fields = array(
			'type',
			'posts_number',
			'columns_number',
			'include_categories',
			'orderby',
			'admin_label',
			'module_id',
			'module_class',
			'sale_badge_color',
			'icon_hover_color',
			'hover_overlay_color',
			'hover_icon',
			'show_title',
			'show_count'
		);

		$this->fields_defaults = array(
			'type'           => array( 'product_category' ),
			'posts_number'   => array( '12', 'add_default_setting' ),
			'columns_number' => array( '0' ),
			'orderby'        => array( 'menu_order' ),
			'include_categories' => array('0'),
			'show_title'     => array( 'on' ),
			'show_count' 	 => array('on')
		);

		$this->main_css_element = '%%order_class%%.et_pb_shop';
		$this->advanced_options = array(
			'border' => array(
				'css'      => array(
					'main' => "{$this->main_css_element} .woocommerce ul.products li.product img",
				)
			),
			
			'fonts' => array(
				'title' => array(
					'label'    => esc_html__( 'Title', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .woocommerce ul.products li.product h3",
					),
				),
				'price' => array(
					'label'    => esc_html__( 'Price', 'et_builder' ),
					'css'      => array(
						'main' => "{$this->main_css_element} .woocommerce ul.products li.product .price, {$this->main_css_element} .woocommerce ul.products li.product .price .amount",
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
				),
			),
		);
		$this->custom_css_options = array(
			'product' => array(
				'label'    => esc_html__( 'Product', 'et_builder' ),
				'selector' => 'li.product',
			),
			'onsale' => array(
				'label'    => esc_html__( 'Onsale', 'et_builder' ),
				'selector' => 'li.product .onsale',
			),
			'image' => array(
				'label'    => esc_html__( 'Image', 'et_builder' ),
				'selector' => '.et_shop_image',
			),
			'overlay' => array(
				'label'    => esc_html__( 'Overlay', 'et_builder' ),
				'selector' => '.et_overlay',
			),
			'title' => array(
				'label'    => esc_html__( 'Title', 'et_builder' ),
				'selector' => 'li.product h3',
			),

		);
	}

	function get_fields() {
		$fields = array(
			'type' => array(
				'label'           => sprintf( __( 'Product Categories Display', '%s'), get_stylesheet() ),
				'type'            => 'select',
				'option_category' => 'basic_option',
				'options'         => array(
					'top_level_cats'  => sprintf( __( 'Top Level Categories', '%s'), get_stylesheet() ),
					'parent_cats'  => sprintf( __( 'Sub-categories', '%s'), get_stylesheet() ),
					'product_category' => sprintf( __( 'Default', '%s'), get_stylesheet() ),
				),
				'affects'            => array(
					'input[name="et_pb_include_categories"]',
				),
				'description'        => sprintf( __( 'Sub-categories will show children of a single category, make sure to deselect all but 1 category at Include Categories list below.', '%s'), get_stylesheet() )
			),
			'include_categories'   => array(
				'label'            => esc_html__( 'Include Categories', 'et_builder' ),
				'type'             => 'basic_option',
				'renderer'         => 'custom_et_builder_categories_shop_option',
				'renderer_options' => array(
					'use_terms'    => true,
					'term_name'    => 'product_cat',
				),
				'depends_show_if_not'  => 'top_level_cats',
				'description'      => esc_html__( sprintf( __( 'Choose which categories you would like to include. Do not check any to include all.', '%s'), get_stylesheet() )  ),		
			),
			'posts_number' => array(
				'label'             => esc_html__( 'Posts Number', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'configuration',
				'description'       => esc_html__( 'Control how many products are displayed.', 'et_builder' ),
			),
			'show_title' => array(
				'label'             => esc_html__( 'Show Title', 'et_builder' ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'affects'            => array(
					'#et_pb_show_count')
			),
			'show_count' => array(
				'label'             => esc_html__( sprintf( __( 'Show products count', '%s'), get_stylesheet() )  ),
				'type'              => 'yes_no_button',
				'option_category'   => 'configuration',
				'id'              => 'et_pb_show_count',
				'depends_show_if'  => 'product_category',
				'options'           => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'depends_show_if'  => 'on'
			),
			'columns_number' => array(
				'label'             => esc_html__( 'Columns Number', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'layout',
				'options'           => array(
					'0' => esc_html__( 'default', 'et_builder' ),
					'6' => sprintf( esc_html__( '%1$s Columns', 'et_builder' ), esc_html( '6' ) ),
					'5' => sprintf( esc_html__( '%1$s Columns', 'et_builder' ), esc_html( '5' ) ),
					'4' => sprintf( esc_html__( '%1$s Columns', 'et_builder' ), esc_html( '4' ) ),
					'3' => sprintf( esc_html__( '%1$s Columns', 'et_builder' ), esc_html( '3' ) ),
					'2' => sprintf( esc_html__( '%1$s Columns', 'et_builder' ), esc_html( '2' ) ),
					'1' => esc_html__( '1 Column', 'et_builder' ),
				),
				'description'        => esc_html__( 'Choose how many columns to display.', 'et_builder' ),
			),
			'orderby' => array(
				'label'             => esc_html__( 'Order By', 'et_builder' ),
				'type'              => 'select',
				'option_category'   => 'configuration',
				'options'           => array(
					'menu_order'  => esc_html__( 'Default Sorting', 'et_builder' ),
					'popularity' => esc_html__( 'Sort By Popularity', 'et_builder' ),
					'rating' => esc_html__( 'Sort By Rating', 'et_builder' ),
					'date' => esc_html__( 'Sort By Date', 'et_builder' ),
					'price' => esc_html__( 'Sort By Price: Low To High', 'et_builder' ),
					'price-desc' => esc_html__( 'Sort By Price: High To Low', 'et_builder' ),
				),
				'description'        => esc_html__( 'Choose how your products should be ordered.', 'et_builder' ),
			),
			'sale_badge_color' => array(
				'label'             => esc_html__( 'Sale Badge Color', 'et_builder' ),
				'type'              => 'color',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
			),
			'icon_hover_color' => array(
				'label'             => esc_html__( 'Icon Hover Color', 'et_builder' ),
				'type'              => 'color',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
			),
			'hover_overlay_color' => array(
				'label'             => esc_html__( 'Hover Overlay Color', 'et_builder' ),
				'type'              => 'color-alpha',
				'custom_color'      => true,
				'tab_slug'          => 'advanced',
			),
			'hover_icon' => array(
				'label'               => esc_html__( 'Hover Icon Picker', 'et_builder' ),
				'type'                => 'text',
				'option_category'     => 'configuration',
				'class'               => array( 'et-pb-font-icon' ),
				'renderer'            => 'et_pb_get_font_icon_list',
				'renderer_with_field' => true,
				'tab_slug'            => 'advanced',
			),
			'disabled_on' => array(
				'label'           => esc_html__( 'Disable on', 'et_builder' ),
				'type'            => 'multiple_checkboxes',
				'options'         => array(
					'phone'   => esc_html__( 'Phone', 'et_builder' ),
					'tablet'  => esc_html__( 'Tablet', 'et_builder' ),
					'desktop' => esc_html__( 'Desktop', 'et_builder' ),
				),
				'additional_att'  => 'disable_on',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will disable the module on selected devices', 'et_builder' ),
			),
			'admin_label' => array(
				'label'       => esc_html__( 'Admin Label', 'et_builder' ),
				'type'        => 'text',
				'description' => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
			),
			'module_id' => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class' => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'option_class'    => 'et_pb_custom_css_regular',
			)
		);
		return $fields;
	}

	function shortcode_callback( $atts, $content = null, $function_name ) {
		$module_id               = $this->shortcode_atts['module_id'];
		$module_class            = $this->shortcode_atts['module_class'];
		$type                    = $this->shortcode_atts['type'];
		$include_categories      = $this->shortcode_atts['include_categories'];
		$show_title         	 = $this->shortcode_atts['show_title'];
		$show_count         	 = $this->shortcode_atts['show_count'];
		$posts_number            = $this->shortcode_atts['posts_number'];
		$orderby                 = $this->shortcode_atts['orderby'];
		$columns                 = $this->shortcode_atts['columns_number'];
		$sale_badge_color        = $this->shortcode_atts['sale_badge_color'];
		$icon_hover_color        = $this->shortcode_atts['icon_hover_color'];
		$hover_overlay_color     = $this->shortcode_atts['hover_overlay_color'];
		$hover_icon              = $this->shortcode_atts['hover_icon'];

		$module_class = ET_Builder_Element::add_module_order_class( $module_class, $function_name );

		if ( 'on' !== $show_title ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .products li h3',
				'declaration' => 'display: none;'
			) );
		} else {
			/**
			 * @see Divi/functions.php
			 */
			$body_font = et_get_option( 'body_font_size' );
	  		$body_lheight = et_get_option( 'body_font_height' );

	  		/**
	  		 * Figure the line-height based on global 'Typography' setting
	  		 * Make it double line so header has decent room 
	  		 */
	  		$half_base_lead = ($body_font * $body_lheight) /2;
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .woocommerce ul.products li.product h3',
				'declaration' => sprintf(
					'padding:%1$spx %2$spx;',
					$half_base_lead,
					$half_base_lead /2
				),
			) );

			if ( 'on' !== $show_count ) {
				ET_Builder_Element::set_style( $function_name, array(
					'selector'    => '%%order_class%% .products li h3 mark',
					'declaration' => 'display: none;'
				) );
			}
		}

		if ( '' !== $sale_badge_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% span.onsale',
				'declaration' => sprintf(
					'background-color: %1$s !important;',
					esc_html( $sale_badge_color )
				),
			) );
		}
		if ( '' !== $icon_hover_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .products li img:before',
				'declaration' => sprintf(
					'color: %1$s !important;',
					esc_html( $icon_hover_color )
				),
			) );
		}

		if ( '' !== $hover_overlay_color ) {
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .products li',
				'declaration' => sprintf(
					'background-color: %1$s !important;',
					esc_html( $hover_overlay_color )
				),
			) );
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .products li img',
				'declaration' => sprintf(
					'opacity: .8;',
					esc_html( $hover_overlay_color )
				),
			) );
			ET_Builder_Element::set_style( $function_name, array(
				'selector'    => '%%order_class%% .products li img:hover',
				'declaration' => sprintf(
					'opacity: 1;',
					esc_html( $hover_overlay_color )
				),
			) );
		}

		$data_icon = '' !== $hover_icon
			? sprintf(
				' data-icon="%1$s"',
				esc_attr( et_pb_process_font_icon( $hover_icon ) )
			)
			: '';

		


		/**
		 * Original Divi filter for WooCommerce shortcode
		 * WP_Query orderby
		 * via modify_woocommerce_shortcode_products_query
		 * @see http://docs.woothemes.com/document/woocommerce-shortcodes/#section-5
		 */
		$modify_woocommerce_query = in_array( $orderby, array( 'price', 'price-desc', 'rating', 'popularity' ) );

		if ( $modify_woocommerce_query ) {
			add_filter( 'woocommerce_shortcode_products_query', array( $this, 'modify_woocommerce_shortcode_products_query' ), 10, 2 );
		}

		/**
		 * Set arguments for shortcode output
		 * Based on Product Categories Display option. * Will show Top level | Children of | Checked cats
		 * @uses woo shortcode product_categories
		 * @see \woocommerce\includes\class-wc-shortcodes.php
		 *
		 */
		switch( $type ) {
			case 'top_level_cats' :
				$parent = ' parent="0" ';
			break;
			case 'parent_cats' : 
				$parent = ' parent="'.$include_categories.'" ';
			break;
			case 'product_category' : 
				$ids = sprintf(' ids="%s"', esc_attr($include_categories) );
			break;
			default:
				$parent = null;
				$ids = null;
			break;

		}

		$output = sprintf(
			'<div%2$s class="et_pb_module et_pb_shop%3$s%4$s"%5$s>
				%1$s
			</div>',
			do_shortcode('[product_categories per_page="'.esc_attr( $posts_number ).'" orderby="'.esc_attr( $orderby ).'" columns="'.esc_attr( $columns ).'"'.$ids . $parent .  ']'
			), /// end do_shortcode()
			( '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : '' ),
			( '' !== $module_class ? sprintf( ' %1$s', esc_attr( $module_class ) ) : '' ),
			'0' === $columns ? ' et_pb_shop_grid' : '',
			$data_icon
		);

		/**
		 * Remove modify_woocommerce_shortcode_products_query method after being used
		 */
		if ( $modify_woocommerce_query ) {
			remove_filter( 'woocommerce_shortcode_products_query', array( $this, 'modify_woocommerce_shortcode_products_query' ) );

			if ( function_exists( 'WC' ) ) {
				WC()->query->remove_ordering_args(); // remove args added by woocommerce to avoid errors in sql queries performed afterwards
			}
		}
		return $output;
	}

	/**
	 * Native
	 *
	 * Modifying WooCommerce' product query filter based on $orderby value given
	 * @see WC_Query->get_catalog_ordering_args()
	 */
	function modify_woocommerce_shortcode_products_query( $args, $atts ) {

		if ( function_exists( 'WC' ) ) {
			// By default, all order is ASC except for price-desc
			$order = 'price-desc' === $this->shortcode_atts['orderby'] ? 'DESC' : 'ASC';

			// Supported orderby arguments (as defined by WC_Query->get_catalog_ordering_args() ): rand | date | price | popularity | rating | title
			$orderby = in_array( $this->shortcode_atts['orderby'], array( 'price-desc' ) ) ? 'price' : $this->shortcode_atts['orderby'];

			// Get arguments for the given non-native orderby
			$query_args = WC()->query->get_catalog_ordering_args( $orderby, $order );

			// Confirm that returned argument isn't empty then merge returned argument with default argument
			if( is_array( $query_args ) && ! empty( $query_args ) ) {
				$args = array_merge( $args, $query_args );
			}
		}

		return $args;
	}
}
new ET_Builder_Module_Shop_Cats;