<?php

namespace core;

class View
{
	public static function render($view, $args = [], $donotflush = false)
	{
		extract($args, EXTR_SKIP);
		
		$file = "../application/views/$view";

        if ($donotflush) {
            ob_start();
        }
		
		if (is_readable($file)) {
			require $file;
		}
		else {
			echo "$file not found";
		}

        if ($donotflush) {
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }
	}
}
