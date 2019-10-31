<?php 
define('ARQUIVO_XML', './GioMovies.xtm');
$xml = new DOMDocument();
$xml->load(ARQUIVO_XML);

$xml->preserveWhiteSpace = false;
$xml->formatOutput = false;
$xml_string = $xml->saveXML();

$xml = new SimpleXMLElement($xml_string);

$buscaFilmes = '//topic[./instanceOf/topicRef/@href="#Filme"]';
$results = $xml->xpath($buscaFilmes);

foreach($results as $item){
    /* $file = $item["id"].'.html';
    $content = "<!doctype html><html>
    <head><meta charset='utf-8'>
    <title>".$item->baseName->baseNameString."</title>
    </head><body>
    <h3>".$item->baseName->baseNameString."</h3>"; */
    foreach($item->occurrence as $occurence){
        var_dump($occurence);
        //$content .= "<h6>".$occurrence."</h6>";
    }
    /* $content .= "</body></html>";
    file_put_contents($file, $content); */
    break;
}

?>