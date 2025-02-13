{literal}
<style>
fieldset p {text-align:left;margin:0;}
</style>
{/literal}

<div class="titrePage">
	<h2>User Mass Register</h2>
</div>

<fieldset>
  <legend>{'Example'|@translate}</legend>
  <p style="">
    john.connor@gmail.com<br>
    sarah.connor@hotmail.com; Sarah<br>
    paul@anderson.com
  </p>
</fieldset>

<fieldset>
  <legend>{'Register users'|@translate}</legend>
<form method="post" action="">
  <p>
{*
    <strong>{'Email addresses'|@translate}</strong> {'(one on each line)'|@translate}<br>
*}
    <textarea name="users" style="width:500px;height:300px;">{$EMAILS|default:''}</textarea>
  </p>

  <p style="margin-top:10px"><input type="submit" name="submit" value="{'Submit'|@translate}"></p>

</form>

</fieldset>