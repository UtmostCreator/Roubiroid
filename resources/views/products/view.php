<?php

/* @var string $product */
/* @var string $scary */

$this->extends('layouts/products');
?>

<h1>Product</h1>
<?php
echo $product;
echo $this->escape($scary);
