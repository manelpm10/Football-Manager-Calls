<h1>Score the match {$match.day}</h1>

<form name="form-score" method="POST">
	<table>
		<thead>
			<tr>
				<th>Player</th>
				<th>Score</th>
				<th>The best</th>
				<th>To Improve</th>
			</tr>
		</thead>
		<tbody>
		{foreach name=players_loop from=$players item=player}
			<tr>
				<input type="hidden" name="player[{$smarty.foreach.players_loop.iteration}][id_player]" value="{$player.id_player}" />
				<td><img title="{$player.name} {$player.middle_name}" src="{$player.image_url}" width="50px" height="50px" /></td>
				<td>
					<select name="player[{$smarty.foreach.players_loop.iteration}][score]">
						<option value=""></option>
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="6">6</option>
						<option value="7">7</option>
						<option value="8">8</option>
						<option value="9">9</option>
						<option value="10">10</option>
					</select>
				</td>
				<td><textarea name="player[{$smarty.foreach.players_loop.iteration}][best]"></textarea></td>
				<td><textarea name="player[{$smarty.foreach.players_loop.iteration}][worst]"></textarea></td>
			</tr>
		{/foreach}
		</tbody>
	</table>

	<input type="submit" name="scored" value="Send!" style="float:right;" />
</form>