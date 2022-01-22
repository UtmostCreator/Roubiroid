<?php

/* @var string $product */
/* @var string $scary */

$this->extends('layouts/products');
?>

<h1>Product</h1>
<p><?= $product->name;?></p>
<?php

echo $product->description;
echo $this->escape('TESTS');
