<?php
/**
 * Class Paginate.
 *
 * This class encapsulates pagination functionality.
 */
class Paginate
{
	/**
	 * Number of items per page.
	 *
	 * @var integer
	 */
	protected $items_per_page = 10;

	/**
	 * Values to display items per page.
	 *
	 * @var array
	 */
	protected $display_items_per_page = array();

	/**
	 * The total number of items.
	 *
	 * @var integer.
	 */
	protected $items_total;

	/**
	 * Current page number (starting from 1).
	 *
	 * @var integer
	 */
	protected $current_page_number = 1;

	/**
	 * Number of pages.
	 *
	 * @var integer
	 */
	protected $num_pages;

	/**
	 * Maxim number of pages to show.
	 *
	 * @var integer
	 */
	protected $max_num_pages = null;

	/**
	 * Previous page number.
	 *
	 * @var ingeter
	 */
	protected $prev_num_page;

	/**
	 * Next page number.
	 *
	 * @var integer
	 */
	protected $next_num_page;

	/**
	 * The URL base for the page link.
	 *
	 * @var string
	 */
	protected $url_base = null;

	/**
	 * The default separator for the page link.
	 *
	 * @var string
	 */
	protected $separator_link_page = '#';

	/**
	 * Number of local pages (i.e., the number of discrete page numbers that will be displayed, including the current page number).
	 *
	 * @var integer
	 */
	protected $page_range;

	/**
	 * Values availables to set page_range ( window ).
	 *
	 * @var integer
	 */
	protected $availables_page_range = array(
		'default' 		=> 15,
		'short'			=> 12,
		'very_short'	=> 10
	);

	/**
	 * Start page number to page range.
	 *
	 * @var integer
	 */
	protected $start_page_range;

	/**
	 * End page number to page range.
	 *
	 * @var integer
	 */
	protected $end_page_range;

	/**
	 * Template name.
	 *
	 * @var string
	 */
	protected $layout_template;

	/**
	 * Config object.
	 *
	 * @var Config
	 */
	protected $config;

	/**
	 * Array of params to use in template (keywords, brand names, etc).
	 *
	 * @var array
	 */
	public $params_template;

	/**
	 * Init pagination data.
	 */
	public function __construct()
	{
		// Init data.
		$this->current_page_number 	= 1;
		$this->items_total 			= 0;
		$this->num_pages 			= 0;
		$this->page_range 			= $this->availables_page_range['default'];
		$this->start_page_range 	= 0;
		$this->end_page_range 		= 0;
		$this->params_template 		= array();
		$this->layout_template		= null;
		$this->setDisplayItemsPerPage( null );
	}

	/**
	 * Get pagination data.
	 *
	 * @return array
	 */
	public function getLinks()
	{
		if ( is_null( $this->layout_template ) )
		{
			throw new Exception( 'Paginate getLinks: Is necessary to set the pagination template for the page link with \'setTemplate( $layout_template )\' method.' );
		}

		// Set number of pages to show.
		$this->num_pages = (int)ceil( ( $this->items_total / $this->items_per_page ) );

		// Check for max_page ( for Google ).
		if ( !is_null( $this->max_num_pages ) && $this->num_pages > $this->max_num_pages )
		{
			$this->num_pages = $this->max_num_pages;
		}

		// Throw exception when page is higher than current page.
		if ( $this->current_page_number > $this->num_pages )
		{
			throw new Exception( 'Pagination error: trying to retrieve an unexisting page' );
		}

		return $this->getPaginateData();
	}

