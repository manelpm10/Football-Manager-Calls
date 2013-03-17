<h1>Call for match {$match.day}</h1>

<dl class="match_info">
	<dt>Who?</dt>
	<dd>{$match.rival}</dd>
	<dt>Type</dt>
	<dd>{$match.type|ucfirst}</dd>
	<dt>Day</dt>
	<dd>{$match.day|date_format:"%d/%m/%Y"}</dd>
	<dt>Hour</dt>
	<dd>{$match.hour|date_format:"%H:%M"}</dd>
</dl>

{if !$match.closed}
{	if false == $has_voted}
		<div id="join">
			<h2>What will you do?</h2>
{			foreach from=$types item=type}
				<input class="join-button" type="button" value="{$type}" />
{			/foreach}
		</div>
{	/if}
{else}
	<div class="msg_warning" style="clear:both;">
		<strong>The call is closed!</strong>
	</div>
{/if}

<div style="clear:both;">
<h2>Call status</h2>

<div id="called_players"><strong>0</strong> called</div>
<div id="available_players"><strong>0</strong> available</div>
<div id="if_necessary_players"><strong>0</strong> if necessary</div>
<div id="unavailable_players"><strong>0</strong> unavailable</div>
<div id="unknown_players"><strong>{$players_not_joined_count}</strong> unknown</div>

<form method="POST" action="{$BASE_URL}?ctname=ClosecallMatchController">
	<input id="id-match" type="hidden" name="id_match" value="{$match.id_match}" />

{* Checkbox to admin manage call *}
{	if ( !$is_match_played AND $is_admin )}
	<input class="close-call-button" type="submit" value="Close Call" />
{	/if}

<ul id="call-state">
{foreach from=$players item=player}
{if $player.position neq $last_position}
	{assign var=separation value="separation"}
{else}
	{assign var=separation value=""}
{/if}
	<li id="player_{$player.id_player}" class="{$player.available} {$separation}">

{assign var=last_position value=$player.position}
{* Checkbox to admin manage call *}
{			if ( !$is_match_played AND $is_admin )}
				<input type="checkbox" name="player[{$player.id_player}]" value="true" {if "called" == $player.available}checked="checked"{/if}/>
{			/if}
		<img title="{$player.name} {$player.middle_name}" src="{$player.image_url}" height="55px" width="50px" />
		<span class="player_data">
			<a title="{$player.name} {$player.middle_name}" href="{$player.player_url}">
				{$player.name} {$player.middle_name}
{				if ( !$is_match_played AND $is_admin ) }
				<span class="times_rotated">({$player.num_times_rotated} rotations)</span>
{				/if}
{*				if ( ( !$is_match_played AND $is_admin ) AND 0.0 != $player.rotate_index )}
				<span class="{if 0 > $player.rotate_index}bad_rotate_index{else}good_rotate_index{/if}">({$player.rotate_index})</span>
{				/if*}
			</a><br/>
			<em>{$player.position}</em>
		</span>
{* Buttons to user provide availibility *}
{			if ( $user.id_player == $player.id_player ) AND !$match.closed AND 'called' !== $player.available}
{			foreach from=$types item=type}
{				if $player.available != $type}
					<input class="join-button" type="button" value="{$type}" />
{				/if}
{			/foreach}
{		/if}
	</li>
{/foreach}
</ul>

{* Checkbox to admin manage call *}
{	if ( !$is_match_played AND $is_admin )}
	<input class="close-call-button" type="submit" value="Close Call" />
{	/if}
</form>
</div>
