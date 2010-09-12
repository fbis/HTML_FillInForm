#!php
<?php

error_reporting( E_ALL );


require_once 'Test.class.php';
require_once 'HTML/FillInForm.class.php';

plan(5);

$form_in = '
<form action="">
<input type="text" name="one" value="not disturbed">
<input type="text" name="two" value="not disturbed">
<input type="text" name="three" value="not disturbed">
</form>
';

$fdat = array(
   'two'   => "new val 2",
   'three' => "new val 3",
);

$fif = new HTML_FillInForm;
$output = $fif->fill(array(
    'scalarref' => $form_in,
    'fdat'      => $fdat,
    'ignore_fields' => 'one'
));

is(
    $output,
    '
<form action="">
<input type="text" name="one" value="not disturbed">
<input type="text" name="two" value="new val 2">
<input type="text" name="three" value="new val 3">
</form>
'
);

# scalar version

$form_in = '
<form action="">
<input type="text" name="one" value="not disturbed">
<input type="text" name="two" value="not disturbed">
</form>
';

$fif = new HTML_FillInForm;
$output = $fif->fill(array(
    'scalar' => $form_in,
    'fdat'      => $fdat,
    'ignore_fields' => 'one'
));

is(
    $output,
    '
<form action="">
<input type="text" name="one" value="not disturbed">
<input type="text" name="two" value="new val 2">
</form>
'
);

# arrayref or array version

$form_in = '
<form action="">
<input type="text" name="one" value="not disturbed">
<input type="text" name="two" value="not disturbed">
</form>
';
$html_array = preg_split('/\n/',$form_in);

$fif = new HTML_FillInForm;
$output = $fif->fill(array(
    'arrayref' => $html_array,
    'fdat'      => $fdat,
    'ignore_fields' => 'one'
));

is(
    $output,
    '<form action=""><input type="text" name="one" value="not disturbed"><input type="text" name="two" value="new val 2"></form>'
);

$output = $fif->fill(array(
    'array' => $html_array,
    'fdat'      => $fdat,
    'ignore_fields' => 'one'
));

is(
    $output,
    '<form action=""><input type="text" name="one" value="not disturbed"><input type="text" name="two" value="new val 2"></form>'
);

# file version

$output = $fif->fill(array(
    'file' => 'data/form1.html',
    'fdat'      => $fdat,
    'ignore_fields' => 'one'
));

is(
    $output,
    '
<form action="">
<input type="text" name="one" value="not disturbed">
<input type="text" name="two" value="new val 2">
</form>
'
);
