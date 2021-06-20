<?php

use app\core\Application;
use app\core\notification\Message;

//if (Session::get('loggedIn') && Notification::hasAny()) : ?>
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
<?php //endif; ?>
