# Sistema de Reserva de Ambientes: Diagramas de Fluxo

> Complementa o [relatório de arquitetura](arquitetura-reservas.md) e o [levantamento de requisitos](requisitos.md).
> Documento de análise. Não contém código-fonte.

O fluxo aqui representado incorpora as decisões de 07/09/2026 (a solicitação nasce **pendente** e não bloqueia o horário; deferir uma indefere as concorrentes, D1) e de 26/09/2026 (hierarquia bloco -> ambiente -> subambiente, perfil de **responsável de bloco**, grade única da universidade e **reserva direta**, D4 a D6). Ver [requisitos.md](requisitos.md) seção 9.

Três perfis, três fluxos: administrador (1.1), responsável de bloco (1.2) e usuário comum (1.3). As sequências 1.4 a 1.8 detalham o motor de disponibilidade, a concorrência e cada perfil.

---

## 1.1 Fluxo do Administrador

```mermaid
flowchart TD
    A([Admin acessa /login]) --> B[Fortify autentica]
    B --> C{role == admin?}
    C -- Não --> D[Redireciona para a área do perfil]
    C -- Sim --> E[Painel administrativo]

    E --> F{Ação}

    F -- Estrutura --> G[Blocos]
    G --> G1[Criar bloco: nome, sigla, descrição]
    G1 --> G2[Ambientes do bloco]
    G2 --> G3[Criar ambiente: nome, tipo,<br/>capacidade, reservável?]
    G3 --> G4[Subambientes do ambiente]
    G4 --> G5[Criar subambiente: nome, tipo,<br/>capacidade, reservável?]
    G5 --> G6{Validações}
    G6 -- pai já é subambiente --> G5
    G6 -- nome repetido entre irmãos --> G5
    G6 -- OK --> G7[(Persiste Place<br/>block_id = do pai)]
    G7 --> G

    F -- Responsáveis --> H[Escolhe o bloco]
    H --> H1[Seleciona usuários com role = manager]
    H1 --> H2[(Substitui block_user do bloco)]
    H2 --> E

    F -- Contas --> I[Criar conta: nome, e-mail,<br/>data de nascimento, perfil]
    I --> I1{Perfil}
    I1 -- manager ou admin --> I2[(Persiste User<br/>com o perfil escolhido)]
    I1 -- user --> I3[Recusa: usuário comum<br/>se cadastra sozinho]
    I2 --> E

    F -- Grade --> J[Sete linhas: dia da semana,<br/>aberto?, abre às, fecha às]
    J --> J1{Existem reservas deferidas<br/>fora da nova janela?}
    J1 -- Sim --> J2[Lista os conflitos:<br/>admin mantém ou cancela + notifica]
    J1 -- Não --> J3[(Persiste operating_hours)]
    J2 --> J3
    J3 --> E

    F -- Desativar --> K[is_active = false no bloco ou lugar<br/>Some da busca, reservas futuras preservadas]
    K --> E

    F -- Tudo que o responsável faz,<br/>em qualquer bloco --> L[[Ver 1.2]]
```

## 1.2 Fluxo do Responsável de Bloco

```mermaid
flowchart TD
    A([Responsável acessa /login]) --> B[Fortify autentica]
    B --> C{role == manager?}
    C -- Não --> D[Redireciona para a área do perfil]
    C -- Sim --> E[Painel do responsável<br/>escopo: blocos em block_user]

    E --> F{Ação}

    F -- Fila de pendentes --> G[Solicitações pendentes do bloco,<br/>agrupadas por lugar, data e slot]
    G --> G1[Concorrentes do mesmo slot<br/>lado a lado]
    G1 --> G2{Decisão}
    G2 -- Indeferir --> G3[(status = rejected<br/>+ motivo + reviewed_by)]
    G3 --> G4[Notifica o solicitante]
    G2 -- Deferir --> H[[Transação de decisão<br/>ver 1.5]]

    F -- Reservar diretamente --> I[Escolhe lugar do bloco,<br/>data e slots]
    I --> I1[Justificativa obrigatória]
    I1 --> I2[(INSERT reservation<br/>origin = direct)]
    I2 --> H

    H --> H1{Slot deferido no lugar,<br/>pai ou filhos?}
    H1 -- Sim --> H2[Conflito: outra decisão<br/>chegou primeiro]
    H1 -- Não --> H3[(approved + active_key = 1<br/>em cada slot)]
    H3 --> H4[(Pendentes que compartilham slot<br/>na árvore ficam rejected)]
    H4 --> H5[Notifica contemplado<br/>e indeferidos]

    F -- Fechar período --> J[Escopo: o bloco ou um lugar dele<br/>data, faixa opcional, motivo]
    J --> J1[(Persiste closure)]
    J1 --> J2[Reservas atingidas são canceladas<br/>+ notificação]

    F -- Agenda --> K[Ocupação dos lugares do bloco<br/>com titular e origem]
    K --> K1[Cancela reserva deferida,<br/>com motivo]
    K1 --> K2[(status = cancelled, cancelled_by<br/>active_key = NULL)]

    F -- Solicitar reserva --> L[Bloqueado: responsável<br/>não solicita, R22]
```

