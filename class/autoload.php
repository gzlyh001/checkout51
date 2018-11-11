<?php

/**
 * loadClassDependency
 * @param array $class
 * @return void
 */
function loadClassDependency($class) {
	global $CLASSPATHS;
	$classPaths = $CLASSPATHS;
	
	$classPath = '';
	if ( isset($classPaths[$class]) ) {
		$classPath = $classPaths[$class];
	} elseif ( ! ctype_upper($class[0]) ) {
		$class = ucfirst($class);
		if ( isset($classPaths[$class]) ) {
			$classPath = $classPaths[$class];
		}
	}
	if ( $classPath ) {
		$classPath = __DIR__.'/..'.$classPath;
		require_once($classPath);
	}
}

spl_autoload_register('loadClassDependency');
