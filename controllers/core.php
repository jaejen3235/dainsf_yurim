<?php
class Core {
	private $param;
	private $now;

	public function __construct($param = null) {
		$this->param = $param;
		$this->now = date("Y-m-d");
	}
	
	public function viewPage($controller, $action) {
        $htmlPath = "views/".$controller."/".$action.".html";
        $phpPath = "views/".$controller."/".$action.".php";
    
        if (file_exists($htmlPath)) {
            require_once($htmlPath);
        } else if (file_exists($phpPath)) {
            require_once($phpPath);
        } else {
            echo "Page not found"; // 혹은 다른 처리를 수행할 수 있도록 로직 추가
        }
    }
}
?>