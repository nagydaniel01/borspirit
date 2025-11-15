<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo esc_html__( 'Coming soon', 'borspirit' ); ?></title>
        <link rel="icon" href="<?php echo esc_url( TEMPLATE_DIR_URI . '/assets/src/images/borspirit-logo-white.svg' ); ?>" type="image/svg+xml">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;700&display=swap" rel="stylesheet">
        <style>
            body {
                margin: 0;
                padding: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: #212121;
                background-image: url('<?php echo TEMPLATE_DIR_URI . "/assets/src/images/login-background.jpg"; ?>');
                background-size: cover;
                background-position: center center;
                height: 100vh;
                font-family: "Roboto", sans-serif;
                text-align: center;
                color: #000;
            }
            body::before {
                content: '';
                position: absolute;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                z-index: -1;
                background: rgba(33,33,33,.65);
            }
            .container {
                max-width: 600px;
                padding: 20px;
                color: #FFFFFF;
            }
            h1 {
                font-size: 3rem;
                margin-top: 0;
                margin-bottom: 1rem;
            }
            .logo {
                margin-bottom: 1.5rem;
            }
            .logo img {
                width: 240px;
                height: auto;
                padding: 0;
                margin: 0 auto;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="logo">
                <img src="<?php echo esc_url( TEMPLATE_DIR_URI . '/assets/src/images/borspirit-logo-white.svg' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
            </div>
            <h1><?php echo esc_html__( 'Coming soon', 'borspirit' ); ?></h1>
            <p><?php echo esc_html__( 'We are working hard on launching our new website. Stay tuned!', 'borspirit' ); ?></p>
        </div>
    </body>
</html>