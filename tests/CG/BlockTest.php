<?php

class CG_BlockTest extends PHPUnit_Framework_TestCase {

	public function testOutdent() {
		$block = new CG_File();
		$cases = array(
			"\tfoo" => "foo",
			"foo" => "foo",
			"\tfoo\nbar" => "foo\nbar",
			"\t\t\tfoo" => "\t\tfoo",
		);
		foreach ($cases as $input => $expected) {
			$output = TestHelper::invokeMethod($block, '_outdent', array($input));
			$this->assertSame($expected, $output);
		}
	}

	public function testOutdentUntilSafe() {
		$block = new CG_File();
		$cases = array(
			"\tfoo\nbar" => "\tfoo\nbar",
			"\t\tfoo\n\tbar" => "\tfoo\nbar",
			"\t\t\tfoo" => "foo",
		);
		foreach ($cases as $input => $expected) {
			$output = TestHelper::invokeMethod($block, '_outdent', array($input, true));
			$this->assertSame($expected, $output);
		}
	}

	public function testIndent() {
		$block = new CG_File();
		$cases = array(
			"foo\nbar" => "\tfoo\n\tbar",
			"\tfoo\n\tbar" => "\t\tfoo\n\t\tbar",
		);
		foreach ($cases as $input => $expected) {
			$output = TestHelper::invokeMethod($block, '_indent', array($input, true));
			$this->assertSame($expected, $output);
		}
	}

	public function testSetIndentation() {
		CG_Block::setIndentation('  ');
		$block = new CG_File();

		$output = TestHelper::invokeMethod($block, '_indent', array("foo", true));
		$this->assertSame("  foo", $output);
		$output = TestHelper::invokeMethod($block, '_outdent', array("  foo\n    bar", true));
		$this->assertSame("foo\n  bar", $output);

		CG_Block::setIndentation('	');
	}
}
