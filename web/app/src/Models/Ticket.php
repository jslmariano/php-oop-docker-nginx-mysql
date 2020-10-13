<?php

namespace App\Josel\Models;

/**
 * This class describes a ticket model.
 */
class Ticket extends Base
{
    const STATE_SOLD     = 'SOLD';
    const STATE_CANCELED = 'CANCELED';

    protected $table = 'tips';

    /**
     * Gets the tips from ticket_id.
     *
     * @param      int  $ticket_id  The ticket identifier
     */
    public function getTips($ticket_id)
    {
        $select_query = "SELECT * FROM %s WHERE ticket_id = ?;";
        $query = sprintf(
            $select_query,
            $this->getTable()
        );

        $this->getDbManager()->runQuery(
            $query,
            array($ticket_id)
        );

        /**
         * reset collection object
         */
        $this->initCollection();

        /**
         * Convert result to collection object
         */
        $statement_result = $this->getDbManager()->getCurrentResult();
        while ($row = $statement_result->fetch_assoc()) {
            $this->addToCollection($row);
        }
        return $this->getCollection();
    }

    /**
     * Determines if tips cancelled by iterating on the collection if one found not
     * equal to cancelled then it will erturn false
     *
     * @return     bool  True if tips cancelled, False otherwise.
     */
    public function isTipsCancelled()
    {
        $was_sold = false;
        foreach ($this->getCollection()->getData() as $data) {
            if ($data->getState() !== self::STATE_SOLD) {
                $was_sold = true;
                break;
            }
        }
        return $was_sold;
    }

    /**
     * Cancel tips by ticket id
     *
     * @param      int  $ticket_id  The ticket identifier
     */
    public function cancelTips($ticket_id)
    {
        $select_query = "UPDATE %s SET state = ? WHERE ticket_id = ?;";
        $query = sprintf(
            $select_query,
            $this->getTable()
        );

        $this->getDbManager()->runQuery(
            $query,
            array(self::STATE_CANCELED, $ticket_id)
        );

        return $this->getObjects();
    }

    /**
     * Generate a ticket id, from php UID
     *
     * @return     int  the ticket id
     */
    public function generateTicketId()
    {
        return (int)str_pad(rand(0, pow(10, 10)-1), 10, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a tip id, from php UID
     *
     * @return     int  the tip id
     */
    public function generateTipId()
    {
        return (int)str_pad(rand(0, pow(10, 10)-1), 10, '0', STR_PAD_LEFT);
    }
}
