<?php

    header("Content-type: text/css");
    // Other headers should be handled with Nginx / Apache.

    if (! empty($_GET['css'])) {

        // No URLS and no paths like '/usr/local/www/pydio/plugins/../../../...'
        $path = realpath('./' . $_GET['css']);

        if ($path !== false
                && preg_match('#^/usr/local/www/pydio/plugins/[a-z0-9_/\.]*\.css$#', $path) === 1) {

            $buffer = file_get_contents($path);

            if (! empty($buffer)) {

                $buffer = preg_replace('#/\*[^*]*\*+([^/][^*]*\*+)*/#', '', $buffer);
                $buffer = str_replace(': ', ':', $buffer);
                $buffer = str_replace(array("\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);

                echo $buffer;

                die();
            }
        }
    }

    echo "/* Please specify a css file. For example: minify.php?css=style.css */";
?>
