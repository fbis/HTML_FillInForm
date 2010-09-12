#!php
<?php

error_reporting( E_ALL );


require_once 'Test.class.php';
require_once 'HTML/FillInForm.class.php';

plan(1);

$form_in = '
<form action="">
<INPUT TYPE="radio" NAME="foo1" value="bar1">
<input type="radio" name="foo1" value="bar2">
<input type="radio" name="foo1" value="bar3">
<input type="radio" name="foo1" checked value="bar4">
</form>
';

$fdat = array(
    'foo1' => 'bar2'
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
<input type="radio" name="foo1" value="bar1">
<input type="radio" name="foo1" value="bar2" checked="checked">
<input type="radio" name="foo1" value="bar3">
<input type="radio" name="foo1" value="bar4">
</form>
'
);

