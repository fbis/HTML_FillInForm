#!php
<?php

error_reporting( E_ALL );


require_once 'Test.class.php';
require_once 'HTML/FillInForm.class.php';

plan(1);

$hidden_form_in = '
<form action="">
<input type="checkbox" name="foo1" value="bar1">
<input type="checkbox" name="foo1" value="bar2">
<input type="checkbox" name="foo1" value="bar3">
<input type="checkbox" name="foo2" value="bar1">
<input type="checkbox" name="foo2" value="bar2">
<input type="checkbox" name="foo2" value="bar3">
<input type="checkbox" name="foo3" value="bar1">
<input type="checkbox" name="foo3" checked value="bar2">
<input type="checkbox" name="foo3" value="bar3">
<input type="checkbox" name="foo4" value="bar1">
<input type="checkbox" name="foo4" checked value="bar2">
<input type="checkbox" name="foo4" value="bar3">
<input type="checkbox" name="foo5">
<input type="checkbox" name="foo6">
<input type="checkbox" name="foo7" checked>
<input type="checkbox" name="foo8" checked>
</form>
';

$fdat = array(
    'foo1' => 'bar1',
    'foo2' => array('bar1', 'bar2'),
    'foo3' => '',
    'foo5' => 'on',
    'foo6' => '',
    'foo7' => 'on',
    'foo8' => ''
);

$fif = new HTML_FillInForm;
$output = $fif->fill(array(
    'scalarref' => $hidden_form_in,
    'fdat'      => $fdat
));

is(
    $output,
    '
<form action="">
<input type="checkbox" name="foo1" value="bar1" checked="checked">
<input type="checkbox" name="foo1" value="bar2">
<input type="checkbox" name="foo1" value="bar3">
<input type="checkbox" name="foo2" value="bar1" checked="checked">
<input type="checkbox" name="foo2" value="bar2" checked="checked">
<input type="checkbox" name="foo2" value="bar3">
<input type="checkbox" name="foo3" value="bar1">
<input type="checkbox" name="foo3" value="bar2">
<input type="checkbox" name="foo3" value="bar3">
<input type="checkbox" name="foo4" value="bar1">
<input type="checkbox" name="foo4" checked value="bar2">
<input type="checkbox" name="foo4" value="bar3">
<input type="checkbox" name="foo5" value="on" checked="checked">
<input type="checkbox" name="foo6" value="on">
<input type="checkbox" name="foo7" value="on" checked="checked">
<input type="checkbox" name="foo8" value="on">
</form>
'
);
