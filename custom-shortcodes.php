<?php

function linha_do_tempo_function()
{

	global $post;

	$categoria = "";
	$cat_page = $_GET['cat'];
	if (!empty($cat_page) && count($cat_page) > 0) {

		$categoria = $cat_page;
	}

	$filtroPorData = $cat_page = get_post_meta($post->ID, "filtro_por_data", true);
	$filtroPorData = $filtroPorData == "Sim" ? true : false;
	$urlSite = site_url();

	$status = "";
	if (!empty($_GET['status'])) {
		$status = $_GET['status'];
	}
	global $wpdb;
	$result = array();
	$query = "SELECT YEAR(post_date) AS Years FROM {$wpdb->posts} p" .
		" WHERE post_type='ato' AND post_status = 'publish' GROUP BY YEAR(post_date) DESC";
	$results = $wpdb->get_results(
		$wpdb->prepare(
			$query
		),
		ARRAY_N
	);
	$years = array();
	if (is_array($results) && count($results) > 0) {
		foreach ($results as $result) {
			$years[] = $result[0];
		}
	}
	$statusdisplay = ($status == 'em-andamento' ||  $status == 'finalizado' || $status == 'suspenso-ou-cancelado' ? 'ativo' : '');

	$html = '';

	if ($filtroPorData) :


		$html .= '<div id=loading class="d-none">';
		$html .= '	<div id="loading_panel" class="row">';
		$html .= '		<div class="col-12 text-center p-5">';
		$html .= '			<i class="fas fa-spinner fa-spin fa-2x"></i>';
		$html .= '		</div>';
		$html .= '	</div>';
		$html .= '</div>';

		$html .= '<div class="row padding_0right">';
		$html .= '<div class="col-lg-9 padding_0right">';
		$html .= '	<div class="slide-anos">';
		$html .= '			<ul class="anos-linha-tempo d-flex justify-content-between center slider">';

		foreach ($years as $year) :
			$html .= '          <li><a href="#!" class="texto-s-sublinhado">' . $year . '</a></li>';
		endforeach;

		$html .= '	        </ul>';
		$html .= '	 <div class="aux-linha-tempo"></div>';
		$html .= '</div><div class="clearfix"></div>';


		$html .= '	<div class="row d-flex justify-content-between acoes-busca-interno">';
		$html .= '	<div class="col-lg-3 busca-interno">';
		$html .= '	        <input type="text" name="s" id="search_ato" value="' . the_search_query() . '" placeholder="Busca por ato" class="texto-maiusculo" />';
		$html .= '	</div>';
		$html .= '	<div class="col-lg-6 selecao-interno">';
		$html .= '	    <form>';
		$html .= '	        <div class="form-group row">';
		$html .= '	            <label for="selecaofornecedores" class="col-sm-3 col-form-label texto-maiusculo texto-negrito">Categorias:</label>';
		$html .= '	            <div class="col-sm-9">';
		$html .= '	                <select class="custom-select my-1 mr-sm-2 texto-maiusculo" id="selecaofornecedores">';

		$catempty = (empty($categoria) ? 'selected' : '');
		$html .= '	                <option ' . $catempty . 'value="">Seleção de fornecedores</option>';

		$parent = get_category_by_slug('selecao-de-fornecedores');
		$categories = get_categories(
			array(
				'parent' => $parent->cat_ID,
				"hide_empty" => 0
			)
		);
		usort($categories, function ($a, $b) {
			return strcmp($a->cat_name, $b->cat_name);
		});

		foreach ($categories as $c) :
			$sel = ($categoria == $c->slug ? 'selected' : '');
			$html .= '                  <option ' . $sel . ' value="' . $c->slug . '">' . $c->cat_name . '</option>';
		endforeach;
		$html .= '                </select>';
		$html .= '            </div>';
		$html .= '        </div>';
		$html .= '    </form>';
		$html .= '</div>';
		$html .= '</div>';

	else :

		$html .= '<div class="row d-flex justify-content-between acoes-busca-interno">';
		$html .= '<div class="col-lg-3 busca-interno">';
		$html .= '    <form action="/" method="get" class="form-busca">';
		$html .= '        <input type="text" name="s" id="search_ato" value="' . the_search_query() . '" placeholder="Busca por ato" class="texto-maiusculo" />';
		$html .= '    </form>';
		$html .= '</div>';
		$html .= '<div class="col-lg-9 selecao-interno">';
		$html .= '    <div class="col-lg-12 ">';
		$html .= '        <ul class="legenda inline">';
		$html .= '            <li><a href="#!" data-setstatus="em-andamento" class="andamento ' . $statusdisplay . '"><span>Em andamento</span></a></li>';
		$html .= '            <li><a href="#!" data-setstatus="finalizado" class="finalizado ' . $statusdisplay . '"><span>Finalizado</span></a></li>';
		$html .= '            <li><a href="#!" data-setstatus="suspenso-ou-cancelado" class="cancelado ' . $statusdisplay . '"><span>Suspenso ou cancelado</span></a></li>';
		$html .= '        </ul>';
		$html .= '    </div>';
		$html .= '</div>';
		$html .= '</div>';

	endif;

	$html .= '<div id="ajax_content" class="position-relative" data-status="' . $status . '" data-url="' . $urlSite . '/pagina-ajax" data-tipo="' . $categoria . '">';
	$html .= '<br />';
	$html .= '<br />';
	$html .= '<br />';
	$html .= '</div>';
	$html .= '</div>';

	if ($filtroPorData) :
		$html .= '<div class="col-lg-3 d-flex justify-content-end">';
		$html .= '		<ul class="legenda">';
		$html .= '				<li><a href="#!" data-setstatus="andamento" class="em-andamento' . $statusdisplay . '"><span>Em andamento</span></a></li>';
		$html .= '				<li><a href="#!" data-setstatus="finalizado" class="finalizado' . $statusdisplay . '"><span>Finalizado</span></a></li>';
		$html .= '				<li><a href="#!" data-setstatus="suspenso" class="suspenso-cancelado' . $statusdisplay . '"><span>Suspenso ou cancelado</span></a></li>';
		$html .= '		</ul>';
		$html .= '</div>';
		$html .= '</div>';
	endif;

	return $html;
}
add_shortcode('linha_do_tempo', 'linha_do_tempo_function');

