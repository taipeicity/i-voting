<?php

/**
* @package     Surveyforce
* @version     1.0-modified
* @copyright   JoomPlace Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
* @license     GPL-2.0+
* @author      JoomPlace Team,臺北市政府資訊局- http://doit.gov.taipei/
*/
defined('_JEXEC') or die;

function SurveyforceBuildRoute(&$query) {
	$segments = array();


	if (isset($query['view'])) {
		if (isset($query['sid'])) {
			switch ($query['view']) {
				case 'intro':
					$segments[] = $query['sid'] . '-survey-intro';
					break;

				case 'statement':
					$segments[] = $query['sid'] . '-survey-statement';
					if ($query['layout']) {
						$segments = $query['layout'] . "-layout";
						unset($query['layout']);
					}
					break;
				case 'verify_opt':
					$segments[] = $query['sid'] . '-survey-verify-opt';
					if ($query['layout']) {
						$segments = $query['layout'] . "-layout";
						unset($query['layout']);
					}
					break;
				case 'verify':
					$segments[] = $query['sid'] . '-survey-verify';
					if ($query['layout']) {
						$segments = $query['layout'] . "-layout";
						unset($query['layout']);
					}
					break;
				case 'verify2nd':
					$segments[] = $query['sid'] . '-survey-verify2nd';
					break;
				case 'question':
					$segments[] = $query['sid'] . '-survey-question';
					if ($query['layout']) {
						$segments[] = $query['layout'] . "-layout";
						unset($query['layout']);
					}
					break;
				case 'finish':
					$segments[] = $query['sid'] . '-survey-finish';
					if ($query['layout']) {
						$segments[] = $query['layout'] . "-layout";
						unset($query['layout']);
					}
					break;
				case 'result':
					$segments[] = $query['sid'] . '-survey-result';
					if (isset($query['orderby'])) {
						$segments[] = $query['orderby'] . '-orderby';
						unset($query['orderby']);
					}
					break;

				// 實體投票
				case 'place_verify':
					$segments[] = $query['sid'] . '-place-verify';
					break;
				case 'place_question':
					$segments[] = $query['sid'] . '-place-question';
					break;
				case 'place_finish':
					$segments[] = $query['sid'] . '-place-finish';
					break;
				default:
			}
		} else {
			switch ($query['view']) {
				// 首頁
				case 'category':
					$segments[] = 'survey';
					if ($query['layout']) {
						$segments[] = $query['layout'] . "-layout";
						unset($query['layout']);
					}
					break;
				// 實體投票
				case 'place_login':
					$segments[] = 'place-login';
					break;
				case 'place_category':
					$segments[] = 'place-category';
					break;
			}
		}

		unset($query['view']);
		unset($query['sid']);
	}
	

	return $segments;
}

function SurveyforceParseRoute($segments) {

	$segment = explode(':', $segments[0]);
	$vars = array();

	switch ($segments[0]) {
		// 首頁
		case 'survey':
			$vars['view'] = 'category';
			if ($segments[1]) {
				$layout = explode(':', $segments[1]);
				$vars['layout'] = $layout[0];
			}
			break;
		// 實體投票
		case 'place:login':	// 工作人員登入
			$vars['view'] = 'place_login';
			break;
		case 'place:category':	// 議題清單
			$vars['view'] = 'place_category';
			break;
	}


	switch ($segment[1]) {
		
		case 'survey-intro': // 介紹頁
			$vars['view'] = 'intro';
			$vars['sid'] = $segment[0];
			break;
		case 'survey-statement': // 個資聲明頁
			$vars['view'] = 'statement';
			$vars['sid'] = $segment[0];
			if ($segments[1]) {
				$layout = explode(':', $segments[1]);
				$vars['layout'] = $layout[0];
			}
			break;
		case 'survey-verify-opt': // 驗證頁
			$vars['view'] = 'verify_opt';
			$vars['sid'] = $segment[0];
			if ($segments[1]) {
				$layout = explode(':', $segments[1]);
				$vars['layout'] = $layout[0];
			}
			break;
		case 'survey-verify': // 驗證頁
			$vars['view'] = 'verify';
			$vars['sid'] = $segment[0];
			if ($segments[1]) {
				$layout = explode(':', $segments[1]);
				$vars['layout'] = $layout[0];
			}
			break;
		case 'survey-verify2nd':  // 驗證第二頁
			$vars['view'] = 'verify2nd';
			$vars['sid'] = $segment[0];
			break;
		case 'survey-question':  // 題目
			$vars['view'] = 'question';
			$vars['sid'] = $segment[0];
			if ($segments[1]) {
				$layout = explode(':', $segments[1]);
				$vars['layout'] = $layout[0];
			}
			break;
		case 'survey-finish':  // 完成
			$vars['view'] = 'finish';
			$vars['sid'] = $segment[0];
			if ($segments[1]) {
				$layout = explode(':', $segments[1]);
				$vars['layout'] = $layout[0];
			}
			break;
		case 'survey-result':  // 結果頁
			$vars['view'] = 'result';
			$vars['sid'] = $segment[0];
			if ($segments[1]) {
				$orderby = explode(':', $segments[1]);
				$vars['orderby'] = $orderby[0];
			}
			break;

		// 實體投票
		case 'place-verify':	// 驗證頁
			$vars['view'] = 'place_verify';
			$vars['sid'] = $segment[0];
			break;
		case 'place-question':	// 題目頁
			$vars['view'] = 'place_question';
			$vars['sid'] = $segment[0];
			break;
		case 'place-finish':	// 結束
			$vars['view'] = 'place_finish';
			$vars['sid'] = $segment[0];
			break;
		// end
		default:
			$segment2 = explode('.', $segment[1]);
			if (count($segment2) > 1) {
				$vars['view'] = $segment2[0];
				$vars['task'] = @end(@explode('-', $segment2[1]));
				$vars['id'] = $segment[0];
			}
			break;
	}

	if (empty($vars['view']))
		$vars['view'] = 'survey';

	return $vars;
}
