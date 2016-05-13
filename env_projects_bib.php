<?
/* env_projects_bib: projects overview as html from bibtex file
 include( 'bibtexbrowser.php' ); version vd4928b33fa2d82db7989e31871c75f917d0b2b8d -->
URL: http://www.monperrus.net/martin/bibtexbrowser/
Feedback & Bug Reports: martin.monperrus@gnieh.org

<!--this is version 11 -->
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
$db->load('ei/ei_projects.bib');

# Publications-titles mapping depends from the language 
$mainTitles = array();
if(isset($_GET['l'])) {
	if ( $_GET['l'] == 1) {
	 	$mainTitles = array('app' => array('Applicants', 'Applicant'),
		'pp' => 'Participating partner',
		'fn' => 'Funding',
		'ps' => array('Project staffs', 'Project staff'),
		'web'=> 'Web',
		'year'=>'Funding'
	);
	} else {	
	 	$mainTitles = array('app' => array('Applicants', 'Applicant'),
		'pp' => 'Participating partner',
		'fn' => 'Funding',
		'ps' => array('Project staffs', 'Project staff'),
		'web'=> 'Web',
		'year'=>'Funding'
	);
 	}
 }
else {
	 	$mainTitles = array('app' => array('Applicants', 'Applicant'),
		'pp' => 'Participating partner',
		'fn' => 'Funding',
		'ps' => array('Project staffs', 'Project staff'),
		'web'=> 'Web',
		'year'=>'Funding'
	);	
}

// send to Browser InProceedings
$query = array('type'=>'InProceedings');
$entries=$db->multisearch($query);
$InProceedings = getContent('inproceedings',  $entries, true, $mainTitles);
if ($InProceedings!= '') echo " var inproceedings = '". $InProceedings . "'; ";
else echo " var inproceedings = '';";

// send to Browser InBook
$query = array('type'=>'InBook');
$entries=$db->multisearch($query);
$InProceedings = getContent('inbook',  $entries, true, $mainTitles);
if ($InProceedings!= '') echo " var inbook = '". $InProceedings . "'; ";
else echo " var inbook = '';";

// send to Browser Article
$query = array('type'=>'Article');
$entries=$db->multisearch($query);
$InProceedings = getContent('article',  $entries, true, $mainTitles);
if ($InProceedings!= '') echo " var article = '". $InProceedings . "'; ";
else echo " var article = '';";


/**
 * Returns HTML coder
 *
 * The function returns HTML code
 *
 * @param type-type of publications, author-autor, entries-array of publications, imgLink-if the HTML code haves a image
 */
function getContent($type, $entries, $imgLink=false, $mainTitles){
	$html='';
        if(count($entries)) {
        	uasort($entries,'compare_bib_entry_by_year');
              	// uasort($entries,'compare_bib_entry_by_name');
		$numberImg = 0;
		
		foreach ($entries as $bibentry) {
			if($bibentry->getType() == $type) {
			
				// Make the float of div - left or right
				if($numberImg%2 == 0) { $classImg = 'Right'; $classContent = 'Left'; }
				else { $classImg = 'Left'; $classContent = 'Right';}
				
				// Title
				$title = $bibentry->getTitle();
				$title = str_replace("\\textbf","", $title);
				
				// URL OR FILE
				$html .=  "<div id=\'" . $type . "\'><div class=\'prContent" . $classContent . "\'> ".
					"<p><b>" . $bibentry->getField("Chapter") . "</b></p>".
					"<p class=\'title\' >" ;
				if($bibentry->getField("url") != "") {
					$html .=  "<a target=\'_blank\' href=\'" . $bibentry->getField("url") . "\'> "
					. $title . "</p></a>";
				} 
				else if($bibentry->getField("file") != "" ) {
					$html .=  "<a target=\'_blank\' href=\'" . $bibentry->getField("file") . "\'> "
					. $title . "</p></a>";
				}
				else {
					$html .=  $title . "</p>";
				}
				
				// Author
				if ($bibentry->getAuthor() != "") {
					$authorName = authorToStr($bibentry->getAuthor(), $mainTitles["app"])   ; 
					$html .= "<p>"  . $authorName . "</p>";
				}
				
				// Project partner
				$html .= "<p>" . $mainTitles["pp"] . ": " . $bibentry->getField("editor") . "</p>";
				 // Funds Projects 
				$html .= "<p class=\'year\'>" . $mainTitles["year"] . ": " . $bibentry->getField("publisher") . "</p>";
				
				// Project staff
				if ($bibentry->getField("note") != "")
					$html .= "<p>" . authorToStr($bibentry->getField("note"), $mainTitles["ps"]) . "</p>";
				
				// WEB
				//if( $bibentry->getField("url")!= "")
				//	$html .= "<p>" . $mainTitles["web"] . ": <a href=\'" . $bibentry->getField("url") . "\'> " . $bibentry->getField("url") . "</a></p></div>";
				//else $html .= "</div>";
				$html .= "</div>";
				// Image 
				//if (url_check("https://github.com/environmentalinformatics-marburg/cvs/blob/master/projects/graphics/".$bibentry->getKey().".png")) {
					//if ($imgLink) {
					$html .= "<div class=\'prImg" .$classImg . 
						"\'><img width=\"205\" height=\"180\" src=\"https://github.com/environmentalinformatics-marburg/cvs/blob/master/projects/graphics/"
						. $bibentry->getKey() .".png?raw=true\" /> </div></div>";
					//}
					//else {
					//	$html .= "<div class=\'prImg" .$classImg . "\'></div></div>";
					//} 

				  // Comment
				  $html .= "<div id=\'commentContent\'><p class=\'comment\'>" . $bibentry->getField("comment") . "<a href=\'" . $bibentry->getField("url") . "\'>  ...</a></p></div>";
				//}
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
function authorToStr($authorstr, $title, $keyAuthor='') {

	$aarr = explode(' and ', $authorstr);
	
	$aarr = array_map('trim', $aarr);
	$link ='';
	for($i=0; $i < count($aarr); $i++) {
	
		
		if(strpos($aarr[$i], '(href)') != false){
			$aarr[$i] = explode('(href)', $aarr[$i] );
			$link = $aarr[$i][1];
			//echo $link;
			$aarr[$i] = $aarr[$i][0];
		}
		$aarr[$i] = str_replace("\\textbf","", $aarr[$i]);
		if(strpos($aarr[$i], ',') == false) {
			// no first/lastname indicator, let's do that ourselves
			$pa = strpos($aarr[$i], '{') != false && (strpos($aarr[$i],'{') == 0 || 
			$aarr[$i][strpos($aarr[$i],'{')-1] == ' ' || 
			$aarr[$i][strpos($aarr[$i],'{')-1] == '.') ? strpos($aarr[$i], '{') : false;
			
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
			
		} else {
			$na = explode(',', $aarr[$i]);
			$fn = explode(' ', str_replace('.', '', str_replace('-', ' ', trim($na[1]))));
			$fn2 = '';
			for($j=0; $j < count($fn); $j++) {
				if(strlen($fn[$j]) > 0)
					$fn2 .= $fn[$j][0] . '.';
			}
			$aarr[$i] = $na[0] . ' ' . $fn2;
		}
		if ($link !='') {
			$aarr[$i] = '<a href=\"' . $link. '\">' . $aarr[$i] . '</a>';
		}
	}
	
	if (count($aarr) > 1)
		return $title[0] . ": " . implode(', ', $aarr);
	return 	$title[1] . ": " . implode(', ', $aarr);
}
	

?>
