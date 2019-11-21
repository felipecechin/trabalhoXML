<?php
libxml_use_internal_errors(true);
ini_set("display_errors", "Off");

define('ARQUIVO_XML', './GioMovies.xtm');
define('ARQUIVO_XSD', './GioMovies.xsd');

function mostrarErros($erro) {
    echo $erro."\n";
    echo "Mensagem: ";
    foreach (libxml_get_errors() as $error) {
        echo $error->message;
    }
}

$xml = new DOMDocument();
//Carrega arquivo XML
$xml->load(ARQUIVO_XML);

//Valida o XML com o XSD definido anteriormente
if (!$xml->schemaValidate(ARQUIVO_XSD)) {
    mostrarErros("XML não validado pelo XSD");
    die();
} else {
    echo "XML validado pelo XSD\n";
}

//Valida o XML com o DTD que está sendo incluído no próprio arquivo XML
if (!$xml->validate()) {
    mostrarErros("XML não validado pelo DTD");
    die();
} else {
    echo "XML validado pelo DTD\n";
}

$xml->preserveWhiteSpace = false;
$xml->formatOutput = false;
$xml_string = $xml->saveXML();

$xml = new SimpleXMLElement($xml_string);

///////////////////////////////////////////CONSULTAS///////////////////////////////////////////////
echo "Consultas no XML:\n";

//QUESTÃO A

$instrucaoA = '//topic[./instanceOf/topicRef/@href="#Genero"]/baseName/baseNameString';
//Executa a instrução XPATH
$result = $xml->xpath($instrucaoA);

$generos = [];
//Percorre todos os gêneros, os formata retirando espaços e adiciona no array de gêneros
foreach ($result as $item) {
    $item = str_replace(" / ", "/", $item);
    $generos[] = trim($item);
}

//Exclui valores repetidos
$generos = array_unique($generos);
$generos = array_values($generos);

echo "a) Quais são os tipos de gênero de filmes, sem repetição?\n";
//Imprime os gêneros selecionados
for ($i = 1; $i <= count($generos); $i++){
    echo "[".$i."] ".$generos[$i-1]."\n";
}

//QUESTÃO B

//Instrução para obter o ID do tópico do ano 2000
$instrucaoB = '//topic[./instanceOf/topicRef/@href="#Ano" and ./baseName/baseNameString[text()="2000"]]/@id';
//Executa a instrução XPATH
$result = $xml->xpath($instrucaoB);

echo "b) Quais são os títulos dos filmes que foram produzidos em 2000, ordenados alfabeticamente?\n";
$idAno2000 = "#".$result[0]["id"];

//Instrução para buscar os IDs dos filmes que estão associados com o ano 2000
$instrucaoB = '//association[./member/topicRef[@href="'.$idAno2000.'"]]/member[1]/topicRef/@href';
//Executa a instrução XPATH
$result = $xml->xpath($instrucaoB);
$nomes = [];

//Percorre todos os IDs dos filmes selecionados anteriormente
foreach ($result as $item) {
    $idFilme = $item["href"];
    //Prepara a string do ID
    $idFilme = str_replace('#', '', $idFilme);
    //Para cada ID, esta instrução é executada para buscar o nome do respectivo filme
    $instrucaoB2 = '//topic[@id="'.$idFilme.'"]/baseName/baseNameString';
    //Executa a instrução
    $result2 = $xml->xpath($instrucaoB2);
    $nomes[] = $result2[0]->__toString();
}

//Organiza os nomes em ordem alfabética
sort($nomes);
//Imprime os nomes dos filmes encontrados
for ($i = 1;$i<=count($nomes);$i++) {
    echo "[".$i."] ".$nomes[$i-1]."\n";
}

//QUESTÃO C

//Instrução para buscar os nomes dos filmes em inglês que contém a palavra "especial", exclusivamente
$instrucaoC = '//topic[./instanceOf/topicRef/@href="#Filme" and ./occurrence[./scope/topicRef[@href="#sinopse"]]/resourceData[contains(text(), "especial,") or contains(text(), "especial ")]]/occurrence[./scope/topicRef[@href="#ingles"]]/resourceData';
//Executa a instrução
$result = $xml->xpath($instrucaoC);

