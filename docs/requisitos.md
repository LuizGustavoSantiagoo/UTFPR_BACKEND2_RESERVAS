# Levantamento e Priorização de Requisitos

> Sistema de Reserva de Ambientes - UTFPR, Desenvolvimento de Aplicações Backend com Framework.
> Autor: Luiz Gustavo de Oliveira Santiago.
> Versão 1.3, de 26/09/2026.

## 1. Propósito

Este documento registra os requisitos do sistema, sua priorização por MoSCoW e a rastreabilidade entre cada requisito, a sprint que o implementa e a forma de verificá-lo. Ele é a fonte de verdade sobre **o que** o sistema faz; as decisões de **como** estão em [arquitetura-reservas.md](arquitetura-reservas.md), e o cronograma em [sprints.md](sprints.md).

## 2. Método de levantamento

### 2.1 Fontes documentais

| Fonte | Contribuição |
|---|---|
| Rascunho MoSCoW do autor | Os sete requisitos de alto nível e sua priorização - base desta versão |
| Entrevista com o autor, 26/09/2026 | Hierarquia bloco -> ambiente -> subambiente, perfil de responsável de bloco, grade única da universidade e reserva direta - ver seção 9, divergências D4 a D6 |
| [README.md](../README.md) | Motivação, objetivos específicos e funcionalidades planejadas |
| [arquitetura-reservas.md](arquitetura-reservas.md) seção 3.3 | Regras de negócio R1-R24, aqui renumeradas como RN01-RN24 |
| [fluxos.md](fluxos.md) | Cenários de uso; cada decisão dos diagramas foi convertida em requisito |
| [STYLE_GUIDE.md](../STYLE_GUIDE.md) | Restrições técnicas de interface, convertidas em requisitos não funcionais |

### 2.2 Técnicas aplicadas

- **Análise de documentos**: leitura do material acima e extração dos requisitos implícitos.
- **Entrevista**: sessão com o autor, no papel de responsável pelo produto, para detalhar a estrutura física, os perfis e a grade. As respostas estão registradas na seção 9.
- **Priorização MoSCoW**: classificação em Must, Should, Could e Won't, feita pelo autor.
- **Derivação por dependência**: requisitos não citados no rascunho mas sem os quais um requisito Must não se sustenta (por exemplo: não há "responsável defere" sem alguém que nomeie o responsável).

### 2.3 Técnicas pendentes

