<?php if ( empty( $instance['hide_rating'] ) ) : ?>
    <div class="form-group">
        <?php if ( 'labels' == $input_titles ) : ?>
            <label for="<?php echo ! empty( $field_id_prefix ) ? $field_id_prefix : ''; ?><?php echo esc_attr( $args['widget_id'] ); ?>_rating"><?php echo __( 'Rating', 'inventor-reviews' ); ?></label>
        <?php endif; ?>

        <select class="form-control"
                name="rating"
                id="<?php echo ! empty( $field_id_prefix ) ? $field_id_prefix : ''; ?><?php echo esc_attr( $args['widget_id'] ); ?>_rating">
            <option value="">
                <?php if ( 'placeholders' == $input_titles ) : ?>
                    <?php echo __( 'Rating', 'inventor-reviews' ); ?>
                <?php else : ?>
                    <?php echo __( 'Any rating', 'inventor-reviews' ); ?>
                <?php endif; ?>
            </option>

            <?php for( $i = 1; $i <= 5; $i++ ): ?>
                <option value="<?php echo $i; ?>" <?php if ( ! empty( $_GET['rating'] ) && $_GET['rating'] == $i ) : ?>selected="selected"<?php endif; ?>><?php echo $i == 5 ? $i : $i . "+"; ?></option>
            <?php endfor; ?>
        </select>
    </div><!-- /.form-group -->
<?php endif; ?>