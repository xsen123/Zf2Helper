<?php
namespace Application\Model;

use Zf2Helper\Model\BaseModel;

class News extends BaseModel{

	protected $id;

	protected $title;

	protected $content;

	public function setId($id)
	{
		$this->id = $id;
		return $this;
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function setTitle($title)
	{
		$this->title = $title;
		return $this;
	}
	
	public function getTitle()
	{
		return $this->title;
	}
	
	public function setContent($content)
	{
		$this->content = $content;
		return $this;
	}
	
	public function getContent()
	{
		return $this->content;
	}	
	
}
