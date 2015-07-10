<?php
/**
 * @package PNixx.Pagination
 * @author  Sergey Odintsov <nixx.dj@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php (MIT License)
 */

namespace PNixx\Pagination;

class Pagination {

	/**
	 * Total items count
	 * @var int
	 */
	protected $total_items = 0;

	/**
	 * Items per page
	 * @var int
	 */
	protected $items_per_page = 20;

	/**
	 * @var int
	 */
	protected $proximity = 2;

	/**
	 * Current page
	 * @var
	 */
	protected $page = 1;

	/**
	 * @var string
	 */
	protected $uri;

	/**
	 * Default is with a $_GET[page] parameter. Set it to "page/{page}" for friendly URLs
	 * @var string
	 */
	protected $uri_pattern = "page={page}";

	/**
	 * Previous label
	 * @var string
	 */
	protected $label_prev = "&laquo;";

	/**
	 * Next label
	 * @var string
	 */
	protected $label_next = "&raquo;";

	/**
	 * Show arrows always or only not on the first or last page
	 * @var bool
	 */
	protected $show_arrow_if_need = true;

	/**
	 * Pagination constructor.
	 * @param array $params Pagination's options
	 */
	public function __construct($params = []) {
		if( $params && is_array($params) ) {
			foreach( $params as $key => $value ) {
				switch( $key ) {
					case "items":
					case "total":
						$this->setItems($value);
						break;
					case "page":
						$this->setPage($value);
						break;
					case "per_page":
						$this->setItemsPerPage($value);
						break;
					case "uri":
						$this->setUri($value);
						break;
					case "pattern":
						$this->setPattern($value);
						break;
					case "proximity":
						$this->setProximity($value);
						break;
				}
			}
		}
	}

	/**
	 * Sets total number of items.
	 * @param integer $total_items
	 * @return Pagination
	 */
	public function setTotalItems($total_items) {
		return $this->setItems($total_items);
	}

	/**
	 * Returns total number of items.
	 *
	 * @return int
	 */
	public function getTotalItems() {
		return $this->getItems();
	}

	/**
	 * @see setTotalItems() alias
	 * @param $totalItems
	 * @return $this
	 * @throws Exception
	 */
	public function setItems($totalItems) {
		if( $totalItems < 0 ) {
			throw new Exception("totalItems must be not negative value");
		}
		$this->total_items = (int)$totalItems;

		return $this;
	}

	/**
	 * @see getTotalItems() alias
	 */
	public function getItems() {
		return $this->total_items;
	}

	/**
	 * Sets current page number.
	 * @param integer $page
	 * @return $this
	 * @throws Exception
	 */
	public function setPage($page) {
		if( $page < 1 ) {
			throw new Exception("page must be positive integer value");
		}
		$this->page = (int)$page;

		return $this;
	}

	/**
	 * Returns current page number.
	 * If the page was not set it'll try to guess it from the URI. If the pattern was not found it will return 1.
	 * @return integer
	 */
	public function getPage() {
		// the page was previously set
		if( $this->page ) {
			return $this->page;
		}

		// get the current page from the URI
		if( preg_match($this->buildPattern(), $this->getUri(), $matches) ) {
			return (int)$matches[1];
		}

		// default page is 1
		return 1;
	}

	/**
	 * Sets items per page.
	 * @param integer $items_per_page
	 * @return $this
	 * @throws Exception
	 */
	public function setItemsPerPage($items_per_page) {
		if( $items_per_page < 1 ) {
			throw new Exception("itemsPerPage must be positive integer value");
		}
		$this->items_per_page = (int)$items_per_page;

		return $this;
	}

	/**
	 * Returns number of items per page. Default is 20.
	 * @return integer.
	 */
	public function getItemsPerPage() {
		return $this->items_per_page;
	}

	/**
	 * @see setItemsPerPage() alias
	 * @param $items_per_page
	 * @return Pagination
	 * @throws Exception
	 */
	public function setLimit($items_per_page) {
		return $this->setItemsPerPage($items_per_page);
	}

	/**
	 * @see getItemsPerPage() alias
	 */
	public function getLimit() {
		return $this->getItemsPerPage();
	}

	/**
	 * Sets proximity - how many page links should be in front and after current page.
	 * Default is 4.
	 * Total number of links (items toArray() method returns) can be calculated by
	 * proximity * 2 + 1 (current page) + 1 (previous) + 1 (next). So if proximity
	 * is set to 4 total number of links will be 13; if proximity is 3 total pages = 9
	 * @param [type] $proximity [description]
	 * @return $this
	 * @throws Exception
	 */
	public function setProximity($proximity) {
		if( $proximity < 0 ) {
			throw new Exception("proximity must be non negative value");
		}
		$this->proximity = (int)$proximity;

		return $this;
	}

