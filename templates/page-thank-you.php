<?php
/**
 * Template Name: Thank You Template
 */
?>

<?php get_header(); ?>

<?php
    // Get the message_id from query string (?message_id=...)
    $message_id = isset($_GET['message_id']) ? sanitize_text_field($_GET['message_id']) : '';
    $data = $message_id ? get_transient($message_id) : false;
?>

<main class="page page--default">
    <section class="section section--default">
        <div class="container">
            <header class="page__header">
                <h1 class="page__title"><?php echo esc_html__('Thank You!', TEXT_DOMAIN); ?></h1>
            </header>
            <div class="page__content">
                <?php if ($data) : ?>
                    <p><?php echo esc_html__('We’ve received your message. Here’s a summary of what you submitted:', TEXT_DOMAIN); ?></p>

                    <ul class="thank-you-details">
                        <li>
                            <strong><?php echo esc_html__('Name:', TEXT_DOMAIN); ?></strong>
                            <?php echo esc_html($data['name']); ?>
                        </li>
                        <li>
                            <strong><?php echo esc_html__('Email:', TEXT_DOMAIN); ?></strong>
                            <?php echo esc_html($data['email']); ?>
                        </li>
                        <?php if (!empty($data['phone'])) : ?>
                            <li>
                                <strong><?php echo esc_html__('Phone:', TEXT_DOMAIN); ?></strong>
                                <?php echo esc_html($data['phone']); ?>
                            </li>
                        <?php endif; ?>
                        <li>
                            <strong><?php echo esc_html__('Subject:', TEXT_DOMAIN); ?></strong>
                            <?php echo esc_html($data['subject']); ?>
                        </li>
                        <li>
                            <strong><?php echo esc_html__('Message:', TEXT_DOMAIN); ?></strong>
                            <?php echo nl2br(esc_html($data['message'])); ?>
                        </li>
                    </ul>

                <?php else : ?>
                    <p><?php echo esc_html__('Sorry, we couldn’t find your message details or the session has expired.', TEXT_DOMAIN); ?></p>
                <?php endif; ?>

                <a href="<?php echo esc_url(home_url('/')); ?>" class="button">
                    <?php echo esc_html__('Back to Home', TEXT_DOMAIN); ?>
                </a>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