## 1.3 Fluxo do Usuário Comum

```mermaid
flowchart TD
    A([Usuário acessa o sistema]) --> B{Autenticado?}
    B -- Não --> C[Login / Cadastro público<br/>cria role = user] --> D
    B -- Sim --> D[Lista de lugares reserváveis e ativos]

    D --> E[Filtros: bloco, nome, tipo,<br/>capacidade mínima, data]
    E --> F[Seleciona um ambiente<br/>ou subambiente]
    F --> G[Escolhe uma data]

    G --> H[[Motor de Disponibilidade]]
    H --> H1[1. Lê operating_hours do dia da semana]
    H1 --> H1a{Aberto?}
    H1a -- Não --> H1b[Grade vazia: fechado]
    H1a -- Sim --> H2[2. Gera os slots de 60 min<br/>entre abre às e fecha às]
    H2 --> H3[3. Remove slots fechados:<br/>câmpus, bloco, lugar ou pai]
    H3 --> H4[4. Remove slots com reserva DEFERIDA<br/>no lugar, no pai ou nos filhos]
    H4 --> H5[5. Remove slots no passado<br/>ou dentro da antecedência mínima]
    H5 --> I[Renderiza: livre / ocupado / fechado / passado<br/>livres mostram nº de pendentes]

    I --> J{Escolhe slots livres contíguos}
    J --> K[Confirmação: lugar, data,<br/>início, fim, justificativa]
    K --> L[[Registro da solicitação]]

    L --> M{Validação no servidor}
    M -- Slot fora da grade --> N[Erro: horário inválido]
    M -- Já existe reserva deferida --> O[Erro: horário indisponível<br/>Recarrega a grade]
    M -- Excede limite do lugar ou do usuário --> P[Erro: limite atingido]
    M -- OK --> Q[(INSERT reservation status = pending<br/>+ N reservation_slots active_key = NULL)]

    Q --> R[E-mail: solicitação registrada]
    R --> S[Minhas Reservas, aguardando decisão]

    S --> T{Desfecho}
    T -- Responsável defere --> U[E-mail: deferida]
    T -- Responsável indefere --> V[E-mail: indeferida, com justificativa]
    T -- Outra solicitação foi deferida<br/>ou o lugar foi reservado diretamente --> W[E-mail: indeferida automaticamente]

    U --> X{Cancelar?}
    X -- Sim, dentro do prazo --> Y[status = cancelled<br/>active_key = NULL, slots voltam a ficar livres]
    X -- Fora do prazo --> Z[Bloqueado: só o responsável cancela]
```

## 1.4 Sequência: cálculo de disponibilidade

```mermaid
sequenceDiagram
    autonumber
    participant U as Navegador (Livewire)
    participant C as Componente
    participant S as AvailabilityService
    participant DB as Banco

    U->>C: selecionar lugar + data
    C->>S: slotsDisponiveis(place, data)
    S->>DB: SELECT operating_hours WHERE weekday = N
    DB-->>S: is_open, opens_at, closes_at
    alt fechado no dia
        S-->>C: [] (grade vazia)
    else aberto
        S->>S: gera slots de 60 min, de opens_at até closes_at
        S->>DB: SELECT closures WHERE date = D<br/>AND escopo alcança o lugar (câmpus, bloco, lugar ou pai)
        DB-->>S: fechamentos
        S->>DB: SELECT starts_at FROM reservation_slots<br/>WHERE date = D AND active_key = 1<br/>AND place_id IN (lugar, pai, filhos)
        DB-->>S: slots ocupados
        S->>S: disponíveis = slots - fechados - ocupados - passados
        S-->>C: coleção de slots com status e nº de pendentes
    end
    C-->>U: grade renderizada
```