	/**
	 * Returns proximity - how many page links should be in front and after current page.
	 * Default is 4
	 * @return integer
	 */
	public function getProximity() {
		return $this->proximity;
	}

	/**
	 * Sets current URI.
	 * @param string $uri
	 * @return $this
	 */
	public function setUri($uri) {
		$this->uri = $uri;

		return $this;
	}

	/**
	 * Returns current URI. If the URI was not set with setUri() it will return URI from the server.
	 * @return string
	 */
	public function getUri() {
		if( $this->uri ) {
			return $this->uri;
		}
		if( isset($_SERVER["REQUEST_URI"]) ) {
			return $_SERVER["REQUEST_URI"];
		}

		return "/";
	}

	/**
	 * Sets the URI pattern for creating links for pages.
	 * Default pattern is "page={page}"  (URLs like /posts/show?page=5)
	 * Can be set for example to "p={page}" or anything else for $_GET parameter
	 * Can be set also to "page/{page}" for friendly URLs which will result with URLs like: /posts/show/page/5
	 * @param string $pattern
	 * @return $this
	 */
	public function setPattern($pattern) {
		$this->uri_pattern = $pattern;

		return $this;
	}

	/**
	 * Returns URI pattern.
	 * @return string
	 */
	public function getPattern() {
		return $this->uri_pattern;
	}

	/**
	 * Calculate and return total number of pages based on total items and items per page settings.
	 * @return integer
	 */
	public function getTotalPages() {
		return (int)ceil($this->total_items / $this->items_per_page);
	}

	/**
	 * Returns first item's index in a page we are on. Used in SQLs (e.g. LIMIT 20 OFFSET 60)
	 *
	 * @return integer
	 */
	public function getOffset() {
		return (int)max(0, ($this->getPage() - 1) * $this->getItemsPerPage());
	}

	/**
	 * Returns pagination in form of an array.
	 * Each pagination item (link) will contain certain info that can be used in a template to build HTML.
	 *
	 * @return array
	 *     [
	 *         [
	 *             "page"        => page number,
	 *             "uri"         => anchor's URI,
	 *             "isCurrent"   => if the page is a current one,
	 *             "isDisabled"  => if the button should be marked as disabled,
	 *             "label"       => label,
	 *             "class"       => class name,
	 *         ]
	 *     ]
	 */
	public function toArray() {
		$result = [];
		$page = $this->getPage();

		// are we on first page?
		if( $page == 1 ) {
			if( $this->show_arrow_if_need ) {
				$result["prev"] = [
					"page"       => false,
					"uri"        => "#",
					"isCurrent"  => false,
					"isDisabled" => true,
					"label"      => $this->label_prev,
					"class"      => "arrow unavailable"
				];
			}
		} else {
			$result["prev"] = [
				"page"       => $page - 1,
				"uri"        => $this->createLink($page - 1),
				"isCurrent"  => false,
				"isDisabled" => false,
				"label"      => $this->label_prev,
				"class"      => "arrow"
			];
		}

		// do we need first link?
		if( $this->getItems() > 0 ) {
			// are we on first page?
			$result[1] = [
				"page"       => 1,
				"uri"        => $this->createLink(1),
				"isCurrent"  => ($page == 1),
				"isDisabled" => ($page == 1),
				"label"      => "1",
				"class"      => ($page == 1 ? "current" : "")
			];
		}

		$proximity = $this->getProximity();
		$maxPages = $proximity * 2 + 1;

		// 2 .. last_page - 1
		if( $last = $this->getTotalPages() ) {
			if( $last <= $maxPages ) {
				$from = 2;
				$to = $last - 1;
			} else {
				$from = max(2, $page - $proximity);
				$to = min($last - 1, $page + $proximity);
				while( $to - $from < $maxPages - 1 ) {
					if( $from > 2 ) {
						$from--;
					} elseif( $to < $last - 1 ) {
						$to++;
					} else {
						break;
					}
				}
			}

			// do we need "..." button after first page
			if( $from > 2 && $proximity ) {
				$result["less"] = [
					"page"       => 0,
					"uri"        => "#",
					"isCurrent"  => false,
					"isDisabled" => true,
					"label"      => "&hellip;",
					"class"      => "unavailable"
				];
				$from++;
			}

			// do we need "..." button before last page
			if( $to < $last - 1 ) {
				$to--;
				$showMore = true;
			} else {
				$showMore = false;
			}
			for( $i = $from; $i <= $to; $i++ ) {
				$result[$i] = [
					"page"       => $i,
					"uri"        => $this->createLink($i),
					"isCurrent"  => ($i == $page),
					"isDisabled" => false,
					"label"      => $i,
					"class"      => ($i == $page ? "current" : "")
				];
			}
			if( !$proximity ) {
				$result[$i] = [
					"page"       => $page,
					"uri"        => $this->createLink($page),
					"isCurrent"  => true,
					"isDisabled" => false,
					"label"      => $page,
					"class"      => "current"
				];
			}
			if( $showMore && $proximity ) {
				$result["more"] = [
					"page"       => 0,
					"uri"        => "#",
					"isCurrent"  => false,
					"isDisabled" => true,
					"label"      => "&hellip;",
					"class"      => "unavailable"
				];
			}
		}

		// do we need last link?
		if( $this->getTotalPages() > 1 ) {
			// are we on last page?
			$result[$last] = [
				"page"       => $last,
				"uri"        => $this->createLink($last),
				"isCurrent"  => ($page == $last),
				"isDisabled" => ($page == $last),
				"label"      => $last,
				"class"      => ($page == $last ? "current" : "")
			];
		}

		// are we on last page
		if( $page >= $last ) {
			if( $this->show_arrow_if_need ) {
				$result["next"] = [
					"page"       => false,
					"uri"        => "#",
					"isCurrent"  => false,
					"isDisabled" => true,
					"label"      => $this->label_next,
					"class"      => "arrow unavailable"
				];
			}
		} else {
			$result["next"] = [
				"page"       => $page + 1,
				"uri"        => $this->createLink($page + 1),
				"isCurrent"  => false,
				"isDisabled" => false,
				"label"      => $this->label_next,
				"class"      => "arrow"
			];
		}

		return $result;
	}

