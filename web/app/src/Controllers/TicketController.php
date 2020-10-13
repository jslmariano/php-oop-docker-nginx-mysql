<?php

namespace App\Josel\Controllers;

use App\Josel\Core\Request;
use App\Josel\Core\VarienObject;
use App\Josel\Core\DBManager;
use App\Josel\Helpers\Common as CommonHelper;
use App\Josel\Helpers\Logger as LoggerHelper;
use App\Josel\Models\Factories\Model;

use Throwable;

/**
 */
class TicketController
{
    /**
     * Contains the response
     */
    public $response = array(
        'success' => true,
        'message' => '',
    );

    /**
     * Cancel a ticket
     *
     * @return     Array result
     */
    public function cancel(Request $request)
    {
        /**
         * Set filter || sanitize rules
         */
        $request->getValidateEntries()->setData(array(
            'ticket_id' => ['VALIDATE_INT'],
        ));

        $get = $request->getInputGet();
        $get->setData($request->validate($get->getData()));
        $ticket_id = $get->getData('ticket_id');

        if (empty($ticket_id)) {
            $error = sprintf("ERROR: bad request %s", $get->getOrigData('ticket_id'));
            $this->response['success'] = false;
            $this->response['message'] = $error;
            LoggerHelper::errorLog($error);
            http_response_code(400);
            return $this->response;
        }

        $ticket_model = Model::getModel('App\Josel\Models\Ticket');

        try {
            $tips = $ticket_model->getTips($ticket_id);
        } catch (Throwable $exception) {
            http_response_code(500);
            LoggerHelper::errorLog($exception->getMessage());
            $this->response['success'] = false;
            $this->response['message'] = "There's something wrong, please try again.";
            return $this->response;
        }

        if (!$tips->hasData()) {
            $error = sprintf("ERROR: ticket %s not found", $ticket_id);
            $this->response['success'] = false;
            $this->response['message'] = $error;
            LoggerHelper::errorLog($error);
            http_response_code(404);
            return $this->response;
        }

        if ($ticket_model->isTipsCancelled()) {
            $error = sprintf("ERROR: ticket %s not cancelable", $ticket_id);
            $this->response['success'] = false;
            $this->response['message'] = $error;
            LoggerHelper::errorLog($error);
            http_response_code(403);
            return $this->response;
        }

        $ticket_model->cancelTips($ticket_id);

        http_response_code(200);
        $this->response['message'] = "Succesfully canceled tickets";
        return $this->response;
    }

    /**
     * Confirm a ticket
     *
     * @return     Array result
     */
    public function confirm(Request $request)
    {
        /**
         * Set filter || sanitize rules
         */
        $request->getValidateEntries()->setData(array(
            'outcome'             => ['SANITIZE_STRING'],
            'print_dt'            => ['SANITIZE_STRING'],
            'state'               => ['SANITIZE_STRING'],
            'bet'                 => ['SANITIZE_FLOAT'],
            'game_round_id'       => ['SANITIZE_STRING'],
            'operator_session_id' => ['VALIDATE_INT', 'SANITIZE_INT'],
        ));

        $input_entries = array();
        $get           = $request->getInputGet();
        $posts         = $request->getInputPost();
        $tickets       = $posts->getData('ticket');
        $get->setData($request->validate($get->getData()));
        $get->setData($request->sanitize($get->getData()));

        $operator_session_id = $get->getData('operator_session_id');

        if (empty($operator_session_id) || empty($tickets)) {
            http_response_code(400);
            $error = 'Operator Session ID and or Tickets is not valid';
            LoggerHelper::errorLog($error);
            $this->response['success'] = false;
            $this->response['message'] = $error;
            return $this->response;
        }

        /**
         * Begin the transaction
         */
        DBManager::beginTransaction();

        $ticket_model = Model::getModel('App\Josel\Models\Ticket');
        $ticket_id = $ticket_model->generateTicketId();
        $this->response['message']  = "Succesfully confirmed tickets";
        $this->response['ticketID'] = $ticket_id;

        /**
         * Save the tcikets
         */
        $tickets = json_decode($tickets, true);
        foreach ($tickets as $tip) {
            $tip = new VarienObject(CommonHelper::convertKeysToSnakeCase($tip));

            $input_entries['outcome']       = $tip->getData('outcome');
            $input_entries['bet']           = $tip->getData('bet');
            $input_entries['game_round_id'] = $tip->getData('game_round_id');
            $sanitized                      = $request->sanitize($input_entries, true);
            $tip_id                         = $ticket_model->generateTipId();

            $ticket_model->getObjects()->setData($sanitized);
            $ticket_model->getObjects()->setPrintDt(date('Y-m-d'));
            $ticket_model->getObjects()->setState($ticket_model::STATE_SOLD);
            $ticket_model->getObjects()->setTipId($tip_id);
            $ticket_model->getObjects()->setTicketId($ticket_id);
            $ticket_model->getObjects()->setOperatorSessionID($operator_session_id);

            try {
                $ticket_model->save();
            } catch (Throwable $exception) {
                http_response_code(500);
                LoggerHelper::errorLog($exception->getMessage());
                $this->response['success']    = false;
                $this->response['message']    = "Failed confirming tickets";
                $this->response['ticketID']   = '';
                /**
                 * Rollback changes
                 */
                DBManager::rollbackTransaction();
                break;
            }
        }

        if ($this->response['success']) {
            /**
             * Apply changes
             */
            http_response_code(200);
            DBManager::commitTransaction();
        }


        return $this->response;
    }
}
