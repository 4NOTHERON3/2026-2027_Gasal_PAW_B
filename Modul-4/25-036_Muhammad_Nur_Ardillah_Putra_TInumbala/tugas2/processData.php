<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Server-side Validation</title>
</head>
<body>
<?php

require 'validate.inc';
$errors = array();
validateName($errors, $_POST, 'surname');
if ($errors)
{
    echo 'Errors:<br/>';
    foreach ($errors as $field => $error)
        echo "$field $error</br>";
}
else
    echo 'Data OK!';
?>
</body>
</html>
