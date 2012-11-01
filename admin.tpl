<div class="titrePage">
	<h2>User Mass Register</h2>
</div>

<form method="post" action="" class="properties">
  <p style="text-align:left;">
    <strong>{'Email addresses'|@translate}</strong> {'(one on each line)'|@translate}<br>
    <textarea name="emails" style="width:500px;height:300px;">{$EMAILS}</textarea>
  </p>

  <p style="text-align:left;"><input type="submit" name="submit" value="{'Register users'|@translate}"></p>

</form>