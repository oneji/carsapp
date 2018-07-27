<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel</title>
    </head>
    <body>
        <form action="/welcome" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="file" name="fucking_image" />
            <button type="submit">Отправить</button>
        </form>
    </body>
</html>
