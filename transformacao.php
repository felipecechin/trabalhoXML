<?php
echo "Gerando arquivos HTML...\n";
define('ARQUIVO_XML', './GioMovies.xtm');
$xml = new DOMDocument();
$xml->load(ARQUIVO_XML);

$xml->preserveWhiteSpace = false;
$xml->formatOutput = false;
$xml_string = $xml->saveXML();

$xml = new SimpleXMLElement($xml_string);

$ocorrencias = ['#sinopse' => 'Sinopse', '#ingles' => 'Título em inglês', '#elencoApoio' => 'Elenco de apoio', '#site' => 'Site', '#distribuicao' => 'Distribuição'];
$associacoes = ['#filme-ano' => 'Ano', '#filme-elenco' => 'Elenco', '#filme-direcao' => 'Direção', '#filme-genero' => 'Gênero', '#filme-duracao' => 'Duração'];
$tipoNome = ['#Duracao' => 'Duração', '#Ano' => 'Ano', '#Elenco' => 'Elenco', '#Direcao' => 'Direção', '#Genero' => 'Gênero'];

//Verifica os diretórios de destino dos arquivos
if (!is_dir('associacoes')) {
    mkdir('associacoes');
}
if (!is_dir('filmes')) {
    mkdir('filmes');
}

//Instrução para buscar todos os filmes
$buscaFilmes = "//topic[./instanceOf/topicRef/@href='#Filme']";
//Executa a instrução XPATH
$results = $xml->xpath($buscaFilmes);
$file = 'index.html';
//Inicio do arquivo HTML
$content = '<!doctype html><html>
    <head>
        <meta charset="utf-8">
        <title>Página inicial</title>
        </head>
    <body>
        <h1>Lista de filmes</h1>';
$content .= '<ol>';

foreach($results as $item){  
    //Concatena o arquivo HTML principal
    $content .= '<li><a href="filmes/'.$item["id"].'.html">'.$item->baseName->baseNameString.'</a></li>';
    $fileFilme = 'filmes/'.$item["id"].'.html';
    //Cria o arquivo específico para o filme
    $paginaFilme = '<!doctype html><html>
    <head>
        <meta charset="utf-8">
        <title>'.$item->baseName->baseNameString.'</title>
        </head>
    <body>';
    $paginaFilme .= '<a href="../index.html">Voltar para página inicial</a>';
    $paginaFilme .= '<h1>Filme: '.$item->baseName->baseNameString.'</h1>';
    $paginaFilme .= '<h3>Ocorrências</h3><ul>';
    //Percorre os filhos do filme
    foreach ($item->children() as $child) {
        if ($child->getName() == "occurrence") {
            if ($child->scope) {
                $tipo = $child->scope->topicRef["href"]->__toString();
                //Seleciona o tipo de ocorrência
                $tipo = $ocorrencias[$tipo];
                $valor = $child->resourceData;
            } else if ($child->instanceOf) {
                $tipo = $child->instanceOf->topicRef["href"]->__toString();
                //Seleciona o tipo de ocorrência
                $tipo = $ocorrencias[$tipo];
                $valor = $child->resourceRef["href"];
            }
            //Adiciona na página do filme
            $paginaFilme .= '<li>'.$tipo.': '.$valor.'</li>';
        }
    }
    $paginaFilme .= '</ul><h3>Associações</h3><ul>';
    //Instrução para buscar as associações do filme
    $buscaAssociacoes = '//association[./member[1]/topicRef/@href = "#'.$item["id"].'"]';
    //Executa a instrução XPATH
    $results2 = $xml->xpath($buscaAssociacoes);
    foreach ($results2 as $item2) {
        $tipo = $item2->instanceOf->topicRef['href']->__toString();
        //Seleciona o tipo da associação pré-definido
        $tipo = $associacoes[$tipo];
        //Seta e prepara o ID do item
        $idTipo = $item2->member[1]->topicRef['href']->__toString();
        $idTipo = str_replace('#', '', $idTipo);
        //Adiciona o link na página
        $paginaFilme .= '<li><a href="../associacoes/'.$idTipo.'.html">'.$tipo.'</a></li>';
    }
    $paginaFilme .= '</ul></body></html>';
    //Cria o arquivo de filme
    file_put_contents($fileFilme, $paginaFilme);
}
$content .= '</ol>';
$content .= '</body></html>';
//Cria o arquivo principal
file_put_contents($file, $content);

//Instrução para buscar os tópicos que não são de filmes
$buscaTopics = "//topic[./instanceOf/topicRef/@href= '#Ano' or ./instanceOf/topicRef/@href='#Direcao' or ./instanceOf/topicRef/@href='#Duracao' or ./instanceOf/topicRef/@href='#Elenco' or ./instanceOf/topicRef/@href='#Genero']";
$results = $xml->xpath($buscaTopics);
foreach ($results as $item) {
    $tipo = $item->instanceOf->topicRef["href"]->__toString();
    $fileTopic = 'associacoes/'.$item["id"].'.html';
    //Inicio do arquivo HTML
    $paginaTopic = '<!doctype html><html>
    <head>
        <meta charset="utf-8">
        <title>'.$item->baseName->baseNameString.'</title>
        </head>
    <body>';
    $paginaTopic .= '<a href="../index.html">Voltar para página inicial</a>';
    $paginaTopic .= '<h2>'.$tipoNome[$tipo].': ' . $item->baseName->baseNameString.'</h2>';

    $paginaTopic .= '<h3>Associações</h3><ul>';
    //Busca as associações que contem o ID do tópico
    $buscaAssociacoes = '//association[./member[2]/topicRef/@href = "#'.$item["id"].'"]';
    //Executa a instrução XPATH
    $results2 = $xml->xpath($buscaAssociacoes);
    $nomesFilmes = [];
    foreach ($results2 as $item2) {
        //Seleciona o ID do filme presente na associação
        $idFilme = $item2->member[0]->topicRef['href']->__toString();
        $idFilme = str_replace('#', '', $idFilme);
        //Instrução para buscar o filme presente na associação
        $instrucaoBuscaFilme = '//topic[@id="'.$idFilme.'"]/baseName/baseNameString';
        //Executa a instrução XPATH
        $resultadoBuscaFilme = $xml->xpath($instrucaoBuscaFilme);
        //Adiciona o resultado no array de filmes
        $nomesFilmes[$idFilme] = $resultadoBuscaFilme[0]->__toString();
    }
    //Ordena o array
    asort($nomesFilmes);
    foreach ($nomesFilmes as $chave => $valor) {
        $paginaTopic .= '<li><a href="../filmes/'.$chave.'.html">'.$valor.'</a></li>';
    }
    $paginaTopic .= '</ul></body></html>';
    //Cria o arquivo do tópico
    file_put_contents($fileTopic, $paginaTopic);
}
echo "Arquivos gerados\n";