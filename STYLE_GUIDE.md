# STYLE_GUIDE: Reservas de Ambientes (Laravel + Livewire Starter Kit)

Fonte: código existente em `resources/views`, `resources/css/app.css`, `app/`, `routes/`, `vendor/livewire/flux` (v2.17.0). Nada abaixo é inventado; ambiguidades ainda não resolvidas estão marcadas com **Ambíguo**.

---

## 1. Stack e versões (instaladas: `composer.lock` / `package.json`)

| Peça | Versão | Observação |
|---|---|---|
| PHP | ^8.3 | |
| Laravel | 13.26.1 | Fortify 1.38 cuida de login/registro/2FA/passkeys (views Blade puras em `pages/auth`) |
| Livewire | 4.4.1 | **Componentes single-file (`⚡nome.blade.php`)**, não Volt, não classes em `app/Livewire` |
| Flux UI | 2.17.0 **free** | `livewire/flux-pro` **não instalado** (`FluxManager::pro()` = false) |
| Blaze | 1.0.17 | Só usado dentro dos stubs do Flux. Não escrever `@blaze` nas views do projeto |
| Tailwind | 4.1 (`@tailwindcss/vite`) | Config só no `app.css` via `@theme` - não existe `tailwind.config.js` |
| Alpine | embutido no Livewire 4 | `resources/js/app.js` está vazio; Alpine é usado inline (`x-data`, `x-show`, `$wire`, `$flux`) |
| Vite | 8 | `@vite(['resources/css/app.css', 'resources/js/app.js'])` em `partials/head.blade.php` |

Locale: `APP_LOCALE=en`, sem pasta `lang/`. Todo texto de UI passa por `__('Texto em inglês')`.

