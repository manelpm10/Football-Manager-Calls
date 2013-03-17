{if empty($next_matches) && empty( $last_matches )}
	<div class="msg_info">No matches planned in this season</div>
{/if}

{if !empty( $next_matches )}
	<h1>Next matches</h1>

	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="table">
		<thead>
			<tr>
				<th class="first_col"></th>
				<th></th>
				<th>When?</th>
				<th>Rival</th>
				<th class="last_col">Call</th>
			</tr>
		</thead>
		<tbody>
{			foreach from=$next_matches item=match name=matches_loop}
			<tr>
				<td width="5%" class="first_col">{$match.journey}</td>
				<td width="5%"><img title="{$match.type}" src="{$BASE_URL}/images/icons/{$match.type}.gif" /></td>
				<td width="20%">{$match.day|date_format:"%d/%m/%Y"} {$match.hour|date_format:"%H:%M"}</td>
				<td width="50%">{$match.rival}</td>
				<td width="15%" class="last_col">
				{if 1 == $smarty.foreach.matches_loop.iteration}
					<a title="join me!" href="{$BASE_URL}?ctname=CallMatchController&0={$match.id_match}">Join me!</a>
				{/if}
				</td>
			</tr>
{			/foreach}
		</tbody>
	</table>
{/if}

<br/>

{if !empty( $last_matches )}
	<h1>Latest matches</h1>

	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="table">
		<thead>
			<tr>
				<th class="first_col"></th>
				<th></th>
				<th>When?</th>
				<th>Rival</th>
				<th class="last_col">Call</th>
			</tr>
		</thead>
		<tbody>
{			foreach from=$last_matches item=match name=matches_loop}
			<tr>
				<td width="5%" class="first_col">{$match.journey}</td>
				<td width="5%"><img title="{$match.type}" src="{$BASE_URL}/images/icons/{$match.type}.gif" /></td>
				<td width="20%">{$match.day|date_format:"%d/%m/%Y"} {$match.hour|date_format:"%H:%M"}</td>
				<td width="50%">{$match.rival}</td>
				<td width="15%" class="last_col">
					<a title="join me!" href="{$BASE_URL}?ctname=CallMatchController&0={$match.id_match}">Show Call!</a>
				</td>
			</tr>
{			/foreach}
		</tbody>
	</table>
{/if}
