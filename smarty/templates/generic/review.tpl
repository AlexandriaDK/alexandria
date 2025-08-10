<div id="content">

  <article>
    <h2 class="pagetitle systemsselect">
      {$_review_review_of|sprintf:$target_html}
    </h2>

    <h3>
      {$_review_reviewed_by|sprintf:$review.reviewer|escape}
    </h3>

    <p>
      {$review.description|escape|textlinks|nl2br}
    </p>

    <p>
      <em>{$_review_published} {$review.published|fulldate}</em>
    <p>
  </article>

  {include file="updatelink.tpl"}

</div>
