<?php
class Pagination{
	private $_url;
	private $_total;
	private $_page = 1;
	private $_size = 20;
	private $_subs = 10;
	private $_onclick = '';
	private $_overview = '';
	private $_selector = '';
	private $_labels = array(
		'first'		=>	'First',
		'previous'	=>	'Previous',
		'next'		=>	'Next',
		'last'		=>	'Last',
	);
	private $_pages;

	public function __construct($aPageData){
		$this->_url = isset($aPageData['url']) ? $aPageData['url'] : '';
		$this->_total = isset($aPageData['total']) ? intval($aPageData['total']) : 0;
		$this->_page = isset($aPageData['page']) ? intval($aPageData['page']) : $this->_page;
		$this->_size = isset($aPageData['size']) ? intval($aPageData['size']) : $this->_size;
		$this->_subs = isset($aPageData['subs']) ? intval($aPageData['subs']) : $this->_subs;
		$this->_subs = (($this->_subs % 2) == 0) ? $this->_subs + 1 : $this->_subs;
		$this->_onclick = isset($aPageData['onclick']) ? $aPageData['onclick'] : $this->_onclick;
		$this->_overview = isset($aPageData['overview']) ? $aPageData['overview'] : $this->_overview;
		$this->_selector = isset($aPageData['selector']) ? $aPageData['selector'] : $this->_selector;
		$this->_labels = isset($aPageData['labels']) ? $aPageData['labels'] : $this->_labels;
		$this->_pages = ceil($this->_total / $this->_size);
	}

	public function display(){
		echo $this->fetch();
	}

