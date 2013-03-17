<div id="player_info">
	<h1>{$player.name} {$player.middle_name} {$player.last_name}</h1>

	<img title="{$player.name} {$player.middle_name} {$player.last_name}" src="{$player.image_url}" />
	<dl>
		<dt>Name:</dt>
		<dd>{$player.name} {$player.middle_name} {$player.last_name}</dd>
{		if $player.type}
		<dt>Player type:</dt>
		<dd>{$player.type}</dd>
{		/if}
{		if $player.number}
		<dt>Number:</dt>
		<dd>{$player.number}&nbsp;</dd>
{		/if}
{		if $player.position}
		<dt>Position:</dt>
		<dd>{$player.position}</dd>
{		/if}
{		if $player.date_add}
		<dt>Signed:</dt>
		<dd>{$player.date_add|date_format:"%d/%m/%Y"}</dd>
{		/if}
{		if $player.date_sold}
		<dt>Sold:</dt>
		<dd>{$player.date_sold}</dd>
{		/if}
	</dd>
</div>
