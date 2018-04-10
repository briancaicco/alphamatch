<div class="stuffbox review-admin-pros">
    <h3><?php echo __( 'Pros', 'inventor-reviews' ); ?></h3>

    <?php wp_editor( get_comment_meta( $comment->comment_ID, 'pros', true), 'pros', array( 'media_buttons' => false, 'tinymce' => false, 'quicktags' => $quicktags_settings ) ); ?>
    <?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
</div>