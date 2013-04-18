FCGIFramework
=============

What is the FCGIFramework?

Quite simply, it's the first framework of it's kind.  It's built from the ground up to be served by a true FCGI server runloop.  It's built to load everything that it might use, and instantiate as much as it can, before a request ever hits your server.  But most importantly, it's built to be *fast*.

##FCGI

Most frameworks aren't designed to take advantage of true FCGI server runloops.  Most of that's because there hasn't been a true FCGI server runloop for PHP.  The FCGIFramework uses [phpfcgi](https://github.com/andyleap/phpfcgi), another piece of software that I've developed, to put the runloop inside the user's code, instead of in the php SAPI.  This allows your code to stay loaded, and keep objects instantiated, for hours at a time, while serving thousands or hundreds of thousands of requests.

##Load Everything

Most frameworks try to be careful about what they load and use fancy autoloaders to load stuff on demand.  That's smart, as all that loading is occuring in their runloop(i.e. while a request is waiting) and the more stuff the load, the longer it'll take.  FCGIFramework loads stuff before the runloop, before a request has come in, and thus, those loading times don't affect response times.  Instead, we load everything.  All of the framework, all of your controllers, all your models, even all of the model metadata from the database.  As a result, all that has to be done to generate the response is to hit a few methods, load whatever you need from the database, and output the template

##Database

This is a simple one.  We use the [PHPActiveRecord](http://www.phpactiverecord.org/) library to handle the database.

##Templates

Another simple one.  We do pure php templates, using echo short tags <?= ?>, and a simple variable assignment:
``` PHP
$this->blogTemplate->title = $blog->title;
$this->blogTemplate->content = $blog->content;
$this->blogTemplate->Render();
```
Renders using a template like
``` HTML
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
  	<h1><?=$title?></h1>
		<?=$content?>
    </body>
</html>
```

###Performance

What does all this boil down to?  Here's some numbers, based off of a 10-year old computer running linux, lighttpd, and FCGIFramework

* Requests/s : 531.2 conn/s
* Total time per connection[ms] : min 0.6 avg 1.9 max 55.6 median 1.5 stddev 1.1
* Average memory usage : 8mb per php instance
