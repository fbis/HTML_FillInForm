#!php
<?php

error_reporting( E_ALL );


require_once 'Test.class.php';
require_once 'HTML/FillInForm.class.php';

plan(2);

$form_in = '
<FORM name="foo1">
<INPUT TYPE="TEXT" NAME="foo1" value="nada">
</FORM>
<FORM name="foo2">
<INPUT TYPE="TEXT" NAME="foo2" value="nada">
</FORM>
<FORM >
<INPUT TYPE="TEXT" NAME="foo3" value="nada">
</FORM>
<FORM id="foo4">
<INPUT TYPE="TEXT" NAME="foo4" value="nada">
</FORM>
';

$fdat = array(
  'foo1' => 'bar1',
  'foo2' => 'bar2',
  'foo3' => 'bar3',
  'foo4' => 'bar4',
);

$fif = new HTML_FillInForm;
$output = $fif->fill(array(
    'scalarref' => $form_in,
    'fdat'      => $fdat,
    'target'    => 'foo2',
));

is(
    $output,
    '
<FORM name="foo1">
<INPUT TYPE="TEXT" NAME="foo1" value="nada">
</FORM>
<FORM name="foo2">
<input type="TEXT" name="foo2" value="bar2">
</FORM>
<FORM >
<INPUT TYPE="TEXT" NAME="foo3" value="nada">
</FORM>
<FORM id="foo4">
<INPUT TYPE="TEXT" NAME="foo4" value="nada">
</FORM>
'
);

$output = $fif->fill(array(
    'scalarref' => $form_in,
    'fdat'      => $fdat,
    'target'    => 'foo4',
));

is(
    $output,
    '
<FORM name="foo1">
<INPUT TYPE="TEXT" NAME="foo1" value="nada">
</FORM>
<FORM name="foo2">
<INPUT TYPE="TEXT" NAME="foo2" value="nada">
</FORM>
<FORM >
<INPUT TYPE="TEXT" NAME="foo3" value="nada">
</FORM>
<FORM id="foo4">
<input type="TEXT" name="foo4" value="bar4">
</FORM>
'
);
