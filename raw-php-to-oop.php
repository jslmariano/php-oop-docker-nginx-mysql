    <?php

// assumed endpoint - https://myapi.com/index.php?action=SOMETHING

header('Content-type: application/json');

require_once '../mysql-conf.php';
require_once '../functions.php';

// This can also be upgraded to mysql_connect ( Procedural style ), that can check connection before executing codes below
$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// May throw undefined index 'action' if the query in url goes missing
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);


switch ($action) {
      case 'cancel-ticket':
          // May throw undefined index 'ticketID' if the query in url goes missing
          $ticketID = filter_input(INPUT_GET, 'ticketID', FILTER_VALIDATE_INT);

          if ($ticketID === null) {
              error_log("ERROR: bad request $ticketID");
              http_response_code(400);
              exit;
          }

          $ticket = getTicket($db, $ticketID);
          if ($ticket === false) {
              error_log("ERROR: ticket $ticketID not found");
              http_response_code(404);
              exit;
          }
          if ($ticket->state !== 'SOLD') {
              error_log("ERROR: ticket $ticketID not cancelable");
              http_response_code(403);
              exit;
          }
          $json['result'] = cancelTicket($db, $ticketID);
          break;
      case 'confirm-ticket':
          // May throw undefined index 'operatorSessionID' if the query in url goes missing
          $operatorSessionID = $_GET['operatorSessionID'];
          // May throw undefined index 'ticket' if the data goes missing on post body
          $ticket = $_POST['ticket'];

          if ($ticket === null || $operatorSessionID === null) {
              http_response_code(400);
              exit;
          }

          $ticket = json_decode($ticket);
          $ticketID = generateTicketID();
          // BEGIN TRANSACTION
          foreach($ticket as $tip) {
              $tipID = generateTipID();
              $sql = "INSERT INTO tips (tipID, printDT, outcome, bet, state, gameRoundID, ticketID, operatorSessionID) ";
              // May throw undefined variable $now
            // security vairable not sanitized
              $sql .= "VALUES ('$tipID', '$now', '$tip->outcome', '$tip->bet', 'SOLD', '$tip->gameRoundID', '$ticketID', '$operatorSessionID')";
              //  may throw sql error if has invalid data
              $query = $db->query($sql);
              if (!$query) {
                       // ROLLBACK TRANSACTION
                  // security concern of exposing database informations, EG: tables, columns, etc,,
                  error_log("ERROR while storing tip: $db->error - $sql");
                  http_response_code(500);
                  exit;
              }
          }
                // COMMIT TRANSACTION

          $json['ticketID'] = $ticketID;

          break;
  }

echo json_encode($json);
