<?php

class Term {
	public $name;
	public $count;

	function __construct($n, $c) {
		$this->name = $n;
		$this->count = $c;
	}
}