function inexigibilidade_function()
{

	global $post;

	//CONFIG
	$post_por_pagina = 9;
	$maximo_visualisacao_paginacao = 5;
	/////////////////

	$post_type = get_post_meta($post->ID, "slug_type", true);
	$pagina = 1;
	$categoria = "";
	$busca = "";
	$link = "";

	if (!empty($_GET['pagina']))
		$pagina = $_GET['pagina'];
	if (!empty($_GET['c']))
		$categoria = $_GET['c'];
	if (!empty($_GET['b']))
		$busca = $_GET['b'];

	$pagina = $pagina <= 0 ? 1 : $pagina;

	$args = array(
		'post_type' =>  $post_type,
		'posts_per_page' => $post_por_pagina,
		'paged' => $pagina,
		'orderby'			=> 'meta_value',
		'order'				=> 'DESC',
		'meta_key'			=> 'ano_e_numero_do_ato'
	);

	if (!empty($categoria)) {
		$args['category_name'] = $categoria;
		$link .= "&c=$categoria";
	}

	if (!empty($busca)) {
		$args['search_prod_title'] = $busca;
		$link .= "&b=$busca";
	}


	$posts = new WP_Query($args);
	$total_page = $posts->max_num_pages;
	$pagina = $pagina > $total_page ? $total_page : $pagina;

	$sel = ($categoria == 'inexigibilidade' || $categoria == 'dispensa' ? 'selected' : '');

	$html = <<<EOD
<div class="row">
    <div class="col-9 mb-5">
        <form method="get" class="campo-buscar">
            <input type='hidden' name="c" value="$categoria">
            <input type='text' class="form-control col-sm-2 " name="b" value="$busca" />
            <button type="submit" class="btn btn-primary"> buscar </button>
        </form>
    </div>
    <div class="col-3 mb-5">
        <select class="select-linked custom-select my-1 mr-sm-2 texto-maiusculo">
            <option $sel value="https://igesdf.org.br/compras-e-contratos/inexigibilidade-dispensa/?c=inexigibilidade">Inexigibilidade</option>
            <option $sel value="https://igesdf.org.br/compras-e-contratos/inexigibilidade-dispensa/?c=dispensa">Dispensa</option>
        </select>
    </div>
EOD;


	foreach ($posts->posts as $post) : setup_postdata($post);
		$data = get_the_date(('j \d\e F \d\e Y'));
		$link_each = get_permalink();
		$desc = nl2br(get_post_meta(get_the_ID(), 'descricao', true));
		$titulo_each =  get_the_title($post);

		$html .= <<<EOD
    <article class="col-lg-12 borda-cinza ">
        <p ><span class="texto-negrito">Publicado em: </span> $data</p> 
        <a class="link-neutro" href="$link_each">
            <h5 class="texto-c-sublinhado">a $titulo_each </h5>
        </a>
        <p>$desc</p>
    </article>			
EOD;
	endforeach;

	$html .= <<<EOD
</div>  
EOD;

	/* paginação */
	if ($total_page > 1) :
		$media = ceil($maximo_visualisacao_paginacao / 2);
		$inicio = $pagina + 1 - $media;
		$inicio = $inicio < 1 ? 1 : $inicio;
		$fim = ($inicio == 1 ? ($total_page > $maximo_visualisacao_paginacao ? $maximo_visualisacao_paginacao : $total_page) : ($pagina + $media - 1));
		$fim =  $fim > $total_page ? $total_page : $fim;
		$dif = $fim - $inicio;
		if ($dif < $maximo_visualisacao_paginacao) {
			if ($total_page > $maximo_visualisacao_paginacao) {
				$inicio = $fim - ($maximo_visualisacao_paginacao - 1);
			}
		}
		$linkvoltar = ($pagina - 1) . $link;

		$html .= <<<EOD
<div class="conteudo-paginacao mt-5">
        <p>Página $pagina de $total_page</p>
        <ul class="paginacao">
EOD;
		if ($pagina > 1) :

			$html .= <<<EOD
            <li class="li-txt"><a href="?pagina=1$link">Primeira</a></li>
            <li>...</li>
            <li><a href="?pagina=$linkvoltar">❮</a></li>
EOD;
		endif;

		for ($i = $inicio; $i <= $fim; $i++) :
			if ($i == $pagina) :
				$html .= '<li class="ativo">' . $i . '</li>';
			else :
				$html .= '<li><a href="?pagina=' . $i . $link . '">' . $i . '</a></li>';
			endif;
		endfor;


		if ($pagina < $total_page) :
			$html .= '<li><a href="?pagina=' . ($pagina + 1) . $link . '">❯</a></li>';
			$html .= '<li>...</li>';
			$html .= '<li class="li-txt"><a href="?pagina=' . $total_page . $link . '" >Última</a></li>';
		endif;

		$html .= '</ul>';
		$html .= '</div>';
	endif;

	return $html;
}
add_shortcode('inexigibilidade_busca', 'inexigibilidade_function');


