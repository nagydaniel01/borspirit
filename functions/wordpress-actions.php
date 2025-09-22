<?php
    if ( ! function_exists( 'move_comment_field' ) ) {
        /**
         * Moves the comment textarea to the bottom of the comment form fields.
         *
         * This function reorders the fields in the WordPress comment form
         * so that the comment textarea appears after other fields like
         * name, email, and website.
         *
         * @param array $fields An array of comment form fields.
         * @return array Modified array of comment form fields with comment at the end.
         */
        function move_comment_field( $fields ) {
            if ( isset( $fields['comment'] ) ) {
                $comment_field = $fields['comment'];
                unset( $fields['comment'] );
                $fields['comment'] = $comment_field;
            }
            return $fields;
        }
        add_filter( 'comment_form_fields', 'move_comment_field' );
    }

    if ( ! function_exists( 'save_term_date' ) ) {
        /**
         * Save Term created date when a new term is created.
         *
         * @param int $term_id The ID of the term being created.
         */
        function save_term_date( $term_id ) {
            add_term_meta(
                $term_id,
                '_term_date', // Meta key for term creation date
                date('Y-m-d H:i:s') // Current date and time
            );
        }

        // Hook into term creation for various taxonomies
        add_action( 'created_category', 'save_term_date' );
        add_action( 'created_post_tag', 'save_term_date' );
        add_action( 'created_product_cat', 'save_term_date' );
        add_action( 'created_product_tag', 'save_term_date' );
    }

    if ( ! function_exists( 'save_term_modified_date' ) ) {
        /**
         * Save Term modified date when a term is edited.
         *
         * @param int $term_id The ID of the term being edited.
         */
        function save_term_modified_date( $term_id ) {
            update_term_meta(
                $term_id,
                '_term_modified', // Meta key for term modified date
                date('Y-m-d H:i:s') // Current date and time
            );
        }
        
        // Hook into term editing for various taxonomies
        add_action( 'edited_category', 'save_term_modified_date' );
        add_action( 'edited_post_tag', 'save_term_modified_date' );
        add_action( 'edited_product_cat', 'save_term_modified_date' );
        add_action( 'edited_product_tag', 'save_term_modified_date' );
    }

    if ( ! function_exists( 'get_the_term_date' ) ) {
        /**
         * Retrieve the created date of a term.
         *
         * @param int $term_id The ID of the term.
         * @return string The created date in 'Y-m-d H:i:s' format, or empty string if not set.
         */
        function get_the_term_date( $term_id ) {
            $created_date = get_term_meta( $term_id, '_term_date', true );
            
            return ! empty( $created_date ) ? $created_date : '';
        }
    }

    if ( ! function_exists( 'get_the_term_modified_date' ) ) {
        /**
         * Retrieve the modified date of a term.
         *
         * @param int $term_id The ID of the term.
         * @return string The modified date in 'Y-m-d H:i:s' format, or empty string if not set.
         */
        function get_the_term_modified_date( $term_id ) {
            $modified_date = get_term_meta( $term_id, '_term_modified', true );
            
            return ! empty( $modified_date ) ? $modified_date : '';
        }
    }

    if ( ! function_exists( 'comment_rating_rating_field' ) ) {
        /**
         * Adds a rating field to the comment form.
         *
         * This function inserts a radio button selection for users to rate a post/comment.
         * It allows users to provide a rating between 1 and 5 stars.
         *
         * @return void
         */
        function comment_rating_rating_field () {
            global $post;

            // Only add the rating field for 'post' type content
            if ( 'post' !== $post->post_type ) {
                return;
            }
            ?>
            <p id="comment-form-rating">
                <label for="rating"><?php echo esc_html__('Rating', TEXT_DOMAIN); ?><span class="required">*</span></label>
                <span class="stars">
                    <?php for ( $i = 5; $i >= 1; $i-- ) : ?>
                        <input type="radio" id="rating-<?php echo esc_attr( $i ); ?>" name="rating" value="<?php echo esc_attr( $i ); ?>" /><label for="rating-<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $i ); ?></label>
                    <?php endfor; ?>
                </span>
            </p>
            <?php
        }
        add_action( 'comment_form_logged_in_after', 'comment_rating_rating_field' );
        add_action( 'comment_form_after_fields', 'comment_rating_rating_field' );
    }

    if ( ! function_exists( 'comment_rating_save_comment_rating' ) ) {
        /**
         * Saves the rating submitted with a comment.
         *
         * This function saves the rating value submitted by the user along with their comment
         * using the `add_comment_meta()` function to store the rating in the comment metadata.
         *
         * @param int $comment_id The ID of the comment being saved.
         * @return void
         */
        function comment_rating_save_comment_rating( $comment_id ) {
            if ( isset( $_POST['rating'] ) && '' !== $_POST['rating'] ) {
                $rating = intval( $_POST['rating'] );
                add_comment_meta( $comment_id, 'rating', $rating );
            }
        }
        add_action( 'comment_post', 'comment_rating_save_comment_rating' );
    }

    if ( ! function_exists( 'comment_rating_require_rating' ) ) {
        /**
         * Ensures a rating is provided when submitting a comment.
         *
         * This function checks if the user has provided a rating before submitting a comment.
         * If no rating is provided, an error message is shown and the comment submission is stopped.
         *
         * @param array $commentdata The comment data being processed.
         * @return array The original comment data if a rating is provided, otherwise exits.
         */
        function comment_rating_require_rating( $commentdata ) {
            if ( ! is_admin() && ( ! isset( $_POST['rating'] ) || 0 === intval( $_POST['rating'] ) ) )
                wp_die( __( 'Error: You did not add a rating. Hit the Back button on your Web browser and resubmit your comment with a rating.', TEXT_DOMAIN ) );
            return $commentdata;
        }
        add_filter( 'preprocess_comment', 'comment_rating_require_rating' );
    }

    if ( ! function_exists( 'comment_rating_display_rating' ) ) {
        /**
         * Displays the rating stars on the comment page.
         *
         * This function retrieves the rating for the comment and displays the corresponding
         * stars as part of the comment content.
         *
         * @param string $comment_text The original comment text.
         * @param string $custom_string A custom message to be displayed after the rating.
         * @return string The comment text, with the added rating stars and custom string (if provided).
         */
        function comment_rating_display_rating( $comment_text, $custom_string = '' ) {
            if ( $rating = get_comment_meta( get_comment_ID(), 'rating', true ) ) {
                $stars = '<p class="stars">';
                for ( $i = 1; $i <= $rating; $i++ ) {
                    $stars .= '<span class="dashicons dashicons-star-filled"></span>';
                }
                $stars .= '</p>';
                $comment_text = $comment_text . $stars;
            }

            // Add custom string (if provided)
            if ( ! empty( $custom_string ) ) {
                $comment_text .= '<p class="custom-note">' . esc_html( $custom_string ) . '</p>';
            }

            return $comment_text;
        }
    }

    if ( ! function_exists( 'comment_rating_get_average_ratings' ) ) {
        /**
         * Retrieves the average rating for a post based on its comments.
         *
         * This function calculates the average rating of a post by checking the ratings
         * in the approved comments associated with the post.
         *
         * @param int $id The ID of the post.
         * @return float|false The average rating, or false if no ratings are found.
         */
        function comment_rating_get_average_ratings( $id ) {
            $comments = get_approved_comments( $id );

            if ( $comments ) {
                $i = 0;
                $total = 0;
                foreach( $comments as $comment ){
                    $rate = get_comment_meta( $comment->comment_ID, 'rating', true );
                    if( isset( $rate ) && '' !== $rate ) {
                        $i++;
                        $total += $rate;
                    }
                }

                if ( 0 === $i ) {
                    return false;
                } else {
                    return round( $total / $i, 1 );
                }
            } else {
                return false;
            }
        }
    }

    if ( ! function_exists( 'average_rating_shortcode' ) ) {
        /**
         * Displays the average rating of a post using a shortcode.
         *
         * This function allows you to insert the average rating stars into post content
         * using the `[average_rating]` shortcode. It will show the average rating stars
         * based on all the comments and ratings submitted.
         *
         * @param string $content The original post content.
         * @return string The content with the average rating stars prepended.
         */
        function average_rating_shortcode( $content ) {
            global $post;

            if ( false === comment_rating_get_average_ratings( $post->ID ) ) {
                return $content;
            }

            $stars   = '';
            $average = comment_rating_get_average_ratings( $post->ID );

            for ( $i = 1; $i <= 5; $i++ ) {
                $width = intval( $i - $average > 0 ? 20 - ( ( $i - $average ) * 20 ) : 20 );
            
                if ( $width > 0 ) {
                    $stars .= '<span class="dashicons dashicons-star-filled"></span>';
                } else {
                    $stars .= '<span class="dashicons dashicons-star-empty"></span>';
                }
            }

            $custom_content  = '<p class="average-rating">' . $stars . '</p>';
            $custom_content .= $content;
            return $custom_content;
        }
        add_shortcode( 'average_rating', 'average_rating_shortcode' );
    }