<form method="POST" action="/cory/laravel/firstapp/public/users">
{!! csrf_field() !!}
	<input type="text" name="name">
	<input type="text" name="email">
	<input type="password" name="password">
	<input type="submit" value="Create">
</form>