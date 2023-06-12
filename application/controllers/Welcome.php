<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once 'vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Welcome extends CI_Controller {
	public $loader;
	public $CI;
	public $db;
	public $ConsumerModel;
	function __construct(){
		parent::__construct();
		//Loading Helper Class
		$this->load->model("ConsumerModel");
	}

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/userguide3/general/urls.html
	 */
	public function index()
	{
		$this->load->view('welcome_message');
	}

	public function call_publisher()
	{
		/*$i=1;
		while ($i<=15000) {
			$this->publisher($i);
			$this->db->query("INSERT INTO `rabbitmq_php_test`.`test_table` (`id`, `data`) VALUES (NULL , '{$i} insertion');");

			$i++;
		}*/
		/*$file = fopen("test.txt","w");
		fwrite($file,"call_publisher method called."."\n");
		fclose($file);*/
		$this->db->query("INSERT INTO `rabbitmq_php_test`.`test_table` (`id`, `data`) VALUES (NULL , 'insertion');");

		echo "called";
		exit();
	}

	public function publisher()
	{
		//echo "<pre>"; print_r("publisher"); echo "</pre>";exit();
		$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
		$channel = $connection->channel();

		$channel->queue_declare('codeIgniterMQ', false, false, false, false);

		$msg = new AMQPMessage(' number Hello World again from publisher!');
		$channel->basic_publish($msg, '', 'codeIgniterMQ');



		$channel->close();
		$connection->close();
	}

	public function consumer()
	{
		ini_set('max_execution_time', 0);
		$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
		$channel = $connection->channel();

		$channel->queue_declare('codeIgniterMQ', false, false, false, false);

		//echo " [*] Waiting for messages. To exit press CTRL+C\n";

		$file = fopen("test.txt","w");
		$self = $this;
		$callback = function ($msg) use ($self, $file) {

			fwrite($file,$msg->body."\n");

			//$welcome = new Welcome();
			$self->callBackFunction($msg);
			/*echo ' [x] Received ', $msg->body, "\n";

			$msg = new AMQPMessage('Hello World!');
			$channel->basic_publish($msg, '', 'hello');*/
		};
		$channel->basic_consume('codeIgniterMQ', '', false, true, false, false, $callback);

		while ($channel->is_open()) {
			$channel->wait();
		}
		fclose($file);
		$channel->close();
		$connection->close();
	}

	public function callBackFunction($msg = null)
	{
		ini_set('max_execution_time', 0);
		/*$counter = 0;
		while ($counter < 5000000000) {
			$counter++;
		}*/
		//echo "<pre>"; print_r($i); echo "</pre>";exit();
		//echo "<pre>"; print_r($this->db); echo "</pre>";exit();

		$this->db->query("INSERT INTO `rabbitmq_php_test`.`test_table` (`id`, `data`) VALUES (NULL , '{$msg->body}');");
	}
}
