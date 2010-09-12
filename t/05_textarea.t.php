#!php
<?php

error_reporting( E_ALL );


require_once 'Test.class.php';
require_once 'HTML/FillInForm.class.php';

plan(2);

$form_in = '<form action=""><TEXTAREA NAME="foo">blah</TEXTAREA></form>';

$fdat = array(
    'foo' => 'bar>bar'
);

$fif = new HTML_FillInForm;
$output = $fif->fill(array(
    'scalarref' => $form_in,
    'fdat'      => $fdat
));

is(
    $output,
    '<form action=""><textarea name="foo">bar&gt;bar</textarea></form>'
);


$form_in = '<form action=""><TEXTAREA NAME="foo">blah</TEXTAREA></form>';

$fdat = array(
    'foo' => ''
);

$fif = new HTML_FillInForm;
$output = $fif->fill(array(
    'scalarref' => $form_in,
    'fdat'      => $fdat
));

is(
    $output,
    '<form action=""><textarea name="foo"></textarea></form>'
);
