<!--C:\OpenServer\domains\php-c-framework\resources\views\migrations.advanced.php-->

<?php $this->extends('layouts/auth2'); ?>
<h1>List of migrations</h1>
<ol>

<?php foreach ($migrations as $m) : ?>
    <li>Migration Name: <span class="badge bg-secondary"><?php print $this->escape( $m->name ); ?></span></li>
<?php endforeach; ?>
</ol>
