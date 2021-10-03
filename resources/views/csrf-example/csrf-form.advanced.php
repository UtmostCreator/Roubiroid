<?php

use \Framework\routing\Router;

?>
<form action="{{Router::route('csrf-example')}}" method="post">
    <input type="text" name="csrf" value="{{csrf()}}">
    <input type="submit" value="Submit">
</form>