> Três consultas, sempre três, independentemente de quantos slots o dia tem (RNF02). Solicitações pendentes não removem o slot - elas apenas se acumulam para a decisão do responsável.

## 1.5 Sequência: concorrência na decisão

A corrida não acontece entre dois usuários solicitando: solicitações pendentes convivem. Ela acontece entre duas **decisões** (dois deferimentos, um deferimento e uma reserva direta, ou duas reservas diretas) e, com a hierarquia, pode ser entre o ambiente e um subambiente dele.

```mermaid
sequenceDiagram
    autonumber
    actor R1 as Responsável
    actor R2 as Admin
    participant SVC as ReservationService
    participant DB as Banco

    Note over R1,R2: solicitação pendente de X no Auditório às 14h<br/>e solicitação pendente de Y na Sala de Apoio (filha do Auditório) às 14h

    par Decisões simultâneas
        R1->>SVC: deferir solicitação de X (Auditório)
    and
        R2->>SVC: deferir solicitação de Y (Sala de Apoio)
    end

    SVC->>DB: BEGIN
    SVC->>DB: lockForUpdate no ambiente-raiz, o Auditório, nos dois casos
    Note over SVC,DB: a segunda decisão espera aqui,<br/>mesmo sendo sobre a Sala de Apoio
    SVC->>DB: existe reservation_slot ativo às 14h<br/>no Auditório, no pai ou nos filhos?
    SVC->>DB: UPDATE reserva de X = approved, slots active_key = 1
    SVC->>DB: UPDATE pendentes às 14h na árvore = rejected (inclui a de Y)
    SVC->>DB: COMMIT
    SVC-->>R1: deferida

    Note over R2,DB: a segunda adquire o lock e revalida
    SVC->>DB: solicitação de Y ainda pending?
    DB-->>SVC: não, foi indeferida em cascata
    SVC->>DB: ROLLBACK
    SVC-->>R2: 422, a solicitação já foi decidida
    Note over DB: UNIQUE (place_id, date, starts_at, active_key) é a rede de segurança<br/>para o mesmo lugar. Para pai vs. filho, só o lock na raiz protege
```

## 1.6 Sequência: perfil `user` (usuário comum)

