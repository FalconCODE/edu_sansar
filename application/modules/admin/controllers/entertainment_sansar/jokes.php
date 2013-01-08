<?php if( !defined('BASEPATH') ) exit('No direct script access allowed');

/**
 *
 * @author Suraj Kumar Adhikari <surajadhikari1929@gmail.com>
 *
 */
class jokes extends MX_Controller
{
	/**
	 * Data Holder
	 *
	 * @var array
	 */
	protected static $data = array();

	/**
	 * Grocery CRUD object
	 *
	 * @var object
	 */
	protected $_crud;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		if (!$this->ion_auth->logged_in()) {
			$this->session->set_flashdata('message','You are not logged in. <br /> Please login before trying to access admin panel');
			redirect('auth/login', 'refresh');
		}
		/*
		 * Links
		 */
		static::$data['links'] = array(
			'Categories' => 'admin/entertainment_sansar/jokes/categories',
			'Jokes' => 'admin/entertainment_sansar/jokes',
			'Comments' => 'admin/entertainment_sansar/jokes/comments'
		);

		/*
		 * Loading grocery crud library
		 */
		$this->load->library('grocery_crud');

		try{
			/*
			 * Crud object
			 */
			$this->_crud = new grocery_CRUD();
		}catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}
	}

	/**
	 * index (default) method
	 *
	 * lists the jokes
	 */
	public function index()
	{

		$this->_crud->set_table('ent_jk_jokes');
		$this->_crud->set_subject('Joke');

		/*
		 * Setting relation betweent category & joke table and accessing name field to use
		 */
		$this->_crud->set_relation('ent_jk_categories_id','ent_jk_categories','name');

		/*
		 *  Setting text to display for more readablity
		 */
		$this->_crud->display_as('ent_jk_categories_id','Joke Category')
				    ->display_as('name', 'Name of joke submitter')
				    ->display_as('title', 'Title of Joke');

		/*
		 * Defining fields that show on Add & Edit forms if you want different then use add_fields & edit_fields
		 */
		$this->_crud->fields('title', 'description', 'name', 'email', 'status','ent_jk_categories_id');

		/*
		 * setting rules for the Grocery CRUD to look for
		 */
		$this->_crud->set_rules('email', 'Email of submitter', 'trim|valid_email');

		/*
		 * Setting required field for CRUD operation, can also be replaced by set_rules method;
		 * But most of the fields are requred, so this method is easier
		 */
		$this->_crud->required_fields('title', 'description', 'name', 'email', 'ent_jk_categories_id');
		static::$data['name'] = 'crud';
		static::$data['content_replace'] = $this->_crud->render();

		$this->_crud_output('main', static::$data);
	}

	/**
	 * Custom view loader
	 *
	 * @access protected
	 * @param  array 		$output
	 */
	protected function _crud_output($view, array $output = array())
	{
		$this->load->view($view, $output);
	}

	public function categories()
	{
		$this->_crud->set_table('ent_jk_categories');
		$this->_crud->set_subject('Category');
		$this->_crud->required_fields('name', 'description');

		static::$data['name'] = 'crud';
		static::$data['content_replace'] = $this->_crud->render();

		$this->_crud_output('main', static::$data);
	}


	/**
	 * Method to load comments
	 */
	public function comments()
	{
		$this->_crud->set_table('ent_jk_comments');
		$this->_crud->set_subject('Comment');
		$this->_crud->set_relation('ent_jk_jokes_id','ent_jk_jokes','title');
		$this->_crud->display_as('ent_jk_jokes_id', 'Joke');
		$this->_crud->required_fields('name', 'email', 'comments', 'ent_jk_jokes_id');
		static::$data['name'] = 'crud';
		static::$data['content_replace'] = $this->_crud->render();

		$this->_crud_output('main', static::$data);
	}
}

?>