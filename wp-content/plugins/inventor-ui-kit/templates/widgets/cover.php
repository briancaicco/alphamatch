<?php
/**
 * Widget template
 *
 * @package Inventor UI Kit
 * @subpackage Widgets/Templates
 */

$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
$subtitle = ! empty( $instance['subtitle'] ) ? $instance['subtitle'] : '';
$height = ! empty( $instance['height'] ) ? $instance['height'] : '';
$overlay_opacity = ! empty( $instance['overlay_opacity'] ) ? $instance['overlay_opacity'] : '';
$overlay_color = ! empty( $instance['overlay_color'] ) ? $instance['overlay_color'] : '';
$poster = ! empty( $instance['poster'] ) ? $instance['poster'] : '';
$video_mp4 = ! empty( $instance['video_mp4'] ) ? $instance['video_mp4'] : '';
$video_ogg = ! empty( $instance['video_ogg'] ) ? $instance['video_ogg'] : '';
$video_embed = ! empty( $instance['video_embed'] ) ? $instance['video_embed'] : '';
$input_titles = ! empty( $instance['input_titles'] ) ? $instance['input_titles'] : 'labels';
$button_text = ! empty( $instance['button_text'] ) ? $instance['button_text'] : '';
$sort = ! empty( $instance['sort'] ) ? $instance['sort'] : '';
$per_row = ! empty( $instance['per_row'] ) ? $instance['per_row'] : '3';
?>

<?php echo wp_kses( $args['before_widget'], wp_kses_allowed_html( 'post' ) ); ?>

<?php $player_id = "listing-banner-player"; ?>

    <div id="video-outer-wrap" class="inventor-cover" style="<?php if ( ! empty( $poster ) ) : ?>background-image: url('<?php echo esc_attr( $poster )?>');<?php endif; ?><?php if ( ! empty( $height ) ) : ?>height: <?php echo esc_attr( $height ); ?><?php endif; ?>">
        <div id="video-wrap">

            <?php if ( ! empty( $video_embed ) ) : ?>
                <div id="inventor-cover" class="embed-video">
                    <?php echo wp_oembed_get( $video_embed, array( 'width' => 4000 ) ); ?>
                </div>
            <?php elseif ( ! empty( $video_mp4 ) || ! empty( $video_ogg ) ) : ?>
                <video id="inventor-cover" preload="metadata" autoplay muted loop>
                    <?php if ( ! empty( $video_mp4 ) ) : ?>
                        <source src="<?php echo esc_attr( $video_mp4 ); ?>" type="video/mp4">
                    <?php endif; ?>

                    <?php if ( ! empty( $video_ogg ) ) : ?>
                        <source src="<?php echo esc_attr( $video_ogg ); ?>" type="video/ogg">
                    <?php endif; ?>
                </video><!-- /#inventor-cover -->
            <?php endif; ?>

        </div><!-- /#video-wrap -->

        <div class="video-wrapper-overlay" style="<?php if ( ! empty( $overlay_opacity ) ) : echo 'opacity:' . esc_attr( $overlay_opacity ) . ';'; endif; ?><?php if ( ! empty( $overlay_color ) ) : echo 'background-color:' . esc_attr( $overlay_color ) . ';'; endif; ?>"></div><!-- /.video-wrapper-overlay -->
    </div><!-- /#video-outer-cover -->

<?php if ( ! empty( $title ) || ! empty( $subtitle ) || ! empty( $instance['filter'] ) ) : ?>
    <div class="inventor-cover-title">
        <?php if ( ! empty( $title ) ) : ?>
            <h1><?php echo esc_attr( $title ); ?></h1>
        <?php endif; ?>

        <?php if ( ! empty( $subtitle ) ) :?>
            <h2><?php echo esc_attr( $subtitle ); ?></h2>
        <?php endif; ?>

        <?php if ( ! empty( $instance['filter'] ) ) : ?>
            <div class="inventor-cover-filter items-per-row-<?php echo $per_row ?>">
                <?php include Inventor_Template_Loader::locate( 'widgets/filter-form' ); ?>
            </div><!-- /.row -->
        <?php endif; ?>

        <?php do_action( 'inventor_after_cover_filter', $instance ); ?>
    </div><!-- /.inventor-cover-title -->
