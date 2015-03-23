<section class="parallax-section clearfix team_template" id="team">
    <div class="mid-content">
        <h1><span>Team</span></h1>
        <div class="parallax-content">
        </div>
        <ul class="team-members">
            <?php $user_query = get_team_members(); ?>
            <?php if ( ! is_wp_error( $user_query ) && is_array( $user_query ) && ! empty( $user_query )): ?>
                <?php foreach ($user_query as $user):
                    // print_r($user); // for debugging
                    $user_name = $user->post_title;
                    $user_bio = strip_tags($user->post_content);
                    // Extract image URL; return default if not available
                    $user_image = get_stylesheet_directory_uri() . "/images/omnilab/default-user-male.png";
                    if (array_key_exists("image", $user)) {
                        $res = Array();
                        if (preg_match('/src=[\"\']([^\"\']+)[\"\']/', $user->image, $res) ){
                            if ( count($res) > 0 ){
                                $user_image = $res[1];
                            }
                        }
                    }
                    ?>

                    <li class="">
                        <div class="team-member">
                            <div class="team-member-image">
                                <img alt="<?php echo $user_name; ?>", src="<?php echo $user_image; ?>">
                            </div>
                            <div class="team-member-overlay">
                                <div class="item-overlay-center">
                                    <h2><?php echo $user_name; ?></h2>
                                    <p><?php echo extract_sentences($user_bio, 2); ?></p>
                                    <ul class="team-social-icon">
                                        <?php if ( array_key_exists("twitter", $user) && $user->twitter != "" ):?>
                                            <li>
                                                <a title="twitter" href="https://twitter.com/<?php echo $user->twitter; ?>">
                                                    <i class="fa fa-twitter fa-2x"></i>
                                                </a>
                                            </li>
                                        <?php endif ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li>

                <?php endforeach ?>
            <?php endif ?>
        </ul>
    </div>
</section>