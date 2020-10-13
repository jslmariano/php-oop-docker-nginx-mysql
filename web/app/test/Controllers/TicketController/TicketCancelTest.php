<?php

namespace AppTest\Josel\Controllers\TicketController;

use App\Josel\Controllers\TicketController;
use App\Josel\Core\Request;
use App\Josel\Core\DBManager;
use App\Josel\Models\Factories\Model;
use App\Josel\Models\Ticket;
use PHPUnit\Framework\TestCase;


/**
 * This class describes a ticket controller test for cancel logic.
 */
class TicketCancelTest extends TestCase
{
    /**
     * @var App\Josel\Controllers\TicketController
     */
    public $controler;

    /**
     * @var App\Josel\Core\Request
     */
    public $request;

    /**
     * @var App\Josel\Models\Ticket
     */
    public $model;

    const CANCEL_URL = "tickets/cancel";

    public function setUp(): void
    {
        parent::setUp();
        DBManager::reConnect();
        DBManager::beginTransaction();
        $this->model     = new Ticket();
        $this->controler = new TicketController();
    }

    public function tearDown(): void
    {
        DBManager::rollbackTransaction();
        $this->controler = null;
        parent::tearDown();
    }

    /**
     * Test cancel valid ticket id
     */
    public function test_cancel_ticket_valid()
    {
        $ticket_id = $this->model->generateTicketId();
        $ticket_model = Model::getModel('App\Josel\Models\Ticket');
        $ticket_model->getObjects()->setData(array(
            'ticket_id' => $ticket_id
        ));
        $ticket_model->save();

        $_SERVER["REQUEST_URI"] = self::CANCEL_URL;
        $_GET['ticket_id']      = $ticket_id;
        $this->request = new Request();
        $response = $this->controler->cancel($this->request);
        $this->assertEquals(200, http_response_code());
        $this->assertEquals($response['success'], true);
        $this->assertEquals($response['message'], "Succesfully canceled tickets");
    }

    /**
     * Test cancel canceled ticket id
     */
    public function test_cancel_canceled_ticket()
    {
        $ticket_id = $this->model->generateTicketId();
        $ticket_model = Model::getModel('App\Josel\Models\Ticket');
        $ticket_model->getObjects()->setData(array(
            'ticket_id' => $ticket_id,
            'state' => $ticket_model::STATE_CANCELED,
        ));
        $ticket_model->save();

        $_SERVER["REQUEST_URI"] = self::CANCEL_URL;
        $_GET['ticket_id']      = $ticket_id;
        $this->request = new Request();
        $response = $this->controler->cancel($this->request);
        $this->assertEquals(403, http_response_code());
        $this->assertEquals($response['success'], false);
        $this->assertEquals($response['message'], "ERROR: ticket $ticket_id not cancelable");
    }

    /**
     * Test cancel invalid ticket id
     */
    public function test_cancel_invalid_ticket()
    {
        $_SERVER["REQUEST_URI"] = self::CANCEL_URL;
        $_GET['ticket_id']      = 'xxx';
        $this->request          = new Request();
        $response               = $this->controler->cancel($this->request);
        $this->assertEquals(400, http_response_code());
        $this->assertEquals($response['success'], false);
        $this->assertEquals($response['message'], "ERROR: bad request xxx");
    }

    /**
     * Test cancel invalid ticket id
     */
    public function test_cancel_ticket_not_found()
    {
        $ticket_id = $this->model->generateTicketId();
        $_SERVER["REQUEST_URI"] = self::CANCEL_URL;
        $_GET['ticket_id']      = $ticket_id;
        $this->request = new Request();
        $response = $this->controler->cancel($this->request);
        $this->assertEquals(404, http_response_code());
        $this->assertEquals($response['success'], false);
        $this->assertEquals($response['message'], "ERROR: ticket $ticket_id not found");
    }


    /**
     * Test cancel valid ticket id failure
     */
    public function test_cancel_valid_ticket_failure()
    {
        $ticket_id = $this->model->generateTicketId();
        $ticket_model = Model::getModel('App\Josel\Models\Ticket');
        $ticket_model->getObjects()->setData(array(
            'ticket_id' => $ticket_id,
        ));
        $ticket_model->save();

        $_SERVER["REQUEST_URI"] = self::CANCEL_URL;
        $_GET['ticket_id']      = $ticket_id;
        $this->request = new Request();
        /**
         * Simulate connection problem
         */
        DBManager::forceClose();
        $response = $this->controler->cancel($this->request);
        $this->assertEquals(500, http_response_code());
        $this->assertEquals($response['success'], false);
        $this->assertEquals($response['message'], "There's something wrong, please try again.");
    }

}
