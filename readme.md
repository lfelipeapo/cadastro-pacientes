# Docker + PHP + Nginx + Postgres + Redis
Projeto desenvolvido com Laravel como Back End e Vue Js Front End
# Usada Imagem Docker PHP com Nginx pré-configurada
[Ver imagem no Docker Hub](https://hub.docker.com/repository/docker/lfelipeapo/php-nginx-web/general)

## Requisitos
- Ter o Docker instalado em sua máquina. Este passo a passo funciona tanto para ambiente Linux como para Windows e macOS.

- Ter Docker Compose instalado na máquina.

## 1º Passo
Agora é hora de ajustarmos nossas aplicações dentro do ambiente.

<b>OBS:</b> Para ajustar variaveis de conexão de banco de dados na aplicação veja exemplo: [index.php](./applications/php1/public/index.php).

Acesse o terminal da pasta raiz do projeto e rode o comando sh script-start-docker-compose.sh

Para acessar o ambiente digite em seu navegador e acesse:

http://localhost
