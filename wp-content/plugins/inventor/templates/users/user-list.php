<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * The template for user list
 *
 * @package Inventor
 * @since Inventor 1.3.0
 */
?>

<?php do_action( 'inventor_before_user_list' ); ?>

<?php if( count( $users ) > 0 ): ?>
    <div class="user-list <?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>items-per-row-2<?php else : ?>items-per-row-2<?php endif; ?>">
        <?php foreach ( $users as $user ) : ?>
            <?php include Inventor_Template_Loader::locate( 'users/card' ); ?>
        <?php endforeach; ?>

        <?php
        $all_authors = Inventor_Post_Type_User::get_users( 'author' );
        $total_users = count( $all_authors );
        $total_query = count( $users );
        $total_pages = ceil( $total_users / $count );

        if ( $total_users > $total_query ) {
            $links = paginate_links(array(
                'base'      => get_pagenum_link(1) . '%_%',
                'format'    => 'page/%#%/',
                'current'   => $page,
                'total'     => $total_pages,
                'type'      => 'plain',
            ));

            if ( $links ) {
                $navigation = _navigation_markup( $links, 'pagination', _('Authors navigation') );
                echo $navigation;
            }
        }
        ?>
    </div>
<?php else : ?>
    <?php get_template_part( 'templates/content', 'none' ); ?>
<?php endif; ?>

<?php do_action( 'inventor_before_user_list' ); ?>