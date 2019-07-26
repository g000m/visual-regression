<?php


class Backstop_Test_Case {

	private $config;


	public function __construct($config = '{}') {
		$this->config = $config;
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

	protected function do_test( $testId, $command ) {
		$url = "http://localhost:3000/project/$testId/$command";

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