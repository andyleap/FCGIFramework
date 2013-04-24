<?php

class BlogController extends Controller
{
	public $blogTemplate;
	public $noBlogTemplate;
	public $blogEntryTemplate;
	public $indexTemplate;
	
	public $blogAdminIndexTemplate;
	public $blogAdminEntryTemplate;
	public $blogAdminViewTemplate;
	public $blogAdminEdidTemplate;
	
	function Init()
	{
		$this->blogViewTemplate = $this->templates['blog'];
		$this->noBlogTemplate = $this->templates['noblog'];
		$this->indexTemplate = $this->templates['blogIndex'];
		$this->indexTemplate->admin = false;
		
		$this->blogAdminEditTemplate = $this->templates['admin/blogedit'];
	}
	
	function index($page = 1)
	{
		$blogCount = $this->cache->Get('blog', 'count', function()
		{
			return Blog::count();
		}, 1);
		$maxPage = ceil($blogCount / 10);
		if($page < 1)
		{
			$page = 1;
		}
		if($page > $maxPage)
		{
			$page = $maxPage;
		}
		$blogs = $this->cache->Get('blog_page', $page, function() use ($page)
		{
			return Blog::find('all', array('limit' => 10, 'offset' => ($page - 1) * 10));
		}, 1);
		$blogDatas = array();
		foreach($blogs as $blog)
		{
			$blogDatas[] = $blog->Attributes();
		}
		$this->indexTemplate->blogs = $blogDatas;
		$this->indexTemplate->Render();
	}
	
	function view($id)
	{
		$blog = $this->cache->Get('blog', $id, function() use ($id)
		{
			return Blog::find_by_id($id);
		});
		if($blog)
		{
			$this->blogViewTemplate->title = $blog->title;
			$this->blogViewTemplate->content = $blog->content;
			$this->blogViewTemplate->Render();
		}
		else
		{
			$this->noBlogTemplate->Render();
		}
	}
	
	function edit($id)
	{
		$blog = Blog::find_by_id($id);
		if(!$blog)
		{
			$this->request->Header('Location', '/');
		}
		if($this->request->SERVER['REQUEST_METHOD'] == 'POST')
		{
			$blog->title = $this->request->POST['title'];
			$blog->content = $this->request->POST['content'];
			$blog->save();
			$this->request->Header('Location', '/view/' . $id);
		}
		else
		{
			$this->blogAdminEditTemplate->title = $blog->title;
			$this->blogAdminEditTemplate->content = $blog->content;
			$this->blogAdminEditTemplate->Render();
		}
	}
	
	function add()
	{
		if($this->request->SERVER['REQUEST_METHOD'] == 'POST')
		{
			$blog = new Blog();
			$blog->title = $this->request->POST['title'];
			$blog->content = $this->request->POST['content'];
			$blog->save();
			$this->request->Header('Location', '/view/' . $blog->id);
		}
		else
		{
			$this->blogEditTemplate->title = '';
			$this->blogEditTemplate->content = '';
			$this->blogEditTemplate->Render();
		}
	}
	
	function delete($id)
	{
		$blog = $this->modelcache->Blog($id);
		if($blog)
		{
			$blog->delete();
		}
	}
}