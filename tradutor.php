<?php 
define('ARQUIVO_XML', './GioMovies.xtm');
$xml = new DOMDocument();
$xml->load(ARQUIVO_XML);

$xml->preserveWhiteSpace = false;
$xml->formatOutput = false;
$xml_string = $xml->saveXML();

$xml = new SimpleXMLElement($xml_string);

if (!is_dir('filmes')) {
    // dir doesn't exist, make it
    mkdir('filmes');
  }

$buscaFilmes = "//topic[./instanceOf/topicRef/@href='#Filme']";
$results = $xml->xpath($buscaFilmes);
$file = 'index.html';
$content = '<!doctype html><html>
    <head>
        <meta charset="utf-8">
        <title>Página inicial</title>
        </head>
    <body>
        <h1>Lista de filmes</h1>';
$content .= '<ol>';
foreach($results as $item){  
    $content .= '<li><a href="filmes/'.$item["id"].'.html">'.$item->baseName->baseNameString.'</a></li>';
    $fileFilme = 'filmes/'.$item["id"].'.html';
    $paginaFilme = '<!doctype html><html>
    <head>
        <meta charset="utf-8">
        <title>'.$item->baseName->baseNameString.'</title>
        </head>
    <body>';
    /* foreach($item->occurrence as $occurence){
        var_dump($occurence);
        //$content .= '<h6>'.$occurrence.'</h6>';
    } */
    $paginaFilme .= '</body></html>';
    file_put_contents($fileFilme, $paginaFilme);
}
$content .= '</ol>';
$content .= '</body></html>';
file_put_contents($file, $content);
?>