<?php

namespace Core\MailChimp;


class SubscribeMailChimp {

	private $email  = '';
	private $name   = '';
	private $attr   = [];
	private $apiKey = '';
	private $listId = '';
	private $status = 'subscribed'; // subscribed|unsubscribed|cleaned|pending
	private $mergeFields = [ 'FNAME'   => '', 'PHONE'   => '', 'MESSAGE' => '', ]; // require fields

	public function __construct() {

		$this->apiKey = '84cf5b9e970486f9218aaf0f5ee6ba1d-us17';
		$this->listId = '963c59a3df';
		// action
		add_action( 'wp_ajax_subscribe', [ $this, 'init' ] );
		add_action( 'wp_ajax_nopriv_subscribe', [ $this, 'init' ] );

	}

	public function init() {

		$this->email           = self::securityInput( $_POST['email'] );
		$this->name            = self::securityInput( $_POST['name'] );
		$this->attr['tags'][0] = 'dwnld_PM_WP'; // default tag
		$this->attr['groups'][0] = [ 'name' => 'Job_title', 'position' => $_POST['position'] ]; // default group
		$return['message']     = __( 'Error: name or email address is not valid', 'nix_united' );
		$return['status']      = 401;
		$return['email']       = $this->email;
		$return['name']        = $this->name;

		if ( ! is_email( $this->email ) || ! validate_username( $this->name ) ) {
			wp_send_json( $return );
		}

		$this->mergeFields = [
			'FNAME' => $this->name,
			'PHONE' => ' ',
			'MESSAGE' => 'I want to subscribe with API.',
		];
		$return = [
			'message'       => __( 'You subscribe' ),
			'email'         => $this->email,
			'status'        => $this->status,
			'list_id'       => $this->listId,
			'api_key'       => $this->apiKey,
			'merge_fields'  => $this->mergeFields,
			'mailchimp_msg' => $this->sendMailchimpSubscriber(), // request to MailChimp
		];

		// If already been subscribed, do add tags. Check response status
		if ( $return['mailchimp_msg']->status == 400 ) {

			$return['status']  = 400;

		} elseif ( $return['mailchimp_msg']->status == 'subscribed' ) {

			$return['status'] = 200;

		}

		wp_send_json( $return );
	}

	public function sendMailchimpSubscriber() {

		self::turnError();

		$args = array(
			'tags'          => $this->attr['tags'],
			'email_address' => $this->email,
			'status'        => $this->status,
			'merge_fields'  => $this->mergeFields,
		);
		$dc = substr( $this->apiKey, strpos( $this->apiKey, '-' ) + 1 ); // rewrite api key for url api service account
		$auth = array(
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( 'user:'. $this->apiKey )
			)
		);
		$client = new \MailchimpMarketing\ApiClient();
		$client->setConfig([
			'apiKey' => $this->apiKey,
			'server' => $dc,
		]);


		if( isset( $this->attr['groups'] ) ) {

			// Get groups in category
			$interestCat = $client->lists->getListInterestCategories($this->listId);

			// Check on exists categories in MailChimps
			if ( ! empty( $interestCat->categories ) ) {

				foreach ( $interestCat->categories as $category ) {

					// Check on exists these group in MailChimp
					if ( $category->title === $this->attr['groups'][0]['name'] ) {

						// Get group items in MailChimp
						$interestList = $client->lists->listInterestCategoryInterests( $this->listId, $category->id );

						if( $interestList->interests ) {

							foreach ($interestList->interests as $interest) {

								$args['interests'][$interest->id] = false;

								if( $interest->name === $_POST['position'] ) {

									$args['interests'][$interest->id] = true;

								}
							}

						}

					}

				}

			}

		}

		return $client->lists->setListMember($this->listId, $this->getHash(), $args);
	}

	public static function turnError(){
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
	}

	public static function securityInput( $data ) {
		$data = trim( $data );
		$data = stripslashes( $data );
		$data = strip_tags( $data );
		$data = htmlspecialchars( $data );

		return $data;
	}

	private function getHash() {
		$return = mb_strtolower($this->email);
		return md5($return);
	}

}


function debug($arg){
	echo "<pre>";
	print_r($arg);
	echo "</pre>";
}