```mermaid
sequenceDiagram
    autonumber
    actor U as Usuário comum
    participant LW as Componente Livewire
    participant POL as ReservationPolicy
    participant SVC as ReservationService
    participant DB as Banco
    participant MAIL as Notificação

    Note over U,MAIL: role = user, enxerga lugares ativos e reserváveis, e as próprias reservas

    U->>LW: cadastro público ou login + acessa /lugares
    LW->>DB: SELECT places WHERE is_active AND is_reservable
    DB-->>LW: lugares com bloco e pai
    LW-->>U: lista com filtros

    U->>LW: escolhe lugar + data
    LW->>SVC: slotsDisponiveis(place, data)
    SVC->>DB: operating_hours + closures + reservation_slots da árvore
    DB-->>SVC: dados
    SVC-->>LW: slots livre / ocupado / fechado / passado
    LW-->>U: grade do dia

    U->>LW: seleciona slots contíguos e solicita
    LW->>POL: authorize request Reservation
    alt é manager ou admin (R22)
        POL-->>LW: deny
        LW-->>U: seu perfil reserva diretamente
    else limite de reservas ativas atingido (R10)
        POL-->>LW: deny
        LW-->>U: limite atingido
    else autorizado
        POL-->>LW: allow
        LW->>SVC: request(place, date, starts_at, slots, notes)
        SVC->>SVC: gera os N slots, nunca aceita ends_at do cliente
        SVC->>DB: valida R3-R10 + algum slot ativo na árvore?
        alt já existe reserva deferida
            SVC-->>LW: horário indisponível
            LW-->>U: recarrega a grade
        else regra violada
            SVC-->>LW: erro de validação + campo
            LW-->>U: mensagem do campo
        else tudo válido
            SVC->>DB: INSERT reservation pending + N reservation_slots (active_key NULL)
            SVC->>MAIL: ReservationRequested
            MAIL-->>U: e-mail de solicitação registrada
            LW-->>U: aguardando decisão do responsável
        end
    end

    Note over U,MAIL: o responsável decide (ver 1.7)

    alt deferida
        MAIL-->>U: e-mail de deferimento
    else indeferida pelo responsável
        MAIL-->>U: e-mail com a justificativa
    else outra solicitação deferida, ou reserva direta no slot
        MAIL-->>U: e-mail informando que o horário foi atribuído
    end

    U->>LW: Minhas Reservas, cancelar
    LW->>POL: authorize cancel Reservation
    alt não é o titular ou fora do prazo (R11)
        POL-->>LW: deny
        LW-->>U: só o responsável pode cancelar agora
    else dentro do prazo
        POL-->>LW: allow
        SVC->>DB: UPDATE status = cancelled, cancelled_by, slots active_key = NULL
        SVC->>MAIL: ReservationCancelled
        MAIL-->>U: e-mail de cancelamento
        LW-->>U: slots liberados na grade
    end
```

## 1.7 Sequência: perfil `manager` (responsável de bloco)

```mermaid
sequenceDiagram
    autonumber
    actor M as Responsável de bloco
    participant LW as Painel Livewire
    participant POL as ReservationPolicy / ClosurePolicy
    participant SVC as ReservationService
    participant DB as Banco
    participant MAIL as Notificação
    actor U as Usuários afetados

    Note over M,U: role = manager, escopo = blocos em block_user. Não solicita reservas.

    M->>LW: login via Fortify
    LW->>DB: SELECT block_user WHERE user_id
    DB-->>LW: blocos do responsável
    LW-->>M: painel com a fila dos seus blocos

    rect rgba(128,128,128,0.1)
    Note over M,U: Decisão sobre solicitações (R17, R19)
    M->>LW: abre a fila de pendentes
    LW->>DB: SELECT reservations pending<br/>WHERE place.block_id IN (blocos do responsável)
    DB-->>LW: solicitações agrupadas por lugar, data e slot
    LW-->>M: concorrentes lado a lado
    M->>LW: defere a escolhida
    LW->>POL: authorize approve, managesBlock(place.block_id)?
    alt bloco de outro responsável
        POL-->>LW: deny
        LW-->>M: 403
    else seu bloco
        POL-->>LW: allow
        SVC->>DB: BEGIN + lock no ambiente-raiz
        SVC->>DB: slot ativo no lugar, pai ou filhos?
        alt já decidido
            SVC->>DB: ROLLBACK
            SVC-->>LW: conflito
            LW-->>M: recarrega a fila
        else livre
            SVC->>DB: UPDATE status = approved, reviewed_by, slots active_key = 1
            SVC->>DB: UPDATE pendentes que compartilham slot na árvore = rejected
            SVC->>DB: COMMIT
            SVC->>MAIL: ReservationApproved + ReservationRejected (cascata)
            MAIL-->>U: e-mails
        end
    end
    end

    rect rgba(128,128,128,0.1)
    Note over M,U: Reserva direta (R22)
    M->>LW: escolhe lugar do bloco, data, slots e justificativa
    LW->>POL: authorize direct, managesBlock + notes obrigatório
    POL-->>LW: allow
    SVC->>DB: BEGIN + lock no ambiente-raiz
    SVC->>DB: INSERT reservation origin = direct, user_id = responsável
    SVC->>DB: mesmo miolo do deferimento: verifica árvore, approved, cascata
    SVC->>DB: COMMIT
    SVC->>MAIL: ReservationRejected (cascata) aos pendentes atingidos
    MAIL-->>U: e-mails
    LW-->>M: reserva registrada
    end

    rect rgba(128,128,128,0.1)
    Note over M,U: Fechamento no escopo do bloco (R24)
    M->>LW: fecha o bloco ou um lugar dele: data, faixa, motivo
    LW->>POL: authorize create Closure, escopo dentro do seu bloco?
    POL-->>LW: allow
    SVC->>DB: INSERT closure
    SVC->>DB: SELECT reservas atingidas no escopo
    loop para cada reserva atingida
        SVC->>DB: UPDATE approved vira cancelled, pending vira rejected
        SVC->>MAIL: ReservationCancelledByManager
        MAIL-->>U: e-mail com o motivo
    end
    end

    rect rgba(128,128,128,0.1)
    Note over M,U: Agenda e cancelamento (R12)
    M->>LW: abre a agenda de um lugar do bloco
    LW->>DB: SELECT reservations do período com titular e origin
    DB-->>LW: agenda
    LW-->>M: calendário
    M->>LW: cancela reserva com motivo
    LW->>POL: authorize cancel, managesBlock
    POL-->>LW: allow
    SVC->>DB: UPDATE cancelled, cancelled_by, slots active_key = NULL
    SVC->>MAIL: ReservationCancelledByManager
    MAIL-->>U: e-mail com o motivo
    end
```

