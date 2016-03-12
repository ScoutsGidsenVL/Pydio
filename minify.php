<?php
	 // Get the file path and get the extension from it.
    $fileParts = pathinfo($_GET['css']);

   	if (! empty($_GET['css'])) {
      if ($fileParts['extension'] == 'css') {
        $buffer = '';
        $buffer .= file_get_contents($_GET['css']);

        $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
        $buffer = str_replace(': ', ':', $buffer);
        $buffer = str_replace(array("\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);

        // We do not do this. Because it should be handled with Nginx / Apache.
        // ob_start("ob_gzhandler");

        header('Cache-Control: public');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');
        header("Content-type: text/css");

        // var_dump($buffer);
        echo $buffer;
      } else {
        header("Content-type: text/css");
        echo "/* Er moet een css bestand worden aangegeven. */";
      }
    } else {
      header('Content-Type: text/css'); 
      echo "/* U moet een bestand ingeven aan minify.php. bv: minify.php?css=style.css */";
    }
?>
