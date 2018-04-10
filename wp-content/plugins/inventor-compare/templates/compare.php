<?php global $wp_query; ?>
<?php $query = $wp_query; ?>

<?php if ( $query->post_count > 0 ) : ?>
    <div class="compare-wrapper">
        <table class="compare-table">
            <thead>
                <tr>
                    <th></th>

                    <?php foreach ( $query->posts as $post ): ?>
                    <th rel="compare-listing-<?php echo $post->ID; ?>">
                        <?php if ( has_post_thumbnail( $post->ID ) ) : ?>
                            <?php $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail' ); ?>
                            <?php $image = $thumbnail[0]; ?>
                        <?php else: ?>
                            <?php $image = esc_attr( plugins_url( 'inventor' ) ) . '/assets/img/default-item.png'; ?>
                        <?php endif; ?>

                        <div class="compare-table-image" style="background-image: url('<?php echo esc_attr( $image ); ?>');">
                            <a href="#" class="inventor-compare-btn-remove" data-listing-id="<?php echo $post->ID; ?>" data-ajax-url="<?php echo admin_url( 'admin-ajax.php' ); ?>">
                                <i class="fa fa-times"></i><?php echo __( 'Remove from comparison', 'inventor-compare' ); ?>
                            </a><!-- /.inventor-compare-btn-remove -->
                        </div><!-- /.compare-table-image-->

                        <?php $featured = get_post_meta( $post->ID, INVENTOR_LISTING_PREFIX . 'featured', true ); ?>
                        <?php $reduced = get_post_meta( $post->ID, INVENTOR_LISTING_PREFIX . 'reduced', true ); ?>

                        <?php if ( $featured ) : ?>
                            <div class="compare-table-label-top compare-table-label-top-left"><?php echo esc_attr__( 'Featured', 'inventor' ); ?></div><!-- /.compare-table-label-top-left -->
                        <?php endif; ?>

                        <?php if ( $reduced ) : ?>
                            <div class="compare-table-label-top compare-table-label-top-right"><?php echo esc_attr__( 'Reduced', 'inventor' ); ?></div><!-- /.compare-table-label-top-right -->
                        <?php endif; ?>

                        <?php $price = Inventor_Price::get_price( $post->ID ); ?>
                        <?php $price = empty( $price ) ? '' : $price; ?>

                        <?php $special_label = apply_filters( 'inventor_listing_special_label', $price, $post->ID, 'column' ); ?>
                        <?php if ( ! empty( $special_label ) ): ?>
                            <div class="compare-table-label-special">
                                <?php echo $special_label; ?>
                            </div><!-- /.compare-table-content -->
                        <?php endif; ?>

                        <div class="compare-table-title">
                            <h3><a href="<?php echo get_the_permalink( $post->ID ); ?>"><?php echo get_the_title( $post->ID ); ?></a></h3>

                            <?php $location = Inventor_Query::get_listing_location_name( $post->ID, '/', false ); ?>
                            <?php $subtitle = apply_filters( 'inventor_listing_subtitle', $location, $post->ID, 'column' ); ?>
                            <?php if ( ! empty( $subtitle ) ): ?>
                                <span class="compare-table-subtitle"><?php echo $subtitle; ?></span>
                            <?php endif; ?>
                        </div><!-- /.compare-table-title -->
                    </th>
                    <?php endforeach; ?>

                </tr>
            </thead>

            <tbody>
                <!-- TYPE -->
                <tr>
                    <td class="compare-table-label"><?php echo __( 'Type', 'inventor-compare' ); ?></td>
                    <?php foreach ( $query->posts as $post ) : ?>
                        <?php $listing_type = Inventor_Post_Types::get_listing_type_name( $post->ID ); ?>
                        <?php $icon = Inventor_Post_Type_Listing::get_icon( $post->ID, true ); ?>

                        <td class="compare-table-col" rel="compare-listing-<?php echo $post->ID; ?>">
                            <span class="compare-table-col-label"><?php echo __( 'Type', 'inventor-compare' ); ?></span>

                            <?php if ( ! empty( $icon ) ) : ?>
                                <?php echo $icon; ?>
                            <?php endif; ?>
                            <?php echo esc_attr( $listing_type ); ?>
                        </td>
                    <?php endforeach; ?>
                </tr>

                <?php foreach( $compare_data as $field_name => $attributes ): ?>
                <tr>
                    <td class="compare-table-label"><?php echo $field_name; ?></td>
                    <?php foreach ( $query->posts as $post ) : ?>
                        <?php $attribute = isset( $attributes[ $post->ID ] ) ? $attributes[ $post->ID ] : null; ?>
                        <td class="compare-table-col" rel="compare-listing-<?php echo $post->ID; ?>">
                            <span class="compare-table-col-label"><?php echo $field_name; ?></span>
                            <?php echo ! empty( $attribute ) ? $attribute : '-'; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div><!-- /.compare-wrapper -->
<?php else : ?>
    <div class="alert alert-info">
        <?php echo __( 'Nothing to compare.', 'inventor-compare' ); ?>
    </div>
<?php endif; ?>
