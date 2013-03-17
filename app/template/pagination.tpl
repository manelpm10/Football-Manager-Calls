{if $pagination_data.num_pages > 1 }
<div class="pagination_navigation">
<ul>
{	if isset( $pagination_data.first_page) }
		<li class="pagination_first"><a title="Go to first page" class="pagination" href="{$pagination_data.first_page.link}"><<</a></li>
{	/if}
{	if isset( $pagination_data.previous_page) }
		<li class="pagination_prev"><a title="Go to previous page" class="pagination" href="{$pagination_data.previous_page.link}"><</a></li>
{	/if}
{	foreach from=$pagination_data.pages item=page_data name=pagination}

		{assign var='subclass' value=''}
		{if $smarty.foreach.pagination.first && isset( $pagination_data.previous_page ) }
			{assign var='subclass' value='first'}
		{elseif $smarty.foreach.pagination.last && !isset( $pagination_data.next_page ) }
			{assign var='subclass' value='last'}
		{/if}

		{if $page_data.is_current}
			<li class="active">{$page_data.number}</li>
		{else}
			<li><a title="Page {$page_data.number}" class="pagination" href="{$page_data.link}">{$page_data.number}</a></li>
		{/if}

{	/foreach}
{	if isset( $pagination_data.next_page) }
	 	<li class="pagination_next"><a title="Go to next page" class="pagination" href="{$pagination_data.next_page.link}">></a></li>
{	/if}
{	if isset( $pagination_data.last_page) }
	 	<li class="pagination_last"><a title="Go to last page" class="pagination" href="{$pagination_data.last_page.link}">>></a></li>
{	/if}
</ul>

{ if ( isset( $pagination_data.items_per_page.display ) && $pagination_data.items_per_page.display ) }
{/if}
</div>
{/if}
