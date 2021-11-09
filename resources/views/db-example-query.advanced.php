<?php

use models\Product;

/** @var $products Product[] */
?>
<ul>
    @foreach($products as $product)
        <li>
            {{ $product->id . ' ' . $product->name }}
            <a href="{{ $product->route }}">{{ $product->name }}</a>
        </li>
    @endforeach
</ul>

<pre>
<?php
    //var_dump($users);
    // would be for an advanced variable string
?>
</pre>
