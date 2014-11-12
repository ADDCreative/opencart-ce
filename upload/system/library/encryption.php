<?php
final class Encryption {
	private $key;
	private $iv;

	public function __construct($key) {
		$this->key = hash('sha256', $key, true);
	}

	public function encrypt($value) {
		$this->iv = secure_random_bytes(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));
		$block_size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);

		$pad = $block_size - (strlen($value) % $block_size);
		$data = $value . str_repeat(chr($pad), $pad);

		return strtr(base64_encode($this->iv . mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->key, $data, MCRYPT_MODE_CBC, $this->iv)), '+/=', '-_,');
	}

	public function decrypt($value) {
		$value = base64_decode(strtr($value, '-_,', '+/='));
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);

		$iv = substr($value, 0, $iv_size);
		$data = substr($value, $iv_size);

		$data = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->key, $data, MCRYPT_MODE_CBC, $iv);

		if (strlen($data)) {
			$pad = ord($data[strlen($data) - 1]);
			$data = substr($data, 0, -$pad);

			return $data;
		} else {
			return false;
		}
	}
}
?>