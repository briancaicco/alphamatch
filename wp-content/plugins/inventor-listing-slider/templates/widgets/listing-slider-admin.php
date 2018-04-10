<?php
$count = ! empty( $instance['count'] ) ? $instance['count'] : 3;
$order = ! empty( $instance['order'] ) ? $instance['order'] : 'ids';
$ids = ! empty( $instance['ids'] ) ? $instance['ids'] : '';
$attribute = ! empty( $instance['attribute'] ) ? $instance['attribute'] : '';
$listing_categories = ! empty( $instance['listing_categories'] ) ? $instance['listing_categories'] : array();
$listing_types = ! empty( $instance['listing_types'] ) ? $instance['listing_types'] : array();
$locations = ! empty( $instance['locations'] ) ? $instance['locations'] : array();
$height = ! empty( $instance['height'] ) ? $instance['height'] : '500px';
$fullscreen = ! empty( $instance['fullscreen'] ) ? $instance['fullscreen'] : false;
$size = ! empty( $instance['size'] ) ? $instance['size'] : 'thumbnail';
$classes = ! empty( $instance['classes'] ) ? $instance['classes'] : '';
$show_arrows = ! empty( $instance['show_arrows'] ) ? $instance['show_arrows'] : '';
$disable_dots = ! empty( $instance['disable_dots'] ) ? $instance['disable_dots'] : '';
$autoplay = ! empty( $instance['autoplay'] ) ? $instance['autoplay'] : '';
$autoplay_timeout = ! empty( $instance['autoplay_timeout'] ) ? $instance['autoplay_timeout'] : '';
?>

<!-- COUNT -->
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>">
		<?php echo __( 'Count', 'inventor' ); ?>
	</label>

	<input  class="widefat"
			id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"
			name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>"
			type="text"
			value="<?php echo esc_attr( $count ); ?>">
</p>

<!-- Pickup -->
<p>
	<strong><?php echo __( 'Pickup', 'inventor' ); ?></strong>

	<?php
	$order_options = apply_filters( 'inventor_widget_listings_order_options', array(
		'alphabetical'	=> __( 'Alphabetical', 'inventor' ),
		'rand'			=> __( 'Random', 'inventor' ),
		'ids'			=> __( 'IDs', 'inventor' ),
	) );
	?>

<ul>
	<li>
		<label>
			<input  type="radio"
					class="radio"
				<?php echo ( empty( $order ) || 'on' == $order ) ? 'checked="checked"' : ''; ?>
					id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>">
			<?php echo __( 'Default (publish date)', 'inventor' ); ?>
		</label>
	</li>

	<?php foreach( $order_options as $order_key => $order_title ): ?>
		<li>
			<label>
				<input  type="radio"
						class="radio"
						value="<?php echo $order_key; ?>"
					<?php echo ( $order_key == $order ) ? 'checked="checked"' : ''; ?>
						id="<?php echo esc_attr( $this->get_field_id( 'order' ) ); ?>"
						name="<?php echo esc_attr( $this->get_field_name( 'order' ) ); ?>">
				<?php echo $order_title; ?>
			</label>
		</li>
	<?php endforeach; ?>
</ul>
</p>

<!-- IDS -->
<p class="ids">
	<label for="<?php echo esc_attr( $this->get_field_id( 'ids' ) ); ?>">
		<?php echo __( 'IDs', 'inventor-listing-slider' ); ?>
	</label>

	<input  class="widefat"
	        id="<?php echo esc_attr( $this->get_field_id( 'ids' ) ); ?>"
	        name="<?php echo esc_attr( $this->get_field_name( 'ids' ) ); ?>"
	        type="text"
	        value="<?php echo esc_attr( $ids ); ?>">
	<br>
	<small><?php echo __( 'Listing IDs, separated by commas.', 'inventor-listing-slider' ); ?></small>
</p>

<!-- ATTRIBUTE -->
<p>
	<strong><?php echo __( 'Attribute', 'inventor' ); ?></strong>

