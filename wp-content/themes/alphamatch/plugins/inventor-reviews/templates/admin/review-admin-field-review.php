<div class="stuffbox review-admin-review">
    <h3><?php echo $is_reply ? __( 'Message', 'inventor-reviews' ) : __( 'Review', 'inventor-reviews' ); ?></h3>

    <?php wp_editor( get_comment_text( $comment->comment_ID ), 'content', array( 'media_buttons' => false, 'tinymce' => false, 'quicktags' => $quicktags_settings ) ); ?>
    <?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
</div>