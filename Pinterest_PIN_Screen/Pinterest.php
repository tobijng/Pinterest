<?php
header('Content-Type: image/jpeg');
header('Content-Disposition: attachment; filename="Download.jpg"');

readfile('images/photo.jpg'); 
exit;
?>