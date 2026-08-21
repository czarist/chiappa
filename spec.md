
# Objetivo

Crie um **CMS completo, porém simplificado**, inspirado nas principais funcionalidades utilizadas em sites institucionais feitos com WordPress.

O objetivo NÃO é recriar todo o WordPress, mas construir uma solução própria, moderna, simples de administrar e reutilizável para sites institucionais, landing pages e single pages.

O projeto deve utilizar:

* Laravel
* PHP compatível com a versão atual escolhida do Laravel
* MySQL
* Blade
* Docker / Docker Compose
* REST API
* Arquitetura seguindo princípios SOLID
* Services / Repositories quando fizer sentido
* Form Requests
* Policies / Gates para autorização
* Componentização Blade no frontend
* Template administrativo conhecido e bem mantido
* Vite para assets
* migrations, seeders e factories

---

# 1. IMPORTANTE — arquivos existentes

Estou executando você a partir de uma pasta que já contém os seguintes arquivos de frontend:

* `index.html`
* `index-cinza.html`
* `index-claro.html`

Antes de desenvolver qualquer coisa:

1. Leia e analise completamente esses três arquivos.
2. Identifique:

   * estrutura HTML;
   * seções;
   * textos;
   * imagens;
   * ícones;
   * cores;
   * fontes;
   * CSS;
   * JavaScript;
   * navegação;
   * responsividade;
   * diferenças entre as três versões.
3. Utilize esses arquivos como **fonte de verdade para o frontend**.
4. NÃO crie um novo design do zero.
5. Preserve ao máximo possível a aparência atual.
6. Converta a estrutura existente para Blade e componentes reutilizáveis.
7. Identifique quais elementos devem se tornar campos editáveis no CMS.

Os três arquivos representam variações do mesmo site/template.

Analise as diferenças e determine se elas podem ser representadas como:

* temas;
* presets;
* variações de aparência;
* configurações de cores;
* configurações de layout.

Quando possível, evite duplicar todo o HTML apenas para representar uma variação visual.

---

# 2. Local do projeto

Todo o novo projeto deve ser criado dentro da pasta:

`./site`

NÃO altere nem apague os arquivos HTML originais existentes na raiz.

A estrutura deve ficar aproximadamente assim:

```text
/
├── index.html
├── index-cinza.html
├── index-claro.html
└── site/
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── docker/
    ├── public/
    ├── resources/
    ├── routes/
    ├── storage/
    ├── tests/
    ├── Dockerfile
    ├── docker-compose.yml
    └── ...
```

---

# 3. Conceito do CMS

O sistema será inicialmente utilizado para administrar **uma single page**, baseada nos HTMLs fornecidos.

Entretanto, a arquitetura NÃO pode ficar presa a apenas uma página.

O CMS deverá suportar posteriormente:

* criação de novas páginas;
* edição de páginas;
* publicação/despublicação;
* slug;
* SEO individual;
* diferentes conteúdos;
* diferentes seções;
* menu;
* página inicial;
* páginas institucionais adicionais.

Portanto:

**implemente a single page inicial, mas modele o sistema como CMS multipágina.**

---

# 4. Gerenciamento de páginas

Criar módulo administrativo para páginas.

Cada página deve possuir pelo menos:

* ID
* título
* slug
* status
* conteúdo
* template/layout
* imagem destacada, se aplicável
* data de publicação
* created_at
* updated_at

Status possíveis:

* draft
* published
* scheduled, se a implementação não gerar complexidade excessiva

Permitir:

* criar;
* editar;
* visualizar;
* publicar;
* despublicar;
* excluir;
* duplicar página.

O slug deve ser único.

Exemplo:

```text
/
/
/sobre
/servicos
/contato
```

A home deverá ser configurável.

---

# 5. Edição da single page existente

A página inicial deve ser construída a partir dos três HTMLs existentes.

Analise cada seção existente e transforme seus conteúdos em campos administráveis.

