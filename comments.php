<?php
if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="section section--comments comments-area">

	<?php if ( have_comments() ) : ?>
		<div class="section__header">
			<h2 class="section__title">
				<?php
				printf(
					_nx(
						'One thought on "%2$s"',
						'%1$s thoughts on "%2$s"',
						get_comments_number(),
						'comments title',
						TEXT_DOMAIN
					),
					number_format_i18n( get_comments_number() ),
					'<span>' . get_the_title() . '</span>'
				);
				?>
			</h2>
			<a href="#respond" class="btn btn-primary btn-sm"><?php _e( 'Add new comment' ); ?></a>
		</div>
		<div class="section__content">
			<ol class="comment-list">
				<?php
				wp_list_comments( array(
					'style'       	=> 'ol',
					'short_ping'  	=> true,
					'avatar_size' 	=> 64,
					'callback' 		=> 'mytheme_comment'
				) );
				?>
			</ol>

			<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
				<nav class="nav nav--comment" role="navigation">
					<span class="visually-hidden"><?php _e( 'Comment navigation', TEXT_DOMAIN ); ?></span>
					<div class="nav__content">
						<?php if ( get_previous_comments_link() ) : ?>
							<div class="nav__item nav__item--prev"><?php previous_comments_link( __( '&larr; Older Comments', TEXT_DOMAIN ) ); ?></div>
						<?php endif; ?>
						<?php if ( get_next_comments_link() ) : ?>
							<div class="nav__item nav__item--next"><?php next_comments_link( __( 'Newer Comments &rarr;', TEXT_DOMAIN ) ); ?></div>
						<?php endif; ?>
					</div>
				</nav>
			<?php endif; ?>

			<?php if ( ! comments_open() && get_comments_number() ) : ?>
				<p class="no-comments"><?php _e( 'Comments are closed.', TEXT_DOMAIN ); ?></p>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php
		$post_id       = $post->ID;
		$commenter     = wp_get_current_commenter();
		$user          = wp_get_current_user();
		$user_identity = $user->exists() ? $user->display_name : '';

		$args = array();
		$args = wp_parse_args( $args );
		if ( ! isset( $args['format'] ) ) {
			$args['format'] = current_theme_supports( 'html5', 'comment-form' ) ? 'html5' : 'xhtml';
		}

		$req   = get_option( 'require_name_email' );
		$html5 = 'html5' === $args['format'];

		$required_attribute = ( $html5 ? ' required' : ' required="required"' );
		$checked_attribute  = ( $html5 ? ' checked' : ' checked="checked"' );

		$consent = empty( $commenter['comment_author_email'] ) ? '' : $checked_attribute;

		$required_indicator = ' ' . wp_required_field_indicator();
		$required_text      = ' ' . wp_required_field_message();

		$fields = array(
			'author' => sprintf(
				'<p id="comment-form-author">%s %s</p>',
				sprintf(
					'<label for="author">%s%s</label>',
					__( 'Name' ),
					( $req ? $required_indicator : '' )
				),
				sprintf(
					'<input id="author" class="form-control" name="author" type="text" value="%s" size="30" maxlength="245" autocomplete="name"%s />',
					esc_attr( $commenter['comment_author'] ),
					( $req ? $required_attribute : '' )
				)
			),
			'email'  => sprintf(
				'<p id="comment-form-email">%s %s</p>',
				sprintf(
					'<label for="email">%s%s</label>',
					__( 'Email' ),
					( $req ? $required_indicator : '' )
				),
				sprintf(
					'<input id="email" class="form-control" name="email" %s value="%s" size="30" maxlength="100" aria-describedby="email-notes" autocomplete="email"%s />',
					( $html5 ? 'type="email"' : 'type="text"' ),
					esc_attr( $commenter['comment_author_email'] ),
					( $req ? $required_attribute : '' )
				)
			),
		);

		if ( has_action( 'set_comment_cookies', 'wp_set_comment_cookies' ) && get_option( 'show_comments_cookies_opt_in' ) ) {
			$consent = empty( $commenter['comment_author_email'] ) ? '' : $checked_attribute;

			$fields['cookies'] = sprintf(
				'<p id="comment-form-cookies-consent" class="form-check">%s %s</p>',
				sprintf(
					'<input id="wp-comment-cookies-consent" class="form-check-input" name="wp-comment-cookies-consent" type="checkbox" value="yes"%s />',
					$consent
				),
				sprintf(
					'<label for="wp-comment-cookies-consent">%s</label>',
					__( 'Save my name, email, and website in this browser for the next time I comment.' )
				)
			);

			if ( isset( $args['fields'] ) && ! isset( $args['fields']['cookies'] ) ) {
				$args['fields']['cookies'] = $fields['cookies'];
			}
		}

		$comments_args = array(
			'fields'               => $fields,
			'comment_field'        => sprintf(
				'<p id="comment-form-comment">%s %s</p>',
				sprintf(
					'<label for="comment">%s%s</label>',
					_x( 'Comment', 'noun' ),
					$required_indicator
				),
				'<textarea id="comment" class="form-control" name="comment" cols="45" rows="8" maxlength="65525"' . $required_attribute . '></textarea>'
			),
			'must_log_in'          => sprintf(
				'<p class="must-log-in">%s</p>',
				sprintf(
					__( 'You must be <a href="%s">logged in</a> to post a comment.' ),
					wp_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ), $post_id ) )
				)
			),
			'logged_in_as' 		   => '',
			'comment_notes_before' => sprintf(
				'<p class="comment-notes">%s%s</p>',
				sprintf(
					'<span id="email-notes">%s</span>',
					__( 'Your email address will not be published.' )
				),
				$required_text
			),
			'comment_notes_after'  => '',
			'action'               => site_url( '/wp-comments-post.php' ),
			'id_form'              => 'commentform',
			'id_submit'            => 'submit',
			'class_container'      => 'comment-respond',
			'class_form'           => 'comment-form',
			'class_submit'         => 'submit',
			'name_submit'          => 'submit',
			'title_reply'          => __( 'Leave a Reply' ),
			'title_reply_to'       => __( 'Leave a Reply to %s' ),
			'title_reply_before'   => '<h3 id="reply-title" class="comment-reply-title">',
			'title_reply_after'    => '</h3>',
			'cancel_reply_before'  => ' <small>',
			'cancel_reply_after'   => '</small>',
			'cancel_reply_link'    => __( 'Cancel reply' ),
			'label_submit'         => __( 'Post Comment' ),
			'submit_button'        => '<input name="%1$s" type="submit" id="%2$s" class="btn btn-primary %3$s" value="%4$s" />',
			'submit_field'         => '<p class="form-submit">%1$s %2$s</p>',
			'format'               => 'xhtml',
		);
		comment_form( $comments_args );
	?>

</section>
