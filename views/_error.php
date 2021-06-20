<?php

/** @var $exception \Exception */

$displayedMessage = $exception->getCode() . ' — ' . $exception->getMessage();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title><?= $displayedMessage ?></title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@800&display=swap" rel="stylesheet">

    <style>
        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: inherit;
        }

        html {
        }

        html,
        body {
            font-size: 62.5%;
            height: 100%;
        }

        body {
            display: flex;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #f5f5f5;
            box-sizing: border-box;
            font-size: 1.6rem;
        }

        a {
            font-size: 2rem !important;
        }

        .error {
            font-family: 'Montserrat';
        }

        .error__code {
            font-size: 20rem;
            border-bottom: 2rem solid #a73beb;
            width: min-content;
            margin: 0 auto;
            /*text-decoration: underline;*/
        }

        .error__description {
            font-size: 2rem;
        }

        .block {
            display: flex;
            justify-content: center;
            gap: 2rem;
            align-items: center;
        }

        .error__image {
            max-width: 40rem;
            width: 100%;
            height: auto;
        }

        @media (max-width: 768px) {
            .error__image {
                display: none;
            }

            .error__code {
                font-size: 15rem;
            }
        }
    </style>
</head>
<body class="text-center">

<main class="error error-<?= $exception->getCode() ?>">
    <div class="block">
        <div>

            <h1 class="h3 mb-3 fw-normal error__code"><?= $exception->getCode() ?></h1>
            <p class="error__description"><?= $exception->getMessage() ?></p>

        </div>
        <?php if ($exception->getCode() === 403) : ?>
        <img src="https://upload.wikimedia.org/wikipedia/commons/8/82/Ei-lock.svg" alt="error image"
            class="error__image">
        <?php endif; ?>

    </div>
    <a href="/" type="button" class="btn btn-outline-secondary">Go Home</a>
</main>


</body>
</html>
