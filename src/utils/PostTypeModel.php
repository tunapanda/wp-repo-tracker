<?php

namespace repotracker;

use \WP_Query;
use \Exception;

/**
 * Something that has an underlying post type.
 */
abstract class PostTypeModel {

	// Override the posttype in the sub class.
	protected static $posttype;
	protected $post;

	/**
	 * Construct.
	 */
	private final function __construct($post) {
		$this->post=$post;
	}

	/**
	 * Get underlying post.
	 */
	public function getPost() {
		return $this->post;
	}

	/**
	 * Get meta.
	 */
	public function getMeta($key) {
		return get_post_meta($this->getId(),$key,TRUE);
	}

	/**
	 * Get meta array.
	 */
	public function getMetaArray($key) {
		return get_post_meta($this->getId(),$key,FALSE);
	}

	/**
	 * Set meta value.
	 */
	public function setMeta($key, $value) {
		update_post_meta($this->getId(),$key,$value);
	}

	/**
	 * Get id.
	 */
	public function getId() {
		if (!$this->post->ID)
			throw new Exception("There is no id.");

		return $this->post->ID;
	}

	/**
	 * Get by id.
	 */
	public static function getById($postId) {
		if (!static::$posttype)
			throw new Exception("Post type not set in subclass");

		if (!$postId)
			return NULL;

		$post=get_post($postId);

		if (!$post)
			return NULL;

		if ($post->post_type!=static::$posttype)
			throw new Exception("Expected posttype: ".static::$posttype);

		return new IssueFilter($post);
	}

	/**
	 * Get all published posts of this posttype.
	 */
	public function getAllPublished() {
		if (!static::$posttype)
			throw new Exception("Post type not set in subclass");

		$q=new WP_Query(array(
			"post_type"=>static::$posttype,
			"post_status"=>"publish",
			"posts_per_page"=>-1
		));

		$all=array();
		foreach ($q->get_posts() as $post)
			$all[]=new static($post);

		return $all;
	}

	/**
	 * Called when a post is saved.
	 * Override in subclass.
	 */
	protected function onSave() {
	}

	/**
	 * Save post handler.
	 */
	public static function savePostHandler($postId) {
		$posttype=get_post_type($postId);
		if ($posttype!=static::$posttype)
			return;

		$model=static::getById($postId);
		$model->onSave();
	}

	/**
	 * Register hooks.
	 */
	public static function register() {
		add_action("save_post",array(get_called_class(),"savePostHandler"));
	}
}