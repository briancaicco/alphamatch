<li id="comment-<?php comment_ID() ?>">
    <div class="review clearfix">

        <?php $image = Inventor_Post_Type_User::get_user_image( $review->user_id ); ?>

        <?php if ( ! empty( $image ) ) : ?>
            <div class="review-image" data-background-image="<?php echo esc_attr( $image ); ?>">
                <img src="<?php echo esc_attr( $image ); ?>" alt="">
            </div><!-- /.review-image -->
        <?php endif; ?>

        <div class="review-inner">
            <?php if ( $review->comment_approved == '0' ) : ?>
                <div class="alert alert-info review-awaiting-moderation">
                    <?php echo __( 'Your review is awaiting moderation.', 'inventor-reviews' ); ?>
                </div><!-- /.review-awaiting-moderation -->
            <?php endif; ?>

            <div class="review-header">
                <h2><?php comment_author(); ?></h2>

                <span class="review-actions">
                    <?php if ( current_user_can( 'edit_comment' ) ) : ?>
                        <span class="review-action review-spam">
                            <span class="separator">&#8226;</span>
                            <?php edit_comment_link( __( 'Edit', 'inventor-reviews' ) ); ?>
                        </span>

                        <span class="review-action review-spam">
                            <span class="separator">&#8226;</span>
                            <a href="<?php echo admin_url("comment.php?action=cdc&dt=spam&c=" . $review->comment_ID); ?>">
                                <?php echo esc_attr__( 'Spam', 'inventor-reviews' ); ?>
                            </a>
                        </span>

                        <span class="review-action review-delete">
                            <span class="separator">&#8226;</span>
                            <a href="<?php echo admin_url("comment.php?action=cdc&c=" . $review->comment_ID); ?>">
                                <?php echo __( 'Delete', 'inventor-reviews' ); ?>
                            </a>
                        </span>
                    <?php endif; ?>

                    <span class="review-action review-reply">
                        <span class="separator">&#8226;</span>
                        <?php comment_reply_link( array_merge( $args, array(
                            'add_below'     => 'comment',
                            'reply_text'    => '<i class="fa fa-reply"></i> ' . __( 'Reply', 'inventor-reviews' ),
                            'max_depth'     => 2,
                        ) ) ); ?>
                    </span>
                </span>

                <?php $rating = get_comment_meta( get_comment_ID(), 'rating', true ); ?>
                <?php if ( ! empty( $rating ) ) : ?>
                    <div class="review-rating-wrapper">
                        <span class="review-rating" <?php do_action( 'inventor_review_rating_total_attrs', get_the_ID() ); ?>data-path="<?php echo plugins_url(); ?>/inventor-reviews/libraries/raty/images" data-score="<?php echo esc_attr( $rating ); ?>"></span>
                        <span class="review-rating-number">
                            <strong><?php echo esc_attr__( 'Rating', 'inventor-review' ); ?>:</strong> <?php echo esc_attr( $rating ); ?> <span class="separator">/</span> 5
                        </span>
                    </div><!-- /.review-rating-wrapper -->
                <?php endif; ?>
            </div><!-- /.review-header -->

            <?php $pros = get_comment_meta( get_comment_ID(), 'pros', true ); ?>
            <?php $cons = get_comment_meta( get_comment_ID(), 'cons', true ); ?>

            <?php if ( ! empty( $pros ) || ! empty( $cons ) ) : ?>
                <div class="review-content-wrapper">
                    <div class="review-content">

                        <?php if ( ! empty( $pros ) ) : ?>
                            <div class="review-pros">
                                <div class="review-comment-subtitle">
                                    <strong><?php echo esc_attr__( 'Pros', 'inventor-reviews' ); ?></strong>
                                </div><!-- /.review-comment-subtitle -->

                                <p><?php echo esc_attr( $pros ); ?></p>
                            </div><!-- /.review-pros -->
                        <?php endif; ?>
                        <?php if ( ! empty( $cons ) ) : ?>
                            <div class="review-cons">
                                <div class="review-comment-subtitle">
                                    <strong><?php echo esc_attr__( 'Cons', 'inventor-reviews' ); ?></strong>
                                </div><!-- /.review-comment-subtitle -->

                                <p><?php echo esc_attr( $cons ); ?></p>
                            </div><!-- /.review-cons -->
                        <?php endif; ?>
                    </div><!-- /.review-content -->
                </div><!-- /.review-content-wrapper -->
            <?php else : ?>
                <?php comment_text(); ?>
            <?php endif; ?>

            <?php $image = get_comment_meta( get_comment_ID(), 'image', true ); ?>
            <?php if ( ! empty( $image ) ) : ?>
                <div class="review-attachment">
                    <a href="<?php echo wp_kses( $image, wp_kses_allowed_html( 'post' ) ); ?>">
                        <img src="<?php echo wp_kses( $image, wp_kses_allowed_html( 'post' ) ); ?>">
                    </a>
                </div><!-- /.review-attachment -->
            <?php endif; ?>

        </div><!-- /.review-inner -->
    </div><!-- /.review -->
