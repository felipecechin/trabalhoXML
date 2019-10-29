<?php
libxml_use_internal_errors(true);
ini_set("display_errors", "Off");

function mostrarErros($erro) {
    echo $erro."\n";
    echo "Mensagem: ";
    foreach (libxml_get_errors() as $error) {
        echo $error->message;
    }
}

$xml = new DOMDocument();
$xml->load('./GioMovies.xtm');
if (!$xml->schemaValidate('./GioMovies.xsd')) {
    mostrarErros("XML não validado pelo XSD");
} else {
    echo "XML validado pelo XSD\n";
}

if (!$xml->validate()) {
    mostrarErros("XML não validado pelo DTD");
} else {
    echo "XML validado pelo DTD\n";
}