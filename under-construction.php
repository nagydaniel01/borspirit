<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo esc_html__( 'Hamarosan', TEXT_DOMAIN ); ?></title>
        <link rel="icon" href="<?php echo esc_url( TEMPLATE_DIR_URI . '/assets/src/images/mmki_logo_short.svg' ); ?>" type="image/svg+xml">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;700&display=swap" rel="stylesheet">
        <style>
            body {
                margin: 0;
                padding: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: #F2E8DA;
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
                background: rgba(255,255,255,.65);
            }
            .container {
                max-width: 600px;
                padding: 20px;
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
                width: 150px;
                height: auto;
                padding: 0;
                margin: 0 auto;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="logo">
                <img src="<?php echo esc_url( TEMPLATE_DIR_URI . '/assets/src/images/lindamestyle-logo.png' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
            </div>
            <h1><?php echo esc_html__( 'Hamarosan', TEXT_DOMAIN ); ?></h1>
            <p><?php echo esc_html__( 'Keményen dolgozunk az új weboldalunk elindításán. Maradjanak velünk!', TEXT_DOMAIN ); ?></p>
        </div>
    </body>
</html>