<?php endif; ?>

<?php do_action( 'inventor_after_cover_title', $instance ); ?>

<?php echo wp_kses( $args['after_widget'], wp_kses_allowed_html( 'post' ) ); ?>

    <script>
        var videoCanvas = jQuery('#inventor-cover');
    </script>

<?php if ( ! empty( $video_embed ) ) : ?>
    <script>
        var videoUrl = "<?php echo esc_attr( $video_embed ); ?>";
        var videoFrame = jQuery('#inventor-cover iframe');

        /* Youtube */
        if (videoUrl.indexOf('youtube.com') > -1) {
            videoCanvas = videoFrame;

            videoFrame.attr('id', function() {
                return 'video-iframe';
            });

            videoFrame.attr('src', function() {
                return this.src + '?feature=oembed&enablejsapi=1&rel=0&iv_load_policy=3&showinfo=0&vq=hd1080&autoplay=1&controls=0&loop=0&muted=1&playlist=' + YouTubeGetID(videoUrl);
            });

            var tag = document.createElement('script');
            tag.src = "https://www.youtube.com/iframe_api";
            var firstScriptTag = document.getElementsByTagName('script')[0];
            firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

            var player;

            function onYouTubeIframeAPIReady() {
                player = new YT.Player('video-iframe', {
                    events: {
                        'onReady': onPlayerReady
                    }
                });
            }

            function onPlayerReady(event) {
                player.mute();
                player.playVideo();
            }

            /* Vimeo */
        } else if (videoUrl.indexOf('vimeo.com') > -1) {
            videoCanvas = videoFrame;

            videoFrame.attr('src', function() {
                return this.src + '?title=0&byline=0&badge=0&color=ffffff';
            });

            var iframe = document.querySelector('#inventor-cover > iframe');
            var player = new Vimeo.Player(iframe);

            player.setVolume(0);
            player.setLoop(true);
            player.disableTextTrack();
            player.play();
        }

        function YouTubeGetID(url){
            var ID = '';
            url = url.replace(/(>|<)/gi,'').split(/(vi\/|v=|\/v\/|youtu\.be\/|\/embed\/)/);
            if(url[2] !== undefined) {
                ID = url[2].split(/[^0-9a-z_\-]/i);
                ID = ID[0];
            }
            else {
                ID = url;
            }
            return ID;
        }
    </script>
<?php endif; ?>

<?php if ( ! empty( $video_embed ) || ! empty( $video_mp4 ) || ! empty( $video_ogg ) ) : ?>
    <script>
        videoCanvas.attr('height', function() {
            var wrapper_width = jQuery('.video-wrapper-overlay').width();
            var wrapper_height = jQuery('.video-wrapper-overlay').height();
            var iframe_width = jQuery(this).attr('width');
            var iframe_height = jQuery(this).attr('height');

            var iframe_ratio = 16/9;

            if ( iframe_width && iframe_height ) {
                iframe_ratio = iframe_width / iframe_height;
            }

            var height = wrapper_width/iframe_ratio;
            var width = iframe_ratio * wrapper_height;

            if( wrapper_width < wrapper_height || height < wrapper_height ) {
                // black bars from top and bottom
                jQuery(this).css('width', width );
                jQuery(this).css('top', '0' );
                jQuery(this).css('left', '50%' );
                jQuery(this).css('transform', 'translate(-50%)');
                return wrapper_height;
            }

            if( height >= iframe_height ) {
                // black bars from left and right
                return height;
            } else {
                return iframe_height;
            }
        });
    </script>
<?php endif; ?>