## inseriondo:
broadcasting
* servido  e cliente conectado, mais o cliente não esta escutando o evento
  
## install
 - PHP 8.x
 - Composer >= 2.x
 - laravel/installer => composer global require laravel/installer
 - npm => npm install

 ## Initial Configuration
 - no renomei .env.exemple para .env, defina seus parametros do banco de dados
 - execute => php artisan migrate

## dependecias de execucao
* php artisan reverb:start --host=127.0.0.1 --port=9000
* php artisan queue:work
* npm run dev
* composer run dev


## enpecificações
* drive de transmissao: reverb

* duvivada:
   - Você também precisará configurar e executar um queue worker . Toda a transmissão de eventos é feita por meio de trabalhos enfileirados para que o tempo de resposta do seu aplicativo não seja seriamente afetado por eventos transmitidos.(tenho que transmitir por jobs?)

## proximos passos 
criar um crud -> php artisan make:controller PhotoController --resource

cria o modelo?
php artisan make:controller PhotoController --model=Photo --resource

## interessante
sobre a tabela
php artisan db:table users