	public function fetch(){
		if(!$this->_url || !$this->_total){
			return '';
		}
		if($this->_total <= $this->_size){
			return '';
		}
		if($this->_page < 1){
			$this->_page = 1;
		}elseif($this->_page > $this->_pages){
			$this->_page = $this->_pages;
		}

		$pageHtml = '<div class="pagination">';

		if($this->_overview){
			$overview = str_replace(array('_TOTAL_', '_PAGE_', '_PAGES_'), array($this->_total, $this->_page, $this->_pages), $this->_overview);
			$pageHtml .= '<div class="_overview">' . $overview . '</div>';
		}

		$pageHtml .= '<div class="pages">';
		if($this->_page > 1){
			if(!$this->_onclick){
				$pageHtml .= '<a href="' . preg_replace('/_PAGE_/', 1, 					$this->_url, 1) . '">' . 	$this->_labels['first'] . '</a>';
				$pageHtml .= '<a href="' . preg_replace('/_PAGE_/', $this->_page - 1, 	$this->_url, 1) . '">' . 	$this->_labels['previous'] . '</a>';
			}else{
				$pageHtml .= '<a onclick="javascript:' . preg_replace('/_PAGE_/', 1, 					$this->_onclick, 1) . '" >' . $this->_labels['first'] . '</a>';
				$pageHtml .= '<a onclick="javascript:' . preg_replace('/_PAGE_/', $this->_page - 1, 	$this->_onclick, 1) . '" >' . $this->_labels['previous'] . '</a>';
			}
		}
		if($this->_pages <= $this->_subs){
			for($i = 1; $i <= $this->_pages; $i++){
				if(!$this->_onclick){
					$hrefLabel = ($this->_page == $i) ? 'class="on" ' : ' href="' . preg_replace('/_PAGE_/', $i, $this->_url, 1) . '"';
				}else{
					$hrefLabel = ($this->_page == $i) ? 'class="on" ' : ' onclick="javascript:' . (preg_replace('/_PAGE_/', $i, $this->_onclick, 1)) . '"';
				}
				$pageHtml .= '<a ' . $hrefLabel . ' >' . $i . '</a>';
			}
		}else{
			if(($this->_page) <= ($this->_subs + 1) / 2){
				for($i = 1; $i <= $this->_subs; $i++){
					if(!$this->_onclick){
						$hrefLabel = ($this->_page == $i) ? 'class="on" ' : ' href="' . preg_replace('/_PAGE_/', $i, $this->_url, 1) . '"';
					}else{
						$hrefLabel = ($this->_page == $i) ? 'class="on" ' : ' onclick="javascript:' . (preg_replace('/_PAGE_/', $i, $this->_onclick, 1)) . '"';
					}
					$pageHtml .= '<a ' . $hrefLabel . ' >' . $i . '</a>';
				}
			}elseif((($this->_page - 1) > ($this->_subs - 1) / 2) && (($this->_pages - $this->_page) > ($this->_subs - 1) / 2)){
				for($i = $this->_page - ($this->_subs - 1) / 2; $i <= $this->_page + ($this->_subs - 1) / 2; $i++){
					if(!$this->_onclick){
						$hrefLabel = ($this->_page == $i) ? ' class="on" ' : ' href="' . preg_replace('/_PAGE_/', $i, $this->_url, 1) . '"';
					}else{
						$hrefLabel = ($this->_page == $i) ? 'class="on" ' : ' onclick="javascript:' . (preg_replace('/_PAGE_/', $i, $this->_onclick, 1)) . '"';
					}
					$pageHtml .= '<a ' . $hrefLabel . ' >' . $i . '</a>';
				}
			}else{
				for($i = $this->_pages + 1 - $this->_subs; $i <= $this->_pages; $i++){
					if(!$this->_onclick){
						$hrefLabel = ($this->_page == $i) ? 'class="on" ' : ' href="' . preg_replace('/_PAGE_/', $i, $this->_url, 1) . '"';
					}else{
						$hrefLabel = ($this->_page == $i) ? 'class="on" ' : ' onclick="javascript:' . (preg_replace('/_PAGE_/', $i, $this->_onclick, 1)) . '"';
					}
					$pageHtml .= '<a ' . $hrefLabel . ' >' . $i . '</a>';
				}
			}
		}

		if($this->_page < $this->_pages){
			if(!$this->_onclick){
				$pageHtml .= '<a href="' . preg_replace('/_PAGE_/', $this->_page + 1, 	$this->_url, 1) . '">' . $this->_labels['next'] . '</a>';
				$pageHtml .= '<a href="' . preg_replace('/_PAGE_/', $this->_pages, 		$this->_url, 1) . '">' . $this->_labels['last'] . '</a>';
			}else{
				$pageHtml .= '<a onclick=javascript:' . preg_replace('/_PAGE_/', $this->_page + 1, 	$this->_onclick, 1) . ' >' . $this->_labels['next'] . '</a>';
				$pageHtml .= '<a onclick=javascript:' . preg_replace('/_PAGE_/', $this->_pages, 		$this->_onclick, 1) . ' >' . $this->_labels['last'] . '</a>';
			}
		}
		$pageHtml .= '</div>';


		if($this->_selector){
			$pageHtml .= '<div class="select">';
			if($this->_pages > 20){
				if(!$this->_onclick){
					$des_url = $this->_url;
					$input_str = <<<HTML
							<input type="text" id="__page" value="{$this->_page}" onkeyup="value=value.replace(/[^\d]/g,'')" onkeypress="if(event.keyCode==13) {__pageGo(); return false;}" />
							<input type="button" value="GO!" onclick="javascript:__pageGo()" />
							<script type="text/javascript">function __pageGo(){var _page = document.getElementById("__page").value; _page = parseInt(_page); if(_page > 0){var des_url = '{$des_url}';des_url = des_url .replace("_PAGE_", _page);	window.location.href = des_url;}}</script>
HTML;
					$pageHtml .= $input_str;
				}else{
					$input_str = <<<HTML
							<input type="text" id="__page" value="{$this->_page}" onkeyup="value=value.replace(/[^\d]/g,'')" onkeypress="if(event.keyCode==13) {__pageGo(); return false;}" />
							<input type="button" value="GO!" onclick="__pageGo();" />
							<script type="text/javascript">	function __pageGo(){var _page = document.getElementById("__page").value;_page = parseInt(_page);if(_page > 0){var _event = "{$this->_onclick}";_event = eval (_event.replace('_PAGE_',_page));	_event;}}</script>
HTML;
					$pageHtml .= $input_str;
				}
			}else{
				if(!$this->_onclick){
					$pageHtml .= '<select onchange="javascript:window.location.href=\'' . preg_replace('/_PAGE_/', '\'+this.value+\'', $this->_url, 1) . '\'">';
					for($i = 1;$i <= $this->_pages;$i++){
						$options = ($this->_page == $i) ? '<option selected value=' . $i . '>' . $i . '</option>' : '<option value=' . $i . '>' . $i . '</option>';
						$pageHtml .= $options;
					}
					$pageHtml .= '</select>';
				}else{
					$pageHtml .= '<select>';
					for($i = 1;$i <= $this->_pages;$i++){
						$sleceted = '';
						if($this->_page == $i){
							$sleceted = ' selected ';
						}
						$options = '<option onclick=javascript:' . preg_replace('/_PAGE_/', $i, $this->_onclick, 1) . $sleceted . ' value=' . $i . '>' . $i . '</option>';
						$pageHtml .= $options;
					}
					$pageHtml .= '</select>';
				}
			}
			$pageHtml .= '</div>';
		}
		$pageHtml .= '</div>';
		return $pageHtml;
	}
}
