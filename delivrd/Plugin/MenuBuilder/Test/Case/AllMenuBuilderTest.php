<?php

class AllTestsTest extends PHPUnit_Framework_TestSuite {

/**
 * Suite method, defines tests for this suite.
 *
 * @return void
 */
	public static function suite() {
		$suite = new CakeTestSuite('All Tests');
		$suite->addTestDirectoryRecursive(App::pluginPath('MenuBuilder') . 'Test' . DS . 'Case' . DS);

		return $suite;
	}

}
