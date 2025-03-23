<?php
echo shell_exec('composer install 2>&1');
echo shell_exec('composer require mongodb/mongodb 2>&1');
?>
