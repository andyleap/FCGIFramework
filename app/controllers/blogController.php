<?php

class BlogController extends Controller
{
	public $blogTemplate;
	public $blogEditTemplate;
	public $noBlogTemplate;
	
	function Init()
	{
		$this->blogTemplate = $this->templates['blog'];
		$this->blogEditTemplate = $this->templates['blogedit'];
		$this->noBlogTemplate = $this->templates['noblog'];
	}
	
	function view($id = null)
	{
		$blog = Blog::find_by_id($id);
		if($blog)
		{
			$this->blogTemplate->title = $blog->title;
			$this->blogTemplate->content = $blog->content;
			$this->blogTemplate->Render();
		}
		else
		{
			$this->noBlogTemplate->Render();		
		}
	}
	
	function edit($id = null)
	{
		$blog = Blog::find_by_id($id);
		if($this->request->SERVER['REQUEST_METHOD'] == 'POST')
		{
			$blog->title = $this->request->POST['title'];
			$blog->content = $this->request->POST['content'];
			$blog->save();
			$this->request->Header('Location', '/view/' . $id);
		}
		else
		{
			$this->blogEditTemplate->title = $blog->title;
			$this->blogEditTemplate->content = $blog->content;
			$this->blogEditTemplate->Render();
		}
	}
}