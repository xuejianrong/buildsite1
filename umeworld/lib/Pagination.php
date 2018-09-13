<?php

namespace umeworld\lib;
use Yii;

class Pagination extends \yii\data\Pagination{
	
    public function createUrl($page, $pageSize = null, $absolute = false){
        $page = (int) $page;
        $pageSize = (int) $pageSize;
        if (($params = $this->params) === null) {
            $request = Yii::$app->getRequest();
            $params = $request instanceof Request ? $request->getQueryParams() : [];
        }
        if ($page > 0 || $page >= 0 && $this->forcePageParam) {
            $params[$this->pageParam] = $page + 1;
        } else {
            unset($params[$this->pageParam]);
        }
        if ($pageSize <= 0) {
            $pageSize = $this->getPageSize();
        }
        if ($pageSize != $this->defaultPageSize) {
            $params[$this->pageSizeParam] = $pageSize;
        } else {
            unset($params[$this->pageSizeParam]);
        }
        $params[0] = $this->route === null ? Yii::$app->controller->getRoute() : $this->route;
        $urlManager = $this->urlManager === null ? Yii::$app->getUrlManager() : $this->urlManager;
        if ($absolute) {
            return $urlManager->createAbsoluteUrl($params);
        } else {
			if($this->aPaginationUrl){
				$aGetParam = ['page' => $params['page'], 'perpage' => $params['perpage']];
				if(isset($this->aPaginationUrl[2]) && is_array($this->aPaginationUrl[2])){
					$aGetParam = array_merge($aGetParam, $this->aPaginationUrl[2]);
					$aGetParam['page'] = $params['page'];
				}
				return \umeworld\lib\Url::to($this->aPaginationUrl[0], $this->aPaginationUrl[1], $aGetParam);
			}else{
				return $urlManager->createUrl($params);
			}
        }
    }

}
