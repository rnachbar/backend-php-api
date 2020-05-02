# BACKEND PHP - PROJETO API REST  
*API de um aplicativo pessoal para monitorar quantas vezes o usuário bebeu água.*

## Tecnologias

* PHP 5.6 ou maior (recomendado 7.2)  
* MySQL ou MariaDB (última versão)  

## Endpoints

* Endepoins estão listadas no arquivo `documentation/endpoints/App Drink Water Collection.postman_collection.json`. Abra esse arquivo no `Postman` para ver os detalhes de cada requisição

* `GET` {{url}}/historic/:iduser  
* `POST` {{url}}/login  
* `POST` {{url}}/users/  
* `GET` {{url}}/users/ ou /users/:iduser  
* `PUT` {{url}}/users/:iduser  
* `DELETE` {{url}}/users/:iduser  
* `POST` {{url}}/users/:iduser/drink  
* `GET` {{url}}/ranking  

## MER

* MER do banco de dados está na pasta `documentation/database/app-drink-water.mer.mwb`. Feito com o MySQL Workbench  

## First Run

* Importar dump do banco de dados em `documentation/database/dump.php` em seu ambiente local (dump sem dados)  

* Alterar arquivo `api/connection/Connection.php` das linhas 14 a 17 com suas configurações de banco local, como `host`, `usuário` e `senha`  

```php
private $host = 'localhost';
private $db_name = 'app_drink_water';
private $user = 'root';
private $pass = '';
```  

* Habilitar PDO no servidor de hospedagem da API, pois as consultas no banco são feitas com PDO

* Você pode usar o XAMPP para simular o ambiente local ou pode instalar a última versão do PHP na sua máquina  

## Instalação do PHP (última versão)

### Windows

* Acesse o link do [site do PHP](https://secure.php.net/downloads.php), na versão **Current Stable PHP**, selecione a opção **Windows Download** e, depois, baixe a versão **Non Thread Safe** equivalente ao tipo de arquitetura do seu Sistema Operacional, que pode ser x86 ou x64.  

* Uma vez baixado o zip, sugiro descompactar a pasta na raiz do seu drive C e renomeá-la para php.  

* Assim, precisaremos também colocar esse diretório no path do Windows, processo necessário para podermos usar o comando php em qualquer diretório do Sistema.  

* Para isso, acesse o **Painel de Controle do Windows** e clique em **Sistemas**. Na janela que se abriu, procure no menu lateral esquerdo a opção **Configurações Avançadas do Sistema**. Na nova janela, clique no ultimo botão chamado **Variáveis de Ambiente**.  

* Na seção **Variáveis do Sistema**, procure pela **path** e clique em **editar**. Uma lista com muitas variáveis se abrirá. Clique no botão **Novo** e, no novo campo que vai se abrir no final da lista, coloque o caminho do diretório da sua instalação do PHP, que se for como colocado aqui, será **C:\php**.  

* Feito isso, clique em **OK**, **OK** novamente e, por fim, **OK**. =)  

* Pronto! Para ver se tudo está funcionando, você pode abrir o prompt de comando do windows e digitar `$ php -v`.  

## Linux (Distribuição baseada no Debian)

* Para instalar o PHP no Linux (uma distribuição baseada no Debian como o Ubuntu, por exemplo), abra o terminal e digite o seguintes comandos:  

* `$ sudo apt-get update`  
* `$ sudo apt-get install libapache2-mod-php7.0 php7.0-mysql php7.0-curl php7.0-json php-memcached php7.0-dev php7.0-mcrypt php7.0-sqlite3 php7.0-mbstring`  

* Para ver se funcionou, após a instalação, basta digitar no terminal `$ php -v`  

## Mac OS X

* Para instalar o PHP no Mac OS X, abra o terminal e digite o seguinte comando:  

* `$ curl -s https://php-osx.liip.ch/install.sh | bash -s 7.1`  
* `$ export PATH=/usr/local/php5/bin:$PATH`  

## Executar o PHP

* Abra o terminal do Windows, ou qualquer outro de sua preferência e navegue até a pasta do projeto  

* Para que o servidor web funcione, usaremos o comando `$ php -S 127.0.0.1:8888` (ou `$ php -S localhost:8888`) passando como parâmetros o IP e a porta (porta de sua preferência) para o qual queremos que o servidor suba, no caso, a mesma máquina que estamos utilizando, ou o IP local  
