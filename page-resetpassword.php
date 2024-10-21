<?php
global $user_ID;
$email ='';
if (!$user_ID) 
{

    $errmsg_arr = array();
    $succmsg_arr = array();
    //Check request url to change password
    if(isset($_GET['key']) && $_GET['action'] == "reset_pwd" && $_GET['user_email'] !="") 
    {
        $user_email=base64_decode($_GET['user_email']);
        $skey=$_GET['key'];
        $user_data = get_user_by_email($user_email);
        $user_id = $user_data->ID;
    }
    else
    {
        wp_redirect( home_url() ); exit;
    }

    if(isset($_POST['action']) && $_POST['action'] == "cl_set_pwd")
    {
        //We shall SQL escape all inputs
        global $wpdb;

        if ( !wp_verify_nonce( $_POST['cl_pwd_nonce'], "cl_pwd_nonce")) 
        {
            exit("No trick please");
        } 
        $user_email = base64_decode($_GET['user_email']);
        $user = get_user_by_email($user_email);
        $user_id = $user->ID;
        $user_login = $user->user_login;
        $skey=$_GET['key'];
        $newPassword = $wpdb->escape($_POST['newPassword']);
        $confirmPassword = $wpdb->escape($_POST['confirmPassword']);

        if(empty($newPassword)) 
        {
            $errmsg_arr[] = 'New password should not be empty.';
        }

        if(empty($confirmPassword)) 
        {
            $errmsg_arr[] = 'confirm password should not be empty.';
        }

        if($confirmPassword != $newPassword) 
        {
            $errmsg_arr[] = 'Password and confirm passowrd should be same.';
        }

        if(get_user_meta( $user_id, 'security_key', $single ) != $skey)
        {
            $errmsg_arr[] = 'Your reset password link has been expired.Please 
            <a href="'.get_permalink(get_page_by_path('client-forgot-password')).'">click here</a> to request password again.';
        }

        if(count($errmsg_arr) < 1)
        {
            wp_set_password( $newPassword, $user_id);
            update_usermeta( $user_id, 'plainPass', $newPassword);
            update_usermeta( $user_id, 'security_key', "");
            //=========Webservice Part=================
            //.Net Part Service Call
            /*$owm_udm_service = new Cl_owm_udm_services;
            $owm_udm_service->resetPassword($user_id, $newPassword);
            */

            $succmsg_arr[] = 'Your password has been changed succesfully.Please 
            <a href="'.get_permalink(get_page_by_path('client-login')).'">click here</a> to login.';
            
        }

    }
     
/**
 * Template Name: Reset Password Page Template
 *
 * Description: A page template that provides a key component of WordPress as a CMS
 * by meeting the need for a carefully crafted introductory page. The front page template
 * in Fratehaul consists of a page content area for adding text, images, video --
 * anything you'd like -- followed by front-page-only widgets in one or two columns.
 *
 * @package WordPress
 * @subpackage Fratehaul
 * @since Fratehaul 1.0
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
                        <div class="alert alert-success  success_box">
                            <a href="#" class="close" data-dismiss="alert">&times;</a>
                            <?php echo $succ;?>
                        </div>
                    <?php endforeach;?>
                <?php endif;?>

                <?php if(count($errmsg_arr) > 0):?>
                    <?php foreach ($errmsg_arr as $err):?>
                    <div class="alert alert-danger  error_box">
                        <a href="#" class="close" data-dismiss="alert">&times;</a>
                        <?php echo $err;?>
                    </div>
                    <?php endforeach;?>
                <?php endif;?>


                </div>
				<form name="setNewPassForm" class="form-horizontal" id="setNewPassForm" method="post" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="control-label col-md-4" for="email">New Password<span style="color:red;">*</span></label>
                    <div class="col-md-8">
                        <input type="password" class="form-control" id="newPassword" name="newPassword" value="<?php echo $email;?>" placeholder="Enter new password" >
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-md-4" for="email">Confirm Password<span style="color:red;">*</span></label>
                    <div class="col-md-8">
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" value="<?php echo $email;?>" placeholder="Enter confirm password" >
                    </div>
                </div>
                <input type="hidden" name="action" value="cl_set_pwd" />
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

get_footer();
}
else
{
  wp_redirect(get_home_url());
}