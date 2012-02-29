<?php

require_once("./urlSearcher.php");

echo "Enter a space separated list of search terms: ";
$line = trim(fgets(STDIN));

$searcher = new UrlSearcher(explode(" ",$line), 3);
$searcher->search("http://techcrunch.com", 0);
$searcher->sortLinks();
$searcher->showTop10();

echo "DONE";



?>