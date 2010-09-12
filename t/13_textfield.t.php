#!php
<?php

error_reporting( E_ALL );


require_once 'Test.class.php';
require_once 'HTML/FillInForm.class.php';

plan(1);

$form_in = '
<FORM action="">
<INPUT TYPE="textfield" NAME="foo1" value="cat1">
</FORM>
';

$fdat = array(
  'foo1' => 'bar1',
);

$fif = new HTML_FillInForm;
$output = $fif->fill(array(
    'scalarref' => $form_in,
    'fdat'      => $fdat,
));

is(
    $output,
    '
<FORM action="">
<input type="textfield" name="foo1" value="bar1">
</FORM>
'
);
