<section class="parallax-section clearfix team_template" id="team">
    <div class="mid-content">
        <h1><span>Team</span></h1>
        <div class="parallax-content">
        </div>
        <ul class="team-members">
            <?php
            $filter = array(
                'limit' 					=> 10,
                'per_row' 					=> null,
                'orderby' 					=> 'menu_order',
                'order' 					=> 'DESC',
                'id' 						=> 0,
                'slug'						=> null,
                'display_author' 			=> true,
                'display_additional' 		=> true,
                'display_avatar' 			=> true,
                'display_url' 				=> true,
                'display_twitter' 			=> true,
                'display_author_archive'	=> true,
                'display_role'	 			=> true,
                'contact_email'				=> true,
                'tel'						=> true,
                'effect' 					=> 'fade', // Options: 'fade', 'none'
                'pagination' 				=> false,
                'echo' 						=> false,
                'size' 						=> 285,
                'title' 					=> '',
                'category' 					=> 0
            );

            $user_query = woothemes_get_our_team($filter);
            // print_r($user_query);
            ?>
            <?php if ( ! is_wp_error( $user_query ) && is_array( $user_query ) && ! empty( $user_query )): ?>
                <?php foreach ($user_query as $user):
                    $user_name = $user->post_title;
                    $user_bio = strip_tags($user->post_content);
                    $user_image = $user->image;
                    ?>

                    <li class="">
                        <div class="team-member">
                            <div class="team-member-image">
                                <?php echo $user_image; ?>
                            </div>
                            <div class="team-member-overlay">
                                <h2><?php echo $user_name; ?></h2>
                                <p><?php echo $user_bio; ?></p>
                                <ul class="team-social-icon">
                                    <li>
                                        <a title="twitter" href="https://twitter.com/xiamingc"></a>
                                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>

                <?php endforeach ?>
            <?php endif ?>
        </ul>
    </div>
</section>