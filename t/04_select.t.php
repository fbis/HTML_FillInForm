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
        return @$this->$key;
    }
}

plan(3);


# normal select option

$hidden_form_in = '
<form action="">
<select name="foo1">
	<option value="0">bar1</option>
	<option value="bar2">bar2</option>
	<option value="bar3">bar3</option>
</select>
<select name="foo2">
	<option value="bar1">bar1</option>
	<option value="bar2">bar2</option>
	<option value="bar3">bar3</option>
</select>
<select name="foo3">
	<option value="bar1">bar1</option>
	<option selected value="bar2">bar2</option>
	<option value="bar3">bar3</option>
</select>
<select name="foo4">
	<option value="bar1">bar1</option>
	<option selected value="bar2">bar2</option>
	<option value="bar3">bar3</option>
</select>
</form>
';

$cgi = new CGI ( array(
    'foo1' => '0',
    'foo2' => array('bar1', 'bar2'),
    'foo3' => ''
));

$fif = new HTML_FillInForm;
$output = $fif->fill(array(
    'scalarref' => $hidden_form_in,
    'fobject'   => $cgi
));

is(
    $output,
    '
<form action="">
<select name="foo1">
	<option value="0" selected="selected">bar1</option>
	<option value="bar2">bar2</option>
	<option value="bar3">bar3</option>
</select>
<select name="foo2">
	<option value="bar1" selected="selected">bar1</option>
	<option value="bar2">bar2</option>
	<option value="bar3">bar3</option>
</select>
<select name="foo3">
	<option value="bar1">bar1</option>
	<option value="bar2">bar2</option>
	<option value="bar3">bar3</option>
</select>
<select name="foo4">
	<option value="bar1">bar1</option>
	<option selected value="bar2">bar2</option>
	<option value="bar3">bar3</option>
</select>
</form>
'
);






# multiple select option

$hidden_form_in = '
<form action="">
<select multiple name="foo1">
	<option value="0">bar1</option>
	<option value="bar2">bar2</option>
	<option value="bar3">bar3</option>
</select>
<select multiple name="foo2">
	<option value="bar1">bar1</option>
	<option value="bar2">bar2</option>
	<option value="bar3">bar3</option>
</select>
<select multiple name="foo3">
	<option value="bar1">bar1</option>
	<option selected value="bar2">bar2</option>
	<option value="bar3">bar3</option>
</select>
<select multiple name="foo4">
	<option value="bar1">bar1</option>
	<option selected value="bar2">bar2</option>
	<option value="bar3">bar3</option>
</select>
</form>
';

$cgi = new CGI ( array(
    'foo1' => '0',
    'foo2' => array('bar1', 'bar2'),
    'foo3' => ''
));

$fif = new HTML_FillInForm;
$output = $fif->fill(array(
    'scalarref' => $hidden_form_in,
    'fobject'   => $cgi
));

is(
    $output,
    '
<form action="">
<select multiple name="foo1">
	<option value="0" selected="selected">bar1</option>
	<option value="bar2">bar2</option>
	<option value="bar3">bar3</option>
</select>
<select multiple name="foo2">
	<option value="bar1" selected="selected">bar1</option>
	<option value="bar2" selected="selected">bar2</option>
	<option value="bar3">bar3</option>
</select>
<select multiple name="foo3">
	<option value="bar1">bar1</option>
	<option value="bar2">bar2</option>
	<option value="bar3">bar3</option>
</select>
<select multiple name="foo4">
	<option value="bar1">bar1</option>
	<option selected value="bar2">bar2</option>
	<option value="bar3">bar3</option>
</select>
</form>
'
);



# empty select option

$hidden_form_in = '<form action=""><select name="x"><option></select></form>';

$cgi = new CGI;

$fif = new HTML_FillInForm;
$output = $fif->fill(array(
    'scalarref' => $hidden_form_in,
    'fobject'   => $cgi
));

is(
    $output,
    '<form action=""><select name="x"><option></select></form>'
);