Por exemplo, caso existam:

* Hero;
* Sobre;
* Serviços;
* Benefícios;
* CTA;
* Portfólio;
* Depoimentos;
* FAQ;
* Contato;
* Footer;

cada seção deverá ter seus respectivos campos editáveis.

Não invente campos arbitrariamente antes de analisar os HTMLs.

Os campos devem refletir **o conteúdo realmente existente nos templates**.

Exemplo conceitual:

```text
Hero
├── título
├── subtítulo
├── texto
├── botão
├── URL do botão
├── imagem
└── configurações visuais
```

Se uma seção possuir vários itens, implemente gerenciamento de itens.

Exemplo:

```text
Serviços
├── título da seção
├── descrição
└── itens
    ├── título
    ├── descrição
    ├── ícone
    ├── imagem
    ├── link
    └── ordem
```

---

# 6. Page Builder simplificado

NÃO é necessário construir algo tão complexo quanto Elementor ou Gutenberg.

Quero um editor de seções simplificado.

Cada página poderá possuir uma coleção ordenada de seções/blocos.

Exemplo:

```text
Page
└── Sections
    ├── Hero
    ├── About
    ├── Services
    ├── CTA
    ├── FAQ
    └── Contact
```

Cada seção deve possuir:

* tipo;
* posição/ordem;
* ativo/inativo;
* conteúdo;
* configurações quando necessário.

Preferencialmente utilize uma arquitetura que permita adicionar novos tipos de seção futuramente sem alterar profundamente o sistema existente.

Evite armazenar a página inteira como HTML bruto.

Use dados estruturados.

JSON pode ser utilizado para configurações específicas de determinados blocos, mas informações importantes para o domínio devem possuir estrutura adequada no banco.

---

# 7. Reordenação das seções

No painel administrativo, permitir:

* ativar/desativar seção;
* editar seção;
* reordenar seções;
* adicionar seções suportadas;
* remover uma seção da página.

Se for simples implementar drag-and-drop de maneira estável, utilize.

Caso contrário, utilize controles de ordenação simples.

---

# 8. Temas / versões existentes

Os arquivos:

* `index.html`
* `index-cinza.html`
* `index-claro.html`

devem ser analisados para entender exatamente o que muda entre eles.

Crie um sistema simples de tema/preset.

Por exemplo:

```text
Tema:
- padrão
- cinza
- claro
```

Entretanto, não assuma que somente cores mudam.

Analise primeiro os arquivos.

Caso seja possível compartilhar os mesmos componentes Blade, faça isso e altere apenas:

* classes;
* CSS variables;
* assets;
* configurações;
* propriedades visuais.

Evite duplicação.

---

# 9. Blade e componentização

Não transforme cada HTML inteiro em uma única view Blade.

Componentize o frontend.

Estrutura esperada conceitualmente:

```text
resources/views/
├── layouts/
│   ├── app.blade.php
│   └── admin.blade.php
├── components/
│   ├── site/
│   │   ├── header.blade.php
│   │   ├── footer.blade.php
│   │   ├── hero.blade.php
│   │   ├── services.blade.php
│   │   ├── about.blade.php
│   │   ├── cta.blade.php
│   │   └── ...
│   └── admin/
└── pages/
    └── show.blade.php
```

Essa estrutura é apenas referência.

Adapte-a de acordo com o conteúdo real encontrado nos HTMLs.

Utilize Blade Components sempre que trouxerem reutilização ou separação de responsabilidades.

---

# 10. SEO

Criar módulo de SEO semelhante ao conjunto essencial oferecido por plugins básicos do WordPress.

Cada página deve poder configurar:

* SEO title;
* meta description;
* canonical URL;
* robots index/noindex;
* robots follow/nofollow;
* Open Graph title;
* Open Graph description;
* Open Graph image;
* Twitter/X card;
* slug.

