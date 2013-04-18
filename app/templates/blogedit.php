<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
		<form method="POST">
			<input type="text" name="title" value="<?=$title?>"/><br/>
			<textarea name="content"><?=$content?></textarea><br/>
			<input type="submit" value="Save"/>
		</form>
    </body>
</html>