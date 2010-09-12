#!php
<?php

error_reporting( E_ALL );


require_once 'Test.class.php';
require_once 'HTML/FillInForm.class.php';

plan(1);

$form_in = '
<form action="">
<INPUT NAME="foo1" value="nada">
<input type="hidden" name="foo2">
</form>
';

$fdat = array(
    'foo1' => 'bar1',
    'foo2' => 'bar2'
);

$fif = new HTML_FillInForm;
$output = $fif->fill(array(
    'scalarref' => $form_in,
    'fdat'      => $fdat
));

is(
    $output,
    '
<form action="">
<input name="foo1" value="bar1">
<input type="hidden" name="foo2" value="bar2">
</form>
'
);

