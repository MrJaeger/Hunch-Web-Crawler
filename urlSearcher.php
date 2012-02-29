<?php

require_once("./result.php");
require_once("./term.php");

class UrlSearcher {

	private $deepest;
	private $goodLinks;
	private $terms;

	function __construct($searchTerms, $depth = 10) {
		$this->terms = $searchTerms;
		$this->deepest = $depth;
		$this->goodLinks = array();
	}

	public function search($site, $depth) {
		if($depth==$this->deepest) {
			return;
		}
		echo "CURRENT DEPTH ".$depth." SITE ".$site."\n";

		$html = file_get_contents($site);
		$pageResult = new Result($site);
		$dom = new DOMDocument();
		@$dom->loadHTML($html);
		$goodFlag = 0;

		foreach($this->terms as $term) {
			$termCount = $this->countWord($html, $term);
			$goodFlag+=$termCount;
			if($termCount>0) {
				$term = new Term($term, $termCount);
				$pageResult->addTerm($term);
			}
		}

		if($goodFlag!=0) {
			array_push($this->goodLinks, $pageResult);
		}
		
		$otherUrls = $this->uniqueURLS($this->getOtherUrls($dom));
		$count = 0;
		foreach($otherUrls as $url) {
		       if(preg_match('/techcrunch.com\/2012/',$url)  && !strpos($url, '#comments')) {
		      		$this->search($url, $depth+1);
		      		$count++;
		       }
		       if($count > (20/($depth+1))) {
		       		break;
		       }
		}
	}

	private function countWord($page, $word) {
		$bodyPos = strpos($page, '<body>');
		$afterBody = substr($page, $bodyPos);
		return count(preg_split("/".$word."/", $afterBody))-1;
	}

	private function getOtherUrls($dom) {
		$xpath = new DOMXPath($dom);
		$hrefs = $xpath->evaluate("/html/body//a");
		return $hrefs;
	}

	private function uniqueURLS($urls) {
		$u = array();

		for ($i = 0; $i < $urls->length; $i++) {
		       $href = $urls->item($i);
		       $url = $href->getAttribute('href');
		       $u[] = $url;
		}

		return array_unique($u);
	}

	public function sortLinks() {
		$gL = $this->goodLinks;
		$length = count($gL)-1;
		$flag = 1;
		while($flag!=0) {
			$flag = 0;
			for($i=0; $i<$length; $i++) {
				if($gL[$i]->totalCount < $gL[$i+1]->totalCount) {
					$temp = $gL[$i];
					$gL[$i] = $gL[$i+1];
					$gL[$i+1] = $temp;
					$flag = 1;
				}
			}
		}
		$this->goodLinks = $gL;
	}

	public function showTop10() {
		for($i = 0; $i<10; $i++) {
			echo $this->goodLinks[$i]->getSite()."\n";
			foreach($this->goodLinks[$i]->getTerms() as $term) {
				echo "TERM: ".$term->name." COUNT: ".$term->count."\n";
			}
			echo "\n";
		}
	}

}

?>