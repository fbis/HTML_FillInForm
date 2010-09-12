#!php
<?php

error_reporting( E_ALL );


require_once 'Test.class.php';
require_once 'HTML/FillInForm.class.php';

plan(1);

$form_in = '
<FORM action="">
<INPUT TYPE="TEXT" NAME="foo1" value="cat1">
<input type="text" name="foo1" value="cat2"/>
</FORM>
';

$fdat = array(
  'foo1' => array('bar1','bar2'),
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
<input type="TEXT" name="foo1" value="bar1">
<input type="text" name="foo1" value="bar2" />
</FORM>
'
);
