#!php
<?php

error_reporting( E_ALL );


require_once 'Test.class.php';
require_once 'HTML/FillInForm.class.php';

class CGI {
    function CGI ($data=array()) {
        foreach ( $data as $key => $val ) {
            $this->$key = $val;
        }
    }
    function param ($key) {
        return $this->$key;
    }
}

plan(3);

$form_in = '
<form action="">
<input type="password" name="foo">
</form>
';

$cgi = new CGI ( array(
    'foo' => 'bar',
));

# fill_password option 0

$fif = new HTML_FillInForm;
$output = $fif->fill(array(
    'scalarref' => $form_in,
    'fobject'   => $cgi,
    'fill_password' => 0,
));

is(
    $output,
    '
<form action="">
<input type="password" name="foo">
</form>
'
);

# fill_password option 1

$output = $fif->fill(array(
    'scalarref' => $form_in,
    'fobject'   => $cgi,
    'fill_password' => 1,
));

is(
    $output,
    '
<form action="">
<input type="password" name="foo" value="bar">
</form>
'
);

# default

$output = $fif->fill(array(
    'scalarref' => $form_in,
    'fobject'   => $cgi,
));

is(
    $output,
    '
<form action="">
<input type="password" name="foo" value="bar">
</form>
'
);