Gerar corretamente no frontend:

```html
<title>
<meta name="description">
<meta name="robots">
<link rel="canonical">

<meta property="og:*">
<meta name="twitter:*">
```

Também implementar:

* `sitemap.xml` dinâmico;
* `robots.txt`;
* URLs amigáveis;
* canonical;
* tratamento de páginas 404.

Adicionar suporte estrutural a Schema.org/JSON-LD quando fizer sentido, principalmente:

* Organization;
* WebSite;
* WebPage;
* LocalBusiness, quando configurado.

Não gerar dados fictícios.

---

# 11. Configurações gerais do site

Criar área:

**Configurações > Site**

Permitir configurar pelo menos:

* nome do site;
* descrição;
* logo;
* logo alternativa;
* favicon;
* telefone;
* WhatsApp;
* e-mail;
* endereço;
* redes sociais;
* copyright;
* URL principal.

Caso os HTMLs apresentem outros dados globais relevantes, adicioná-los.

---

# 12. Menu

Criar gerenciamento simplificado de menu.

Permitir links para:

* páginas internas;
* âncoras;
* URLs externas.

Campos:

* label;
* URL/destino;
* ordem;
* target;
* ativo/inativo.

Como a página inicial é single page, suportar naturalmente:

```text
#inicio
#sobre
#servicos
#contato
```

Mas manter suporte a páginas futuras:

```text
/sobre
/blog
/contato
```

---

# 13. Mídia

Criar gerenciamento simples de mídia.

Permitir:

* upload de imagens;
* listagem;
* seleção;
* exclusão;
* utilização nos campos do CMS.

Validar:

* MIME type;
* extensão;
* tamanho máximo;
* segurança do nome do arquivo.

Utilizar Laravel Storage.

Criar configuração adequada para:

```bash
php artisan storage:link
```

Quando possível, gerar nomes seguros e únicos para os arquivos.

---

# 14. Usuários

Criar autenticação administrativa.

Usuário deve possuir:

* nome;
* e-mail;
* senha;
* status;
* role.

Roles iniciais:

* Administrator
* Editor

Administrator:

* acesso completo.

Editor:

* gerenciar conteúdo;
* páginas;
* SEO;
* mídia;

mas não deve poder executar configurações administrativas críticas nem gerenciar administradores.

Utilize:

* Policies;
* Gates;
* middleware;

conforme apropriado.

Não espalhe verificações manuais de role pelos controllers/views.

---

# 15. Segurança

Implementar boas práticas padrão do Laravel:

* CSRF;
* validação via Form Requests;
* escaping;
* proteção contra mass assignment;
* autorização;
* password hashing;
* rate limiting no login;
* validação de uploads;
* proteção das rotas administrativas;
* proteção contra IDOR através de autorização adequada.

Não permita editar propriedades sensíveis simplesmente porque vieram no request.

---

# 16. Painel administrativo

Utilize algum template administrativo conhecido, gratuito, open source e compatível com o projeto.

Preferência por algo como:

* AdminLTE;
* Tabler;

ou equivalente adequado.

Escolha uma solução que não complique desnecessariamente a aplicação.

O painel deve possuir sidebar semelhante a:

```text
Dashboard

Conteúdo
├── Páginas
├── Página inicial
├── Menu
└── Mídia

SEO
├── Configurações gerais
└── Sitemap

Aparência
├── Tema
└── Configurações

Administração
├── Usuários
└── Configurações
```

Adapte conforme a arquitetura final.

---

# 17. Dashboard

Criar dashboard simples contendo informações úteis, como:

* quantidade de páginas;
* páginas publicadas;
* páginas em rascunho;
* usuários;
* arquivos de mídia;
* última alteração de conteúdo.

Não é necessário criar analytics próprio.

---

# 18. REST API

O sistema deve possuir uma REST API organizada.

Utilizar versionamento:

```text
/api/v1/
```

Exemplos:

