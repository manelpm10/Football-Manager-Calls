<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xml:lang="" xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<title>{$layout_vars.title}</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="shortcut icon" href="{$layout_vars.home_link}favicon.ico" type="image/x-icon">

	<link rel="stylesheet" type="text/css" href="{$layout_vars.home_link}css/main.css" media="screen">
	<link rel="stylesheet" type="text/css" href="{$layout_vars.home_link}css/jquery-ui-1.8.11.custom.css" media="screen">

	<script type="text/javascript" src="{$layout_vars.home_link}js/jquery-1.5.1.min.js"></script>
	<script type="text/javascript" src="{$layout_vars.home_link}js/jquery-ui-1.8.11.min.js"></script>
	<script type="text/javascript" src="{$layout_vars.home_link}js/general.js"></script>
</head>
<body>
	<div id="wrapper">
		<div id="header">
			<h1><a title="Football Team" href="{$BASE_URL}">Football Team</a></h1>
			<p class="top_links">
				<a title="Football Team" href="{$BASE_URL}team">Football Team</a> |
				<a title="Application Development" href="{$BASE_URL}team/Application%20Development">Application Development</a>
			</p>
			<div class="work_group"></div>
		</div>
		<div id="login_form">
			{if true == $user.is_logged}
				Welcome, <span>{$user.name}</span> - <a title="login" href="{$BASE_URL}?ctname=MainUserController">Logout</a>
			{else}
				<a title="login" href="{$BASE_URL}?ctname=MainUserController">Login</a>
			{/if}
		</div>

		<ul id="main_tabs">
			<li class="matches{if 'match' == $acvite_tab} active{/if}"><a title="Matches" href="{$BASE_URL}?ctname=IndexMatchController">Matches</a></li>
			<li class="team{if 'team' == $acvite_tab} active{/if}"><a title="Team" href="{$BASE_URL}?ctname=IndexTeamController">Team</a></li>
		</ul>

		<div id="contents" class="clearfix">
			<div id="mainbar">
				<div id="content">
					{include file="$main_template"}
				</div>
			</div>

			<div id="sidebar">
				<div id="shortcuts" class="module">
					<h2><span>Useful shortcuts</span></h2>

					<ul class="links">
						<li><a title="Clasificaciones" target="_blank" href="http://www.valldaurasport.com/es/competicion-detalle.html?Id=47&Co=5&Tipo=1&Jornada=&Equipo=MAMARRACHOS%20TEAM">Clasificacions</a></li>
						<li><a title="Resultados" target="_blank" href="http://www.valldaurasport.com/es/competicion-detalle.html?Id=47&Co=5&Tipo=2&Jornada=&Equipo=MAMARRACHOS%20TEAM">Resultados</a></li>
						<li><a title="Calendario" target="_blank" href="http://www.valldaurasport.com/es/competicion-detalle.html?Equipo=MAMARRACHOS+TEAM&Id=0&Jornada=0&Tipo=3&Co=5">Calendario</a></li>
					</ul>
				</div>

{				if isset( $players_not_joined )}
				<div id="next_birthdays" class="module items birthdays">
					<h2><span>Players not joined</span></h2>

					<ul>
{					foreach from=$players_not_joined item=player}
						<li>
							<p>
								<img title="{$player.name} {$player.middle_name} {$player.last_name}" src="{$player.image_url}" class="avatar" width="25" />
								<a title="{$player.name} {$player.middle_name} {$player.last_name}" href="{$BASE_URL}?ctname=IndexPlayerController&0={$player.sanitized_name}">
									{$player.name} {$player.middle_name} {$player.last_name}
								</a>
							</p>
						</li>
{					/foreach}
					</ul>
				</div>
{				/if}

			</div>
		</div>
		<div id="footer">
			<p>Football Team - {$smarty.now|date_format:"%Y"}</p>
		</div>
	</div>
</body>
</html>
