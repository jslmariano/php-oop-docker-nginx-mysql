<?php

namespace App\Test\Models\Ticket;

use PHPUnit\Framework\TestCase;
use App\Josel\Core\DBManager;
use App\Josel\Models\Factories\Model;
use App\Josel\Models\Ticket;

/**
 * This class describes a ticket function test.
 */
class TicketFunctionTest extends TestCase
{
    /**
     * @var App\Josel\Models\Ticket
     */
    public $model;

    public function setUp(): void
    {
        parent::setUp();
        DBManager::reConnect();
        DBManager::beginTransaction();
        $this->model = new Ticket();
    }

    public function tearDown(): void
    {
        DBManager::rollbackTransaction();
        $this->model = null;
        parent::tearDown();
    }

    /**
     * Test to load model
     */
    public function test_load_model()
    {
        $ticket_model = Model::getModel('App\Josel\Models\Ticket');
        $this->assertInstanceOf(Ticket::class, $ticket_model);
        $ticket_model = Model::getModel('App\Josel\Models\TicketInvalidName');
        $this->assertEquals(null, $ticket_model);
        $ticket_model = Model::getModel('');
        $this->assertEquals(null, $ticket_model);
    }

    /**
     * test to get tips
     */
    public function test_get_tips()
    {
        $ticket_id = $this->model->generateTicketId();
        for ($i = 0; $i < 5; $i++) {
            $this->model->getObjects()->setData(array(
                'ticket_id' => $ticket_id
            ));
            $this->model->save();
        }
        $tips = $this->model->getTips($ticket_id);
        $this->assertTrue($tips->hasData());
        $this->assertEquals(5, count($tips->toArray()));
    }

    /**
     * Test to check tips cancelled
     */
    public function test_check_tips_is_cancelled()
    {
        $ticket_id_cancelled = $this->model->generateTicketId();
        for ($i = 0; $i < 5; $i++) {
            $this->model->getObjects()->setData(array(
                'ticket_id' => $ticket_id_cancelled,
                'state' => Ticket::STATE_CANCELED,
            ));
            $this->model->save();
        }
        $ticket_id_sold = $this->model->generateTicketId();
        for ($i = 0; $i < 5; $i++) {
            $this->model->getObjects()->setData(array(
                'ticket_id' => $ticket_id_sold,
                'state' => Ticket::STATE_SOLD,
            ));
            $this->model->save();
        }
        $tips = $this->model->getTips($ticket_id_cancelled);
        $this->assertTrue($this->model->isTipsCancelled());
        $tips = $this->model->getTips($ticket_id_sold);
        $this->assertTrue(!$this->model->isTipsCancelled());
    }

    /**
     * Test to cancel tips
     */
    public function test_cancel_tips()
    {
        $ticket_id_sold = $this->model->generateTicketId();
        for ($i = 0; $i < 5; $i++) {
            $this->model->getObjects()->setData(array(
                'ticket_id' => $ticket_id_sold,
                'state' => Ticket::STATE_SOLD,
            ));
            $this->model->save();
        }
        $this->model->cancelTips($ticket_id_sold);
        $tips = $this->model->getTips($ticket_id_sold);
        foreach ($tips->getData() as $tip) {
            $this->assertEquals(Ticket::STATE_CANCELED, $tip->getState());
        }
    }

}

