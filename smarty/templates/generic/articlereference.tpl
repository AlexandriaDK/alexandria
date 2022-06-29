{if $articles}
<h3 class="parttitle">{$_articles_referenced}</h3>
	<table id="personarticles">
	{foreach $articles as $article}
	<tr>
	<td>{if $article.game_id}<a href="data?scenarie={$article.game_id}">{$article.title|escape}</a>{else}{$article.title|escape}{/if}</td>
	<td>{if $article.page}{$_file_page} {$article.page|escape}{/if}</td>
	<td><a href="magazines?issue={$article.issue_id}">{$article.issuetitle|escape}</a>{if $article.releasetext} ({$article.releasetext|escape}){/if}</td>
	<td><a href="magazines?id={$article.magazine_id}">{$article.magazinename|escape}</a></td>
	</tr>
	{/foreach}
	</table>
{/if}