```text
GET    /api/v1/pages
GET    /api/v1/pages/{id}
POST   /api/v1/pages
PUT    /api/v1/pages/{id}
DELETE /api/v1/pages/{id}

GET    /api/v1/settings
GET    /api/v1/menus
GET    /api/v1/media
```

Separe adequadamente:

* Controllers Web;
* Controllers Admin;
* Controllers API.

Não faça controllers gigantes.

Utilize API Resources para padronizar respostas.

---

# 19. Padrão de resposta da API

Utilize respostas consistentes.

Exemplo:

```json
{
    "success": true,
    "data": {},
    "message": null
}
```

E para erro:

```json
{
    "success": false,
    "data": null,
    "message": "Validation failed",
    "errors": {}
}
```

Use corretamente os HTTP Status Codes.

---

# 20. SOLID e arquitetura

O código deve respeitar SOLID sem cair em overengineering.

Controllers devem principalmente:

1. receber request;
2. validar através de Form Request;
3. verificar autorização;
4. chamar a camada responsável;
5. devolver response/view.

Regras de negócio não devem ficar concentradas nos controllers.

Utilize Services para casos de uso relevantes.

Repositories devem ser utilizados somente quando realmente criarem uma abstração útil. Não crie interfaces e classes vazias apenas para dizer que existe Repository Pattern.

Exemplo conceitual:

```text
Controller
    ↓
Form Request
    ↓
Service
    ↓
Model / Repository
    ↓
Database
```

Aplicar:

* Dependency Injection;
* Single Responsibility;
* interfaces quando houver benefício real;
* baixo acoplamento;
* código testável.

---

# 21. Models esperados

Analise o domínio antes de definir definitivamente o banco.

Entretanto, provavelmente serão necessários conceitos semelhantes a:

```text
User
Page
PageSection
Media
Menu
MenuItem
Setting
SeoMetadata
```

Outras entidades devem surgir da análise dos HTMLs.

Por exemplo, se houver estruturas repetíveis complexas:

```text
Service
Testimonial
Faq
PortfolioItem
```

decida se devem ser entidades próprias ou dados de uma seção.

Justifique através da arquitetura e da reutilização esperada.

---

# 22. Banco de dados

Utilizar MySQL.

Todas as estruturas devem ser criadas através de migrations.

Adicionar:

* foreign keys;
* índices;
* unique indexes;
* timestamps;
* nullable corretamente;
* cascading/restrict conforme a regra de negócio.

Não criar tabelas manualmente fora das migrations.

Criar seeders para:

* usuário administrador inicial;
* configurações padrão;
* temas;
* conteúdo inicial extraído dos HTMLs.

O conteúdo atual do template deve aparecer no site após a instalação inicial.

---

# 23. Conteúdo inicial

Extraia os textos e demais conteúdos existentes dos HTMLs originais e transforme-os em seed inicial do CMS.

Objetivo:

Após executar a instalação e os seeders, o frontend deve ficar visualmente e textualmente equivalente ao template original escolhido.

Depois disso, o administrador poderá alterar os conteúdos pelo CMS.

---

# 24. Preview

Adicionar funcionalidade de preview.

O administrador deve conseguir visualizar alterações antes de publicar quando tecnicamente viável.

Para páginas em draft, a URL pública normal não deve disponibilizar o conteúdo sem autorização.

---

# 25. Cache

Prepare o sistema para utilizar cache em dados que mudam pouco, principalmente:

* settings;
* menus;
* páginas publicadas;
* SEO.

Quando esses conteúdos forem atualizados pelo painel, invalidar o cache correspondente.

Não introduza Redis obrigatoriamente neste primeiro momento.

O cache padrão do Laravel é suficiente, desde que a arquitetura permita trocar o driver futuramente.

---

# 26. Docker

Todo o ambiente deve funcionar via Docker.

Criar pelo menos:

```text
app
nginx
mysql
```

