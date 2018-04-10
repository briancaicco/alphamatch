<div class="stuffbox review-admin-rating">
    <h3><?php echo __( 'Rating', 'inventor-reviews' ); ?></h3>

    <div class="inside">
        <?php $rating = get_comment_meta( $comment->comment_ID, 'rating', true); ?>

        <select name="rating">
            <option value="0">-</option>
            <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                <option value="<?php echo $i ?>" <?php echo ( $rating == $i ) ? 'selected' : ''; ?>>
                    <?php echo $i ?>
                </option>
            <?php endfor; ?>
        </select>
        <?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
    </div>

</div>