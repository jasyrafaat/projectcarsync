<?php
if (class_exists("MongoDB\Driver\Manager")) {
    echo "MongoDB extension is loaded!";
} else {
    echo "MongoDB extension is NOT loaded!";
}
?>
