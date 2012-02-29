<?php

class Result {
	private $site;
	private $terms;
	public $totalCount;

	function __construct($s) {
		$this->terms = array();
		$this->site = $s;
	}

	public function addTerm($t) {
		array_push($this->terms, $t);
		$this->totalCount+=$t->count;
	}

	public function getSite() {
		return $this->site;
	}

	public function getTerms() {
		return $this->terms;
	}

}