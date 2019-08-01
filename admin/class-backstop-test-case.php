<?php


class Backstop_Test_Case {

	/**
	 * @var string
	 */
	private $config;

	/**
	 * @var unique ID of backstop test
	 */
	private $test_id;

	public function __construct($config = '{}', $test_id = 'default_id') {
		$this->config = $config;
		$this->test_id = $test_id;
	}

	public function get_config() {
		return $this->config;
	}

	public function list_scenarios() {
		$output = '';
		foreach ( $this->config->scenarios as $scenario ) {
			$output .= "<div>". $scenario->url . "</div>";
		}

		return $output;
	}


	/**
	 * @param $testId
	 * @param $command
	 *
	 * @return mixed
	 */
	protected function do_test( $testId, $command ) {
		$host = "http://localhost:3000";
		$url  = "{$host}/project/$testId/$command";

		$config_to_post         = new stdClass();
		$config_to_post->config = $this->config;

		return wp_remote_post( $url, array(
				'headers'     => array( 'Content-Type' => 'application/json; charset=utf-8' ),
				'body'        => json_encode( $config_to_post ),
				'method'      => 'POST',
				'data_format' => 'body'
			)
		);
	}

	/**
	 * @param $command
	 *
	 * @return string
	 */
	public function handle_command( $command ) {
		switch ( $command ) {
			case 'reference':
				$response = $this->do_test( 'test_F', 'reference' );
				break;

			case 'test':
				$response = $this->do_test( 'test_F', 'test' );
				break;

			default:
				$response = "invalid command";
				break;
		}

		return $response;
	}
}
