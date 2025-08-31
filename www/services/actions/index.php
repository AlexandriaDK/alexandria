<?php
chdir("../../");
require("./connect.php");
require("base.inc.php");

$postdata = file_get_contents("php://input");

$r = json_decode($postdata);

$project = 'alexandria-1489351053036';
$responseId = $r->responseId;
$session = $r->session;
$intentName = $r->queryResult->intent->displayName;
$languageCode = $r->queryResult->languageCode;

$json_raw_output = '
{
  "fulfillmentText": "This is a text response.",
  "fulfillmentMessages": [
    {
      "card": {
        "title": "card title",
        "subtitle": "card text",
        "imageUri": "https://assistant.google.com/static/images/molecule/Molecule-Formation-stop.png",
        "buttons": [
          {
            "text": "button text",
            "postback": "https://assistant.google.com/"
          }
        ]
      }
    }
  ],
  "source": "alexandria.dk",
  "payload": {
    "google": {
      "expectUserResponse": true,
      "richResponse": {
        "items": [
          {
            "simpleResponse": {
              "textToSpeech": "this is a simple response"
            }
          }
        ]
      }
    },
    "slack": {
      "text": "This is a text response for Slack."
    }
  },
  "outputContexts": [
    {
      "name": "projects/' . $project . '/agent/sessions/' . $session . '/contexts/context name",
      "lifespanCount": 5,
      "parameters": {
        "param": "param value"
      }
    }
  ],
  "followupEventInput": {
    "name": "event name",
    "languageCode": "' . $languageCode . '",
    "parameters": {
      "param": "param value"
    }
  }
}
';

header("Content-Type: application/json");
print $json_raw_output;

# Log
doquery("INSERT INTO actions_log (incoming_raw, outgoing_raw, responseid, session, intent, language, logtime) values ('" . dbesc($postdata) . "','" . dbesc($json_raw_output) . "','" . dbesc($responseId) . "','" . dbesc($session) . "','" . dbesc($intentName) . "','" . dbesc($languageCode) . "', NOW() )");
