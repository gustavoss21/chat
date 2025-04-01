## install
 - PHP 8.x
 - Composer >= 2.x
 - laravel/installer => composer global require laravel/installer
 - npm => npm install

 ## Initial Configuration
 - no renomei .env.exemple para .env, defina seus parametros do banco de dados
 - execute => php artisan migrate
 - execute => php artisan key:generate

## dependecias de execucao
* php artisan reverb:start --host=127.0.0.1 --port=9000
* php artisan queue:work --queue=high,default
* npm run dev
<!-- * composer run dev -->

## proximos passos 
- carregameto dinamico da pagina
- adiconar validador de message no messageControler
- api auth request message
- verificar e padronizar retornos de metodos de controller
- envio de midia(videos, musicas, doc,imagens)
- UserContactChat os usuarios devem ser unicos
- metodo para apagar as mensagens de chat main
- adicionar o campo typo em mensagem

## teste
- testar rotas com tipo de dado para metch diferente de id e slug

## inseriondo:

## planejamento
usuario da emprasa(unico comunicável, disponibilidade do usuario)

usuario da chamada

usuario da chamada2
  

## enpecificações
* drive de transmissao: reverb

* duvivada:
   - Você também precisará configurar e executar um queue worker . Toda a transmissão de eventos é feita por meio de trabalhos enfileirados para que o tempo de resposta do seu aplicativo não seja seriamente afetado por eventos transmitidos.(tenho que transmitir por jobs?)

## interessante
sobre a tabela
php artisan db:table users

## git commit 
