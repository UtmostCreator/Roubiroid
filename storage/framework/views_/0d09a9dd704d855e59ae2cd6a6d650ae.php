<?php

use \Framework\routing\Router;

?>
<form action="
<?php print $this->escape(Router::route('csrf-example')); ?>" method="post">
    <input type="text" name="csrf" value="
<?php print $this->escape(csrf()); ?>">
    <input type="submit" value="Submit">
</form>
