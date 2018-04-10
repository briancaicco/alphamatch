<div class="stuffbox review-admin-cons">
    <h3><?php echo __( 'Cons', 'inventor-reviews' ); ?></h3>

    <?php wp_editor( get_comment_meta( $comment->comment_ID, 'cons', true), 'cons', array( 'media_buttons' => false, 'tinymce' => false, 'quicktags' => $quicktags_settings ) ); ?>
    <?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
</div>