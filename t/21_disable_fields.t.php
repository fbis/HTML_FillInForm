#!php
<?php

error_reporting( E_ALL );


require_once 'Test.class.php';
require_once 'HTML/FillInForm.class.php';

plan(4);

$form_in = '
<form action="">
<input type="text" name="one" value="not disturbed">
<input type="text" name="two" value="not disturbed">
</form>
';

$fdat = array(
   'two'   => "new val 2",
);

$fif = new HTML_FillInForm;
$output = $fif->fill(array(
    'scalarref' => $form_in,
    'fdat'      => $fdat,
    'disable_fields' => array('two'),
));

is(
    $output,
    '
<form action="">
<input type="text" name="one" value="not disturbed">
<input type="text" name="two" value="new val 2" disable="1">
</form>
'
);

# option once

$fif = new HTML_FillInForm;
$output = $fif->fill(array(
    'scalarref' => $form_in,
    'fdat'      => $fdat,
    'disable_fields' => 'two',
));

is(
    $output,
    '
<form action="">
<input type="text" name="one" value="not disturbed">
<input type="text" name="two" value="new val 2" disable="1">
</form>
'
);

# disable exists 0

$form_in = '
<form action="">
<input type="text" name="one" value="not disturbed">
<input type="text" name="two" value="not disturbed" disable="0">
</form>
';

$fif = new HTML_FillInForm;
$output = $fif->fill(array(
    'scalarref' => $form_in,
    'fdat'      => $fdat,
    'disable_fields' => 'two',
));

is(
    $output,
    '
<form action="">
<input type="text" name="one" value="not disturbed">
<input type="text" name="two" value="new val 2" disable="1">
</form>
'
);

# disable exists not 0

$form_in = '
<form action="">
<input type="text" name="one" value="not disturbed">
<input type="text" name="two" value="not disturbed" disable="disable">
</form>
';

$fif = new HTML_FillInForm;
$output = $fif->fill(array(
    'scalarref' => $form_in,
    'fdat'      => $fdat,
    'disable_fields' => 'two',
));

is(
    $output,
    '
<form action="">
<input type="text" name="one" value="not disturbed">
<input type="text" name="two" value="new val 2" disable="disable">
</form>
'
);

