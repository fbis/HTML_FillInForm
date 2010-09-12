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

plan(1);

$hidden_form_in = '
<form action="">
<INPUT TYPE="TEXT" NAME="foo1" value="nada">
<input type="hidden" name="foo2">
</form>
';

$q1 = new CGI ( array( 'foo1' => 'bar1' ) );
$q2 = new CGI ( array( 'foo2' => 'bar2' ) );

$fif = new HTML_FillInForm;
$output = $fif->fill(array(
    'scalarref' => $hidden_form_in,
    'fobject'   => array( $q1,$q2 ),
));

is(
    $output,
    '
<form action="">
<input type="TEXT" name="foo1" value="bar1">
<input type="hidden" name="foo2" value="bar2">
</form>
'
);
