<?php

namespace AppTest\Josel\Controllers\TicketController;

use App\Josel\Controllers\TicketController;
use App\Josel\Core\Request;
use App\Josel\Core\DBManager;
use App\Josel\Models\Factories\Model;
use PHPUnit\Framework\TestCase;


/**
 * This class describes a ticket controller test for cancel logic.
 */
class TicketConfirmTest extends TestCase
{
    /**
     * @var App\Josel\Controllers\TicketController
     */
    public $controler;

    /**
     * @var App\Josel\Core\Request
     */
    public $request;

    const CONFIRM_URL = "tickets/confirm";

    public function setUp(): void
    {
        parent::setUp();
        DBManager::reConnect();
        DBManager::beginTransaction();
        $this->controler = new TicketController();
    }

    public function tearDown(): void
    {
        DBManager::rollbackTransaction();
        $this->controler = null;
        parent::tearDown();
    }

    /**
     * Test cofirm valid tips
     */
    public function test_confirm_valid_tips()
    {
        $_SERVER["REQUEST_URI"]    = self::CONFIRM_URL;
        $_GET['operatorSessionID'] = '456789';
        $tips = array(
            [
                "outcome"     => "Number 6",
                "bet"         => 500,
                "gameRoundID" => 3
            ],
            [
                "outcome"     => "Number 6",
                "bet"         => 500,
                "gameRoundID" => 3
            ],
        );
        $_POST['ticket'] = json_encode($tips);

        $this->request = new Request();
        $response      = $this->controler->confirm($this->request);
        $this->assertEquals(200, http_response_code());
        $this->assertEquals($response['success'], true);
        $this->assertEquals($response['message'], "Succesfully confirmed tickets");
        $ticket_model = Model::getModel('App\Josel\Models\Ticket');
        $tip = $ticket_model->getTips($response['ticketID']);
        $this->assertEquals(2, count($tip->toArray()));
    }


    /**
     * Test cofirm invalid tips
     */
    public function test_confirm_invalid_tips()
    {
        $_SERVER["REQUEST_URI"]    = self::CONFIRM_URL;
        $_GET['operatorSessionID'] = '456789';
        $tips = array(
            [
                "outcome"     => "Number 6",
                "bet"         => 500,
                "gameRoundID" => 3
            ],
            [
                "outcome"     => "Number 6",
                "bet"         => "500ss", // Invalid bet
                "gameRoundID" => 3
            ],
        );
        $_POST['ticket'] = json_encode($tips);

        $this->request = new Request();
        $response      = $this->controler->confirm($this->request);
        $this->assertEquals(500, http_response_code());
        $this->assertEquals($response['success'], false);
        $this->assertEquals($response['message'], "Failed confirming tickets");
    }

    /**
     * Test confirm invalid operator id
     */
    public function test_confirm_invalid_operator_id()
    {
        $_SERVER["REQUEST_URI"]      = self::CONFIRM_URL;
        $_GET['operator_session_id'] = 'xxx';

        $this->request = new Request();
        $response      = $this->controler->confirm($this->request);
        $this->assertEquals(400, http_response_code());
        $this->assertEquals($response['success'], false);
        $this->assertEquals(
            $response['message'], "Operator Session ID and or Tickets is not valid"
        );
    }

    /**
     * Test confirm invalid tickets
     */
    public function test_confirm_invalid_tickets()
    {
        $_SERVER["REQUEST_URI"]      = self::CONFIRM_URL;
        $_GET['operator_session_id'] = '456789';
        $_POST['ticket']             = '';

        $this->request = new Request();
        $response      = $this->controler->confirm($this->request);
        $this->assertEquals(400, http_response_code());
        $this->assertEquals($response['success'], false);
        $this->assertEquals(
            $response['message'], "Operator Session ID and or Tickets is not valid"
        );
    }

}
