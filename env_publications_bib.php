<?
/* env_publications: publication overview as html from bibtex file
 include( 'bibtexbrowser.php' ); version vd4928b33fa2d82db7989e31871c75f917d0b2b8d -->
URL: http://www.monperrus.net/martin/bibtexbrowser/
Feedback & Bug Reports: martin.monperrus@gnieh.org

<!--this is version 1 -->
URL: http://www.umweltinformatik-marburg.de/mitarbeiterinnen-und-mitarbeiter/forteva-spaska/
Feedback & Bug Reports: spaska.forteva@uni-marburg.de
The programm use
(C) 2015 The University Marburg / Spaska Forteva
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License as
published by the Free Software Foundation; either version 2 of the
License, or (at your option) any later version.
*/

$_GET['library'] = 1;
define('BIBTEXBROWSER_BIBTEX_LINKS',false); // no [bibtex] link by default
require_once('lib/bibtexbrowser.php');
global $db;
$db = new BibDataBase();
$db->load('ei/ei_publications.bib');

$author = $_GET['author'];

# Publications-titles mapping depends from the language 
$mainTitles = array();
if(isset($_GET['l'])) {
	if ( $_GET['l'] == 1) {
		$mainTitles = array('article'=>'Articles', 
		'techreport'=>'Software/technical reports',
		'inbook'=>'Book chapters',
		'book'=>'Book chapters',
		'conference'=>'Conference papers/contributions',
		'unpublished'=>'Submitted articles'
	);
	} else {	
	 	$mainTitles = array('article'=>'Artikel', 
		'techreport'=>'Software/Handb&uuml;cher',
		'inbook'=>'Buchkapitel',
		'book'=>'B&uuml;cher',
		'conference'=>'Konferenzbeitr&auml;ge',
		'unpublished'=>'Eingereichte Artikel'
	);
 	}
 }
else {
		$mainTitles = array('article'=>'Artikel', 
		'techreport'=>'Software/Handb&uuml;cher',
		'inbook'=>'Buchkapitel',
		'book'=>'B&uuml;cher',
		'conference'=>'Konferenzbeitr&auml;ge',
		'unpublished'=>'Eingereichte Artikeln'
	);	
}


$query = array( 'author'=>$author);
$entries=$db->multisearch($query);


// send to Browser
# Article 
$article = getContent('article', $author, $entries, true);
if ($article != '') echo " var article = '<h5>". utf8_encode($mainTitles['article']) . "</h5>". $article ."'; ";
else echo " var article = '';";

# Book
$book = getContent('book', $author, $entries, false);
if ($book != '') echo " var books = '<h5>". utf8_encode($mainTitles['book']) . "</h5>". $book ."'; ";
else echo " var books = '';";

# Techreport
$techreport = getContent('techreport', $author, $entries, false).getContent('manual', $author, $entries, true);
if ($techreport != '') echo " var techreport = '<h5>". utf8_encode($mainTitles['techreport']) . "</h5>". $techreport ."'; ";
else echo " var techreport = '';";

# Conference
$conference = getContent('conference', $author, $entries, false).getContent('inproceedings', $author, $entries, false).getContent('incollection', $author, $entries, false);
if ($conference != '') echo " var conference = '<h5>". utf8_encode($mainTitles['conference']) . "</h5>". $conference ."'; ";
else echo " var conference = '';";

# Unpublished
$unpublished = getContent('unpublished', $author, $entries, true);
if ($unpublished != '') echo " var unpublished = '<h5>". utf8_encode($mainTitles['unpublished']) . "</h5>". $unpublished ."'; ";
else echo " var unpublished = '';";

# Inbook
$inbook= getContent('inbook', $author, $entries, false);
if ($inbook != '') echo " var inbook = '<h5>". utf8_encode($mainTitles['inbook']) . "</h5>". $inbook ."'; ";
else echo " var inbook = '';";

/**
 * Returns HTML coder
 *
 * The function returns HTML code
 *
 * @param type-type of publications, author-autor, entries-array of publications, imgLink-if the HTML code haves a image
 */
