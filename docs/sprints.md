# Planejamento de Sprints

> Sistema de Reserva de Ambientes - UTFPR, Desenvolvimento de Aplicações Backend com Framework.
> Cinco sprints de duas semanas, de 08/09/2026 a 16/11/2026.

> **Revisão do planejamento (26/09/2026).** O planejamento inicial foi feito para a modelagem inicial: um único perfil de gestão, grade por ambiente e reserva amarrada a uma linha da grade. Três decisões posteriores mudaram cada uma dessas premissas - D1 (07/09) e D4 a D6 (26/09), registradas em [requisitos.md](requisitos.md) seção 9. As tabelas abaixo já refletem o escopo corrigido. Itens ~~riscados~~ estão concluídos.

## Visão geral

| Sprint | Período | Tema |
|---|---|---|
| 1 | 08/09 a 21/09 | Fundação de dados |
| 2 | 22/09 a 05/10 | Disponibilidade, decisão e autorização |
| 3 | 06/10 a 19/10 | Área administrativa e do responsável |
| 4 | 20/10 a 02/11 | Área do usuário |
| 5 | 03/11 a 16/11 | Qualidade e entrega |
| - | - | Backlog (fora do escopo) |

A ordem segue a estratégia da seção 5.2 do [relatório de arquitetura](arquitetura-reservas.md): schema primeiro, depois o motor de disponibilidade e a decisão (o núcleo, testável sem HTTP), depois as telas (admin e responsável antes do usuário, por dependência), e as notificações por último.

**Sobre o calendário.** A Sprint 1 foi reaberta em 26/09 com escopo novo, porque a modelagem que ela materializava mudou. As datas acima são as originais; o replanejamento é decisão do autor e depende do prazo final da disciplina.

## Ponto de partida

Já existe no repositório:

- Starter kit Livewire com autenticação Fortify (login, registro, recuperação de senha, 2FA e passkeys). A verificação de e-mail tem as telas prontas, mas não é obrigatória: o model `User` ainda não implementa `MustVerifyEmail`
- Telas de configuração de perfil, segurança e aparência
- Cadastro estendido com `born_date` e `role`
- Migrations, models e enums de uma **primeira modelagem**, que a Sprint 1 substitui inteira
- CI em GitHub Actions rodando Pint, PHPStan e PHPUnit **contra MySQL** (`composer ci:check`)
- `README.md`, `STYLE_GUIDE.md` e, em `docs/`: requisitos, arquitetura, fluxos, DBML e este planejamento

---

## Sprint 1: Fundação de dados
**08/09 a 21/09/2026: reaberta em 26/09**

A modelagem inicial tinha uma tabela plana de ambientes, grade por ambiente e a reserva apontando para uma linha da grade. A entrevista de 26/09 trouxe a hierarquia bloco -> ambiente -> subambiente, o responsável de bloco, a grade única e a reserva direta. Esta sprint materializa o schema de [database.dbml](database.dbml) **antes** de qualquer tela - é o momento mais barato para essa mudança, já que nada está em produção.

| Item | Prioridade |
|---|---|
| Reescrever `places` com `block_id`, `parent_id`, `is_reservable` e os parâmetros de solicitação; criar `blocks` e `block_user` | Must |
| Criar `operating_hours` (grade única, sete linhas) no lugar de `place_schedules`; seed seg a sex 06h às 23h | Must |
| Criar `closures` com escopo câmpus / bloco / lugar, no lugar de `place_exceptions` | Must |
| Reescrever `reservations` (com `origin` e `date`) e criar `reservation_slots` com o índice único `(place_id, date, starts_at, active_key)` | Must |
| Models, casts e relacionamentos: `Place::parent/children/root`, `User::managedBlocks/managesBlock`, `Reservation::slots`; scopes `active`, `reservable`, `pending`, `approved` | Must |
| Enums: `Role` ganha `manager`; `ReservationOrigin` novo; `ReservationStatus` mantido | Must |
| Factories e seeder: dois blocos com responsáveis distintos, um ambiente com subambientes e um sem, um lugar não reservável, um admin, dois `manager`, três `user` | Must |
| ~~Atualizar o DER e as decisões de modelagem no README~~ - concluída em 26/09 | - |

**Entregável:** `migrate:fresh --seed` produz dois blocos com estrutura distinta, a grade gera 17 slots por dia útil e nenhum no sábado, e o banco já impede fisicamente dois `active_key = 1` no mesmo lugar, data e slot.

**Risco:** o seeder tratar só o caso fácil. Um bloco sem subambientes não exercita a regra pai/filho (R21) nem o escopo do responsável (R17); o seeder precisa ter explicitamente os dois blocos e a hierarquia.

---

## Sprint 2: Disponibilidade, decisão e autorização
**22/09 a 05/10/2026**

O núcleo do sistema, e a parte que dá para testar sem escrever uma linha de interface. Disponibilidade é diferença de conjuntos sobre chaves de slot; a decisão (deferimento ou reserva direta) é uma transação com lock no ambiente-raiz, que confirma uma reserva e indefere as concorrentes na árvore de uma vez só.

| Item | Prioridade |
|---|---|
| `AvailabilityService`: gerar os 17 slots da janela do dia a partir de `operating_hours` | Must |
| `AvailabilityService`: subtrair fechamentos por escopo e slots ativos no lugar, no pai e nos filhos | Must |
| Testar a hierarquia: ambiente ocupa os filhos, filho ocupa o pai, irmãos não se afetam | Must |
| `ReservationService::request` - só `user`; gera N `reservation_slots` com `active_key` nulo | Must |
| Aplicar as regras R3 a R10 na solicitação | Must |
| `ReservationService::cancel` - cancelamento lógico que zera `active_key` | Must |
| `ReservationService::approve` e `reject` - transação com lock no ambiente-raiz e indeferimento em cascata na árvore | Must |
| `ReservationService::direct` - reserva direta como `INSERT` + o mesmo `approve`; justificativa obrigatória; ignora R6-R8 e R10 | Must |
| Provar a defesa de concorrência em MySQL, com a variante ambiente vs. subambiente | Must |
| Policies `Block`, `Place`, `OperatingHour`, `Closure` e `Reservation`, com `managesBlock` e a regra "manager não solicita" (R22) | Must |

