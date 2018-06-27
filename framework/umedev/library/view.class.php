<?php
class View{

	protected $viewDir = '';
	protected $data = '';
	
	public function __construct($viewDir){
		$this->viewDir = $viewDir;
	}

    public function assign($key, $value){
		$this->data[$key] = $value;
    }

	public function display($viewFileName){
		echo self::_parse($viewFileName);
    }

	public function fetch($viewFileName){
		return self::_parse($viewFileName);
    }

    private function _parse($viewFileName){
		if($this->data){
			extract($this->data, EXTR_PREFIX_SAME, 'data');
		}
		ob_start();
		ob_implicit_flush(false); 
		$viewFilePath = $this->viewDir . $viewFileName;
		if(!file_exists($viewFilePath)){
			return false;
		}
		require $viewFilePath;
		return ob_get_clean();
    }
	
}