	/**
	 * Get pagination data.
	 *
	 * @return array
	 */
	protected function getPaginateData()
	{
		// Choose page range ( window ) and set start/end page range.
		$this->setPageRange();

		// Set previous/next page number.
		$this->setPrevNumPage();
		$this->setNextNumPage();

		$paginate['template']		= $this->layout_template;
		$paginate['items_total'] 	= $this->getNumTotalResults();
		$paginate['item_start']		= ( ( $this->prev_num_page * $this->items_per_page ) + 1 );
		$paginate['item_end']		= ( $this->current_page_number * $this->items_per_page );
		$paginate['params']			= $this->params_template;
		if ( $paginate['item_end'] > $this->getNumTotalResults() )
		{
			$paginate['item_end'] = $this->getNumTotalResults();
		}
		$paginate['num_pages']		= $this->num_pages;
		$paginate['current_page']	= $this->current_page_number;

		// Links.
		for ( $i = $this->start_page_range; $i<= $this->end_page_range; $i++ )
		{
			$paginate['pages'][$i]['number']		= (int)$i;
		 	$paginate['pages'][$i]['is_current'] 	= ( $i == $this->current_page_number );

		 	$paginate['pages'][$i]['link'] 	= $this->url_base . $this->separator_link_page . $i;
		}

		if ( $this->prev_num_page > 0 )
		{
			$paginate['previous_page']['number'] 	= $this->prev_num_page;

			$paginate['previous_page']['link'] 		= $this->url_base . $this->separator_link_page . $this->prev_num_page;
			if ( 1 != $this->prev_num_page )
			{
				$paginate['previous_page']['link'] = $this->url_base . $this->separator_link_page . $this->prev_num_page;
			}
		}

		if ( $this->next_num_page > 0 )
		{
			$paginate['next_page']['number'] 		= $this->next_num_page;
			$paginate['next_page']['link'] 			= $this->url_base . $this->separator_link_page . $this->next_num_page;
		}

		if ( 1 != $this->current_page_number )
		{
			$paginate['first_page']['number'] 		= 1;
			$paginate['first_page']['link'] 		= $this->url_base . $this->separator_link_page . 1;
		}

		if ( $this->num_pages != $this->current_page_number )
		{
			$paginate['last_page']['number']	 	= $this->num_pages;
			$paginate['last_page']['link'] 			= $this->url_base . $this->separator_link_page . $this->num_pages;
		}

		$paginate['items_per_page'] 		= $this->items_per_page;
		$paginate['display_items_per_page'] = $this->display_items_per_page;

		return $paginate;
	}

	/**
	 * Get the url variable.
	 *
	 * @return string
	 */
	protected function getUrlBase()
	{
		return $this->url_base;
	}

	/**
	 * Get the total number of items.
	 *
	 * @return integer
	 */
	protected function getNumTotalResults()
	{
		return $this->items_total;
	}

	/**
	 * Set previous page number.
	 */
	protected function setPrevNumPage()
	{
		$this->prev_num_page = 0;
		if ( $this->current_page_number > 1 )
		{
			$this->prev_num_page = ( $this->current_page_number - 1 );
		}
	}

	/**
	 * Set next page number.
	 */
	protected function setNextNumPage()
	{
		$this->next_num_page = 0;
		if ( $this->current_page_number < $this->num_pages )
		{
			$this->next_num_page = ( $this->current_page_number + 1 );
		}
	}

	/**
	 * Calculate the start and end of the page range ( windows ) and his possible deviations.
	 */
	protected function setPageRange()
	{
		$this->page_range = $this->availables_page_range['default'];
		if ( $this->current_page_number > 9992 )
		{
			$this->page_range = $this->availables_page_range['very_short'];
		}
		elseif ( $this->current_page_number > 986 )
		{
			$this->page_range = $this->availables_page_range['short'];
		}

		$this->page_range = $this->page_range - 1;
		$this->half_page_range = floor( ( $this->page_range - 1 ) / 2 );
		$this->start_page_range = ( $this->current_page_number > $this->half_page_range )? ( $this->current_page_number - $this->half_page_range ) : 1;
		$this->end_page_range = ( $this->current_page_number > $this->half_page_range )? ( $this->current_page_number + $this->half_page_range ) : $this->page_range;
		if ( $this->end_page_range > $this->num_pages )
		{
			 $this->end_page_range = $this->num_pages;
		}

		// Calculate deviations at the start or end of the page range ( window ).
		$extra = ( $this->page_range - ( $this->end_page_range - $this->start_page_range ) );
		if ( $extra > 0 )
		{
			if ( $this->end_page_range == $this->num_pages )
			{
				$this->start_page_range = ( $this->start_page_range - $extra );
				if ( $this->start_page_range < 1 )
				{
					$this->start_page_range = 1;
				}
			}
			else
			{
				$this->end_page_range = ( $this->end_page_range + $extra );
				if ( $this->end_page_range > $this->num_pages )
				{
					$this->end_page_range = $this->num_pages;
				}
			}
		}
	}

