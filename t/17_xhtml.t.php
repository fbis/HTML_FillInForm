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

plan(1);

$form_in = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html>
<body>
    <form action="">
    <input type="radio" name="status" value=0 />Canceled<br>
    <input type="radio" name="status" value=1 />Confirmed<br>
    <input type="radio" name="status" value=2 />Wait List<br>

    <input type="radio" name="status" value=3 />No Show<br>
    <input type="radio" name="status" value=4 />Moved to Another Class<br>
    <input type="radio" name="status" value=5 />Late Cancel<br>
    </form>
</body>
</html>
';

$cgi = new CGI ( array(
    'status' => '1',
));

$fif = new HTML_FillInForm;
$output = $fif->fill(array(
    'scalarref' => $form_in,
    'fobject'   => $cgi,
));

is(
    $output,
    '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html>
<body>
    <form action="">
    <input type="radio" name="status" value="0" />Canceled<br>
    <input type="radio" name="status" value="1" checked="checked" />Confirmed<br>
    <input type="radio" name="status" value="2" />Wait List<br>

    <input type="radio" name="status" value="3" />No Show<br>
    <input type="radio" name="status" value="4" />Moved to Another Class<br>
    <input type="radio" name="status" value="5" />Late Cancel<br>
    </form>
</body>
</html>
'
);