function proc_seletivo_function()
{
	global $post;
	$urlSite = esc_url(site_url());
	$url = esc_url(get_template_directory_uri());

	$post_por_pagina = 9;
	$maximo_visualisacao_paginacao = 5;

	$post_type = get_post_meta($post->ID, "slug_type", true);
	$pagina = !empty($_GET['pagina']) && is_numeric($_GET['pagina']) ? intval($_GET['pagina']) : 1;
	$categoria = !empty($_GET['c']) ? sanitize_text_field($_GET['c']) : '';
	$busca = !empty($_GET['b']) ? sanitize_text_field($_GET['b']) : '';
	$link = '';

	$pagina = max($pagina, 1);

	$args = array(
		'post_type' => sanitize_text_field($post_type),
		'posts_per_page' => $post_por_pagina,
		'paged' => $pagina,
	);

	if (!empty($categoria)) {
		$args['category_name'] = $categoria;
		$link .= "&c=" . urlencode($categoria);
	}

	if (!empty($busca)) {
		$args['s'] = $busca;
		$link .= "&b=" . urlencode($busca);
	}

	$posts = new WP_Query($args);
	$total_page = $posts->max_num_pages;
	$pagina = min($pagina, $total_page);

	$html = '<div class="row">
                <div class="col-12 mb-5">
                    <div class="processo">
                        <ul>
                            <li class="verde"><a href="">Inscrições abertas</a></li>
                            <li class="amarelo"><a href="">Em andamento</a></li>
                            <li class="azul"><a href="">Finalizado</a></li>
                            <li class="preto"><a href="">Suspenso ou cancelado</a></li>
                        </ul>
                    </div>
                    <form method="get" class="campo-buscar">
                        <input type="text" class="form-control col-sm-2" name="b" value="' . esc_attr($busca) . '" />
                        <button type="submit" class="btn btn-primary">Buscar</button>
                    </form>
                </div>';

	foreach ($posts->posts as $post) : setup_postdata($post);
		$status = get_post_meta(get_the_ID(), 'status', true);
		switch ($status) {
			case "1":
				$status = "Inscrições abertas: ";
				$inscreva_se = "Inscreva-se";
				$class = "inscricao-aberta";
				break;
			case "2":
				$status = "Em andamento";
				$inscreva_se = "";
				$class = "processo-andamento";
				break;
			case "3":
				$status = "Finalizado";
				$inscreva_se = "";
				$class = "processo-finalizado";
				break;
			case "4":
				$status = "Suspenso ou cancelado";
				$inscreva_se = "";
				$class = "suspenso-ou-cancelado";
				break;
			default:
				$status = "";
		}

		$date = DateTime::createFromFormat('Ymd', get_post_meta(get_the_ID(), 'data_limite', true));
		$prazo = ($date !== false) ? esc_html($date->format('d/m/Y')) : '';
		$data_do_post = get_the_date('j \d\e F \d\e Y');
		$link_each = get_permalink();
		$titulo_each = get_the_title($post);
		$link_inscricao = get_post_meta(get_the_ID(), 'link_inscricoes', true);

		$html .= '<article class="col-lg-12 ' . esc_attr($class) . '">
                    <p class="texto-negrito">' . esc_html($data_do_post) . '</p>
                    <a class="link-neutro" href="' . esc_url($link_each) . '">
                        <h5 class="texto-c-sublinhado">' . esc_html($titulo_each) . '</h5>
                        <p>Prazo de inscrição: ' . $prazo . '</p>
                        <p>' . esc_html($status);

		if (!empty($inscreva_se)) {
			$html .= '<a class="texto-negrito texto-s-sublinhado" href="' . esc_url($link_inscricao) . '">' . esc_html($inscreva_se) . '</a>';
		}

		$html .= '</p></a></article>';

	endforeach;

	$html .= '</div>'; // Fechamento da div row

	// Continuação para paginação...

	return $html;
}
add_shortcode('proc_seletivo', 'proc_seletivo_function');



function proc_seletivo_get_status_function()
{
	global $post;
	$status = get_post_meta(get_the_ID($post), 'status', true);
	switch ($status) {
		case "1":
			$status = "Inscrições abertas";
			break;
		case "2":
			$status = "Em andamento";
			break;
		case "3":
			$status = "Finalizado";
			break;
		case "4":
			$status = "Suspenso ou cancelado";
			break;
		default:
			$status = "";
	}

	$html = '<b>' . $status . '</b>';

	return $html;
}
add_shortcode('proc_seletivo_get_status', 'proc_seletivo_get_status_function');

function proc_seletivo_get_prazo_function()
{
	global $post;

	$date_string = get_post_meta(get_the_ID($post), 'data_limite', true);
	$date = DateTime::createFromFormat('Ymd', $date_string);

	if ($date) {
		$html = '<p>Prazo de inscrição: ' . $date->format('d/m/Y') . '</p>';
	} else {
		// Manipulação de erro ou mensagem padrão se a data não estiver disponível
		$html = '<p>Data limite não disponível.</p>';
	}

	return $html;
}
add_shortcode('proc_seletivo_get_prazo', 'proc_seletivo_get_prazo_function');



function proc_seletivo_inscricoes_botao_function()
{
	global $post;

	$status = get_post_meta(get_the_ID($post), 'status', true);
	switch ($status) {
		case "1":
			$status = "Inscrições abertas";
			break;
		case "2":
			$status = "Em andamento";
			break;
		case "3":
			$status = "Finalizado";
			break;
		case "4":
			$status = "Suspenso ou cancelado";
			break;
		default:
			$status = "";
	}

	$html = '';
	if ($status == "Inscrições abertas"):
		$html .= '<p><a class="inscrevase texto-negrito texto-s-sublinhado" href="' . get_post_meta(get_the_ID($post), 'link_inscricoes', true) . '">Inscreva-se</a></p>';
	endif;


	return $html;
}
add_shortcode('proc_seletivo_inscricoes_botao', 'proc_seletivo_inscricoes_botao_function');


