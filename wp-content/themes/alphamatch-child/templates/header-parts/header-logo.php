<div class="header-logo">
    <div class="header_logo">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
            <?php if ( get_theme_mod( 'superlist_logo' ) ) : ?>
                <img src="<?php echo esc_attr( get_theme_mod( 'superlist_logo' ) ); ?>" alt="<?php bloginfo( 'name' ); ?>">
            <?php else : ?>
                <i class="superlist-logo"></i>
            <?php endif; ?>

            <?php if ( display_header_text() ) : ?>
                <strong><?php bloginfo( 'name' ); ?></strong>
                <span><?php bloginfo( 'description' ); ?></span>
            <?php endif; ?>
        </a>
    </div>
    <div class="shiftNav">
        <?php if(function_exists('shiftnav_toggle')){ shiftnav_toggle( 'shiftnav-main' , '' , array( 'icon' => 'bars' , 'class' => 'shiftnav-toggle-button') ); } ?>
    </div>
</div><!-- /.header-logo -->