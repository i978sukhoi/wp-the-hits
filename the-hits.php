<?php
/**
 * Plugin Name: the-hits
 * Plugin URI: https://github.com/i978sukhoi/wp-the-hits
 * Description: 조회수 기능. usage: &lt;?php the_hits(); ?&gt;
 * Version: 0.1
 * Author: sukhoi
 * License: GPLv2 or later
 */
function the_hits($echo = true) {
	if($post_id = get_the_ID()) {
		$hits = apply_filters('the_hits', ($hits = get_post_meta($post_id, 'the_hits', true)) ? intval($hits) : 0);
		if($hits === - 1) return;
		if($echo) echo $hits;
		return $hits;
	}
}
function the_hits_increase_hits() {
	if(! is_singular()) return; // 단독으로 이 포스트를 보는 경우에만 조회수를 증가시킨다.
	$post_id = get_the_ID();
	$hits = apply_filters('increase_hits', ($hits = get_post_meta($post_id, 'the_hits', true)) ? intval($hits) : 0);
	if($hits === - 1) {
		// 조회수 filter에서 -1 을 return하는 경우에는 아무 작업도 하지 않는다.
	} else {
		$cookie = isset($_COOKIE['the_hits']) ? explode('|', $_COOKIE['the_hits']) : array();
		if($post_id && ! in_array($post_id, $cookie)) {
			// 이 포스트를 수정할 수 있는 사용자는 조회수를 증가시키면 안된다.
			if(current_user_can('edit_post', $post_id)) return;
			update_post_meta($post_id, 'the_hits', $hits + 1, $hits);
			$cookie[] = $post_id;
			while(count($cookie) > 15)
				array_shift($cookie);
			setcookie('the_hits', implode('|', $cookie), time() + 86400 * 15, '/', COOKIE_DOMAIN);
		}
	}
}
add_action('template_redirect', 'the_hits_increase_hits');
