<?php 
class Chat{
	public static function get($last_id=false) {
		global $CFG;
		
		if ($CFG->memcached) {
			$cached = $CFG->m->get('chat');
			if ($cached) {
				$orig_messages = array();
				if ($last_id > 0) {
					if (!empty($cached['messages']) && is_array($cached['messages'])) {
						$orig_messages = $cached['messages'];
						foreach ($cached['messages'] as $k => $message) {
							if ($message['id'] <= $last_id)
								unset($cached['messages'][$k]);
						}
					}
				}
				
				if ($last_id > 0 && count($cached['messages']) > 0) {
					$cached['lastId'] = $cached['messages'][0]['id'];
					$CFG->m->set('chat',array('numUsers'=>$cached['numUsers'],'messages'=>$orig_messages,'lastId'=>$cached['messages'][0]['id']),300);
				}
				return $cached;
			}
		}
		
		$sql = 'SELECT COUNT(DISTINCT user_id) AS total FROM sessions';
		$result = db_query_array($sql);
		$total = ($result) ? $result[0]['total'] : 0;
		
		$sql = 'SELECT id,message,username FROM chat '.($last_id > 0 ? 'WHERE id > '.$last_id : '').' ORDER BY id DESC LIMIT 0,30';
		$result = db_query_array($sql);
		
		if ($CFG->memcached && !$last_id)
			$CFG->m->set('chat',array('numUsers'=>$total,'messages'=>$result,'lastId'=>$result[0]['id']),300);
		
		return array('numUsers'=>$total,'messages'=>$result,'lastId'=>$result[0]['id']);
	}
	
	public static function newMessage($message=false) {
		global $CFG;
	
		if (!$CFG->session_active)
			return false;
	
		if (!$message)
			return false;
		
		$message = preg_replace('/[^\pL 0-9a-zA-Z!@#$%&*?\.\-\_\,]/u','',$message);
		$handle = (empty(User::$info['chat_handle'])) ? 'Guest-'.User::$info['user'] : User::$info['chat_handle'];
		$id = db_insert('chat',array('message'=>$message,'username'=>$handle,'site_user'=>User::$info['id']));
		
		if ($CFG->memcached) {
			$cached = $CFG->m->get('chat');
			if ($cached) {
				if (empty($cached['messages']))
					$cached['messages'][] = array('id'=>$id,'message'=>$message,'username'=>$handle,'site_user'=>User::$info['id']);
				else
					$cached['messages'] = array_merge(array(array('id'=>$id,'message'=>$message,'username'=>$handle,'site_user'=>User::$info['id'])),$cached['messages']);
				
				if (count($cached['messages']) > 30)
					array_pop($cached['messages']);
				
				$CFG->m->set('chat',$cached,300);
			}
		}
		
		return array('lastId'=>$id);
	}
}
?>