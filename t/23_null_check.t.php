#!php
<?php

error_reporting( E_ALL );

require_once 'Test.class.php';
require_once 'HTML/FillInForm.class.php';

plan(2);

# fdat null

$hidden_form_in = '<form action=""><INPUT TYPE="TEXT" NAME="foo1" value="nada"></form>';

$fdat = array(
    'foo1' => null,
);

$fif = new HTML_FillInForm;
$output = $fif->fill(array(
    'scalarref' => $hidden_form_in,
    'fdat'      => $fdat
));

is(
    $output,
    '<form action=""><INPUT TYPE="TEXT" NAME="foo1" value="nada"></form>'
);


# fobject null

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

$hidden_form_in = '<form action=""><INPUT TYPE="TEXT" NAME="foo1" value="nada"></form>';

$cgi = new CGI ( array(
    'foo1' => null,
));

$fif = new HTML_FillInForm;
$output = $fif->fill(array(
    'scalarref' => $hidden_form_in,
    'fobject'   => $cgi
));

is(
    $output,
    '<form action=""><INPUT TYPE="TEXT" NAME="foo1" value="nada"></form>'
);
