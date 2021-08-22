<?php
    function loadAllInDir($path) {
        $files = scandir( $path);
        unset( $files['0']); unset( $files['1']);
        
        foreach( $files as $file) {
            $new_path = $path .'/'. $file;

            if( is_dir( $new_path)) {
                loadAllInDir( $new_path);
            } else {
                require_once $new_path;
            }
        }
    }

    $files = scandir(__DIR__);
    unset( $files['0']); unset( $files['1']);

    foreach( $files as $file) {
        $path = __DIR__ .'/'. $file;

        if( is_dir( $path)) {
            loadAllInDir( $path);
        } else {
            require_once $path;
        }
    }
