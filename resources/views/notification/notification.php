<?php

use Framework\Application;
use Framework\notification\Message;

// TODO add UserModel Session::get('loggedIn')
if (app()->session->hasAnyFlash()) : ?>
    <div class="notification-area">

        <div class="notification-area--user">
            <?php Application::$app->session->getAllFlashes(['visibility' => Message::USER_VISIBLE], true); ?>
        </div>

<!--        --><?php //if (Session::get('role') === 'Admin') : ?>
            <div class="notification-area--admin">
                <?php Application::$app->session->getAllFlashes(['visibility' => Message::ADMIN_VISIBLE], false); ?>
            </div>
<!--        --><?php //endif; ?>

    </div>
<?php endif; ?>
