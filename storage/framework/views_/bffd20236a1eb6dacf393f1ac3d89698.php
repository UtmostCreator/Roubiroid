<!--C:\OpenServer\domains\Roubiroid\resources\views\home.advanced.php-->
<?php $this->extends('layouts/auth'); ?>
<?php $this->include('includes/large-text'); ?>;
<?php foreach ($products as $i => $product) : ?>
<div class="
<?php if ($i % 2 === 0) : ?>
            bg-grey
        <?php endif; ?>
            ">
    <div class="container">
        <h2>ID: <?php print $this->escape( $product->id ); ?>; <?php print $this->escape( $product->name ); ?></h2>
        <p><?php print $this->escape( $product->description ); ?></p>
        <a href="<?php print $this->escape( $product->route ); ?>">Order</a>
    </div>
</div>
<?php endforeach; ?>