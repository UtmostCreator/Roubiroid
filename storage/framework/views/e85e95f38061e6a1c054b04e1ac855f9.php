<?php
//\Modules\DD::dd($this);
/* @var numeric $test */
?>
<?php $this->include('products/product-detail', ['name' => 'test']); ?>
<h1>All Products</h1>
<a href="#">Show All Products</a>


<?php if ($test == 321) : ?>
    <a href="#">IF true statement</a>
<?php else : ?>
<a href="#">ELSE statement</a>
<?php endif; ?>
<textarea name="" id="" cols="30" rows="10">
    
<?php print $this->escape($scary); ?>
</textarea>
<p>
<?php print $scary; ?></p>