Se necessário, separar outros serviços.

Criar:

* `Dockerfile`
* `docker-compose.yml`
* configuração Nginx
* `.env.example`

Persistir:

* banco MySQL;
* storage necessário.

Adicionar healthcheck quando fizer sentido.

O projeto deve poder ser iniciado aproximadamente com:

```bash
docker compose up -d --build
```

Documente os comandos posteriores necessários.

---

# 27. Ambiente de desenvolvimento

Configurar adequadamente:

```env
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=mysql
```

Banco e credenciais devem ser obtidos de variáveis de ambiente.

Não hardcode:

* passwords;
* secrets;
* hosts;
* URLs;
* tokens.

---

# 28. Testes

Criar testes para funcionalidades importantes.

Prioridade:

* autenticação;
* autorização;
* CRUD de páginas;
* publicação/despublicação;
* slug;
* SEO;
* settings;
* API;
* permissões Administrator/Editor.

Utilizar Feature Tests onde apropriado.

Não é necessário buscar 100% de cobertura.

Priorize regras críticas.

---

# 29. Rotas

Organize as rotas.

Exemplo:

```text
routes/web.php
routes/api.php
```

Caso seja benéfico:

```text
routes/admin.php
```

Área administrativa:

```text
/admin
/admin/pages
/admin/pages/{id}/edit
/admin/media
/admin/menu
/admin/settings
/admin/users
```

---

# 30. Frontend público

O frontend público deve continuar utilizando o design existente.

NÃO utilize o template administrativo no site público.

O frontend deve ser derivado dos HTMLs originais e convertido para:

* Blade layouts;
* Blade components;
* assets organizados;
* conteúdo dinâmico.

Evite JavaScript desnecessário.

Preserve responsividade e comportamento do template original.

---

# 31. Performance

Aplicar boas práticas básicas:

* evitar N+1 queries;
* eager loading;
* índices;
* assets pelo Vite;
* cache;
* imagens com atributos adequados;
* lazy loading quando aplicável;
* CSS/JS organizados;
* evitar dependências desnecessárias.

---

# 32. Acessibilidade

Preserve e melhore, quando possível:

* HTML semântico;
* labels;
* alt de imagens;
* navegação;
* heading hierarchy;
* contraste;
* atributos ARIA somente quando necessários.

Alt text das imagens de conteúdo deve poder ser administrável.

---

# 33. Logs

Utilizar logging padrão do Laravel.

Registrar eventos administrativos relevantes quando útil, como:

* publicação;
* exclusão;
* alteração de configurações críticas.

Não registrar:

* senhas;
* tokens;
* secrets;
* informações sensíveis desnecessárias.

---

# 34. README

Crie um `README.md` completo dentro de `./site`.

Deve explicar:

* arquitetura;
* requisitos;
* instalação;
* Docker;
* configuração do `.env`;
* migrations;
* seeders;
* criação do storage link;
* Vite/assets;
* execução dos testes;
* usuário administrador inicial;
* estrutura da API;
* principais diretórios;
* como adicionar uma nova seção ao CMS;
* como adicionar uma nova página;
* como criar um novo tema/preset.

---

# 35. Fluxo de instalação esperado

Quero chegar o mais próximo possível de:

```bash
cd site

cp .env.example .env

docker compose up -d --build

docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link
```

Se o processo escolhido exigir outros comandos, documente-os.

---

# 36. Critérios de qualidade

Não considere a tarefa concluída apenas porque o projeto inicializa.

Antes de finalizar, verifique:

* Docker sobe corretamente;
* Laravel conecta no MySQL;
* migrations executam;
* seeders executam;
* login administrativo funciona;
* permissões funcionam;
* CRUD de páginas funciona;
* edição da página inicial funciona;
* conteúdo salvo aparece no frontend;
* SEO aparece no HTML renderizado;
* sitemap funciona;
* upload funciona;
* menu funciona;
* temas funcionam;
* REST API funciona;
* testes principais passam;
* frontend mantém fidelidade aos HTMLs originais.

