#!php
<?php

error_reporting( E_ALL );


require_once 'Test.class.php';
require_once 'HTML/FillInForm.class.php';


$contents = array(
    '404',
    '404 Not Found',
    'Hello World',
    '<html><body>Hello World</body></html>'
);

plan( count($contents) );

foreach ( $contents as $html ) {
    $fif = new HTML_FillInForm;
    $output = $fif->fill(array(
        'scalarref' => $html,
    ));

    is(
        $output,
        $html
    );
}
