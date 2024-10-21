<?php

global $user_ID;
if (!$user_ID) 
{
    $errmsg_arr = array();
    $succmsg_arr = array();
    $username ='';
    $password = '';
    if(isset($_POST['username']) && isset($_POST['password']))
    {
            //We shall SQL escape all inputs
            global $wpdb;
            session_start();
            $_SESSION['bpmLoginStatus'] = 0;
        
            $username = $_POST['username'];
            $password = $wpdb->escape($_REQUEST['password']);
            if(empty($username)) 
            {
            $errmsg_arr[]= "Please enter your username or email address!";
            }
            if(empty($password)) 
            {
                $errmsg_arr[]= "Please enter your password!";
            }

            $user = cloasis_authenticate_username_or_email($username);

            //print_r($user);
            $valid = authenticate_user_against_edr_us($username);
           //$valid = 1;
            //echo "Status:".$valid;
            if($user != '' && $valid == 1)
            {      
            if(count($errmsg_arr) < 1)
            {
                        if(empty($user) || $user->caps[administrator] == 1)
                        {
                            $errmsg_arr[]= "Invalid FedEx ID.";
                        }
                        else
                        {
                            $user_id = $user->ID;
                            $user_login = $user->user_login;
                            $user_email = $user->user_email;
                            $key = wp_generate_password(20, false);
                            update_usermeta( $user_id, 'security_key', $key);
                            //echo esc_attr( get_the_author_meta( 'security_key', $user_id ) );
                            if(cloasis_user_forgot_pass_request($user_email, $key) == 1) 
                            {
                                $succmsg_arr[] =  "We have just sent you an email with password reset instructions.</div>";
                            }
                            else
                            {
                                $errmsg_arr[] = "Email failed to send for some unknown reason.";
                            }
                        }
            }

    }
/*
Template Name: Blank Page
*/

get_header();

$post_id              = get_the_ID();
$is_page_builder_used = et_pb_is_pagebuilder_used( $post_id );
$container_tag        = 'product' === get_post_type( $post_id ) ? 'div' : 'article'; ?>

    <div id="main-content">

<?php if ( ! $is_page_builder_used ) : ?>

    <div class="container">
        <div id="content-area" class="clearfix">
            <div id="left-area">

<?php endif; ?>

            <?php while ( have_posts() ) : the_post(); ?>

                <<?php echo $container_tag; ?> id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <?php if ( ! $is_page_builder_used ) : ?>

                    <h1 class="main_title"><?php the_title(); ?></h1>
                <?php
                    $thumb = '';

                    $width = (int) apply_filters( 'et_pb_index_blog_image_width', 1080 );

                    $height = (int) apply_filters( 'et_pb_index_blog_image_height', 675 );
                    $classtext = 'et_featured_image';
                    $titletext = get_the_title();
                    $alttext = get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );
                    $thumbnail = get_thumbnail( $width, $height, $classtext, $alttext, $titletext, false, 'Blogimage' );
                    $thumb = $thumbnail["thumb"];

                    if ( 'on' === et_get_option( 'divi_page_thumbnails', 'false' ) && '' !== $thumb )
                        print_thumbnail( $thumb, $thumbnail["use_timthumb"], $titletext, $width, $height );
                ?>

                <?php endif; ?>

                    <div class="entry-content">
                    <?php
                        the_content();

                        if ( ! $is_page_builder_used )
                            wp_link_pages( array( 'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'Divi' ), 'after' => '</div>' ) );
                    ?>
                    </div> 
                    <!-- .entry-content -->



<div class="row">
            <div class="col-md-8 offset-md-2 col-sm-12 innersec">
                    <div id="result">
                            <?php if(count($succmsg_arr) > 0):?>
                                <?php foreach ($succmsg_arr as $succ):?>
                                    <div class="alert alert-success success_box">
                                        <a href="#" class="close" data-dismiss="alert">&times;</a>
                                        <?php echo $succ;?>
                                    </div>
                                <?php endforeach;?>
                            <?php endif;?>

                            <?php if(count($errmsg_arr) > 0):?>
                                <?php foreach ($errmsg_arr as $err):?>
                                    <div class="alert alert-danger error_box">
                                        <a href="#" class="close" data-dismiss="alert">&times;</a>
                                        <?php echo $err;?>
                                    </div>
                                <?php endforeach;?>
                            <?php endif;?>


                    </div>
                    <form name="forgotPassForm" class="form-horizontal" id="forgotPassForm" method="post" action="<?php echo get_permalink();?>" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="control-label col-md-4" for="email">FedEx ID<span style="color:red;">*</span></label>
                        <div class="col-md-8">
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $email;?>" placeholder="Enter your FedEx ID" required>
                        </div>
                    </div>
                    <input type="hidden" name="action" value="cl_pwd_reset" />
                    <input type="hidden" name="cl_pwd_nonce" value="<?php echo wp_create_nonce("cl_pwd_nonce"); ?>" />
                    <div class="form-group">
                        <label class="control-label col-md-4" for=" Contact Person"></label>
                        <div class="col-md-8">
                            <input type="submit" id="submit" class="btn btn-primary pull-right" value="Submit">
                        </div>
                        <div class="clear"></div>
                    </div>
                    </form>
        
        </div>       

    </div>
                    </div>




                <?php
                    if ( ! $is_page_builder_used && comments_open() && 'on' === et_get_option( 'divi_show_pagescomments', 'false' ) ) comments_template( '', true );
                ?>

                </<?php echo $container_tag; ?>> <!-- .et_pb_post -->

            <?php endwhile; ?>

<?php if ( ! $is_page_builder_used ) : ?>

            </div> <!-- #left-area -->

            <?php get_sidebar(); ?>
        </div> <!-- #content-area -->
    </div> <!-- .container -->

<?php endif; ?>

</div> <!-- #main-content -->

<?php

get_footer();
}
else
{
  wp_redirect(get_home_url());
}