<?php
libxml_use_internal_errors(true);
//ini_set("display_errors", "Off");

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
$xml->load(ARQUIVO_XML);
if (!$xml->schemaValidate(ARQUIVO_XSD)) {
    mostrarErros("XML não validado pelo XSD");
    die();
} else {
    echo "XML validado pelo XSD\n";
}

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
$result = $xml->xpath($instrucaoA);

$generos = [];
foreach ($result as $item) {
    $item = str_replace(" / ", "/", $item);
    $generos[] = trim($item);
}
$generos = array_unique($generos);
$generos = array_values($generos);
echo "a) Quais são os tipos de gênero de filmes, sem repetição?\n";
for ($i = 1; $i <= count($generos); $i++){
    echo "[".$i."] ".$generos[$i-1]."\n";
}


//QUESTÃO B

$instrucaoB = '//topic[./instanceOf/topicRef/@href="#Ano" and ./baseName/baseNameString[text()="2000"]]/@id';
$result = $xml->xpath($instrucaoB);

echo "b) Quais são os títulos dos filmes que foram produzidos em 2000, ordenados alfabeticamente?\n";
$idAno2000 = "#".$result[0]["id"];

$instrucaoB = '//association[./member/topicRef[@href="'.$idAno2000.'"]]/member[1]/topicRef/@href';
$result = $xml->xpath($instrucaoB);
$i = 1;
foreach ($result as $item) {
    $idFilme = $item["href"];
    $idFilme = str_replace('#', '', $idFilme);
    $idFilme = str_replace(',', '', $idFilme);
    $instrucaoB2 = '//topic[@id="'.$idFilme.'"]/baseName/baseNameString';
    $result2 = $xml->xpath($instrucaoB2);
    $nome = $result2[0];
    echo "[".$i."] ".$nome."\n";
    $i++;
}

//QUESTÃO C

$instrucaoC = '//topic[./instanceOf/topicRef/@href="#Filme" and ./occurrence[./scope/topicRef[@href="#sinopse"]]/resourceData[contains(text(), "especial,") or contains(text(), "especial ")]]/occurrence[./scope/topicRef[@href="#ingles"]]/resourceData';
$result = $xml->xpath($instrucaoC);

echo "c) Quais são os títulos em inglês dos filmes que tem a palavra 'especial' na sinopse?\n";
for ($i = 1;$i<=count($result);$i++) {
    echo "[".$i."] ".$result[$i-1]."\n";
}

//QUESTÃO E

$instrucaoE = '//topic[./instanceOf/topicRef/@href="#Filme" and count(./occurrence/scope/topicRef[@href="#elencoApoio"])>3]';
$result = $xml->xpath($instrucaoE);

echo "e) Quantos filmes contém mais de 3 atores como elenco de apoio?\n";
echo count($result);

////////////////////////////////////////////////////////////////////////////////////////////////


