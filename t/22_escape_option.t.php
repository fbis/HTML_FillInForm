#!php
<?php

error_reporting( E_ALL );

require_once 'Test.class.php';
require_once 'HTML/FillInForm.class.php';

plan(4);


$html = '
<form action="">
<input type="text" name="one" value="">
</form>
';

$fdat = array(
    'one' => '<foo>'
);

$fif = new HTML_FillInForm;
$output = $fif->fill(array(
    'scalarref' => $html,
    'fdat'      => $fdat,
));

is(
    $output,
    '
<form action="">
<input type="text" name="one" value="&lt;foo&gt;">
</form>
'
);

# escape off

$fif = new HTML_FillInForm;
$output = $fif->fill(array(
    'scalarref' => $html,
    'fdat'      => $fdat,
    'escape'    => null,
));

is(
    $output,
    '
<form action="">
<input type="text" name="one" value="<foo>">
</form>
'
);

# escape orginal function

function escape ($value) {
    return "bar:[$value]";
}

$fif = new HTML_FillInForm;
$output = $fif->fill(array(
    'scalarref' => $html,
    'fdat'      => $fdat,
    'escape'    => 'escape',
));

is(
    $output,
    '
<form action="">
<input type="text" name="one" value="bar:[<foo>]">
</form>
'
);

# escape orginal object method

class Baz {
    function escape ($value) {
        return "baz:[$value]";
    }
}

$obj = new Baz;
$fif = new HTML_FillInForm;
$output = $fif->fill(array(
    'scalarref' => $html,
    'fdat'      => $fdat,
    'escape'    => array(&$obj,'escape'),
));

is(
    $output,
    '
<form action="">
<input type="text" name="one" value="baz:[<foo>]">
</form>
'
);


