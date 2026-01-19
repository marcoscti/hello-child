# Tema Filho do Hello Elementor (IgesDF)

Este é um tema filho para o [Hello Elementor](https://elementor.com/hello-theme/), customizado para atender às necessidades específicas do IgesDF. O arquivo `functions.php` contém toda a lógica customizada, adicionando funcionalidades, tipos de post, otimizações e integrações.

## Estrutura e Funcionalidades

### 1. Inclusão de Shortcodes
O tema carrega shortcodes customizados a partir do arquivo `custom-shortcodes.php`.

```php
include('custom-shortcodes.php');
```

### 2. Otimização de SEO e Redes Sociais

#### Open Graph Dinâmico
Para garantir uma boa aparência ao compartilhar links em redes sociais (como Facebook, WhatsApp, etc.), o tema gera meta tags Open Graph (OG) dinamicamente.
- **`og:title`**: Usa o título do post/página.
- **`og:description`**: Usa o resumo (excerpt), um campo ACF chamado `resumo`, ou um resumo do conteúdo principal.
- **`og:image`**: Usa a imagem destacada do post, um campo de meta `og_image`, ou uma imagem padrão do IgesDF.
- **`og:url`**: O link permanente do post/página.

Essa funcionalidade é ativada pela função `add_dynamic_og_image` no hook `wp_head`.

#### Título da Página
O título exibido na aba do navegador (`<title>`) para posts e páginas é simplificado para mostrar apenas o título do conteúdo, melhorando a clareza.

```php
add_filter('pre_get_document_title', function ($title) {
    return is_singular() ? get_the_title() : $title;
}, 99);
```

### 3. Menus de Navegação
O tema registra 4 localizações de menu no WordPress:
- **Menu Topo** (`menu_topo`)
- **Menu Social** (`menu_social`)
- **Menu Principal** (`menu_principal`)
- **Menu Unidades** (`menu_unidades`)

### 4. Acessibilidade (VLibras)
O widget do [VLibras](https://www.vlibras.gov.br/) (Suíte de Ferramentas de Acessibilidade do Governo Brasileiro) é integrado ao site, adicionando um tradutor de conteúdo para a Língua Brasileira de Sinais (Libras). Os scripts são carregados de forma assíncrona no rodapé.

### 5. Otimizações de Performance

#### Remoção do jQuery Migrate
Para reduzir o carregamento de scripts desnecessários no front-end, o `jquery-migrate.js` é removido.

```php
add_action('wp_default_scripts', 'remove_jquery_migrate');
```

#### Sistema de Cache Simples
Um sistema de cache embutido foi implementado para acelerar o tempo de carregamento para usuários não logados.
- **Como funciona**: Ele salva uma versão HTML estática das páginas em um diretório `cache/` na raiz do WordPress.
- **Servindo o cache**: Se um visitante não está logado e uma versão em cache da página existe, o arquivo HTML é servido diretamente, sem carregar o PHP e o banco de dados do WordPress.
- **Limpeza de cache**: O cache é limpo automaticamente sempre que um post é criado, atualizado ou excluído.
- **Observação**: O diretório `cache/` na raiz da instalação do WordPress (`/var/www/html/cache/` ou similar) precisa ter permissões de escrita.

### 6. Tipos de Post Customizados (CPTs)
Para organizar melhor o conteúdo do site, foram criados os seguintes Tipos de Post:
- **Estimativas** (`ato`)
- **Notícias** (`noticia`)
- **Impresso** (`impresso`)
- **Inexigibilidade / Dispensa** (`dispensa`)
- **Produções** (`producao`)

Cada um possui configurações específicas de visibilidade, ícones no painel e suporte a funcionalidades como editor, miniaturas e categorias.

### 7. Integrações

#### Desativar Pesquisa no TablePress
As tabelas criadas com o plugin TablePress são impedidas de aparecer nos resultados de busca nativa do WordPress para evitar poluição nos resultados.

#### CSS de Templates Elementor
O CSS de um template específico do Elementor (ID `25594`) é carregado globalmente em todas as páginas para garantir a consistência de estilos de elementos globais, como o cabeçalho.
