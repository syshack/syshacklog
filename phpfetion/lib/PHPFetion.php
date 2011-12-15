<?php
/**
 * PHP飞信发送类
 * @author quanhengzhuang <blog.quanhz.com>
 * @version 1.2.1
 */
class PHPFetion {
	
	/**
	 * 发送者手机号
	 * @var string
	 */
	protected $_mobile;
	
	/**
	 * 飞信密码
	 * @param string
	 */
	protected $_password;
	
	/**
	 * Cookie字符串
	 * @param string
	 */
	protected $_cookie = '';
	
	/**
	 * 构造函数
	 * @param string $mobile 手机号(登录者)
	 * @param string $password 飞信密码
	 */
	public function __construct($mobile, $password) {
		if($mobile === '' || $password === '') {
			return false;
		}
		
		$this->_mobile = $mobile;
		$this->_password = $password;
		
		$this->_login();
	}
	
	/**
	 * 析构函数
	 */
	public function __destruct() {
		$this->_logout();
	}
	
	/**
	 * 登录
	 * @return string
	 */
	protected function _login() {
		$uri = '/im/login/inputpasssubmit1.action';
		$data = 'm='.$this->_mobile.'&pass='.urlencode($this->_password).'&loginstatus=1';
		
		$result = $this->_postWithCookie($uri, $data);
		
		// 解析Cookie
		preg_match_all('/.*?\r\nSet-Cookie: (.*?);.*?/si', $result, $matches);
		if(isset($matches[1])) {
			$this->_cookie = implode('; ', $matches[1]);
		}

		return $result;
	}

	/**
	 * 向指定的手机号发送飞信
	 * @param string $mobile 手机号(接收者)
	 * @param string $message 短信内容
	 * @return string
	 */
	public function send($mobile, $message) {
		if($message === '') {
			return '';
		}

		// 判断是给自己发还是给好友发
		if($mobile == $this->_mobile) {
			return $this->_toMyself($message);
		} else {
			$uid = $this->_getUid($mobile);
			return $uid === '' ? '' : $this->_toUid($uid, $message);
		}
	}

	/**
	 * 获取飞信ID
	 * @param string $mobile 手机号
	 * @return string
	 */
	protected function _getUid($mobile) {
		$uri = '/im/index/searchOtherInfoList.action';
		$data = 'searchText='.$mobile;
		
		$result = $this->_postWithCookie($uri, $data);
		
		// 匹配
		preg_match('/toinputMsg\.action\?touserid=(\d+)/si', $result, $matches);
		
		return isset($matches[1]) ? $matches[1] : '';
	}
	
	/**
	 * 向好友发送飞信
	 * @param string $uid 飞信ID
	 * @param string $message 短信内容
	 * @return string
	 */
	protected function _toUid($uid, $message) {
		$uri = '/im/chat/sendMsg.action?touserid='.$uid;
		$data = 'msg='.urlencode($message);
		
		$result = $this->_postWithCookie($uri, $data);
		
		return $result;
	}
	
	/**
	 * 给自己发飞信
	 * @param string $message
	 * @return string
	 */
	protected function _toMyself($message) {
		$uri = '/im/user/sendMsgToMyselfs.action';
		$result = $this->_postWithCookie($uri, 'msg='.urlencode($message));
		
		return $result;
	}
	
	/**
	 * 退出飞信
	 * @return string
	 */
	protected function _logout() {
		$uri = '/im/index/logoutsubmit.action';
		$result = $this->_postWithCookie($uri, '');
		
		return $result;
	}
	
	/**
	 * 携带Cookie向f.10086.cn发送POST请求
	 * @param string $uri
	 * @param string $data
	 */
	protected function _postWithCookie($uri, $data) {
		$fp = fsockopen('f.10086.cn', 80);
		fputs($fp, "POST $uri HTTP/1.1\r\n");
		fputs($fp, "Host: f.10086.cn\r\n");
		fputs($fp, "Cookie: {$this->_cookie}\r\n");
		fputs($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
		fputs($fp, "Content-Length: ".strlen($data)."\r\n");
		fputs($fp, "Connection: close\r\n\r\n");
		fputs($fp, $data);

		$result = '';
		while(!feof($fp)) {
			$result .= fgets($fp);
		}

		fclose($fp);

		return $result;
	}

}
