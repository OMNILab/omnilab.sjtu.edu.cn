<?php
/**
 * A template to display team member content
 */
global $post;
?>

<?php
$team_member_email = esc_attr( get_post_meta( $post->ID, '_gravatar_email', true ) );
$user = esc_attr( get_post_meta( $post->ID, '_user_id', true ) );
$user_search = esc_attr( get_post_meta( $post->ID, '_user_search', true ) );
$twitter = esc_attr( get_post_meta( $post->ID, '_twitter', true ) );
$role = esc_attr( get_post_meta( $post->ID, '_byline', true ) );
$url = esc_attr( get_post_meta( $post->ID, '_url', true ) );
$tel = esc_attr( get_post_meta( $post->ID, '_tel', true ) );
$contact_email = esc_attr( get_post_meta( $post->ID, '_contact_email', true ) );
?>

<section id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php
        if ( has_post_thumbnail() ) {
            the_post_thumbnail();
        } elseif ( isset( $team_member_email ) && ( '' != $team_member_email ) ) {
            echo '<figure itemprop="image">' . get_avatar( $team_member_email, 250 ) . '</figure>';
        }
        ?>
        <h1 class="entry-title">
            <?php if ( is_single() ) : ?>
                <?php
                if ( '' != $url && apply_filters( 'woothemes_our_team_member_url', true ) ) {
                    echo '<a href="' . $url . '">' . get_the_title() . '</a>';
                } else {
                    the_title();
                }
                ?>
            <?php else : ?>
                <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
            <?php endif; // is_single() ?>
        </h1>

        <?php
        $member_role = '';
        if ( isset( $role ) && '' != $role && apply_filters( 'woothemes_our_team_member_role', true ) ) {
            $member_role .= ' <p class="role" itemprop="jobTitle">' . $role . '</p>' . "\n";
        }
        echo apply_filters( 'woothemes_our_team_member_fields_display', $member_role );
        ?>

    </header><!-- .entry-header -->

    <div class="entry-content">
        <?php the_content(); ?>
    </div><!-- .entry-content -->

    <footer>
        <?php
        $author .= '<ul class="author-details">';
        $member_fields = '';
        if ( apply_filters( 'woothemes_our_team_member_user_id', true ) ) {
            if ( 0 == $user && '' != $user_search ) {
                $user = get_user_by( 'slug', $user_search );
                if ( $user ) {
                    $user = $user;
                }
            }
            if ( 0 != $user ) {
                $member_fields .= '<li class="our-team-author-archive" itemprop="url"><a href="' . get_author_posts_url( $user ) . '">' . sprintf( __( 'Read posts by %1$s', 'woothemes' ), get_the_title() ) . '</a></li>' . "\n";
            }
        }
        if ( '' != $contact_email && apply_filters( 'woothemes_our_team_member_contact_email', true ) ) {
            $member_fields .= '<li class="our-team-contact-email" itemprop="email"><a href="mailto:' . esc_html( $contact_email ) . '">' . __( 'Email ', 'our-team-by-woothemes' ) . get_the_title() . '</a></li>';
        }
        if ( '' != $tel && apply_filters( 'woothemes_our_team_member_tel', true ) ) {
            $call_protocol = apply_filters( 'woothemes_our_team_call_protocol', $protocol = 'tel' );
            $member_fields .= '<li class="our-team-tel" itemprop="telephone"><span>' . __( 'Tel: ', 'our-team-by-woothemes' ) . '</span><a href="' . $call_protocol . ':' . esc_html( $tel ) . '">' . esc_html( $tel ) . '</a></li>';
        }
        if ( '' != $twitter && apply_filters( 'woothemes_our_team_member_twitter', true ) ) {
            $member_fields .= '<li class="our-team-twitter" itemprop="contactPoint"><a href="//twitter.com/' . esc_html( $twitter ) . '" class="twitter-follow-button" data-show-count="false">Follow @' . esc_html( $twitter ) . '</a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?"http":"https";if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document, "script", "twitter-wjs");</script></li>' . "\n";
        }
        $author .= apply_filters( 'woothemes_our_member_fields_display', $member_fields );
        $author .= '</ul>';
        echo $author;
        ?>
    </footer><!-- .entry-meta -->
</section><!-- #post --> 