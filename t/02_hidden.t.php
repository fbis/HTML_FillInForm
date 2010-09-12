#!php
<?php

error_reporting( E_ALL );


require_once 'Test.class.php';
require_once 'HTML/FillInForm.class.php';

plan(1);

$hidden_form_in = '<form action=""><input type="hidden" name="foo1"/><input type="hidden" name="foo2"/></form>';

$fdat = array(
    'foo1a' => 'bar1',
    'foo2'  => array('bar2','bar2'),
);

$fif = new HTML_FillInForm;
$output = $fif->fill(array(
    'scalarref' => $hidden_form_in,
    'fdat'      => $fdat
));

is(
    $output,
    '<form action=""><input type="hidden" name="foo1"/><input type="hidden" name="foo2" value="bar2" /></form>'
);
