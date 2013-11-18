<?php
namespace Models;

class AppKey extends \Jenssegers\Mongodb\Model
{
	protected $guarded = array();

	public static function boot() {
		parent::boot();
		static::saving(function($instance) { $instance->beforeSave(); });
	}

	public function beforeSave() {
		$res = openssl_pkey_new(array(
			"digest_alg" => "sha1",
			"private_key_bits" => 512,
			"private_key_type" => OPENSSL_KEYTYPE_RSA,
		));

		// Extract the public key from $res to $pubKey
		$public_key = openssl_pkey_get_details($res);

		// $public_key ->
		//	'rsa' ->
		//		'n'
		//		'e'
		//		'd'
		//		'p'
		//		'q'
		//		'dmp1'
		//		'dmq1'
		//		'iqmp'

		$this->key    = base64_encode($public_key['rsa']['dmq1']);
		$this->secret = base64_encode($public_key['rsa']['iqmp']);
	}
}