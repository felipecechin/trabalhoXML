<?php 
define('ARQUIVO_XML', './GioMovies.xtm');
$xml = new DOMDocument();
$xml->load(ARQUIVO_XML);

$xml->preserveWhiteSpace = false;
$xml->formatOutput = false;
$xml_string = $xml->saveXML();

$xml = new SimpleXMLElement($xml_string);

$ocorrencias = ['#sinopse' => 'Sinopse', '#ingles' => 'Título em inglês', '#elencoApoio' => 'Elenco de apoio', '#site' => 'Site', '#distribuicao' => 'Distribuição'];
$associacoes = ['#filme-ano' => 'Ano', '#filme-elenco' => 'Elenco', '#filme-direcao' => 'Direção', '#filme-genero' => 'Gênero', '#filme-duracao' => 'Duração'];
$tipoPasta = ['#Duracao' => 'duracao', '#Ano' => 'ano', '#Elenco' => 'elenco', '#Direcao' => 'direcao', '#Genero' => 'genero'];
$tipoNome = ['#Duracao' => 'Duração', '#Ano' => 'Ano', '#Elenco' => 'Elenco', '#Direcao' => 'Direção', '#Genero' => 'Gênero'];

foreach ($associacoes as $chave => $valor) {
    $chave = str_replace('#filme-', '', $chave);
    if (!is_dir($chave)) {
        mkdir($chave);
    }
}
if (!is_dir('filmes')) {
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
    $paginaFilme .= '<h1>Filme: '.$item->baseName->baseNameString.'</h1>';
    $paginaFilme .= '<h3>Ocorrências</h3><ul>';
    foreach ($item->children() as $child) {
        if ($child->getName() == "occurrence") {
            if ($child->scope) {
                $tipo = $child->scope->topicRef["href"]->__toString();
                $tipo = $ocorrencias[$tipo];
                $valor = $child->resourceData;
            } else if ($child->instanceOf) {
                $tipo = $child->instanceOf->topicRef["href"]->__toString();
                $tipo = $ocorrencias[$tipo];
                $valor = $child->resourceRef["href"];
            }
            $paginaFilme .= '<li>'.$tipo.': '.$valor.'</li>';
        }
    }
    $paginaFilme .= '</ul><h3>Associações</h3><ul>';
    $buscaAssociacoes = '//association[./member[1]/topicRef/@href = "#'.$item["id"].'"]';
    $results2 = $xml->xpath($buscaAssociacoes);
    foreach ($results2 as $item2) {
        $tipo = $item2->instanceOf->topicRef['href']->__toString();
        $nomePasta = str_replace('#filme-', '', $tipo);
        $tipo = $associacoes[$tipo];
        $idTipo = $item2->member[1]->topicRef['href']->__toString();
        $idTipo = str_replace('#', '', $idTipo);
        $paginaFilme .= '<li><a href="../'.$nomePasta.'/'.$idTipo.'.html">'.$tipo.'</a></li>';
    }
    $paginaFilme .= '</ul></body></html>';
    file_put_contents($fileFilme, $paginaFilme);
}
$content .= '</ol>';
$content .= '</body></html>';
file_put_contents($file, $content);

$buscaTopics = "//topic[./instanceOf/topicRef/@href= '#Ano' or ./instanceOf/topicRef/@href='#Direcao' or ./instanceOf/topicRef/@href='#Duracao' or ./instanceOf/topicRef/@href='#Elenco' or ./instanceOf/topicRef/@href='#Genero']";
$results = $xml->xpath($buscaTopics);
foreach ($results as $item) {
    $tipo = $item->instanceOf->topicRef["href"]->__toString();
    $nomePasta = $tipoPasta[$tipo];
    $fileTopic = $nomePasta.'/'.$item["id"].'.html';
    $paginaTopic = '<!doctype html><html>
    <head>
        <meta charset="utf-8">
        <title>'.$item->baseName->baseNameString.'</title>
        </head>
    <body>';
    $paginaTopic .= '<h2>'.$tipoNome[$tipo].': ' . $item->baseName->baseNameString.'</h2>';
    $paginaTopic .= '</body></html>';
    file_put_contents($fileTopic, $paginaTopic);
}