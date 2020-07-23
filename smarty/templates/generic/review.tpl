{assign var="pagetitle" value="$_review_title"}

<article>
<div id="content">

	<h2 class="pagetitle systemsselect">
		{$_review_title} of {$target_html}
	</h2>

	<h3>
		{$_review_reviewed_by|sprintf:$review.reviewer|escape}
	</h3>

	<p>
		{$review.description|escape|textlinks|nl2br}
	</p>
</div>
</article>

{include file="end.tpl"}
