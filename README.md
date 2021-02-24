# 📝 lexartlabs-fullstack-test
Teste para a vaga de desenvolvedor fullstack

> OBS: como o buscapé não fornece uma api, so foi possível implementar com o mercado livre

## 🛠 Primeiros passos
É possível clonar o repositório por meio do seu terminal apenas digitando:

```
git clone https://github.com/whendev/lexartlabs-fullstack-test.git
```

Entre no repositório clonado e instale todas as dependências com o comando:

```
composer install
```

Crie sua aplicação no mercado livre e reencha os dados necessários no arquivo .env.example e o renomeie para .env

Instale o docker, renomeie o arquivo docker-composer-example.yml para docker-composer.yml e coloque as mesmas informações do banco de dados do .env no docker-compose.yml

Prepare seu ambiente executando o comando:

```
sudo docker-compose up -d
```

Acesse seu banco de dados e importe o arquivo lexarttest.sql

Agora acesse https://seudominio.com/login e faça o login, em seguida clique em autentificar aplicação do mercado livre.

Tudo pronto, basta acessar https://seudominio.com pelo navegador.

## 🚀 Contribuição

1. Faça o _fork_ do projeto (<https://github.com/whendev/lexartlabs-fullstack-test/fork>)
2. Crie uma _branch_ para sua modificação (`git checkout -b feature/my-new-resource`)
3. Faça _commit_ (`git commit -m 'Adicionando um novo recurso ...'`)
4. _Push_ (`git push origin feature/my-new-feature`)
5. Crie um novo _Pull Request_

**Depois que sua solicitação pull for mesclada**, poderá excluir sua branch com segurança.

---