Corrija os erros encontrados antes de considerar o projeto concluído.

---

# 37. Metodologia de implementação

Não tente gerar o projeto inteiro cegamente de uma só vez.

Trabalhe em etapas.

## Etapa 1 — Análise

Primeiro:

1. analise `index.html`;
2. analise `index-cinza.html`;
3. analise `index-claro.html`;
4. identifique assets e dependências;
5. compare as três versões;
6. identifique componentes;
7. identifique campos editáveis;
8. proponha o modelo de dados.

## Etapa 2 — Fundação

Depois:

1. crie `./site`;
2. configure Laravel;
3. configure Docker;
4. configure MySQL;
5. configure Nginx;
6. configure autenticação;
7. configure template administrativo.

## Etapa 3 — Domínio

Implemente:

1. páginas;
2. seções;
3. settings;
4. mídia;
5. menu;
6. SEO;
7. usuários/permissões.

## Etapa 4 — Frontend

Converta os HTMLs para:

* layouts Blade;
* componentes;
* conteúdo dinâmico;
* temas/presets.

## Etapa 5 — Administração

Crie os formulários administrativos baseando-se nos campos reais encontrados nos templates.

## Etapa 6 — API

Implemente e documente `/api/v1`.

## Etapa 7 — Qualidade

Execute:

* migrations;
* seeders;
* testes;
* análise de erros;
* validação das páginas;
* validação dos containers.

---

# 38. Regra importante sobre decisões

Você possui autonomia para tomar decisões técnicas razoáveis.

Não pare a implementação para perguntar sobre pequenos detalhes que podem ser decididos seguindo boas práticas.

Quando houver mais de uma alternativa válida:

1. escolha a solução mais simples;
2. priorize manutenção;
3. evite dependências desnecessárias;
4. documente a decisão.

Somente solicite minha intervenção caso exista uma decisão realmente bloqueante ou uma ambiguidade que possa alterar significativamente o produto.

---

# 39. O que NÃO fazer

Não:

* recriar o frontend do zero;
* substituir o design existente por um template genérico;
* apagar os HTMLs originais;
* colocar o projeto Laravel na raiz;
* criar tudo dentro de controllers;
* armazenar a página inteira como HTML editável;
* criar um page builder excessivamente complexo;
* adicionar React/Vue sem necessidade;
* criar uma SPA administrativa sem necessidade;
* hardcodar conteúdo que deveria ser editável;
* hardcodar secrets;
* ignorar SEO;
* ignorar autorização;
* deixar endpoints administrativos públicos;
* criar abstrações SOLID artificiais sem benefício;
* duplicar os três templates inteiros se as diferenças puderem ser tratadas por temas.

---

# 40. Resultado esperado

Ao final, quero ter em:

`./site`

um CMS Laravel funcional e reutilizável que ofereça o conjunto essencial de funcionalidades que normalmente faria alguém utilizar WordPress para um site institucional, mas com uma experiência administrativa muito mais simples.

O usuário administrativo deverá conseguir gerenciar o site sem editar código.

A instalação inicial deve reproduzir a single page existente nos HTMLs da pasta raiz, incluindo suas variações visuais, enquanto a arquitetura deve permitir posteriormente adicionar outras páginas.

O resultado deve possuir:

* CMS;
* painel administrativo;
* gerenciamento da página inicial;
* páginas adicionais;
* editor simplificado por seções;
* mídia;
* menu;
* SEO;
* usuários e permissões;
* configurações gerais;
* temas/presets;
* Blade componentizado;
* REST API;
* MySQL;
* Docker;
* testes;
* documentação.

Comece agora pela análise dos três arquivos HTML existentes e, a partir deles, defina a estrutura real de componentes, campos e entidades antes de iniciar a implementação.
