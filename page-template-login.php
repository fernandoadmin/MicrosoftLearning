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
                                $login_data = array();
                                $login_data['user_login'] = $user->user_login;
                                $login_data['user_password'] = $password;
                                $user_verify = wp_signon( $login_data, true );
								print_r($user_verify);

                                if ( is_wp_error($user_verify))
                                {
                                $errmsg_arr[]= "Invalid username or password.";
                                }
                                else
                                {
                                                
                                            //$login = "https://bpm.bpmcontext.com/api_v3_1_9/bpmcontext_wordpress.php?BPM_Email=".$user->user_email."&BPM_Password=".$password."&security=private";

                                        // echo $response = file_get_contents($login);

                                        $homepage_id= get_the_author_meta( 'homepage_id', $user->ID );
                                        if($homepage_id):
                                        // wp_redirect(get_permalink($homepage_id));
                                        wp_redirect(get_home_url());
                                        else:
                                        wp_redirect(get_home_url());
                                        endif;

                                }

                        }
            

            }
            else
            {
            $errmsg_arr[]= "Invalid username or email address.";
            }

    }

/*
Template Name: Login Page
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
						<form name="createsiteform" class="form-horizontal" id="createsiteform" method="post" action="<?php echo get_permalink();?>" enctype="multipart/form-data">
						<div class="form-group">
							<label class="control-label col-md-4" for="email">FedEx ID<span style="color:red;">*</span></label>
							<div class="col-md-8">
								<input type="text" class="form-control" id="email" name="username" value="<?php echo $username;?>" placeholder="Enter FedEx ID" required>
							</div>
							</div>

							<div class="form-group">
							<label class="control-label col-md-4" for="companyname">Password<span style="color:red;">*</span></label>
							<div class="col-md-8">
								<input type="password" class="form-control"  id="password" class="form-control" value="<?php echo $password;?>" name="password" placeholder="Enter password" required>
							</div>
							</div>


							<div class="form-group ">
							<label class="control-label col-md-4" for=" Contact Person"></label>
							<div class="col-md-8">
								<input type="submit" id="submit" class="btn btn-primary pull-right" value="Login">
								<div class="forgot-password pull-left register_button_strong">
								<a href="<?php echo get_permalink(get_page_by_path('client-forgot-password'))?>">Forgot Password?</a><br/>
									<span class="blue-text">Firstime user?</span>  <a href="<?php echo get_permalink(get_page_by_path('client-registration'))?>"><strong >Register Here</strong></a>
								</div>
							</div>
							<div class="clear"></div>
							</div>

							<div class="form-group ">
                            <label class="control-label col-md-4" for=" Info Text"></label>
                            <div class="col-md-8">
                                <p>If you are having trouble logging in, please email <a href="mailto:fedex.support@managewireless.com">fedex.support@managewireless.com</a> with a screenshot and the issue in which you are facing and someone will assist you as soon as possible.</p>
                                </div>

						</form>
             </div>
	    </div>

					</div>

                            

				<?php
					if ( ! $is_page_builder_used && comments_open() && 'on' === et_get_option( 'divi_show_pagescomments', 'false' ) ) comments_template( '', true );
				?>

				</<?php echo et_core_intentionally_unescaped( $container_tag, 'fixed_string' ); ?>>

			<?php endwhile; ?>

<?php if ( ! $is_page_builder_used ) : ?>

			</div>

			<?php get_sidebar(); ?>
		</div>
	</div>

<?php endif; ?>

</div>

<?php
/*
get_footer();
}
else
{
*/
  wp_redirect(get_home_url());
}