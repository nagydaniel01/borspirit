<?php
    /**
     * Variables available:
     * - $email_subject
     */
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />
		<meta content="width=device-width, initial-scale=1.0" name="viewport">
		<title><?php echo get_bloginfo( 'name', 'display' ); ?></title>
    </head>
    <body style="margin:0px; padding:0px; background-color:#f7f7f7; font-family:Arial, sans-serif; font-size:14px; line-height:1.5;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f7f7f7; padding:20px 0;">
            <tr>
                <td align="center">
                    <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color:#ffffff; border:1px solid #e4e4e4;">
                        <!-- Header -->
                        <tr>
                            <td align="center" style="background-color:#9ab4c6; padding:20px; color:#ffffff;">
                                <?php
                                    $logo_id = get_field('email_logo', 'option');
                                    $logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : '';
                                ?>
                                <?php if ($logo_url): ?>
                                    <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" style="max-height:60px; margin-bottom:10px;" />
                                <?php endif; ?>
                                <h1 style="margin:0px; font-size:20px; color:#ffffff;"><?php echo esc_html($email_subject); ?></h1>
                            </td>
                        </tr>

                        <!-- Body -->
                        <tr>
                            <td style="padding:30px; color:#333333;">