# Arquitetura

Escolhi uma arquitetura orientada a eventos, cujo ao registrar um novo usuário é criada a uma carteira com um valor
inicial de 1000, somente para o usuário poder transacionar.

Apesar de não ser claro no enunciado a diferenciação de tipos resolvi optar por quando o documento ser CNPJ o usuário é
do tipo Lojista.

Utilizei os evento de criação da transação para disparar a notificação.

Optei pela logica da transação estar no controle somente por simplificação do exercício, somente abstraindo a parte de
requisição externa para a autorização

Disponibilizei um insomnia.json para facilitar a execução das requisições

# Requests

Criação de usuário:
POST /user
```
{
	"name":"teste",
	"email":"test@test.com",
	"document":"99.999.999/9999-89",
	"password":"$Este12345",
	"password_confirmation":"$Este12345"
}
```

POST /transaction
```
{
	"value":10,
	"payer":{
		"id":8
	},
	"payee":{
		"id":7
	}
}
```

# Execução

executar o comando para subir os containers:
```
docker-compose up -d
```

executar o comando para instalar as dependencias:
```
docker-compose exec fpm composer install
```

executar o comando para criar o banco de dados:
```
docker-compose exec fpm touch database.sqlite
```

# Comando para a execução dos testes:
executar o comando para criar o banco de dados:
```
docker-compose exec fpm php artisan test
```
