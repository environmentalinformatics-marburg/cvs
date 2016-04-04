<?
if(!isset($Path['lib-bibtex']))
	$Path['lib-bibtex'] = "lib/";
	
require_once($Path['lib-bibtex'] . 'lib_bibtex.inc.php');

$bib = new Bibtex( 'ei.bib','Detsch');
#$bib->Select(array('owner' => 'fdetsch'));
$bib->Select(array('author' => 'detsch'));

echo '<div> Articles';
# Artikel
$bib->PrintBibliography('article');
echo '</div></br> ';

echo '<div> Software/technical reports';
# Software/Handbücher
$bib->PrintBibliography('manual');
$bib->PrintBibliography('techreport');
echo '</div></br> ';

/// Book chapters
echo '<div> Book chapters';
# Buchkapitel
$bib->PrintBibliography('inbook');
echo '</div></br> ';

/// Books
echo '<div> Books';
# Bücher
$bib->PrintBibliography('book');
$bib->PrintBibliography('phdthesis');
echo '</div></br> ';


//Conference papers
# Konferenzbeiträge
echo '<div> Conference papers/contributions';
$bib->PrintBibliography('conference');
$bib->PrintBibliography('inproceedings');
$bib->PrintBibliography('incollection');
echo '</div></br> ';

/// Submitted
echo '<div> Submitted articles';
# Eingereichte Artikel
$bib->PrintBibliography('unpublished');
echo '</div></br> ';
////////
?>
