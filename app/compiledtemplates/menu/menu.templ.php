<?php return function($vars) { extract($vars); ?><?php if($admin) { ?><div class="navbar navbar-static-top">
	<div class="navbar-inner">
		<a class="brand" href="#">FCGI Blog</a>
		<ul class="nav">
			<li class="active"><a href="#">Home</a></li>
			<li><a href="#">Link</a></li>
			<li><a href="#">Link</a></li>
		</ul>
	</div>
</div><?php } else { ?><div class="navbar navbar-static-top">
	<div class="navbar-inner">
		<a class="brand" href="#">FCGI Blog</a>
		<ul class="nav">
			<li class="active"><a href="#">Home</a></li>
			<li><a href="#">Link</a></li>
			<li><a href="#">Link</a></li>
		</ul>
	</div>
</div><?php } ?><?php } ?>