<ul>
	<li>
		<label>
			<input  type="radio"
					class="radio"
				<?php echo ( empty( $attribute ) || 'on' == $attribute ) ? 'checked="checked"' : ''; ?>
					id="<?php echo esc_attr( $this->get_field_id( 'attribute' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'attribute' ) ); ?>">
			<?php echo __( 'None', 'inventor' ); ?>
		</label>
	</li>

	<li>
		<label>
			<input  type="radio"
					class="radio"
					value="featured"
				<?php echo ( 'featured' == $attribute ) ? 'checked="checked"' : ''; ?>
					id="<?php echo esc_attr( $this->get_field_id( 'attribute' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'attribute' ) ); ?>">
			<?php echo __( 'Featured only', 'inventor' ); ?>
		</label>
	</li>

	<li>
		<label>
			<input  type="radio"
					class="radio"
					value="reduced"
				<?php echo ( 'reduced' == $attribute ) ? 'checked="checked"' : ''; ?>
					id="<?php echo esc_attr( $this->get_field_id( 'attribute' ) ); ?>"
					name="<?php echo esc_attr( $this->get_field_name( 'attribute' ) ); ?>">

			<?php echo __( 'Reduced only', 'inventor' ); ?>
		</label>
	</li>
</ul>
</p>

<!-- LISTING CATEGORIES  -->
<p>
	<strong><?php echo __( 'Listing Categories', 'inventor' ); ?></strong>
</p>

<p>
	<select id="<?php echo esc_attr( $this->get_field_id( 'listing_categories' ) ); ?>" style="width: 100%;"
			multiple="multiple"
			name="<?php echo esc_attr( $this->get_field_name( 'listing_categories' ) ); ?>[]">
		<?php $terms = get_terms( 'listing_categories', array( 'hide_empty' => false ) ); ?>

		<?php if ( is_array( $terms ) ) : ?>
			<?php foreach ( $terms as $term ) : ?>
				<option value="<?php echo esc_attr( $term->term_id ); ?>" <?php if ( in_array( $term->term_id, $listing_categories ) ) : ?>selected="selected"<?php endif; ?>><?php echo esc_attr( $term->name ); ?></option>
			<?php endforeach; ?>
		<?php endif; ?>
	</select>
</p>


<!-- LISTING TYPES  -->
<p>
	<strong><?php echo __( 'Types', 'inventor' ); ?></strong>
</p>

<p>
	<select id="<?php echo esc_attr( $this->get_field_id( 'listing_types' ) ); ?>" style="width: 100%;"
			multiple="multiple"
			name="<?php echo esc_attr( $this->get_field_name( 'listing_types' ) ); ?>[]">
		<?php $types = Inventor_Post_types::get_listing_post_types(); ?>

		<?php if ( is_array( $types ) ) : ?>
			<?php foreach ( $types as $type ) : ?>
				<?php $obj = get_post_type_object( $type ); ?>
				<option value="<?php echo esc_attr( $type ); ?>" <?php if ( in_array( $type, $listing_types ) ) : ?>selected="selected"<?php endif; ?>>
					<?php echo esc_attr( $obj->labels->name ); ?>
				</option>
			<?php endforeach; ?>
		<?php endif; ?>
	</select>
</p>

<!-- LOCATIONS  -->
<p>
	<strong><?php echo __( 'Locations', 'inventor' ); ?></strong>
</p>

<p>
	<select id="<?php echo esc_attr( $this->get_field_id( 'locations' ) ); ?>" style="width: 100%;"
			multiple="multiple"
			name="<?php echo esc_attr( $this->get_field_name( 'locations' ) ); ?>[]">
		<?php $terms = get_terms( 'locations', array( 'hide_empty' => false ) ); ?>

		<?php if ( is_array( $terms ) ) : ?>
			<?php foreach ( $terms as $term ) : ?>
				<option value="<?php echo esc_attr( $term->term_id ); ?>" <?php if ( in_array( $term->term_id, $locations ) ) : ?>selected="selected"<?php endif; ?>><?php echo esc_attr( $term->name ); ?></option>
			<?php endforeach; ?>
		<?php endif; ?>
	</select>
</p>