echo "c) Quais são os títulos em inglês dos filmes que tem a palavra 'especial' na sinopse?\n";
//Imprime os títulos encontrados
for ($i = 1;$i<=count($result);$i++) {
    echo "[".$i."] ".$result[$i-1]."\n";
}

//QUESTÃO D

//Busca o ID do tópico que contém o gênero "Thriller"
$instrucaoD = '//topic[./instanceOf/topicRef/@href="#Genero" and ./baseName/baseNameString[text()="Thriller"]]/@id';
//Executa a instrução
$result = $xml->xpath($instrucaoD);

echo "d) Quais são os sites dos filmes que são do tipo 'thriller'?\n";
$idGeneroThriller = "#".$result[0]["id"];

//Instrução para achar os IDs dos filmes que estão associados ao gênero "Thriller"
$instrucaoD = '//association[./member/topicRef[@href="'.$idGeneroThriller.'"]]/member[1]/topicRef/@href';
//Executa a instrução
$result = $xml->xpath($instrucaoD);
$i = 1;
//Percorre todos os IDs dos filmes encontrados
foreach ($result as $item) {
    //Prepara o ID do filme
    $idFilme = $item["href"];
    $idFilme = str_replace('#', '', $idFilme);
    //Para o filme, é feita uma instrução para encontrar seu respectivo site
    $instrucaoD2 = '//topic[@id="'.$idFilme.'"]/occurrence[./instanceOf/topicRef/@href="#site"]/resourceRef/@href';
    //Executa a instrução
    $result2 = $xml->xpath($instrucaoD2);
    //Verifica se o filme possui um site, caso não possua, o valor atribuído é de uma string vazia
    $site = $result2 ? $result2[0]["href"] : '';
    //Imprime o site do filme, caso exista
    if ($site!="") {
        echo "[" . $i . "] " . $site . "\n";
        $i++;
    }
}

//QUESTÃO E

//Instrução para buscar filmes que contém mais que 3 atores no elenco de apoio
$instrucaoE = '//topic[./instanceOf/topicRef/@href="#Filme" and count(./occurrence/scope/topicRef[@href="#elencoApoio"])>3]';
//Executa a instrução
$result = $xml->xpath($instrucaoE);

echo "e) Quantos filmes contém mais de 3 atores como elenco de apoio?\n";
//Imprime o número de registros encontrados
echo count($result)."\n";

//QUESTÃO F

//Instrução para buscar todas as associações que relacionam filme com ator
$instrucaoF = '//association[./instanceOf/topicRef/@href="#filme-elenco"]';
//Executa a instrução
$result = $xml->xpath($instrucaoF);

$i = 1;
$idsFilmes = [];
//Percorre todas as associações
foreach ($result as $item) {
    //Seta e prepara os IDs dos membros da relação
    $idElenco = $item->member[1]->topicRef["href"];
    $idFilme = $item->member[0]->topicRef["href"];
    $idFilme = str_replace('#', '', $idFilme);
    $idElenco = str_replace('#', '', $idElenco);
    //Instrução para buscar o nome do ator que está na relação
    $instrucaoF2 = '//topic[@id="'.$idElenco.'"]/baseName/baseNameString';
    //Executa a instrução
    $result2 = $xml->xpath($instrucaoF2);
    $nomeElenco = $result2[0]->__toString();
    //Instrução para buscar a sinopse do filme que está na relação
    $instrucaoF3 = '//topic[@id="'.$idFilme.'"]/occurrence[./scope/topicRef/@href = "#sinopse"]/resourceData';
    //Executa a instrução
    $result3 = $xml->xpath($instrucaoF3);
    $sinopse = ($result3) ? $result3[0]->__toString() : '';
    //Verifica se o nome do ator está presente na sinopse, caso estiver, adiciona no array de IDs
    $pos = strpos($sinopse, $nomeElenco);
    if ($pos !== false) {
        $idsFilmes[] = $idFilme;
    }
}

//Exclui valores repetidos
$idsFilmes = array_unique($idsFilmes);
$idsFilmes = array_values($idsFilmes);
echo "f) Quais são os ID dos filmes que tem o nome de algum membro do elenco citado na sinopse?\n";
//Imprime os IDs encontrados
for ($i = 1; $i <= count($idsFilmes); $i++){
    echo "[".$i."] ".$idsFilmes[$i-1]."\n";
}

////////////////////////////////////////////////////////////////////////////////////////////////