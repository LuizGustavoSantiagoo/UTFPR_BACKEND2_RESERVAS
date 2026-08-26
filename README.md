# Sistema de Reserva de Ambientes

Aplicação web para reserva de ambientes institucionais (auditórios, quadras, salas de reunião, laboratórios e outros espaços físicos), desenvolvida em Laravel com Livewire.

- Autor: Luiz Gustavo de Oliveira Santiago
- Instituição: UTFPR
- Disciplina: Desenvolvimento de Aplicações Backend com Framework

## Motivação

A reserva de espaços físicos em instituições costuma ser feita de forma manual ou informal (e-mail, planilhas, papel), o que gera conflitos de horário, falta de visibilidade sobre a disponibilidade e retrabalho para quem administra os ambientes.

Além do problema em si, o tema foi escolhido porque o meu Trabalho de Conclusão de Curso terá um tema semelhante. Este projeto funciona, portanto, como base técnica e de aprendizado para o TCC, permitindo validar a modelagem, o fluxo de reservas e a stack escolhida antes do trabalho final.

## Objetivos

### Objetivo geral

Desenvolver um sistema web que centralize o cadastro de ambientes e o processo de solicitação, aprovação e acompanhamento de reservas, eliminando conflitos de horário e dando visibilidade da disponibilidade aos usuários.

### Objetivos específicos

- Permitir que administradores cadastrem e gerenciem os ambientes disponíveis para reserva.
- Permitir que usuários autenticados solicitem reservas de ambientes em datas e horários específicos.
- Garantir que não existam duas reservas para o mesmo ambiente no mesmo intervalo de tempo.
- Implementar um fluxo de aprovação de reservas por parte do administrador.
- Notificar os usuários por e-mail sobre o andamento de suas reservas.
- Oferecer uma visualização em calendário da ocupação dos ambientes.
- Aplicar na prática os conceitos da disciplina: rotas, controllers, models, migrations, autenticação, autorização, validação e envio de e-mails com o framework Laravel.

## Funcionalidades planejadas

### Perfis de usuário

| Perfil | Permissões |
|---|---|
| Administrador | Gerencia ambientes, aprova ou recusa reservas, visualiza todas as reservas |
| Usuário comum | Consulta ambientes e disponibilidade, solicita e cancela suas próprias reservas |

### Funcionalidades

| Funcionalidade | Descrição | Prioridade |
|---|---|---|
| Autenticação | Cadastro, login, recuperação de senha e verificação de e-mail | Alta |
| Gestão de ambientes | CRUD de ambientes com nome, bloco/localização, capacidade e descrição | Alta |
| Solicitação de reserva | Usuário escolhe ambiente, data, horário de início e fim e justificativa | Alta |
| Validação de conflito de horário | Bloqueia reservas sobrepostas para o mesmo ambiente | Alta |
| Aprovação de reservas | Administrador aprova ou recusa solicitações pendentes | Alta |
| Minhas reservas | Usuário acompanha o status (pendente, aprovada, recusada, cancelada) e cancela reservas | Média |
| Notificação por e-mail | Envio de e-mail ao criar, aprovar, recusar ou cancelar uma reserva | Média |
| Calendário de disponibilidade | Visualização por dia/semana da ocupação de cada ambiente | Média |
| Painel administrativo | Resumo de reservas pendentes e ambientes mais utilizados | Baixa |

## Tecnologias

- PHP 8.3
- Laravel 13
- Livewire
- Tailwind CSS
- MySQL
- Docker (Laravel Sail)

## Artefatos de engenharia de software

Os artefatos de planejamento técnico estão na pasta [docs/](docs/):

- Levantamento e priorização de requisitos (MoSCoW): `docs/requisitos.md` (a ser adicionado)
- Diagrama de classes: `docs/diagrama-classes.png` (a ser adicionado)
- Modelagem do banco de dados (DER): `docs/der.png` (a ser adicionado)
- Diagrama de sequência do fluxo de reserva: `docs/diagrama-sequencia.png` (a ser adicionado)
- Protótipos de telas: `docs/prototipos/` (a ser adicionado)
- Planejamento de sprints: `docs/sprints.md` (a ser adicionado)

## Vídeo de apresentação

Vídeo de até 3 minutos apresentando a proposta, os objetivos e as funcionalidades planejadas:

LINK_DO_VIDEO (a ser adicionado)

## Como executar localmente

Pré-requisitos: Docker e Docker Compose. O projeto usa Laravel Sail, que sobe a aplicação e o MySQL em containers.

```bash
git clone https://github.com/LuizGustavoSantiagoo/UTFPR_BACKEND2_RESERVAS.git
cd UTFPR_BACKEND2_RESERVAS

cp .env.example .env
docker run --rm -v "$(pwd):/var/www/html" -w /var/www/html laravelsail/php83-composer:latest composer install

./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

A aplicação ficará disponível em `http://localhost`.

Alternativamente, com `make` instalado, todo o setup acima é feito com um único comando, e `make help` lista os demais atalhos (`up`, `down`, `fresh`, `dev`, `test`):

```bash
make setup
make dev
```

Usuário de teste criado pelo seeder:

- E-mail: `test@example.com`
- Senha: `12345678`
