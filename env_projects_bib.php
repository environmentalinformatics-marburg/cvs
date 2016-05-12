<?
/* env_projects_bib: projects overview as html from bibtex file
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
$db->load('ei/ei_projects.bib');

# Publications-titles mapping depends from the language 
$mainTitles = array();
if(isset($_GET['l'])) {
	if ( $_GET['l'] == 1) {
		$mainTitles = array('app'=>'Applicants', 
		'pp'=>'Participating partner',
		'fn'=>'Funding',
		'ps'=>'Project staff',
		'web'=>'Web',
		'year'=>'Funding'
	);
	} else {	
	 	$mainTitles = array('app'=>'Applicants', 
		'pp'=>'Participating partner',
		'fn'=>'Funding',
		'ps'=>'Project staff',
		'web'=>'Web',
		'year'=>'Funding'
	);
 	}
 }
else {
		$mainTitles = array('app'=>'Applicants', 
		'pp'=>'Participating partner',
		'fn'=>'Funding',
		'ps'=>'Project staff',
		'web'=>'Web',
		'year'=>'Funding'
	);	
}

$query = array('type'=>'InBook');
$entries=$db->multisearch($query);

// send to Browser
# projekts

$projekts = getContent('inbook',  $entries, true, $mainTitles);
if ($projekts != '') echo " var inbook = '". $projekts . "'; ";
else echo " var inbook = 'test';";

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
			
				if($numberImg%2 == 0) { $classImg = 'Right'; $classContent = 'Left'; }
				else { $classImg = 'Left'; $classContent = 'Right';}

				$title = $bibentry->getTitle();
				$title = str_replace("\\textbf","", $title);
				$title = str_replace("\\textit","", $title);

				if(preg_match('/http/',$bibentry->getField("url"))) {
					$html .=  "<div id=\'" . $type . "\'><div class=\'prContent" . $classContent . "\'> <p class=\'title\' ><a target=\'_blank\' href=\'" 
					. $bibentry->getField("url") . "\'> "
					. $title . ".</p></a>";
				} 
				else if(preg_match('/http/',$bibentry->getField("file")) ) {

					$html .=  "<div id=\'" . $type . "\'><div class=\'prContent" . $classContent . "\'> <p class=\'title\'><a target=\'_blank\' href=\'" 
					. $bibentry->getField("file") . "\'> "
					. $title . ".</p></a>";
				}
				else if($type== 'manual'){
					$html .=  "<div id=\'" . $type . "\'><div class=\'prContent" . $classContent . "\'> <p class=\'title\'><a target=\'_blank\' href=\'https://github.com/environmentalinformatics-marburg\'> "
					. $title . ".</p></a>";
		
				}
				else {
					$html .=  "<div id=\'" . $type . "\'><div class=\'prContent" . $classContent . "\'><p class=\'title\'>"
					. $title . ".</p>";
				}
				
				$authorName = authorToStr($bibentry->getAuthor()) . " - " . $bibentry->getYear()  ; 
				$html .= "<p>" . $mainTitles["app"] . ": " . $authorName . ".</p>";
				$html .= "<p>" . $mainTitles["pp"] . ": " . authorToStr($bibentry->getField("note")) . "</p>";
				$html .= "<p class=\'year\'>" . $mainTitles["year"] . ": " . $bibentry->getYear() . "</p>";
				$html .= "<p>" . $mainTitles["ps"] . ": " . authorToStr($bibentry->getField("editor")) . "</p>";
				$html .= "<p>" . $mainTitles["web"] . ": <a hrep=\'" . $bibentry->getField("url") . "\'> " . $bibentry->getField("url") . "</a></p>";
				$html .= "<p class=\'comment\'>" . $bibentry->getField("comment") . "</div>";
				/*if($type == 'unpublished') {
					$html .= " <p class=\'jornal\'>" . $bibentry->getField("note") . "</p>";
			
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
				*/
				//URL check
				if (url_check("https://github.com/environmentalinformatics-marburg/cvs/blob/master/projects/graphics/".$bibentry->getKey().".png")) {
					if ($imgLink) {
					$html .= "<div class=\'prImg" .$classImg . 
						"\'><img width=\"200\" height=\"140\" src=\"https://github.com/environmentalinformatics-marburg/cvs/blob/master/projects/graphics/"
						. $bibentry->getKey() .".png?raw=true\" /> </div></div>";
					}
					else {
						$html .= "</div>";
					} 

				  $numberImg += 1;
				}
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
function authorToStr($authorstr, $keyAuthor='') {

	$aarr = explode(' and ', $authorstr);
	$aarr = array_map('trim', $aarr);
	$link ='';
	for($i=0; $i < count($aarr); $i++) {
			if(strpos(',',$aarr[$i]) != false) {
				$aarr[$i] = explode(',', $aarr[$i] );
				$link = $aarr[$i][1];
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
	
	return implode(', ', $aarr);	
}
	

?>