function single_audio_function($post_id)
{

	$audio = get_field('audio_da_radio');
	$post_title = get_the_title($post_id);
	$html = '<h1 id="aud">' . $audio . '</h1>';

	$html .= <<<EOF
	<style>
	#aud{ display: none}
	div.control-container {
		margin-top: 10px;
		padding-bottom: 10px; }
		div.control-container div.amplitude-play-pause {
		  width: 74px;
		  height: 74px;
		  cursor: pointer;
		  float: left;
		  margin-left: 10px; }
		div.control-container div.amplitude-play-pause.amplitude-paused {
		  background: url("https://521dimensions.com/img/open-source/amplitudejs/examples/single-song/play.svg");
		  background-size: cover; }
		div.control-container div.amplitude-play-pause.amplitude-playing {
		  background: url("https://521dimensions.com/img/open-source/amplitudejs/examples/single-song/pause.svg");
		  background-size: cover; }
		div.control-container div.meta-container {
		  float: left;
		  width: calc(100% - 84px);
		  text-align: center;
		  color: white;
		  margin-top: 10px; }
		  div.control-container div.meta-container span[data-amplitude-song-info="name"] {
			font-family: "Open Sans", sans-serif;
			font-size: 18px;
			color: #fff;
			display: block; }
		  div.control-container div.meta-container span[data-amplitude-song-info="artist"] {
			font-family: "Open Sans", sans-serif;
			font-weight: 100;
			font-size: 14px;
			color: #fff;
			display: block; }
		div.control-container:after {
		  content: "";
		  display: table;
		  clear: both; }
	  @media screen and (max-width: 39.9375em) {
		div.control-container div.amplitude-play-pause {
		  background-size: cover;
		  width: 64px;
		  height: 64px; }
		div.control-container div.meta-container {
		  width: calc(100% - 74px); } }
	  div.time-container {
		opacity: 0.5;
		font-family: 'Open Sans';
		font-weight: 100;
		font-size: 12px;
		color: #fff;
		height: 15px; }
		div.time-container span.current-time {
		  float: left;
		  margin-left: 5px; }
		div.time-container span.duration {
		  float: right;
		  margin-right: 5px; }
	  progress.amplitude-song-played-progress {
		background-color: #313252;
		-webkit-appearance: none;
		appearance: none;
		width: 100%;
		height: 5px;
		display: block;
		cursor: pointer;
		border: none; }
		progress.amplitude-song-played-progress:not([value]) {
		  background-color: #313252; }
	  
	  progress[value]::-webkit-progress-bar {
		background-color: #313252; }
	  
	  progress[value]::-moz-progress-bar {
		background-color: #00a0ff; }
	  
	  progress[value]::-webkit-progress-value {
		background-color: #00a0ff; }
	  
	  div.bottom-container {
		background-color: #202136;
		border-bottom-right-radius: 10px;
		border-bottom-left-radius: 10px; }
	  div#single-song-player {
		border-radius: 10px;
		margin: auto;
		box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.5);
		margin-top: 50px;
		width: 100%;
		max-width: 460px;
		-webkit-font-smoothing: antialiased; }
		div#single-song-player img[data-amplitude-song-info="cover_art_url"] {
		  width: 100%;
		  border-top-right-radius: 10px;
		  border-top-left-radius: 10px;
		  padding: 20px;
    	  background-color: antiquewhite;}
	  a.learn-more{
		display: block;
		width: 300px;
		margin: auto;
		margin-top: 30px;
		text-align: center;
		color: white;
		text-decoration: none;
		background-color: #202136;
		padding: 20px;
		font-weight: 100;
	  }
	</style>

    <div id="single-song-player">
      <img data-amplitude-song-info="cover_art_url"/>
      <div class="bottom-container">
        <progress class="amplitude-song-played-progress" id="song-played-progress"></progress>

        <div class="time-container">
          <span class="current-time">
            <span class="amplitude-current-minutes"></span>:<span class="amplitude-current-seconds"></span>
          </span>
          <span class="duration">
            <span class="amplitude-duration-minutes"></span>:<span class="amplitude-duration-seconds"></span>
          </span>
        </div>

        <div class="control-container">
          <div class="amplitude-play-pause" id="play-pause"></div>
          <div class="meta-container">
            <span data-amplitude-song-info="name" class="song-name"></span>
            <span data-amplitude-song-info="artist"></span>
          </div>
        </div>
      </div>
    </div>
	<script src="https://cdn.jsdelivr.net/npm/amplitudejs@latest/dist/amplitude.min.js"></script>
	<script>
	 //let aud = document.getElementById('aud').innerText;
		Amplitude.init({
			"bindings": {
			37: 'prev',
			39: 'next',
			32: 'play_pause'
			},
			"songs": [
			{
				"name": "$post_title",
				"artist": "IGES-DF",
				"album": "Rádio IGES-DF",
				"url": "$audio",
				"cover_art_url": "https://igesdf.org.br/wp-content/uploads/2021/01/Logo-IGESDF.frontellosvg-03.svg"
			}
			]
		});
		
		window.onkeydown = function(e) {
			return !(e.keyCode == 32);
		};
		
		/*
			Handles a click on the song played progress bar.
		*/
		document.getElementById('song-played-progress').addEventListener('click', function( e ){
			var offset = this.getBoundingClientRect();
			var x = e.pageX - offset.left;
		
			Amplitude.setSongPlayedPercentage( ( parseFloat( x ) / parseFloat( this.offsetWidth) ) * 100 );
		});
	</script>
EOF;

	return $html;
}
add_shortcode('single_audio', 'single_audio_function');

function archive_audio_function()
{

	// $audio = get_field('audio_da_radio');
	// $html = '<h1 id="aud">'. $audio .'</h1>';
	$html = <<<EOF
	<style>
	div#amplitude-player{background:#fff;box-shadow:0 2px 12px 8px rgba(0,0,0,.1);margin:auto;margin-top:20px;margin-bottom:20px;display:flex;max-width:900px}@media screen and (max-width:39.9375em){div#amplitude-player{flex-direction:column}}@media screen and (min-width:40em) and (max-width:63.9375em){div#amplitude-player{max-height:715px}}@media screen and (min-width:64em){div#amplitude-player{max-height:715px}}div#amplitude-left{padding:0;border-right:1px solid #cfd8dc;width:50%;display:flex;flex-direction:column}div#amplitude-left img.album-art{width:100%}div#amplitude-left div#player-left-bottom{flex:1;background-color:#f1f1f1;padding:20px 10px}div#amplitude-left div#player-left-bottom div#volume-container:after{content:"";display:table;clear:both}@media screen and (max-width:39.9375em){div#amplitude-player div#amplitude-left{width:100%}div#amplitude-player div#amplitude-left img[amplitude-song-info=cover_art_url]{width:auto;height:auto}}div#amplitude-right{padding:0;overflow-y:scroll;width:50%;display:flex;flex-direction:column}div#amplitude-right div.song{cursor:pointer;padding:10px}div#amplitude-right div.song div.song-now-playing-icon-container{float:left;width:20px;height:20px;margin-right:10px}div#amplitude-right div.song div.song-now-playing-icon-container img.now-playing{display:none;margin-top:15px}div#amplitude-right div.song div.play-button-container{display:none;background:url(https://521dimensions.com/img/open-source/amplitudejs/blue-player/list-play-light.png) no-repeat;width:22px;height:22px;margin-top:10px}div#amplitude-right div.song div.play-button-container:hover{background:url(https://521dimensions.com/img/open-source/amplitudejs/blue-player/list-play-hover.png) no-repeat}div#amplitude-right div.song.amplitude-active-song-container div.song-now-playing-icon-container img.now-playing{display:block}div#amplitude-right div.song.amplitude-active-song-container:hover div.play-button-container{display:none}div#amplitude-right div.song div.song-meta-data{float:left;width:calc(100% - 110px)}div#amplitude-right div.song div.song-meta-data span.song-title{color:#272726;font-size:16px;display:block;font-weight:300;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}div#amplitude-right div.song div.song-meta-data span.song-artist{color:#607d8b;font-size:14px;font-weight:700;text-transform:uppercase;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}div#amplitude-right div.song img.bandcamp-grey{float:left;display:block;margin-top:10px}div#amplitude-right div.song img.bandcamp-white{float:left;display:none;margin-top:10px}div#amplitude-right div.song span.song-duration{float:left;width:55px;text-align:center;line-height:45px;color:#607d8b;font-size:16px;font-weight:500}div#amplitude-right div.song:after{content:"";display:table;clear:both}@media screen and (max-width:39.9375em){div#amplitude-player div#amplitude-right{width:100%}}div#progress-container{width:70%;float:left;position:relative;height:20px;cursor:pointer}div#progress-container:hover input[type=range].amplitude-song-slider::-webkit-slider-thumb{display:block}div#progress-container:hover input[type=range].amplitude-song-slider::-moz-range-thumb{visibility:visible}div#progress-container progress#song-played-progress{width:100%;position:absolute;left:0;top:8px;right:0;width:100%;z-index:60;-webkit-appearance:none;-moz-appearance:none;appearance:none;height:4px;border-radius:5px;background:0 0;border:none}@media all and (-ms-high-contrast:none){div#progress-container ::-ms-backdrop,div#progress-container progress#song-played-progress{color:#00a0ff;border:none;background-color:#cfd8dc}}@supports (-ms-ime-align:auto){div#progress-container progress#song-played-progress{color:#00a0ff;border:none}}div#progress-container progress#song-played-progress[value]::-webkit-progress-bar{background:0 0;border-radius:5px}div#progress-container progress#song-played-progress[value]::-webkit-progress-value{background-color:#00a0ff;border-radius:5px}div#progress-container progress#song-played-progress::-moz-progress-bar{background:0 0;border-radius:5px;background-color:#00a0ff;height:5px;margin-top:-2px}div#progress-container progress#song-buffered-progress{position:absolute;left:0;top:8px;right:0;width:100%;z-index:10;-webkit-appearance:none;-moz-appearance:none;appearance:none;height:4px;border-radius:5px;background:0 0;border:none;background-color:#d7dee3}div#progress-container progress#song-buffered-progress[value]::-webkit-progress-bar{background-color:#cfd8dc;border-radius:5px}div#progress-container progress#song-buffered-progress[value]::-webkit-progress-value{background-color:#78909c;border-radius:5px;transition:width .1s ease}div#progress-container progress#song-buffered-progress::-moz-progress-bar{background:0 0;border-radius:5px;background-color:#78909c;height:5px;margin-top:-2px}div#progress-container progress::-ms-fill{border:none}@-moz-document url-prefix(){div#progress-container progress#song-buffered-progress{top:9px;border:none}}@media all and (-ms-high-contrast:none){div#progress-container ::-ms-backdrop,div#progress-container progress#song-buffered-progress{color:#78909c;border:none}}@supports (-ms-ime-align:auto){div#progress-container progress#song-buffered-progress{color:#78909c;border:none}}div#progress-container input[type=range]{-webkit-appearance:none;width:100%;margin:7.5px 0;position:absolute;z-index:9999;top:-7px;height:20px;cursor:pointer;background-color:inherit}div#progress-container input[type=range]:focus{outline:0}div#progress-container input[type=range]::-webkit-slider-runnable-track{width:100%;height:0;cursor:pointer;box-shadow:0 0 0 transparent,0 0 0 rgba(13,13,13,0);background:#0075a9;border-radius:0;border:0 solid #010101}div#progress-container input[type=range]::-webkit-slider-thumb{box-shadow:0 0 0 #000,0 0 0 #0d0d0d;border:1px solid #00a0ff;height:15px;width:15px;border-radius:16px;background:#00a0ff;cursor:pointer;-webkit-appearance:none;margin-top:-7.5px}div#progress-container input[type=range]:focus::-webkit-slider-runnable-track{background:#00adfb}div#progress-container input[type=range]::-moz-range-track{width:100%;height:0;cursor:pointer;box-shadow:0 0 0 transparent,0 0 0 rgba(13,13,13,0);background:#0075a9;border-radius:0;border:0 solid #010101}div#progress-container input[type=range]::-moz-range-thumb{box-shadow:0 0 0 #000,0 0 0 #0d0d0d;border:1px solid #00a0ff;height:15px;width:15px;border-radius:16px;background:#00a0ff;cursor:pointer}div#progress-container input[type=range]::-ms-track{width:100%;height:0;cursor:pointer;background:0 0;border-color:transparent;color:transparent}div#progress-container input[type=range]::-ms-fill-lower{background:#003d57;border:0 solid #010101;border-radius:0;box-shadow:0 0 0 transparent,0 0 0 rgba(13,13,13,0)}div#progress-container input[type=range]::-ms-fill-upper{background:#0075a9;border:0 solid #010101;border-radius:0;box-shadow:0 0 0 transparent,0 0 0 rgba(13,13,13,0)}div#progress-container input[type=range]::-ms-thumb{box-shadow:0 0 0 #000,0 0 0 #0d0d0d;border:1px solid #00a0ff;height:15px;width:15px;border-radius:16px;background:#00a0ff;cursor:pointer;height:0;display:block}@media all and (-ms-high-contrast:none){div#progress-container ::-ms-backdrop,div#progress-container input[type=range].amplitude-song-slider{padding:0}div#progress-container ::-ms-backdrop,div#progress-container input[type=range].amplitude-song-slider::-ms-thumb{height:15px;width:15px;border-radius:10px;cursor:pointer;margin-top:-8px}div#progress-container ::-ms-backdrop,div#progress-container input[type=range].amplitude-song-slider::-ms-track{border-width:15px 0;border-color:transparent}div#progress-container ::-ms-backdrop,div#progress-container input[type=range].amplitude-song-slider::-ms-fill-lower{background:#cfd8dc;border-radius:10px}div#progress-container ::-ms-backdrop,div#progress-container input[type=range].amplitude-song-slider::-ms-fill-upper{background:#cfd8dc;border-radius:10px}}@supports (-ms-ime-align:auto){div#progress-container input[type=range].amplitude-song-slider::-ms-thumb{height:15px;width:15px;margin-top:3px}}div#progress-container input[type=range]:focus::-ms-fill-lower{background:#0075a9}div#progress-container input[type=range]:focus::-ms-fill-upper{background:#00adfb}div#control-container{margin-top:25px;margin-top:20px}div#control-container div#repeat-container{width:25%;float:left;padding-top:20px}div#control-container div#repeat-container div#repeat{width:24px;height:19px;cursor:pointer}div#control-container div#repeat-container div#repeat.amplitude-repeat-off{background:url(https://521dimensions.com/img/open-source/amplitudejs/blue-player/repeat-off.svg)}div#control-container div#repeat-container div#repeat.amplitude-repeat-on{background:url(https://521dimensions.com/img/open-source/amplitudejs/blue-player/repeat-on.svg)}div#control-container div#repeat-container div#shuffle{width:23px;height:19px;cursor:pointer;float:right}div#control-container div#repeat-container div#shuffle.amplitude-shuffle-off{background:url(https://521dimensions.com/img/open-source/amplitudejs/blue-player/shuffle-off.svg)}div#control-container div#repeat-container div#shuffle.amplitude-shuffle-on{background:url(https://521dimensions.com/img/open-source/amplitudejs/blue-player/shuffle-on.svg)}@media all and (-ms-high-contrast:none){div#control-container ::-ms-backdrop,div#control-container div#control-container{margin-top:40px;float:none}}div#control-container div#central-control-container{width:50%;float:left}div#control-container div#central-control-container div#central-controls{width:130px;margin:auto}div#control-container div#central-control-container div#central-controls div#previous{display:inline-block;width:40px;height:40px;cursor:pointer;background:url(https://521dimensions.com/img/open-source/amplitudejs/blue-player/prev.svg);background-repeat:no-repeat;float:left;margin-top:10px;margin-right:-5px}div#control-container div#central-control-container div#central-controls div#play-pause{display:inline-block;width:60px;height:60px;cursor:pointer;float:left}div#control-container div#central-control-container div#central-controls div#play-pause.amplitude-paused{background:url(https://521dimensions.com/img/open-source/amplitudejs/blue-player/play.svg)}div#control-container div#central-control-container div#central-controls div#play-pause.amplitude-playing{background:url(https://521dimensions.com/img/open-source/amplitudejs/blue-player/pause.svg)}div#control-container div#central-control-container div#central-controls div#next{display:inline-block;width:40px;height:40px;cursor:pointer;background:url(https://521dimensions.com/img/open-source/amplitudejs/blue-player/next.svg);background-repeat:no-repeat;float:left;margin-top:10px;margin-left:-5px}div#control-container div#volume-container{width:25%;float:left;padding-top:20px}div#control-container div#volume-container div#shuffle-right{width:23px;height:19px;cursor:pointer;margin:auto}div#control-container div#volume-container div#shuffle-right.amplitude-shuffle-off{background:url(https://521dimensions.com/img/open-source/amplitudejs/blue-player/shuffle-off.svg)}div#control-container div#volume-container div#shuffle-right.amplitude-shuffle-on{background:url(https://521dimensions.com/img/open-source/amplitudejs/blue-player/shuffle-on.svg)}div#control-container div.amplitude-mute{cursor:pointer;width:25px;height:19px;float:left}div#control-container div.amplitude-mute.amplitude-not-muted{background:url(https://521dimensions.com/img/open-source/amplitudejs/blue-players/volume.svg);background-repeat:no-repeat}div#control-container div.amplitude-mute.amplitude-muted{background:url(https://521dimensions.com/img/open-source/amplitudejs/blue-player/mute.svg);background-repeat:no-repeat}div#control-container:after{content:"";display:table;clear:both}@media screen and (max-width:39.9375em){div#amplitude-player div#repeat-container div#repeat{margin-left:auto;margin-right:auto;float:none}div#amplitude-player div#repeat-container div#shuffle{display:none}div#amplitude-player div#volume-container div.volume-controls{display:none}div#amplitude-player div#volume-container div#shuffle-right{display:block}}@media screen and (min-width:40em) and (max-width:63.9375em){div#amplitude-player div#repeat-container div#repeat{margin-left:auto;margin-right:auto;float:none}div#amplitude-player div#repeat-container div#shuffle{display:none}div#amplitude-player div#volume-container div.volume-controls{display:none}div#amplitude-player div#volume-container div#shuffle-right{display:block}}@media screen and (min-width:64em){div#amplitude-player div#repeat-container div#repeat{margin-left:10px;margin-right:20px;float:left}div#amplitude-player div#volume-container div#shuffle-right{display:none}}input[type=range].amplitude-volume-slider{-webkit-appearance:none;width:calc(100% - 33px);float:left;margin-top:10px;margin-left:5px}@-moz-document url-prefix(){input[type=range].amplitude-volume-slider{margin-top:0}}@supports (-ms-ime-align:auto){input[type=range].amplitude-volume-slider{margin-top:3px;height:12px;background-color:rgba(255,255,255,0)!important;z-index:999;position:relative}div.ms-range-fix{height:1px;background-color:#a9a9a9;width:67%;float:right;margin-top:-6px;z-index:9;position:relative}}@media all and (-ms-high-contrast:none){::-ms-backdrop,input[type=range].amplitude-volume-slider{margin-top:-24px;background-color:rgba(255,255,255,0)!important}}input[type=range].amplitude-volume-slider:focus{outline:0}input[type=range].amplitude-volume-slider::-webkit-slider-runnable-track{width:75%;height:1px;cursor:pointer;animate:.2s;background:#cfd8dc}input[type=range].amplitude-volume-slider::-webkit-slider-thumb{height:10px;width:10px;border-radius:10px;background:#00a0ff;cursor:pointer;margin-top:-4px;-webkit-appearance:none}input[type=range].amplitude-volume-slider:focus::-webkit-slider-runnable-track{background:#cfd8dc}input[type=range].amplitude-volume-slider::-moz-range-track{width:100%;height:1px;cursor:pointer;animate:.2s;background:#cfd8dc}input[type=range].amplitude-volume-slider::-moz-range-thumb{height:10px;width:10px;border-radius:10px;background:#00a0ff;cursor:pointer;margin-top:-4px}input[type=range].amplitude-volume-slider::-ms-track{width:100%;height:1px;cursor:pointer;animate:.2s;background:0 0;border-color:transparent;border-width:15px 0;color:transparent}input[type=range].amplitude-volume-slider::-ms-fill-lower{background:#cfd8dc;border-radius:10px}input[type=range].amplitude-volume-slider::-ms-fill-upper{background:#cfd8dc;border-radius:10px}input[type=range].amplitude-volume-slider::-ms-thumb{height:10px;width:10px;border-radius:10px;background:#00a0ff;cursor:pointer;margin-top:2px}input[type=range].amplitude-volume-slider:focus::-ms-fill-lower{background:#cfd8dc}input[type=range].amplitude-volume-slider:focus::-ms-fill-upper{background:#cfd8dc}input[type=range].amplitude-volume-slider::-ms-tooltip{display:none}div#time-container span.current-time{color:#607d8b;font-size:14px;font-weight:700;float:left;width:15%;text-align:center}div#time-container span.duration{color:#607d8b;font-size:14px;font-weight:700;float:left;width:15%;text-align:center}div#time-container:after{content:"";display:table;clear:both}div#meta-container{text-align:center;margin-top:5px}div#meta-container span.song-name{display:block;color:#272726;font-size:20px;font-family:'Open Sans',sans-serif;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}div#meta-container div.song-artist-album{color:#607d8b;font-size:14px;font-weight:700;text-transform:uppercase;font-family:'Open Sans',sans-serif;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}div#meta-container div.song-artist-album span{display:block}div.amplitude-wave-form{margin-top:-14px}div.amplitude-wave-form svg{stroke:#00a0ff;height:50px;width:100%;stroke-width:.5px}div.amplitude-wave-form svg g{stroke:#00a0ff;height:50px;width:100%}div.amplitude-wave-form svg g path{stroke:#00a0ff;height:50px;width:100%}div#large-visualization{width:100%;background-color:#000;visibility:hidden}img.album-art {padding: 25px;background-color: antiquewhite;}
	</style>

	<!-- Blue Playlist Container -->
	<div id="blue-playlist-container">

		<!-- Amplitude Player -->
		<div id="amplitude-player">

			<!-- Left Side Player -->
			<div id="amplitude-left">
				<img data-amplitude-song-info="cover_art_url" class="album-art"/>
	  <div class="amplitude-visualization" id="large-visualization">

		</div>
				<div id="player-left-bottom">
					<div id="time-container">
						<span class="current-time">
							<span class="amplitude-current-minutes" ></span>:<span class="amplitude-current-seconds"></span>
						</span>
						<div id="progress-container">
							<div class="amplitude-wave-form">

					</div>
			<input type="range" class="amplitude-song-slider"/>
							<progress id="song-played-progress" class="amplitude-song-played-progress"></progress>
							<progress id="song-buffered-progress" class="amplitude-buffered-progress" value="0"></progress>
						</div>
						<span class="duration">
							<span class="amplitude-duration-minutes"></span>:<span class="amplitude-duration-seconds"></span>
						</span>
					</div>

					<div id="control-container">
						<div id="repeat-container">
							<div class="amplitude-repeat" id="repeat"></div>
							<div class="amplitude-shuffle amplitude-shuffle-off" id="shuffle"></div>
						</div>

						<div id="central-control-container">
							<div id="central-controls">
								<div class="amplitude-prev" id="previous"></div>
								<div class="amplitude-play-pause" id="play-pause"></div>
								<div class="amplitude-next" id="next"></div>
							</div>
						</div>

						<div id="volume-container">
							<div class="volume-controls">
								<div class="amplitude-mute amplitude-not-muted"></div>
								<input type="range" class="amplitude-volume-slider"/>
								<div class="ms-range-fix"></div>
							</div>
							<div class="amplitude-shuffle amplitude-shuffle-off" id="shuffle-right"></div>
						</div>
					</div>

					<div id="meta-container">
						<span data-amplitude-song-info="name" class="song-name"></span>

						<div class="song-artist-album">
							<span data-amplitude-song-info="artist"></span>
							<span data-amplitude-song-info="album"></span>
						</div>
					</div>
				</div>
			</div>
			<!-- End Left Side Player -->

			<!-- Right Side Player -->
			<div id="amplitude-right">
EOF;

	$count = 0;
	$songsjs = '';
	while (have_posts()) : the_post(); // standard WordPress loop. 
		$audio = get_field('audio_da_radio');
		$titulo = get_the_title();
		$data = get_the_date('d/m/Y');
		$html .= <<<EOF
			<div class="song amplitude-song-container amplitude-play-pause" data-amplitude-song-index="$count">
				<div class="song-now-playing-icon-container">
					<div class="play-button-container">
					</div>
					<img class="now-playing" src="https://521dimensions.com/img/open-source/amplitudejs/blue-player/now-playing.svg"/>
				</div>
				<div class="song-meta-data">
					<span class="song-title">$titulo</span>
					<span class="song-artist">IGES-DF</span>
				</div>
				<span class="song-duration">$data</span>
			</div>
			EOF;
		$count++;

		$songsjs .= <<<EOF
			{
				"name": "$titulo",
				"artist": "IGES-DF",
				"album": "Rádio IGES-DF",
				"url": "$audio",
				"cover_art_url": "https://igesdf.org.br/wp-content/uploads/2021/01/Logo-IGESDF.frontellosvg-03.svg"
			},
			EOF;

	endwhile; // end of the loop. 

	$html .= <<<EOF
			</div>
			<!-- End Right Side Player -->
		</div>
		<!-- End Amplitdue Player -->
		EOF;

	$js = <<<EOF
		<script src="https://cdn.jsdelivr.net/npm/amplitudejs@latest/dist/amplitude.min.js"></script>
		<script>
		let songElements = document.getElementsByClassName('song');

		for( var i = 0; i < songElements.length; i++ ){
			/*
				Ensure that on mouseover, CSS styles don't get messed up for active songs.
			*/
			songElements[i].addEventListener('mouseover', function(){
				this.style.backgroundColor = '#00A0FF';

				this.querySelectorAll('.song-meta-data .song-title')[0].style.color = '#FFFFFF';
				this.querySelectorAll('.song-meta-data .song-artist')[0].style.color = '#FFFFFF';

				if( !this.classList.contains('amplitude-active-song-container') ){
					this.querySelectorAll('.play-button-container')[0].style.display = 'block';
				}

				this.querySelectorAll('img.bandcamp-grey')[0].style.display = 'none';
				this.querySelectorAll('img.bandcamp-white')[0].style.display = 'block';
				this.querySelectorAll('.song-duration')[0].style.color = '#FFFFFF';
			});

			/*
				Ensure that on mouseout, CSS styles don't get messed up for active songs.
			*/
			songElements[i].addEventListener('mouseout', function(){
				this.style.backgroundColor = '#FFFFFF';
				this.querySelectorAll('.song-meta-data .song-title')[0].style.color = '#272726';
				this.querySelectorAll('.song-meta-data .song-artist')[0].style.color = '#607D8B';
				this.querySelectorAll('.play-button-container')[0].style.display = 'none';
				this.querySelectorAll('img.bandcamp-grey')[0].style.display = 'block';
				this.querySelectorAll('img.bandcamp-white')[0].style.display = 'none';
				this.querySelectorAll('.song-duration')[0].style.color = '#607D8B';
			});

			/*
				Show and hide the play button container on the song when the song is clicked.
			*/
			songElements[i].addEventListener('click', function(){
				this.querySelectorAll('.play-button-container')[0].style.display = 'none';
			});
		}

		Amplitude.init({
			"songs": [
				$songsjs
			],
		"callbacks": {
				'play': function(){
					document.getElementById('album-art').style.visibility = 'hidden';
					document.getElementById('large-visualization').style.visibility = 'visible';
				},

				'pause': function(){
					document.getElementById('album-art').style.visibility = 'visible';
					document.getElementById('large-visualization').style.visibility = 'hidden';
				}
			},
		waveforms: {
			sample_rate: 50
		}
		});
		document.getElementById('large-visualization').style.height = document.getElementById('album-art').offsetWidth + 'px';
		</script>
	EOF;

	$resultado = $html . $js;
	return $resultado;
}