**Idioma: decidido em 07/09/2026.** A interface é **pt-BR**, com todo texto passando por `__()` (requisito RNF12 em [docs/requisitos.md](docs/requisitos.md)). O starter kit está em inglês e a padronização é tarefa da Sprint 5 (issue #31): até lá, telas novas já nascem em pt-BR e nenhuma tela mistura os dois idiomas.

As colunas do model `Place` (`nome`, `bloco`, `capacidade`, `descricao`) são a exceção deliberada em sentido contrário: o **schema** vai para o inglês na Sprint 1 (issue #1), acompanhando o restante do banco gerado pelo framework. Idioma do código e idioma da interface são decisões separadas.

---

## 2. Componentes Flux disponíveis

Regra de verificação: existe stub em `vendor/livewire/flux/stubs/resources/views/flux/`. Se não existe, é **Pro -> proibido** (lança `Unable to locate a class or view for component [flux::x]`).

### 2.1 Usados no projeto (padrão canônico já existe)

| Componente | Onde | Props/variações vistas |
|---|---|---|
| `flux:button` | todas as telas | `variant="primary\|filled\|outline\|danger\|ghost"`, `size="sm"`, `type="submit"`, `icon`, `icon:variant="outline"`, `class="w-full"`, `data-test` |
| `flux:input` | forms | `wire:model`, `:label`, `type`, `required`, `autofocus`, `autocomplete`, `placeholder`, `viewable` (senha) |
| `flux:checkbox` | login | `name`, `:label`, `:checked` |
| `flux:radio.group` / `flux:radio` | appearance | `variant="segmented"`, `icon`, `x-model` |
| `flux:otp` | 2FA | `length="6"`, `label:sr-only` |
| `flux:heading` | tudo | `size="xl\|lg"` (default base), `level="1\|3"`, `class="sr-only"` |
| `flux:subheading` | tudo | `size="lg"` (default base) |
| `flux:text` | tudo | `variant="subtle"`, `color="red"`, `class="text-xs"` |
| `flux:link` | auth, profile | `:href`, `wire:navigate`, `wire:click.prevent` |
| `flux:separator` | settings | `variant="subtle"`, `class="md:hidden"` |
| `flux:badge` | passkeys | `size="sm"` |
| `flux:callout` | 2FA | `variant="danger" icon="x-circle" heading="..."` |
| `flux:modal` / `.trigger` / `.close` | settings | `name`, `wire:model`, `@close`, `:show`, `focusable`, `class="max-w-md md:min-w-md"` |
| `flux:toast` / `.group` | layouts | `@persist('toast')`; disparo por PHP `Flux::toast(variant: 'success', text: ...)` |
| `flux:tooltip` | header | `:content`, `position="bottom"` |
| `flux:dropdown` / `flux:menu.*` | user menu | `menu.item` (`icon`, `as="button"`), `menu.separator`, `menu.radio.group` |
| `flux:avatar`, `flux:profile`, `flux:brand` | layouts | `:name`, `:initials` |
| `flux:sidebar.*`, `flux:navbar.*`, `flux:navlist.*`, `flux:header`, `flux:main`, `flux:spacer` | layouts | `sidebar.item icon="..." :href :current wire:navigate` |
| `flux:icon.<nome>` | vários | set **Heroicons** (318 ícones), `variant="outline\|solid\|mini\|micro"`, `class="size-4/5/7"`. `flux:icon.loading` = spinner. Ícones extras (Lucide) publicados em `resources/views/flux/icon/`: `layout-grid`, `folder-git-2`, `book-open-text`, `chevrons-up-down` |

### 2.2 Disponíveis (stub existe) mas ainda sem uso no projeto: liberados

`flux:table` (+ `.columns`, `.column sortable sorted direction align`, `.rows`, `.row key`, `.cell`, prop `:paginate`), `flux:pagination :paginator`, `flux:card` (`variant="soft"`, `size="sm"`), `flux:skeleton` (+ `.group`, `.line`), `flux:textarea` (`rows`, `resize`), `flux:select` (**somente nativo** - `variant="default"`; `flux:select.option`, `flux:select.group`), `flux:switch`, `flux:field` + `flux:label` + `flux:description` + `flux:error`, `flux:fieldset` + `flux:legend`, `flux:checkbox.group` (variants `default|buttons|cards|pills`), `flux:checkbox.all`, `flux:radio` variants `default|buttons|cards|pills|segmented`, `flux:breadcrumbs` + `.item`, `flux:progress`, `flux:navmenu`, `flux:menu.submenu`, `flux:menu.checkbox`, `flux:menu.radio`, `flux:menu.heading`, `flux:menu.group`, `flux:container`, `flux:accent`, `flux:aside`, `flux:footer`, `flux:toggle`.

> O prompt da Etapa 1 assume que tabela é Pro. **Nesta versão `flux:table` é free** - pode usar.

### 2.3 Flux Pro: **PROIBIDO** (sem stub instalado)

`flux:tabs`, `flux:accordion`, `flux:autocomplete`, `flux:calendar`, `flux:date-picker`, `flux:time-picker`, `flux:color-picker`, `flux:chart`, `flux:command`, `flux:context`, `flux:editor`, `flux:kbd`, `flux:pillbox`, `flux:popover`, `flux:slider`, `flux:composer`, `flux:select variant="listbox"`, `flux:select variant="combobox"`, `flux:select searchable|multiple`.

Substitutos: tabs -> `flux:radio.group variant="segmented"` ou `flux:navbar`; date picker -> `flux:input type="date"`; combobox -> `flux:select` nativo ou `flux:input` com `wire:model.live.debounce`; popover -> `flux:dropdown` + `flux:menu`; kbd -> `<kbd>` + Tailwind.

---

## 3. Tokens visuais

### 3.1 `@theme` (`resources/css/app.css`)

```css
--font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif, ...;
--color-zinc-50..950: /* remapeados para os hex de neutral (#fafafa ... #0a0a0a) */
--color-accent: var(--color-neutral-800);          /* light */
--color-accent-content: var(--color-neutral-800);
--color-accent-foreground: var(--color-white);
.dark { --color-accent: white; --color-accent-content: white; --color-accent-foreground: neutral-800; }
```

Consequências: a marca é **monocromática** (botão `primary` = neutral-800 no light, branco no dark). `zinc-*` e `neutral-*` renderizam **a mesma cor**. Fonte: Instrument Sans (Bunny Fonts, pesos 400/500/600) declarada em `vite.config.js` (`bunny('Instrument Sans', { weights: [400, 500, 600] })`) e emitida pela diretiva `@fonts` em `partials/head.blade.php`.

**Ambíguo (amber).** `text-amber-700` / `shadow-2xl shadow-amber-700` aparecem em `welcome.blade.php` e `pages/auth/login.blade.php` (commit "setup inicial"), mas não há token nem uso em telas internas. Não tratar como cor de marca até virar token em `@theme`.

**Ambíguo (famílias de cinza).** Três famílias coexistem: `zinc` (layouts, settings: majoritária), `neutral` (dashboard, layouts auth) e `stone` (modal 2FA, `layouts/auth/card`). Usar **`zinc`** em telas novas.

### 3.2 Cores por função

| Uso | Classes |
|---|---|
| Fundo da página | `bg-white dark:bg-zinc-800` (body) |
| Sidebar / header | `bg-zinc-50 dark:bg-zinc-900` + `border-zinc-200 dark:border-zinc-700` |
| Borda de card/lista | `border-zinc-200 dark:border-zinc-700` (alternativa vista: `dark:border-white/10`) |
| Fundo de bloco secundário | `bg-zinc-100 dark:bg-zinc-800`, `bg-zinc-50 dark:bg-zinc-800/50`, `bg-zinc-100 dark:bg-white/5` |
| Texto secundário | `text-zinc-500 dark:text-zinc-400` (ou `flux:text variant="subtle"`) |
| Ícone apagado | `text-zinc-400 dark:text-zinc-500` |
| Sucesso (texto) | `text-green-600 dark:text-green-400` |
| Erro (texto) | `text-red-600 dark:text-red-400` ou `flux:text color="red"` |
| Ação destrutiva | `flux:button variant="danger"`; ícone: `text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/50` |
| Status em badge | `flux:badge color="green|amber|red|zinc|..."` (cores nativas do Flux) |

### 3.3 Espaçamento

| Contexto | Escala em uso |
|---|---|
| Campos de formulário | `space-y-6` (Livewire) / `flex flex-col gap-6` (auth) |
| Seções dentro da mesma página | `mt-12` (security); delete-account usa `mt-10`; padronizar em `mt-12` |
| Heading -> conteúdo | `mt-5` (settings layout), `mt-6` (listas), `mb-6` (bloco de título) |
| Cards do dashboard | `gap-4` |
| Conteúdo de modal | `space-y-6` externo, `space-y-2` no bloco título+texto |
| Botões lado a lado | `flex items-center gap-4` (submit), `flex gap-3 justify-end` (modal), `flex gap-2` (inline) |
| Linha de lista | `p-4`; estado vazio `p-8` |
| Item de meta-informação | `space-y-1`, `gap-2.5` |
| Padding de card | `p-6` (`flux:card` default) / `px-6 py-6` (recovery codes) |

### 3.4 Raios

`rounded-xl` cards e containers grandes; `rounded-lg` listas com borda, inputs, botões base (`h-10`); `rounded-md` botões `sm`, caixa do logo; `rounded-2xl` caixa de ícone do estado vazio (`size-14`); `rounded-full` avatar.

### 3.5 Tipografia (via Flux)

| Papel | Componente | Renderiza |
|---|---|---|
| Título da página | `<flux:heading size="xl" level="1">` | `text-2xl` |
| Subtítulo da página | `<flux:subheading size="lg">` | |
| Título de seção/card/modal | `<flux:heading size="lg" level="3">` ou `<flux:heading>` | `text-base` / `text-sm` bold |
| Descrição de seção | `<flux:subheading>` | |
| Corpo | `<flux:text>` | `text-sm` |
| Meta | `<p class="text-xs text-zinc-500 dark:text-zinc-400">` | |
| Nome em lista | `<p class="font-medium tracking-tight">` | |

### 3.6 Larguras

Conteúdo de settings `max-w-lg`; nav lateral de settings `md:w-[220px]`; auth simple `max-w-sm`; auth card `max-w-md`; modal `max-w-md md:min-w-md` ou `max-w-lg`.

---

## 4. Dark mode

- Estratégia por classe: `@custom-variant dark (&:where(.dark, .dark *));`. Todos os layouts têm `<html class="dark">` fixo; `@fluxAppearance` (em `partials/head.blade.php`) troca a classe em runtime conforme `$flux.appearance` (`light|dark|system`, selecionável em Settings -> Appearance).
- **Ambíguo:** o `class="dark"` hardcoded no `<html>` é o estado inicial antes do script rodar; não remover sem testar flash de tema.
- Componentes Flux já tratam dark mode. Para qualquer classe de cor **fora** de componente Flux, escrever o par light/dark na mesma declaração:

```html
<div class="border rounded-lg border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50">
<p class="text-zinc-500 dark:text-zinc-400">
<flux:icon.key class="size-5 text-zinc-500 dark:text-zinc-400" />
```

- Overrides com `!`: usados pontualmente (`!text-green-600 !dark:text-green-400`, `!mt-1`). Evitar em telas novas.

---

## 5. Anatomia de uma página

### 5.1 Layouts existentes

| Layout | Arquivo | Uso |
|---|---|---|
| `layouts::app` | `resources/views/layouts/app.blade.php` -> `layouts::app.sidebar` -> `<flux:main>` | Todas as telas autenticadas. **Default** para `Route::livewire` (`component_layout` do Livewire 4) |
| `layouts::app.header` | `layouts/app/header.blade.php` | Variante com navbar superior - **não usada** |
| `layouts::auth` | `layouts/auth.blade.php` -> `layouts::auth.simple` | Telas de auth (`max-w-sm`, centralizado) |
| `layouts::auth.card` / `.split` | `layouts/auth/*.blade.php` | **Não usadas** |

Sidebar (`layouts/app/sidebar.blade.php`): grupo `flux:sidebar.group :heading="__('Platform')"` com itens `flux:sidebar.item icon="home" :href="route('x')" :current="request()->routeIs('x')" wire:navigate`. **Toda tela nova precisa de um item aí.** Menu do usuário via `<x-desktop-user-menu />`; toasts via `@persist('toast')`.

**Ambíguo:** `flux:sidebar.nav class="h-[90vh]"` (valor arbitrário) e `{{ __('dashboard') }}` em minúscula foram adicionados no último commit - não replicar sem confirmar.

### 5.2 Página Blade simples (dashboard)

```blade
<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">...</div>
        </div>
    </div>
</x-layouts::app>
```

### 5.3 Página Livewire (padrão settings: o mais completo do projeto)

```blade
<section class="w-full">                                     {{-- root único obrigatório --}}
    @include('partials.settings-heading')                    {{-- heading xl + subheading lg + separator subtle, wrapper "relative mb-6 w-full" --}}

    <flux:heading class="sr-only">{{ __('Profile settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        {{-- conteúdo: entra em <div class="mt-5 w-full max-w-lg"> --}}
    </x-pages::settings.layout>
</section>
```

Cabeçalho canônico de página (extraído de `partials/settings-heading.blade.php`):

```blade
<div class="relative mb-6 w-full">
    <flux:heading size="xl" level="1">{{ __('Settings') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ __('Manage your profile and account settings') }}</flux:subheading>
    <flux:separator variant="subtle" />
</div>
```

Seção interna: `<section class="mt-12"><flux:heading>...</flux:heading><flux:subheading>...</flux:subheading><div class="mt-6 ...">...</div></section>`.

Breadcrumbs: **não existem** em nenhuma tela. `flux:breadcrumbs` está disponível; se usar, é decisão nova.

### 5.4 Lista com borda + estado vazio (padrão passkeys: canônico para listagens)

```blade
<div class="border rounded-lg border-zinc-200 dark:border-zinc-700 overflow-hidden">
    @forelse ($items as $item)
        <div class="flex items-center justify-between p-4 {{ ! $loop->last ? 'border-b border-zinc-200 dark:border-zinc-700' : '' }}">
            <div class="flex items-center gap-4">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-zinc-100 dark:bg-zinc-800">
                    <flux:icon.key class="size-5 text-zinc-500 dark:text-zinc-400" />
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-2.5">
                        <p class="font-medium tracking-tight">{{ $item['name'] }}</p>
                        <flux:badge size="sm">{{ $item['tag'] }}</flux:badge>
                    </div>
                    <p class="text-zinc-500 dark:text-zinc-400 text-xs">{{ $item['meta'] }}</p>
                </div>
            </div>
            <flux:button variant="ghost" size="sm" icon="trash" icon:variant="outline" wire:click="confirmDelete({{ $item['id'] }})"
                class="text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/50" />
        </div>
    @empty
        <div class="p-8 text-center">
            <div class="mx-auto mb-4 flex size-14 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-zinc-800">
                <flux:icon.key class="size-7 text-zinc-400 dark:text-zinc-500" />
            </div>
            <p class="font-medium">{{ __('No passkeys yet') }}</p>
            <flux:text class="mt-1">{{ __('Add a passkey to sign in without a password') }}</flux:text>
        </div>
    @endforelse
</div>
```

Tabela: não há exemplo no projeto. Usar `flux:table` (free) seguindo as bordas/raios acima; em telas pequenas ela rola horizontalmente sozinha (`ui-table-scroll-area overflow-auto`).

### 5.5 Modal de confirmação (canônico)

```blade
<flux:modal name="delete-passkey-modal" class="max-w-md md:min-w-md" @close="closeDeleteModal" wire:model="showDeleteModal">
    <div class="space-y-6">
        <div class="space-y-2">
            <flux:heading size="lg">{{ __('Remove passkey') }}</flux:heading>
            <flux:text>{{ __('Are you sure...?') }}</flux:text>
        </div>
        <div class="flex gap-3 justify-end">
            <flux:button variant="outline" wire:click="closeDeleteModal">{{ __('Cancel') }}</flux:button>
            <flux:button variant="danger" wire:click="deletePasskey">{{ __('Remove passkey') }}</flux:button>
        </div>
    </div>
</flux:modal>
```

**Ambíguo, dois controles de modal coexistem:** por estado (`wire:model="showDeleteModal"` + `@close`) e por nome (`<flux:modal.trigger name="x">` + `<flux:modal.close>`, com `variant="filled"` no Cancelar). Regra prática: trigger/close para modal sem estado de servidor; `wire:model` quando o modal precisa de dados carregados por ação (ex.: "excluir X").

### 5.6 Estados

| Estado | Como o projeto faz |
|---|---|
| Carregando (botão) | Automático: `flux:button` com `wire:click` ou `type="submit"` mostra spinner e desabilita |
| Carregando (bloco) | `wire:loading.class="opacity-50 animate-pulse"` no item; placeholder `animate-pulse` + `<flux:icon.loading />`; `wire:cloak` em containers que dependem de Alpine |
| Vazio | bloco `@empty` de 5.4 |
| Erro de campo | Automático via `flux:input wire:model` (Flux lê `$errors` pelo nome) |
| Erro geral | `@error('chave') <flux:callout variant="danger" icon="x-circle" heading="{{ $message }}" /> @enderror` |
| Sucesso | `Flux::toast(variant: 'success', text: __('Profile updated.'))` no PHP; ou texto verde de `session('status')` |
| Responsivo | mobile-first: `max-md:flex-col`, `md:w-[220px]`, `md:grid-cols-3`, `sm:flex-row sm:items-center sm:justify-between`, `lg:hidden` / `max-lg:hidden` para sidebar vs header |

---

## 6. Convenções de código

### 6.1 Componentes Livewire (single-file, Livewire 4)

- Local: `resources/views/pages/<area>/⚡<nome>.blade.php` (prefixo `⚡` = componente Livewire; sem prefixo = Blade component `x-pages::...`). Namespace `pages::` -> `resources/views/pages`; `layouts::` -> `resources/views/layouts` (config padrão do Livewire 4, não publicada).
- `app/Livewire/` contém apenas `Actions/Logout.php` (classe invocável injetada em métodos: `public function deleteUser(Logout $logout)`). **Não há componentes em classe**; não criar.
- Estrutura:

```php
<?php

use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Profile settings')] class extends Component {
    public string $name = '';

    #[Locked]
    public array $items = [];

    /**
     * Mount the component.
     */
    public function mount(): void { ... }

    public function save(): void
    {
        $validated = $this->validate([...]);
        // ...
        Flux::toast(variant: 'success', text: __('Saved.'));
    }

    #[Computed]
    public function hasItems(): bool { ... }

    #[On('event-name')]
    public function onEvent(): void { ... }
}; ?>

<section class="w-full">   {{-- exatamente UM elemento raiz --}}
    ...
</section>
```

- Métodos com docblock curto em inglês. Nomes de ação em camelCase descritivo (`updateProfileInformation`, `confirmDelete`, `closeDeleteModal`, `regenerateRecoveryCodes`).
- Utilitários vistos: `$this->reset('a', 'b')`, `$this->resetErrorBag()`, `$this->addError('chave', 'msg')`, `$this->dispatch('evento')`, `$this->redirect('/', navigate: true)`, `$this->redirectIntended(default: route('dashboard', absolute: false))`, `#[Validate('required|string|size:6', onUpdate: false)]`.
- Regras de validação reutilizáveis ficam em traits `app/Concerns/*ValidationRules.php` (`ProfileValidationRules`, `PasswordValidationRules`).
- Componente aninhado: `<livewire:pages::settings.delete-user-form />`; props: `:requires-confirmation="$requiresConfirmation"` ou `:$requiresConfirmation`.
- Dados: `Auth::user()` / `auth()->user()` (ambos usados). Modelos: só `App\Models\Place` (tabela `places`: `nome`, `bloco`, `capacidade?`, `descricao?`).

### 6.2 Rotas

```php
// routes/web.php  (auth pages vêm do Fortify; settings em routes/settings.php via require)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::livewire('settings/profile', 'pages::settings.profile')->name('profile.edit');
});
```

Nomes de rota: `recurso.acao` (`profile.edit`, `security.edit`). Links internos sempre com `wire:navigate`. `{{ route('x') }}` / `:href="route('x')"`.

**Ambíguo:** `resources/views/pages/ambientes/index.blade.php` é um `<h1>` solto, sem layout e sem rota - não é padrão a seguir.

### 6.3 Blade / atributos

- Blade components anônimos em `resources/views/components/` (`x-app-logo`, `x-auth-header :title :description`, `x-auth-session-status :status`, `x-desktop-user-menu`, `x-placeholder-pattern`).
- Overrides de Flux em `resources/views/flux/` (`navlist/group.blade.php`, ícones extras).
- `wire:model` (deferred) por padrão; `wire:submit="metodo"`; `wire:click="metodo(arg)"`; `wire:click.prevent` em links; `wire:key` não aparece no projeto (use em loops mesmo assim).
- Botões acionáveis levam `data-test="<acao>-button"` em kebab-case (`update-profile-button`, `logout-button`, `confirm-delete-user-button`) - testes usam `assertSee`, então é convenção, não dependência.
- Alpine inline: `x-data="{ ... }"`, `x-show`, `x-cloak`, `x-transition`, `x-ref`, `x-bind:disabled="$wire.code.length < 6"`, `$wire.metodo()`, `$flux.appearance`. Acessibilidade vista: `aria-expanded`, `aria-controls`, `aria-hidden`, `role="list"/"listitem"`, `aria-label`, `label:sr-only`, `<span class="sr-only">`.
- Comentários HTML `<!-- Email Address -->` só nas views Fortify; nas Livewire não há.
- Estilo PHP: Pint preset `laravel`; PHPStan nível 7 (`app/`, `routes/`, `config/`, `database/`).

---

## 7. Padrão de formulário

### 7.1 Livewire (canônico: `⚡profile.blade.php`)

```blade
<form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
    <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />
    <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

    <div class="flex items-center gap-4">
        <flux:button variant="primary" type="submit" data-test="update-profile-button">
            {{ __('Save') }}
        </flux:button>
    </div>
</form>
```

- Label **sempre via prop** `:label`. Erro de validação renderizado pelo próprio `flux:input` (nome = `wire:model`). `flux:field`/`flux:label`/`flux:error` existem mas não são usados - só compor manualmente quando o campo não é Flux.
- Texto auxiliar abaixo do campo: `<flux:text class="mt-4">...</flux:text>` (ou `!mt-1` colado ao campo).
- Senha: `type="password" viewable autocomplete="current-password|new-password"` + `passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"`.
- Após sucesso: `Flux::toast(variant: 'success', text: __('...'))`; reset de campos sensíveis com `$this->reset(...)`.
- Em falha de validação de senha: `try { validate } catch (ValidationException $e) { reset; throw $e; }`.
- **Ambíguo:** margem do form varia (`my-6` no profile, `mt-6` no security). Usar `mt-6 space-y-6`.

### 7.2 Blade puro (Fortify: `pages/auth/*`)

```blade
<form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
    @csrf
    <flux:input name="email" :label="__('Email address')" :value="old('email')" type="email" required autofocus autocomplete="email" placeholder="email@example.com" />
    <flux:checkbox name="remember" :label="__('Remember me')" :checked="old('remember')" />
    <div class="flex items-center justify-end">
        <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">{{ __('Log in') }}</flux:button>
    </div>
</form>
```

Só para páginas do Fortify. Telas novas são Livewire (7.1).

### 7.3 Ação destrutiva

`<flux:modal.trigger name="confirm-x"><flux:button variant="danger">...</flux:button></flux:modal.trigger>` -> `<flux:modal name="confirm-x" :show="$errors->isNotEmpty()" focusable class="max-w-lg">` contendo `<form wire:submit="..." class="space-y-6">` com `flux:heading size="lg"` + `flux:subheading`, campo(s) e rodapé `flex justify-end space-x-2 rtl:space-x-reverse` (`flux:modal.close` + `variant="filled"` cancelar, `variant="danger" type="submit"` confirmar).

### 7.4 CSS global que afeta forms (`app.css`): não duplicar

```css
[data-flux-field]:not(ui-radio, ui-checkbox) { @apply grid gap-2; }
[data-flux-label] { @apply !mb-0 !leading-tight; }
input:focus[data-flux-control], textarea:focus[data-flux-control], select:focus[data-flux-control] {
    @apply outline-hidden ring-2 ring-accent ring-offset-2 ring-offset-accent-foreground;
}
```
