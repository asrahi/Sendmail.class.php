<?
/* ******************************************
메일 처리 함수 120812 by ASRAHI
	- UTF-8만 지원됨
	- 에러는 예외로 뱉어냄
****************************************** */
Class SendMail{
	protected $to = array(); // 받는사람
	protected $cc  = array(); // 참조
	protected $bcc  = array(); // 숨은 참조
	protected $from = array(); // 보낸사람

	protected $headers = ''; //메일헤더
	protected $subject = ''; //제목
	protected $message = ''; //본문

	function __construct() {

	}

	//받는사람 설정
	public function setTo($name,$mail='') {
		$this->to[] = $this->parseEmailString($name,$mail);
	}

	public function setFrom($name,$mail='') {
		$this->from[] = $this->parseEmailString($name,$mail);
	}

	public function setCc($name,$mail='') {
		$this->cc[] = $this->parseEmailString($name,$mail);
	}

	public function setBcc($name,$mail='') {
		$this->bcc[] = $this->parseEmailString($name,$mail);
	}

	public function setMessage($p_message) {
		$message = "<html><head>\n";
		$message.= "<meta http-equiv=\"Content-Language\" content=\"ko\">\n";
		$message.= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n";
		$message.= "<title>{$_POST['subject']}</title>";
		$message.= "</head><body>\n";

		$p_message = nl2br($p_message);
		$message.= "{$p_message}\n";

		$message.= "</div>\n";
		$message.= "</body></html>\n";
		$this->message = $message;
	}

	public function setSubject($subject) {
		$this->subject = $subject;
	}


	//발송 하기
	public function send() {
		$to = implode(', ',$this->to);
		$subject = "=?UTF-8?B?".base64_encode($this->subject)."?=";
		return mail( $to,$subject, $this->message, $this->makeHeaders() );
	}

	// 헤더 만들기
	protected function makeHeaders() {
		$to = implode(', ',$this->to);
		$from = implode(', ',$this->from);
		$cc = implode(', ',$this->cc);
		$bcc = implode(', ',$this->bcc);

		$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=UTF-8\r\n";
		$headers .= "To: {$to}\r\n";
		$headers .= "From: {$from}\r\n";
		if($cc) $headers .= "Cc: {$cc}\r\n";
		if($bcc) $headers .= "Bcc: {$bcc}\r\n";
		return $headers;
	}

	//bool 메일형식 체크
	protected function is_mail($email) {
		return preg_match("/^([^<]+)\\@(.+)\\.(.+)$/", $email);
	}

	//array 이메일 형식 빼오기
	protected function splitMail($str) {
		if(!preg_match("/(.*)<(.*)>/",$str,$out)) throw new Exception('잘못된 이메일 형식입니다.');
		return array(trim($out[1]),trim($out[2]));
	}

	//메일값 만들기
	protected function parseEmailString($name,$mail='') {
		if( $this->is_mail($name) ) {
			return $name;
		} elseif($mail){
			if(!$this->is_mail($mail)) throw new Exception('잘못된 이메일 형식입니다.');
			return "=?UTF-8?B?".base64_encode($name)."?="."<".$mail. ">";
		} else {
			$out = $this->splitMail($name);
			if(!$this->is_mail($out[1])) throw new Exception('잘못된 이메일 형식입니다.');
			return "=?UTF-8?B?".base64_encode($out[0])."?="."<".$out[1]. ">";
		}
	}

}
?>