**Entregável:** o sistema inteiro funciona por `tinker`. Três usuários solicitam o mesmo slot; o responsável defere uma e as outras duas caem com justificativa. O admin tenta deferir uma solicitação no subambiente no mesmo horário e recebe conflito. O responsável do outro bloco recebe 403.

**Risco:** travar o lugar em vez do ambiente-raiz. Em MySQL, dois deferimentos (um no pai, um no filho) passariam juntos. O teste da variante ambiente vs. subambiente existe para pegar isso. Segundo risco: a reserva direta ganhar um caminho de escrita próprio, com regras que divergem do deferimento em semanas.

---

## Sprint 3: Área administrativa e do responsável
**06/10 a 19/10/2026**

Sem blocos, lugares e responsáveis cadastrados, não há o que reservar nem quem decida. O admin e o responsável vêm antes do usuário comum por dependência, não por importância.

| Item | Prioridade |
|---|---|
| CRUD de blocos e nomeação de responsáveis (substituição integral de `block_user`) | Must |
| CRUD de ambientes e subambientes, com `is_reservable` e validação de profundidade e de nome entre irmãos | Must |
| Criação de contas `manager` e `admin` pelo administrador | Must |
| Tela da grade única: sete linhas, aberto e janela por dia | Must |
| Tratar reservas conflitantes ao alterar a grade | Should |
| Fechamentos com escopo; responsável só no seu bloco; cancelamento das reservas atingidas | Should |
| Painel do responsável: fila do bloco com concorrentes lado a lado, reserva direta, agenda e cancelamento com motivo | Must |
| Restringir rotas por perfil **e por bloco**; navegação por perfil | Must |

**Entregável:** um administrador monta do zero os dois blocos do seeder e nomeia os responsáveis; cada responsável só vê e decide sobre o seu bloco; uma reserva direta aparece na agenda marcada como `direct`.

**Risco:** o painel do responsável filtrar pelo `role` e não pela pivô - todo `manager` veria todos os blocos. O teste de autorização da Sprint 2 cobre, e a restrição de rotas precisa checar o bloco explicitamente.

---

## Sprint 4: Área do usuário
**20/10 a 02/11/2026**

O caminho completo do usuário comum: encontrar um lugar, ver a disponibilidade, solicitar, acompanhar e cancelar.

| Item | Prioridade |
|---|---|
| Listagem de lugares reserváveis com filtro por bloco, tipo, capacidade e data | Must |
| Tela de disponibilidade com seletor de data e a grade de 17 slots, com o número de pendentes por slot | Must |
| Solicitação com escolha de slots contíguos, tratamento de conflito e validação | Must |
| Minhas reservas: acompanhamento do status e cancelamento dentro do prazo | Must |
| Notificações por e-mail: solicitar, deferir, indeferir (manual, em cascata ou por reserva direta), cancelar | Should |
| Painel por perfil: próximas reservas (`user`), pendentes do bloco (`manager`), tudo (`admin`) - sem o relatório de ambientes mais usados | Could |

**Entregável:** o fluxo do README funciona ponta a ponta, e o usuário que perde o slot recebe um e-mail claro e a grade recarregada, não um erro do banco.

**Risco:** recalcular disponibilidade na tela. Se a view reimplementar a regra, passam a existir duas fontes de verdade - a tela apenas renderiza o que o `AvailabilityService` devolve.

---

## Sprint 5: Qualidade e entrega
**03/11 a 16/11/2026**

Fecha o que a disciplina cobra como artefato e o que o código cobra como garantia.

| Item | Prioridade |
|---|---|
| Testes de feature dos fluxos de cada perfil | Must |
| ~~Rodar a suíte do CI contra MySQL em vez de SQLite~~ - concluída em 26/09 | - |
| Fechar as ambiguidades do guia de estilo e revisar acessibilidade | Should |
| ~~Escrever `docs/requisitos.md` com priorização MoSCoW~~ - concluída (v1.3, 26/09) | - |
| Produzir o diagrama de classes - os de sequência já estão em [fluxos.md](fluxos.md) | Must |
| Criar os protótipos de tela em `docs/prototipos/` | Should |
| Conferir os artefatos publicados e o README para a entrega | Must |
| ~~Gravar o vídeo de apresentação de até 3 minutos~~ - concluída em 27/09 | - |

**Entregável:** repositório com todos os artefatos referenciados pelo README efetivamente publicados, CI verde e vídeo disponível.

**Risco:** o README anuncia o diagrama de classes e os protótipos, que ainda não existem.

---

## Backlog: fora do escopo desta versão

| Item |
|---|
| Reservas recorrentes |
| Expor a API REST com Sanctum |

O deferimento **saiu do backlog** em 07/09 e está na Sprint 2 como Must.

Também deliberadamente adiados: fila de espera, check-in e no-show, relatórios de ocupação, integração com Google Calendar, multi-tenant, um terceiro nível de hierarquia e grade por bloco. Os dois últimos a modelagem já suporta sem migração de dados - ver a seção 5.5 do [relatório de arquitetura](arquitetura-reservas.md).

---

## Convenções

**Definição de pronto:** o critério de aceite do item está satisfeito, os testes correspondentes passam, `composer ci:check` está verde e a documentação afetada foi atualizada no mesmo pull request.
