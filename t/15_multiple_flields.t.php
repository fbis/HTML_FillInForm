#!php
<?php

error_reporting( E_ALL );


require_once 'Test.class.php';
require_once 'HTML/FillInForm.class.php';

plan(1);

$form_in = '
<FORM action="">
<INPUT TYPE="hidden" NAME="foo" value="ack">
<input type="hidden" name="foo" >
</FORM>
';

$fdat = array(
  'foo' => 'bar',
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
<input type="hidden" name="foo" value="bar">
<input type="hidden" name="foo" value="bar">
</FORM>
'
);