function getContent($type, $author, $entries, $imgLink=false){
	$html='';
        if(count($entries) ) {
        	uasort($entries,'compare_bib_entry_by_year');
              	// uasort($entries,'compare_bib_entry_by_name');
		$numberImg = 0;
		
		foreach ($entries as $bibentry) {
			if($bibentry->getType() == $type) {
			
				if($numberImg%2 == 0) { $classImg = 'Right'; $classContent = 'Left'; }
				else { $classImg = 'Left'; $classContent = 'Right';}

				$title = $bibentry->getTitle();
				$title = str_replace("\\textbf","", $title);
				$title = str_replace("\\textit","", $title);

				if( preg_match('/http/',$bibentry->getField("url"))  ) {
					$html .=  "<div id=\'" . $type . "\'><div class=\'pubContent" . $classContent . "\'> <p class=\'title\' ><a target=\'_blank\' href=\'" 
					. $bibentry->getField("url") . "\'> "
					. $title . ".</p></a>";
				} 
				else if( preg_match('/http/',$bibentry->getField("file"))  ) {

					$html .=  "<div id=\'" . $type . "\'><div class=\'pubContent" . $classContent . "\'> <p class=\'title\'><a target=\'_blank\' href=\'" 
					. $bibentry->getField("file") . "\'> "
					. $title . ".</p></a>";
				}
				else if($type== 'manual'){
					$html .=  "<div id=\'" . $type . "\'><div class=\'pubContent" . $classContent . "\'> <p class=\'title\'><a target=\'_blank\' href=\'https://github.com/environmentalinformatics-marburg\'> "
					. $title . ".</p></a>";
		
				}
				else {
					$html .=  "<div id=\'" . $type . "\'><div class=\'pubContent" . $classContent . "\'><p class=\'title\'>"
					. $title . ".</p>";
				}
				$authorName = authorToStr($bibentry->getAuthor(), $author) . " - " . $bibentry->getYear()  ; 
		
				$html .= "<p>" . $authorName . ".</p>";
				$html .= "<p>" . $bibentry->getField("link") . "</p>";
				if($type == 'unpublished') {
					$html .= " <p class=\'jornal\'>Submitted to " . $bibentry->getField("note") . "</p>";
			
				} 
				else {
					$html .= " <p class=\'jornal\'>" . $bibentry->getField("journal") . "</p>";
				}
		
				if($bibentry->getField("comment") != '') {
		
					$html .= " <p class=\"comment\">" . $bibentry->getField("comment") . "</p></div>";
				} 
				else {
					$html .= "</div>";
				}
				//URL pruefen
				//if (url_check("https://github.com/environmentalinformatics-marburg/cvs/blob/master/publications/graphics/".$bibentry->getKey().".png")) {
				if ($imgLink) {
				$html .= "<div class=\'pubImg" .$classImg . 
					"\'><img width=\"200\" height=\"140\" src=\"https://github.com/environmentalinformatics-marburg/cvs/blob/master/publications/graphics/"
					. $bibentry->getKey() .".png?raw=true\" /> </div></div>";
				}
				else {
					$html .= "</div>";
				} 

				  $numberImg += 1;
			}
		}
			
	}
	return $html;
	
}


/**
 * Returns true or false
 *
 * The function check if url
 *
 * @param url-link string
 */
function url_check($url) { 
        $hdrs = @get_headers($url); 
        return is_array($hdrs) ? preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/',$hdrs[0]) : false; 
    }

/**
 * Returns the authors as string
 *
 * The function returns the author name as string commas sparated
 *
 * @param authorstr, keyAuthor
 */
function authorToStr($authorstr, $keyAuthor) {

	$aarr = explode(' and ', $authorstr);
	$aarr = array_map('trim', $aarr);
	for($i=0; $i < count($aarr); $i++) {
			$aarr[$i]= str_replace("\\textbf","", $aarr[$i]);
		if(strpos($aarr[$i], ',') == false) {
			// no first/lastname indicator, let's do that ourselves
			
			$pa = strpos($aarr[$i], '{') != false && (strpos($aarr[$i],'{') == 0 || $aarr[$i][strpos($aarr[$i],'{')-1] == ' ' || $aarr[$i][strpos($aarr[$i],'{')-1] == '.') ? strpos($aarr[$i], '{') : false;
			$pl = ($pa == true) ? strpos($aarr[$i], '{') : strrpos($aarr[$i], ' ');
			
			$lastname = trim(substr($aarr[$i], $pl+1, ($pa && strpos($aarr[$i], '}', $pl) != false ? strpos($aarr[$i], '}', $pl) - $pl : strlen($aarr[$i])-$pl)-1) );
			
			$firstname = trim(substr($aarr[$i], 0, $pl));
			$fn = explode(' ', str_replace('.', '', str_replace('-', ' ', $firstname)));
			
			$fn2 = '';
			for($j=0; $j < count($fn); $j++) {
				if(strlen($fn[$j]) > 0)
					$fn2 .= $fn[$j][0] . '.';
			}
			
			$aarr[$i] = $lastname . ' ' . $fn2;
			if($lastname  == $keyAuthor){
				$aarr[$i] = '<b>'.$aarr[$i].'</b>';
			}
		} else {
			$na = explode(',', $aarr[$i]);
			
			
			$fn = explode(' ', str_replace('.', '', str_replace('-', ' ', trim($na[1]))));
			$fn2 = '';
			for($j=0; $j < count($fn); $j++) {
				if(strlen($fn[$j]) > 0)
					$fn2 .= $fn[$j][0] . '.';
			}
			$aarr[$i] = $na[0] . ' ' . $fn2;
			
			if($na[0] == $keyAuthor){
				$aarr[$i] = '<b>'.$aarr[$i].'</b>';
			}
		}
	}
	return implode(', ', $aarr);	
}
	

?>