<!-- CLASSES -->
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'classes' ) ); ?>">
		<?php echo __( 'Classes', 'inventor-listing-slider' ); ?>
	</label>

	<input  class="widefat"
	        id="<?php echo esc_attr( $this->get_field_id( 'classes' ) ); ?>"
	        name="<?php echo esc_attr( $this->get_field_name( 'classes' ) ); ?>"
	        type="text"
	        value="<?php echo esc_attr( $classes ); ?>">
	<br>
	<small><?php echo __( 'Additional classes appended to body class and separated by , e.g. <i>header-transparent, listing-slider-append-top</i>', 'inventor-listing-slider' ); ?></small>
</p>

<!-- HEIGHT -->
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>">
		<?php echo __( 'Container height', 'inventor-listing-slider' ); ?>
	</label>

	<input  class="widefat"
	        id="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>"
	        name="<?php echo esc_attr( $this->get_field_name( 'height' ) ); ?>"
	        type="text"
	        value="<?php echo esc_attr( $height ); ?>">
	<br>
	<small><?php echo __( 'Default value 500px.', 'inventor-listing-slider' ); ?></small>
</p>

<!-- FULLSCREEN -->
<p>
	<input  type="checkbox"
	        class="checkbox"
		<?php echo ! empty( $fullscreen ) ? 'checked="checked"' : ''; ?>
		    id="<?php echo esc_attr( $this->get_field_id( 'fullscreen' ) ); ?>"
		    name="<?php echo esc_attr( $this->get_field_name( 'fullscreen' ) ); ?>">

	<label for="<?php echo esc_attr( $this->get_field_id( 'fullscreen' ) ); ?>">
		<?php echo __( 'Fullscreen', 'inventor-listing-slider' ); ?>
	</label>
</p>

<!-- SIZE -->
<?php $sizes = get_intermediate_image_sizes(); ?>

<?php if ( ! empty( $sizes ) ) : ?>
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>">
			<?php echo __( 'Thumbnail size', 'inventor-listing-slider' ); ?>
		</label>

		<select id="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'size' ) ); ?>">
			<?php foreach ( $sizes as $thumb_size ) : ?>
				<option value="<?php echo esc_attr( $thumb_size ); ?>" <?php echo ( $size == $thumb_size ) ? 'selected="selected"' : ''; ?>>
					<?php echo esc_attr( $thumb_size ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</p>
<?php endif; ?>

<!-- SHOW ARROWS -->
<p>
	<input  type="checkbox"
	        class="checkbox"
		<?php echo ! empty( $show_arrows ) ? 'checked="checked"' : ''; ?>
	        id="<?php echo esc_attr( $this->get_field_id( 'show_arrows' ) ); ?>"
	        name="<?php echo esc_attr( $this->get_field_name( 'show_arrows' ) ); ?>">

	<label for="<?php echo esc_attr( $this->get_field_id( 'show_arrows' ) ); ?>">
		<?php echo __( 'Show arrows', 'inventor-listing-slider' ); ?>
	</label>
</p>

<!-- AUTOPLAY -->
<p>
	<input  type="checkbox"
	        class="checkbox"
		<?php echo ! empty( $autoplay ) ? 'checked="checked"' : ''; ?>
	        id="<?php echo esc_attr( $this->get_field_id( 'autoplay' ) ); ?>"
	        name="<?php echo esc_attr( $this->get_field_name( 'autoplay' ) ); ?>">

	<label for="<?php echo esc_attr( $this->get_field_id( 'autoplay' ) ); ?>">
		<?php echo __( 'Autoplay', 'inventor-listing-slider' ); ?>
	</label>
</p>

<!-- AUTOPLAY TIMEOUT -->
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'autoplay_timeout' ) ); ?>">
		<?php echo __( 'Autoplay timeout', 'inventor-listing-slider' ); ?>
	</label>

	<input  class="widefat"
	        id="<?php echo esc_attr( $this->get_field_id( 'autoplay_timeout' ) ); ?>"
	        name="<?php echo esc_attr( $this->get_field_name( 'autoplay_timeout' ) ); ?>"
			type="number"
			step="1"
	        value="<?php echo esc_attr( $autoplay_timeout ); ?>">
	<br>
	<small><?php echo __( 'Default value 2000.', 'inventor-listing-slider' ); ?></small>
</p>