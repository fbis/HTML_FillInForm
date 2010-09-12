#!php
<?php

error_reporting( E_ALL );


require_once 'Test.class.php';
require_once 'HTML/FillInForm.class.php';

plan(1);

$form_in = '
<form action="">
<INPUT TYPE="TEXT" NAME="foo1" value="nada">
<input type="hidden" name="foo2">
</form>
';

$fdat = array(
    'foo1' => array('bar1'),
    'foo2' => 'bar2'
);

$fif = new HTML_FillInForm;
$output = $fif->fill(array(
    'scalarref' => $form_in,
    'fdat'      => $fdat
));
$output = $fif->fill(array(
    'scalarref' => $output,
    'fdat'      => $fdat
));

is(
    $output,
    '
<form action="">
<input type="TEXT" name="foo1" value="bar1">
<input type="hidden" name="foo2" value="bar2">
</form>
'
);