A elicitação com o responsável real pelos espaços da instituição ainda não foi feita. As perguntas em aberto estão na [seção 10](#10-lacunas-de-elicitação) e podem alterar a priorização desta versão.

### 2.4 Rascunho original

Os sete itens levantados e priorizados pelo autor, transcritos literalmente:

| # | Item | Prioridade |
|---|---|---|
| 1 | Admin criar, editar, apagar e ver reservas | MUST |
| 2 | Usuário ver e solicitar reserva | MUST |
| 3 | Sistema verificar e impedir conflito na solicitação de reservas | MUST |
| 4 | Admin visualizar reservas de usuários e deferir ou indeferir | MUST |
| 5 | Notificações por e-mail | SHOULD |
| 6 | Verificações de feriados | SHOULD |
| 7 | Dashboard com ambientes mais usado | WON'T |

Cada requisito das seções seguintes indica em **Origem** de qual desses itens ele deriva, se foi derivado por dependência, ou se nasceu da entrevista de 26/09 (D4, D5, D6).

## 3. Escopo

O sistema centraliza o cadastro da estrutura física da instituição (blocos, ambientes e subambientes) e o ciclo de vida das reservas: solicitação, deferimento, acompanhamento e cancelamento. A decisão sobre cada solicitação é descentralizada: cada bloco tem um responsável, que defere ou indefere os pedidos do seu bloco e pode reservar diretamente. Toda reserva respeita uma grade de funcionamento única, da universidade.

Substitui o processo informal por e-mail e planilha, dando visibilidade da disponibilidade e garantindo, no banco de dados, que nunca existam duas reservas deferidas para o mesmo lugar e horário.

**Fora do escopo desta versão:** reservas recorrentes, fila de espera, check-in e no-show, integração com calendários externos, múltiplas instituições e aplicativo móvel.

## 4. Atores

| Ator | Descrição | Papel no sistema |
|---|---|---|
| **Usuário comum** | Aluno, professor ou servidor, com conta criada por cadastro público | Consulta ambientes e disponibilidade, solicita e cancela as próprias reservas |
| **Responsável de bloco** | Servidor nomeado pelo administrador para um ou mais blocos; conta criada pelo administrador | No seu bloco: defere e indefere solicitações, reserva diretamente com justificativa, cancela reservas com motivo, cadastra fechamentos e acompanha a agenda. Não solicita reservas |
| **Administrador** | Responsável pela estrutura e pelos acessos; conta criada por outro administrador ou pelo seeder | Cadastra blocos, ambientes e subambientes; define o que aceita reserva; nomeia responsáveis; cria contas de responsável e de administrador; edita a grade; e faz, em qualquer bloco, tudo o que um responsável faz |
| **Sistema** | A própria aplicação | Executa verificações automáticas de conflito, prazo e disponibilidade, indefere concorrentes em cascata e dispara notificações |

## 5. Requisitos funcionais

Prioridade conforme MoSCoW (seção 11). A coluna Origem indica o item do rascunho, a fonte da derivação ou a divergência da seção 9 que o originou.

### 5.1 Autenticação e controle de acesso

| ID | Requisito | Prio. | Origem |
|---|---|---|---|
| RF01 | Must | Derivado do README |
| RF02 | Must | Derivado de RN01 |
| RF03 | Must | Derivado do README |
| RF04 | Must | D4 |
| RF05 | Must | D4 |
| RF06 | Must | Derivado de RN02, RN17 |

### 5.2 Estrutura física

| ID | Requisito | Prio. | Origem |
|---|---|---|---|
| RF07 | Must | D4 |
| RF08 | Must | Item 1 + D4 |
| RF09 | Must | D4 |
| RF10 | Must | Item 1 |
| RF11 | Must | Item 1 + RN13 |
| RF12 | Must | Item 1 + RN14 |

### 5.3 Grade de funcionamento

| ID | Requisito | Prio. | Origem |
|---|---|---|---|
| RF13 | Must | D5 |
| RF14 | Must | D5 |
| RF15 | Should | Derivado de RN15 |

### 5.4 Disponibilidade

| ID | Requisito | Prio. | Origem |
|---|---|---|---|
| RF16 | Must | Item 2 + D4 |
| RF17 | Must | Item 2 |
| RF18 | Should | Item 6 |
| RF19 | Should | Item 2 |

### 5.5 Solicitação de reserva

| ID | Requisito | Prio. | Origem |
|---|---|---|---|
| RF20 | Must | Item 2 |
| RF21 | Must | Item 3 |
| RF22 | Must | Item 3 + D1 |
| RF23 | Must | Item 3 + D4 |
| RF24 | Must | Item 3 |
| RF25 | Must | Derivado da arquitetura, seção 4.4 |

### 5.6 Acompanhamento e cancelamento

| ID | Requisito | Prio. | Origem |
|---|---|---|---|
| RF26 | Must | Item 2 |
| RF27 | Must | Item 2 + RN11 |
| RF28 | Should | Derivado de RN11 |
| RF29 | Must | Derivado de RN16 |

### 5.7 Decisão e administração de reservas

| ID | Requisito | Prio. | Origem |
|---|---|---|---|
| RF30 | Must | Item 4 + D4 |
| RF31 | Must | Item 4 + D1 |
| RF32 | Must | Item 4 + D4 |
| RF33 | Must | Item 4 + D1 + D4 |
| RF34 | Must | Item 4 |
| RF35 | Must | Item 1 + RN12 |
| RF36 | Must | Item 1 + D6 |
| RF37 | Must | D6 |
| RF38 | Must | Item 1 |
| RF39 | Must | Item 4 |

### 5.8 Fechamentos

| ID | Requisito | Prio. | Origem |
|---|---|---|---|
| RF40 | Should | Item 6 + D4 |
| RF41 | Should | Item 6 |
| RF42 | Should | Item 6 |
| RF43 | Should | Item 6 |

### 5.9 Notificações

| ID | Requisito | Prio. | Origem |
|---|---|---|---|
| RF44 | Should | Item 5 |
| RF45 | Should | Item 5 |
| RF46 | Should | Item 5 |
| RF47 | Should | Item 5 |

### 5.10 Painel

| ID | Requisito | Prio. | Origem |
|---|---|---|---|
| RF48 | Could | Derivado do README |
| RF49 | Could | Derivado do item 4 |
| RF50 | Won't | Item 7 |

## 6. Requisitos não funcionais

| ID | Categoria | Requisito | Prio. |
|---|---|---|---|
| RNF01 | A exclusividade entre reservas deferidas deve ser garantida pelo banco de dados, por restrição de unicidade slot a slot, e não apenas por verificação na aplicação | Must |
| RNF02 | A grade de disponibilidade de um dia deve ser montada com um número fixo de consultas ao banco, independentemente da quantidade de horários exibidos | Must |
| RNF03 | A tela de disponibilidade deve responder em até 1 segundo para uma consulta de até 31 dias | Should |
| RNF04 | As senhas devem ser armazenadas com algoritmo de hash, nunca em texto claro | Must |
| RNF05 | Toda rota de gestão ou decisão deve verificar no servidor o perfil do usuário e, para o responsável, o vínculo com o bloco, sem depender da ocultação de elementos na interface | Must |
| RNF06 | A identidade do titular de uma reserva não deve ser exposta a usuários comuns | Should |
| RNF07 | Nenhuma reserva deve ser removida fisicamente; o cancelamento é lógico e preserva data e autor | Must |
| RNF08 | Falha no envio de notificação não deve invalidar um deferimento já registrado | Should |
| RNF09 | As telas devem ser utilizáveis em largura de 360 px, com o conteúdo largo rolando dentro do próprio contêiner | Should |
| RNF10 | A interface deve funcionar nos temas claro e escuro, respeitando a preferência do sistema do usuário | Should |
| RNF11 | Os controles devem ter rótulo associado, foco visível e contraste mínimo de 4,5:1 para texto | Should |
| RNF12 | A interface deve usar um único idioma (pt-BR), com todo texto passando pela função de tradução | Should |
| RNF13 | O ambiente de desenvolvimento deve subir por Docker, com PHP 8.3 e MySQL, sem instalação manual de dependências | Must |
| RNF14 | A regra de disponibilidade deve existir em um único ponto do código, nunca duplicada entre interface e servidor | Must |
| RNF15 | Toda alteração deve passar na verificação automatizada de estilo, análise estática e testes antes de ser integrada | Must |
| RNF16 | Os instantes de início e fim das reservas devem ser armazenados em UTC e exibidos no fuso America/Sao_Paulo; horas da grade e dos slots são horas locais, sem fuso | Must |
| RNF17 | O deferimento de uma solicitação (ou o registro de uma reserva direta) e o indeferimento das concorrentes devem ocorrer na mesma transação: ou tudo acontece, ou nada | Must |

## 7. Regras de negócio

Restrições do domínio, independentes da tecnologia. Correspondem a R1-R24 de [arquitetura-reservas.md](arquitetura-reservas.md) seção 3.3; RN17 a RN20 nasceram da decisão sobre o deferimento (D1) e RN21 a RN24 da entrevista de 26/09 (D4 a D6).

| ID | Regra |
|---|---|
| RN01 | Apenas usuário autenticado pode reservar |
| RN02 | Apenas administrador cria, edita ou remove bloco, ambiente, subambiente e a grade de funcionamento |
| RN03 | Toda reserva ocupa slots inteiros da grade da universidade, dentro da janela de funcionamento do dia |
| RN04 | Bloco não recebe reserva; só ambiente ou subambiente marcado como reservável |
| RN05 | Não é permitido reservar data e hora já passadas |
| RN06 | A solicitação de usuário deve respeitar a antecedência mínima definida no lugar |
| RN07 | A solicitação de usuário não pode ultrapassar a janela máxima futura definida no lugar |
| RN08 | A solicitação de usuário não pode exceder o número máximo de horários contíguos definido no lugar |
| RN09 | O mesmo usuário não pode ter duas reservas ativas sobrepostas, ainda que em lugares diferentes |
| RN10 | O usuário comum está sujeito a um limite de reservas ativas futuras |
| RN11 | O usuário só pode cancelar a própria reserva até um prazo definido antes do início |
| RN12 | O responsável, no seu bloco, e o administrador, em qualquer bloco, podem cancelar qualquer reserva, a qualquer momento, mediante motivo |
| RN13 | Lugar desativado não aparece na busca, mas mantém válidas as reservas futuras já registradas |
| RN14 | Lugar com reservas registradas não pode ser excluído |
| RN15 | Alterar a grade não invalida reservas existentes automaticamente: o sistema lista os conflitos e o administrador decide |
| RN16 | Reserva cancelada ou indeferida preserva data, autor e motivo da decisão, sem exclusão física |
| RN17 | O administrador defere ou indefere em qualquer bloco; o responsável, apenas no bloco ao qual está vinculado |
| RN18 | Solicitação pendente não ocupa o horário: o horário só deixa de aceitar novas solicitações quando alguma é deferida |
| RN19 | Deferir uma solicitação indefere todas as pendentes que compartilhem slot com ela (no mesmo lugar, no ambiente-pai ou nos subambientes), com a justificativa de que outra foi atendida |
| RN20 | Só uma solicitação pendente pode ser deferida; uma já decidida não volta ao fluxo |
| RN21 | Reservar o ambiente ocupa todos os seus subambientes no horário; reservar um subambiente impede reservar o ambiente inteiro no horário; subambientes irmãos não se afetam |
| RN22 | Responsável e administrador não solicitam reservas: reservam diretamente, com justificativa obrigatória, sujeitos à mesma exclusividade e à mesma cascata, e sem os limites RN06 a RN08 e RN10 |
| RN23 | A hierarquia física tem no máximo dois níveis abaixo do bloco: um subambiente não contém outro |
| RN24 | Um fechamento tem escopo (câmpus, bloco ou lugar) e alcança todos os lugares abaixo dele |

## 8. Matriz de rastreabilidade

Relaciona cada grupo de requisitos à sprint que o implementa e à forma de verificação.

| Requisitos | Sprint | Verificação |
|---|---|---|
| RF01-RF03, RNF04 | 3 | Cadastro público cria `user`; usuário comum recebe 403 em rota de gestão |
| RF04-RF06, RNF05 | 3 | Responsável de um bloco recebe 403 ao deferir em outro bloco |
| RF07-RF12 | 1, 3 | CRUD de bloco, ambiente e subambiente; excluir lugar com reserva é bloqueado; subambiente de subambiente é recusado |
| RF13-RF15, RN15 | 1, 3 | Grade seg a sex 06h às 23h produz 17 slots por dia e nenhum no sábado; encurtar a grade com reserva às 22h exige decisão |
| RF16-RF19, RNF02, RNF14 | 2, 4 | Reserva deferida no ambiente remove o slot de todos os subambientes; deferida no subambiente remove o slot do ambiente |
| RF20, RF25 | 2, 4 | Fluxo completo de solicitação com dois slots contíguos |
| RF21-RF24, RNF01 | 1, 2 | Teste de concorrência em MySQL: dois deferimentos simultâneos do mesmo slot - um vence, o outro recebe conflito; idem para ambiente vs. subambiente |
| RF26-RF29, RNF07 | 2, 4 | Cancelar libera o slot; registro permanece consultável |
| RF30, RF35, RF39 | 3 | Agenda filtrada pelo bloco do responsável; cancelamento exige motivo |
| RF31-RF34, RNF17, RN17-RN21 | 2 | Três solicitações no mesmo slot: ao deferir uma, as outras ficam indeferidas com justificativa; solicitação no subambiente é indeferida ao deferir o ambiente |
| RF36-RF37, RN22 | 2 | Reserva direta em slot com solicitações pendentes as indefere; reserva direta em slot já deferido recebe conflito |
| RF38 | - | Ver divergência D2 |
| RF40-RF43, RN24 | 1, 3 | Fechamento do câmpus remove todos os slots da data em todos os lugares; fechamento de bloco não afeta outro bloco |
| RF44-RF47, RNF08 | 4 | Testes com simulação de envio de notificação |
| RF48-RF49 | 4 | Teste de feature do painel por perfil |
| RF50 | - | Ver divergência D3 |
| RNF09-RNF12 | 5 | Revisão de acessibilidade e responsividade |
| RNF13, RNF15 | - | Verificação automatizada verde contra MySQL no GitHub Actions |
| RNF16 | 1, 4 | Teste de exibição de horário em fuso local |

## 9. Divergências com o backlog atual

O planejamento inicial deste repositório foi montado antes desta priorização e assumia um fluxo sem deferimento, um único perfil de gestão e uma grade por ambiente. Três decisões posteriores (D1 em 07/09, D4 a D6 em 26/09) mudaram cada uma dessas premissas.

### D1: O deferimento voltou a ser obrigatório

O item 4 do rascunho torna o deferimento um requisito Must, mas o planejamento inicial o tratava como evolução futura e previa reduzir os status da reserva a apenas confirmada e cancelada. A priorização do rascunho prevalece.

**Decisão do responsável pelo produto (07/09/2026), em resposta a L01:**

> A solicitação pendente **não** bloqueia o horário. Vários usuários podem solicitar o mesmo horário. Quando uma solicitação é deferida, as demais são indeferidas com a justificativa de que a primeira solicitação foi atendida.

**Consequências:**

- O status da reserva contempla pendente, deferida, indeferida e cancelada.
- O deferimento deixa de ser backlog e passa a integrar a Sprint 2, com prioridade Must.
- `active_key` só é preenchido no **deferimento**. Enquanto pendente, indeferida ou cancelada, permanece nulo - por isso várias solicitações concorrentes convivem sem violar a restrição de unicidade.
- A trava de concorrência muda de lugar: a corrida deixa de ser entre dois usuários solicitando e passa a ser entre duas decisões simultâneas. A transação com bloqueio e a restrição de unicidade continuam sendo a defesa, mas atuam no deferimento.
- Deferir e indeferir as concorrentes é uma operação atômica (RNF17): as duas coisas acontecem na mesma transação, ou nenhuma acontece.
- Verificar disponibilidade na solicitação continua necessário, mas contra reservas **deferidas** apenas (RF21).

### D2: Editar reservas de terceiros

O item 1 do rascunho atribui ao administrador criar, editar, apagar e ver reservas. "Criar" foi esclarecido na entrevista de 26/09 como a **reserva direta** (D6, RF36). Resta "editar": alterar o horário ou o lugar de uma reserva existente (RF38) não está em nenhuma sprint.

Observação: editar uma reserva existente é, na prática, cancelar e recriar - precisa passar pelas mesmas verificações de conflito, prazo e grade, e não pode contornar a trava de unicidade.

### D3: Ambientes mais utilizados

O item 7 do rascunho classifica o relatório de ambientes mais utilizados como Won't. O planejamento inicial o incluía entre as tarefas do painel. Ele sai do escopo, e o painel mantém apenas o resumo operacional (reservas do dia, solicitações pendentes).

### D4: Hierarquia física e responsável de bloco

**Decisão do responsável pelo produto (26/09/2026):**

> O sistema será dividido em três níveis: admin, responsável de bloco e usuários. O admin decide quais blocos, ambientes e subambientes podem ser reservados, quem é responsável por cada bloco, cria blocos, ambientes e subambientes e cria acessos ao sistema. O responsável só defere ou indefere no bloco vinculado a ele. Cadastro público para usuários; o admin cria responsáveis e admins.

**Consequências:**

- Entram as tabelas `blocks` e `block_user`; `places` ganha `block_id`, `parent_id` e `is_reservable`. O campo `bloco` da migration original, que era texto, volta como chave estrangeira.
- Ambiente e subambiente vivem na mesma tabela; a profundidade é limitada a dois níveis (RN23).
- `role` ganha o valor `manager`. A autorização para decidir consulta `block_user`, não só o perfil (RNF05).
- Reservar o ambiente ocupa os subambientes e vice-versa (RN21). A cascata de indeferimento (RN19) e a verificação de conflito (RF23) passam a considerar pai e filhos.
- Entram duas tarefas novas: CRUD de blocos com nomeação de responsáveis, e criação de contas de responsável e administrador.
- O cadastro público continua criando apenas `user` - a tela de registro não muda.

### D5: Grade única da universidade

**Decisão do responsável pelo produto (26/09/2026):**

> A universidade tem uma grade de horários e as reservas devem respeitar essa grade; todos os blocos usam essa grade. Por padrão, de segunda a sexta, das 06:00 às 23:00.

**Consequências:**

- `place_schedules` sai. Entra `operating_hours`: sete linhas globais, uma por dia da semana, com a janela de funcionamento. Nenhum lugar tem grade própria.
- `slot_minutes` sai de `places`. O slot é fixo em 60 minutos (RF14) - constante de configuração, não editável na tela.
- O motor de disponibilidade deixa de gerar slots por ambiente e passa a subtrair, de um conjunto fixo de 17 horários, os fechamentos e as reservas deferidas. A aritmética de intervalos e o teste de fronteira entre slots adjacentes perdem a razão de existir: cada slot é uma chave.
- Não há mais "grade semanal por ambiente" para modelar nem "editor de grade" para construir; há uma tela única de janela por dia da semana.
- `place_exceptions` vira `closures`, com escopo câmpus, bloco ou lugar (RN24) - um feriado é uma linha, não uma por ambiente.

### D6: Reserva direta

**Decisão do responsável pelo produto (26/09/2026):**

> O responsável não faz reservas e as defere; ele pode escolher diretamente data e ambiente ou subambiente e marcar como reservado, com uma justificativa.

**Consequências:**

- Responsável e administrador não usam o fluxo de solicitação (RN22). O fluxo de solicitação passa a ser exclusivo do usuário comum (RF20).
- A reserva direta nasce deferida, com `origin = direct`, `reviewed_by` igual ao titular e justificativa obrigatória em `notes` (RF36).
- Ela passa pela mesma transação com trava e dispara a mesma cascata de um deferimento (RF37, RNF17). Os limites de antecedência, janela e quantidade de slots (RN06-RN08, RN10) não se aplicam.
- RF32 da versão anterior, "criar reserva deferida em nome de outro usuário", foi reinterpretado como a reserva direta - o rascunho dizia apenas "criar reservas". Reservar em nome de terceiros permanece como lacuna (L03).
- Uma tarefa nova: reserva direta com justificativa e cascata.

## 10. Lacunas de elicitação

Perguntas em aberto que dependem do responsável real pelos espaços. Cada resposta pode alterar a priorização desta versão.

| # | Pergunta | Impacto |
|---|---|---|
| ~~L01~~ | ~~Um horário solicitado e ainda pendente fica bloqueado para outros usuários?~~ **Respondida em 07/09/2026:** não bloqueia; o deferimento de uma solicitação indefere as concorrentes | Resolvida - ver D1 e requisitos RF22, RF23 e RF33 |
| L02 | Existe prioridade entre finalidades de uso (aula regular, evento institucional, uso individual)? | Pode introduzir um requisito de sobreposição autorizada, hoje inexistente. A reserva direta (D6) cobre parcialmente o caso do evento institucional |
| L03 | Um professor pode reservar em nome de uma turma ou de terceiros? Um responsável pode registrar uma reserva direta em nome de um usuário? | Amplia RF36 com um campo de titular distinto de quem registra |
| L04 | Qual é o prazo-limite para o usuário cancelar sem intervenção do responsável? | Parametriza RN11 |
| L05 | Quantas reservas futuras simultâneas um usuário pode manter? E quantas solicitações pendentes? | Parametriza RN10; com a decisão de D1, o limite de pendentes evita que um usuário solicite todos os horários do dia |
| L06 | Os feriados devem ser cadastrados manualmente ou importados de um calendário oficial? | Define se o item 6 do rascunho se limita a RF40-RF43 ou exige integração externa |
| ~~L07~~ | ~~Reservas fora do horário de funcionamento do câmpus são permitidas em algum caso?~~ **Respondida em 26/09/2026:** não; a grade é única e o administrador a altera quando necessário | Resolvida - ver D5 e RF13 |
| L08 | Qual o processo atual (e-mail, planilha, formulário) e onde ele trava com mais frequência? | Valida se a priorização MoSCoW reflete a dor real |
| L09 | Existe prazo máximo para o responsável decidir sobre uma solicitação pendente? | Sem prazo, uma solicitação esquecida permanece pendente indefinidamente enquanto o horário segue disponível a outros |
| L10 | Se um responsável precisar de um lugar de **outro** bloco, como ele reserva? | Hoje não há caminho: o responsável não solicita (RN22) e só reserva diretamente no próprio bloco. Pode exigir que o responsável também possa solicitar fora do seu bloco |
| L11 | O slot de 60 minutos está correto, ou a instituição reserva em períodos de aula (50 minutos)? | Assumido 60 minutos em 26/09 porque a janela 06h às 23h fecha em 17 slots exatos. Se for 50, a janela precisa ser redefinida em múltiplos de 50 |

## 11. Distribuição da priorização

| Prioridade | Requisitos funcionais | Proporção |
|---|---|---|
| Must | 35 | 70% |
| Should | 12 | 24% |
| Could | 2 | 4% |
| Won't | 1 | 2% |

A proporção de Must está acima da faixa recomendada (até 60%) porque o escopo mínimo da disciplina exige autenticação, estrutura física, solicitação, deferimento, reserva direta e verificação de conflito funcionando de ponta a ponta, e porque a entrevista de 26/09 trouxe oito requisitos novos, todos estruturais. Caso o prazo aperte, os candidatos naturais a rebaixamento são RF38 (editar reserva existente), que não bloqueia nenhum outro requisito, e RF31 (comparar solicitações concorrentes lado a lado), que é conforto de interface sobre uma lista que já existe.

## 12. Histórico de revisões

| Versão | Data | Alteração |
|---|---|---|
| 1.0 | 07/09/2026 | Versão inicial, a partir do rascunho MoSCoW do autor e da documentação existente |
| 1.1 | 07/09/2026 | Decisão sobre L01: solicitação pendente não bloqueia o horário e o deferimento indefere as concorrentes. Inclusão de RF18, RF19, RF27, RF29 e RNF17; renumeração da seção 5 |
| 1.2 | 07/09/2026 | Revisão de consistência entre os documentos: inclusão das regras RN17 a RN20, ajuste de RN16 para cobrir o indeferimento e alinhamento com a arquitetura e os diagramas de fluxo revisados |
| 1.3 | 26/09/2026 | Entrevista com o autor: hierarquia bloco -> ambiente -> subambiente, perfil de responsável de bloco, grade única da universidade e reserva direta (D4 a D6). Novo ator; RF04, RF05, RF07, RF09, RF13, RF14, RF36 e RF37 incluídos; RF32 reinterpretado; RN21 a RN24 incluídas; RN02, RN03, RN04, RN12, RN17 e RN19 reescritas; L07 resolvida; L10 e L11 abertas; renumeração da seção 5 |
