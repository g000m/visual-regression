<?php


class Backstop_Test_CaseTest extends WP_UnitTestCase {

	protected $class_instance, $default_config;

	public function setUp() {
		parent::setUp();

		$string = file_get_contents( __DIR__ . "/backstopConfig.json" );

		$this->default_config = json_decode( $string, true );

		$this->class_instance = new Backstop_Test_Case( $this->default_config );
	}



	public function test_get_config_matches_default_config() {

		$config = $this->class_instance->get_config();

		$this->assertEquals( $this->default_config, $config );
	}

	public function test_list_scenarios_returns_urls_wrapped_in_divs() {

		$config = $this->class_instance->get_config();
		$output = '';

		foreach ( $config['scenarios'] as $scenario ) {
			$output .= "<div>" .$scenario['url']. "</div>";
		}

		$result = $this->class_instance->list_scenarios();


		$this->assertEquals( $output, $result );
	}


}