## 1.8 Sequência: perfil `admin` (administrador)

```mermaid
sequenceDiagram
    autonumber
    actor A as Administrador
    participant LW as Painel Livewire
    participant POL as BlockPolicy / PlacePolicy / OperatingHourPolicy
    participant SVC as PlaceService / OperatingHourService
    participant DB as Banco
    participant MAIL as Notificação
    actor U as Usuários afetados

    Note over A,U: role = admin, estrutura, acessos e grade, mais tudo do responsável em qualquer bloco

    rect rgba(128,128,128,0.1)
    Note over A,DB: Estrutura (R2, R23)
    A->>LW: novo bloco
    LW->>POL: authorize create Block
    POL-->>LW: allow
    SVC->>DB: INSERT blocks
    A->>LW: novo ambiente no bloco
    SVC->>DB: INSERT places (parent_id NULL, block_id)
    A->>LW: novo subambiente no ambiente
    SVC->>SVC: pai já tem pai? nome repetido entre irmãos?
    alt viola R23 ou nome
        SVC-->>LW: erro de validação
        LW-->>A: corrigir
    else válido
        SVC->>DB: INSERT places (parent_id, block_id = do pai)
    end
    end

    rect rgba(128,128,128,0.1)
    Note over A,DB: Responsáveis e contas (RF04, RF05)
    A->>LW: cria conta com perfil manager ou admin
    SVC->>DB: INSERT users (role)
    A->>LW: nomeia responsáveis do bloco
    SVC->>DB: DELETE + INSERT block_user do bloco
    end

    rect rgba(128,128,128,0.1)
    Note over A,U: Grade da universidade (R15)
    A->>LW: encurta sexta-feira de 23h para 20h
    LW->>SVC: substituirGrade(sete linhas)
    SVC->>DB: SELECT reservation_slots ativos futuros fora da nova janela
    DB-->>SVC: conflitos
    alt existem conflitos
        SVC-->>LW: reservas atingidas
        LW-->>A: manter ou cancelar e notificar?
        A->>LW: cancelar
        SVC->>DB: UPDATE cancelled, cancelled_by = admin, slots active_key = NULL
        SVC->>MAIL: ReservationCancelledByAdmin
        MAIL-->>U: e-mail com o motivo
    else sem conflito
        SVC->>DB: UPDATE operating_hours
    end
    end

    rect rgba(128,128,128,0.1)
    Note over A,DB: Desativar x excluir (R13, R14)
    A->>LW: desativar lugar
    SVC->>DB: UPDATE places SET is_active = false
    Note over DB: some da busca, reservas futuras continuam válidas
    A->>LW: excluir lugar
    LW->>DB: DELETE places
    alt possui reservas ou filhos
        DB-->>LW: restrictOnDelete bloqueia
        LW-->>A: desative em vez de excluir
    else sem vínculos
        DB-->>LW: removido
    end
    end

    Note over A,U: Decisão, reserva direta, fechamento e agenda: idênticos a 1.7, em qualquer bloco
```