	/**
	 * Returns a link for a given page based on current URL and URI pattern.
	 * @param  integer $page Page number
	 * @return string
	 */
	public function createLink($page) {
		// if we find any matches, the URI we have to change the URI
		if( preg_match($this->buildPattern(), $this->getUri(), $matches) ) {
			$link = str_replace($matches[0], preg_replace("~([1-9][0-9]*)~i", $page, $matches[0]), $this->getUri());
			// not found a pattern in the URI, we'll check do we deal with a get parameter
		} elseif( strpos($this->uri_pattern, "=") ) {
			if( strpos($this->getUri(), "?") ) {
				$link = $this->getUri() . "&" . str_replace("{page}", $page, $this->uri_pattern);
			} else {
				$link = $this->getUri() . "?" . str_replace("{page}", $page, $this->uri_pattern);
			}
			// URI friendly
		} else {
			if( strpos($this->getUri(), "?") ) {
				$parts = explode("?", $this->getUri(), 2);
				$link = $parts[0] . "/" . str_replace("{page}", $page, $this->uri_pattern) . "?" . $parts[1];
			} else {
				$link = $this->getUri() . "/" . str_replace("{page}", $page, $this->uri_pattern);
			}
		}

		return $link;
	}

	/**
	 * Render html for Foundation style
	 * @return string
	 */
	public function render() {
		if( $this->getTotalPages() > 1 ) {
			$html = '<ul class="pagination">';
			foreach( $this->toArray() as $key => $page ) {
				$html .= '<li class="' . $page['class'] . '"><a href="' . $page['uri'] . '">' . $page['label'] . '</a></li>';
			}
			$html .= '</ul>';

			return $html;
		}

		return '';
	}

	/**
	 * @return string
	 */
	protected function buildPattern() {
		// get the current page from the URI
		$pattern = str_replace("{page}", "([1-9][0-9]*)", $this->uri_pattern);
		if( strpos($pattern, "=") > 0 ) {
			// starts with "&" or "?"
			// at the end of the URI or next char "&"
			$pattern = "[&\?]" . $pattern . "(&|\Z)";
		} else {
			// starts with "/";
			// it should be end of the URI or next char should be "/" or "?"
			$pattern = "/" . $pattern . "(/|\?|\Z)";
		}

		return "~{$pattern}~i";
	}

	/**
	 * @param string $label_prev
	 */
	public function setLabelPrev($label_prev) {
		$this->label_prev = $label_prev;
	}

	/**
	 * @param string $label_next
	 */
	public function setLabelNext($label_next) {
		$this->label_next = $label_next;
	}

	/**
	 * @param boolean $show_arrow_if_need
	 */
	public function setShowArrowIfNeed($show_arrow_if_need) {
		$this->show_arrow_if_need = $show_arrow_if_need;
	}
}
