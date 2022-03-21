<!--C:\OpenServer\domains\Roubiroid\resources\views\migrations.advanced.php-->

<?php $this->extends('layouts/auth2'); ?>
<h1>Список виконаних міграцій</h1>
<ol>

<?php foreach ($migrations as $m) : ?>
    <li>Назва міграції: <span class="badge bg-secondary"><?php print $this->escape( $m->name ); ?></span></li>
<?php endforeach; ?>
</ol>