	/**
	 * Sets the current page number.
	 *
	 * @param integer $page_number Page number.
	 */
	public function setCurrentPage( $page_number )
	{
		if ( !is_int( $page_number ) )
		{
			throw new Exception( 'Paginate setCurrentPage: Page number must be an integer.' );
		}

		if ( $page_number <= 0 )
		{
			throw new Exception( 'Paginate setCurrentPage: Page number must be a positive integer.' );
		}

		$this->current_page_number = $page_number;
	}

	/**
	 * Set number of items per page.
	 *
	 * @param integer $items_per_page Number of items per page.
	 */
	public function setItemsPerPage( $items_per_page )
	{
		if ( !is_int( $items_per_page ) )
		{
			throw new Exception( 'Paginate setItemsPerPage: Items per page must be an integer.' );
		}

		if ( $items_per_page <= 0 )
		{
			throw new Exception( 'Paginate setItemsPerPage: Items per page must be a positive integer.' );
		}

		$this->items_per_page = $items_per_page;
	}

	/**
	 * Set maxim number of pages to show.
	 *
	 * @param integer $max_num_pages Maxim number of pages.
	 */
	public function setMaxPages( $max_num_pages )
	{
		if ( !is_int( $max_num_pages ) )
		{
			throw new Exception( 'Paginate setMaxPages:  Maxim number of pages must be an integer.' );
		}

		if ( $max_num_pages <= 0 )
		{
			throw new Exception( 'Paginate setMaxPages:  Maxim number of pages must be a positive integer.' );
		}

		$this->max_num_pages = $max_num_pages;
	}

	/**
	 * Set the total number of items.
	 *
	 * @param integer $total The total number of items.
	 */
	public function setNumTotalResults( $total )
	{
		$total = (int)$total;
		if ( $total < 0 )
		{
			throw new Exception( 'Paginate setNumTotalResults: Total must be positive.' );
		}

		$this->items_total = $total;
	}

	/**
	 * Set the url used in the links.
	 *
	 * @param string $url_base The pagination url.
	 */
	public function setUrlBase( $url_base )
	{
		if ( empty( $url_base ) )
		{
			throw new Exception( 'Paginate setUrl: Url must have a value.' );
		}

		$this->url_base = $url_base;
	}

	/**
	 * Set a parameter assigned to the pagination template.
	 *
	 * @param string $param Parameter name assigned to template.
	 * @param mixed $value Value of the parameter.
	 */
	public function setTemplateParam( $param, $value )
	{
		if ( !isset( $param ) || empty( $param ) || !isset( $value ) )
		{
			throw new Exception( "The parameter \$param or \$value can't be empty." );
		}
		$this->params_template[$param] = $value;
	}

	/**
	 * Set the default separator for the page link.
	 *
	 * @param string $separator Separator string.
	 */
	public function setSeparatorLink( $separator )
	{
		if ( empty( $separator ) )
		{
			throw new Exception( 'Paginate setSeparatorLink: The separator must have a value.' );
		}

		$this->separator_link_page = $separator;
	}

	/**
	 * Set the pagination template.
	 *
	 * @param string $layout_template Name pagination template (this is the .tpl file).
	 * @return boolean
	 */
	public function setTemplate( $layout_template )
	{
		$this->layout_template = $layout_template;
	}

	/**
	 * Set display items per page.
	 *
	 * @param array $values_to_show Values to show.
	 */
	public function setDisplayItemsPerPage( $values_to_show )
	{
		$this->display_items_per_page['display'] = false;
		if ( !empty( $values_to_show ) && is_array( $values_to_show ) )
		{
			$result = array_filter( $values_to_show, "is_integer" );
			$this->display_items_per_page['display']	= true;
			$this->display_items_per_page['values'] 	= $result;
		}
	}

	/**
	 * Set page range ( window ).
	 *
	 * @param array $type_page_range Name of the type of page range to set.
	 * @param integer $value Value of the page range.
	 */
	public function setWindowPage( $type_page_range, $value )
	{
		if ( !isset( $this->availables_page_range[$type_page_range] ) )
		{
			throw new Exception( "Paginate setWindowPage: The type of page range ( $type_page_range ), not is valid. ( Availables: default, short and very_short )" );
		}

		if ( !is_int( $value ) )
		{
			throw new Exception( 'Paginate setWindowPage: The value to set page range must be an integer.' );
		}

		$this->availables_page_range[$type_page_range] = $value;
	}
}
