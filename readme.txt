Todo o processo de desenvolvimento do trabalho foi feito em uma máquina com Ubuntu 19.04. O código foi 
escrito e executado através do Visual Studio Code e seu terminal embutido, respectivamente. 
1- Instalação
    a) Atualizar a lista de pacotes com o comando apt-get update && apt-get upgrade.
    b) Instalar o PHP com o comando apt-get install php.
    c) Após isso, é possível checar a versão do PHP através do comando php -v.
    d) Depois de instalar o PHP, é necessário utilizar a classe DOMDocument. Para isso, temos que 
    instalar o pacote php-dom, digitando, no terminal, sudo apt-get install php-dom.
2- Preparação do arquivo
    a) O arquivo original continha erros de referência, devido à erros de digitação. Em vista disso, foi preciso 
    abrir o arquivo "GioMovies.xtm" com o Visual Studio Code ou qualquer editor de texto e fazer as alterações
    necessárias.
    b) Buscamos as ocorrências de ",-" e substituímos por "-".
    c) Também tivemos que buscar as ocorrências de "alien,-o-8º-passageiro" e substituir por 
    "alien-o-8-passageiro".
    Obs.: Não é necessário executar esses passos, já que o arquivo alterado e correto encontra-se no diretório
    do projeto.
3- Execução
    a) Para executar as validações (DTD e XMLSchema) e as consultas, basta abrir o terminal no 
    diretório raiz do projeto e rodar o comando php index.php.
    b) Para executar a transformação do arquivo "GioMovies.xtm" para páginas HTML, executamos o comando 
    php transformacao.php. Após isso, um arquivo index.html e várias pastas referentes aos nodos serão 
    criadas no diretório. Para visualizar o arquivo e navegar pelos nodos, basta abri-lo utilizando 
    qualquer navegador.