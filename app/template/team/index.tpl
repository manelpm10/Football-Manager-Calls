<div id="team-tabs">
	<ul>
		<li><a href="#team">Team</a></li>
	</ul>

	<div id="team">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="table">
			<tbody>
{			foreach from=$team item=player}
		    	<tr>
		    		<td class="first_col"><img title="{$player.name} {$player.middle_name} {$player.last_name}" src="{$player.image_url}" width="50px" height="50px" /></td>
		    		<td>
		    			<a title="{$player.name} {$player.middle_name} {$player.last_name}" href="{$BASE_URL}?ctname=IndexPlayerController&0={$player.sanitized_name}">
		    				{$player.name} {$player.middle_name} {$player.last_name}
		    			</a>
		    		</td>
		    		<td class="player_number">{$player.number}</td>
		    		<td class="player_position">{$player.position}</td>
		    		<td class="last_col">{$player.date_add|date_format:"%d/%m/%Y"}</td>
		    	</tr>
{			/foreach}
			</tbody>
		</table>
	</div>
</div>
