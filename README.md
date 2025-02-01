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
* php artisan queue:work
* npm run dev
<!-- * composer run dev -->

## proximos passos 
- adicionar o envio de message
- adicionar evento no envio de mensagem

## teste
- testar rotas com tipo de dado para metch diferente de id e slug

## inseriondo:
broadcasting
* servido  e cliente conectado, mais o cliente não esta escutando o evento

## planejamento
usuario da emprasa(unico comunicalvel, disponibilidade do usuario)

usuario da chamada

usuario da chamada2
  

## enpecificações
* drive de transmissao: reverb

* duvivada:
   - Você também precisará configurar e executar um queue worker . Toda a transmissão de eventos é feita por meio de trabalhos enfileirados para que o tempo de resposta do seu aplicativo não seja seriamente afetado por eventos transmitidos.(tenho que transmitir por jobs?)

## interessante
sobre a tabela
php artisan db:table users