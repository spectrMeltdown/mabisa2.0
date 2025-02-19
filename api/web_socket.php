<?php

require __DIR__ . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

// using a websocket using pub-sub method for real-time announcements/alerts (basically anything needed to be real-time)
class PubSubServer implements MessageComponentInterface
{
  protected $clients;
  protected $topics;

  public function __construct()
  {
    $this->clients = new \SplObjectStorage;
    $this->topics = [];
  }

  public function onOpen(ConnectionInterface $conn)
  {
    $this->clients->attach($conn);
    echo "New connection ({$conn->resourceId})\n";
  }

  public function onMessage(ConnectionInterface $from, $msg)
  {
    echo "Message received: $msg\n";
    $data = json_decode($msg, true);

    if (!isset($data['action'])) {
      $from->send(json_encode(['error' => 'Invalid message format'], JSON_PRETTY_PRINT));
      return;
    }

    switch ($data['action']) {
      case 'subscribe':
        $this->subscribe($from, $data['topic']);
        break;
      case 'publish':
        $this->publish($data['topic'], $data['message']);
        break;
      default:
        $from->send(json_encode(['error' => 'Unknown action'], JSON_PRETTY_PRINT));
        break;
    }
  }

  public function onClose(ConnectionInterface $conn)
  {
    $this->clients->detach($conn);
    foreach ($this->topics as &$subscribers) {
      $subscribers = array_filter($subscribers, fn($client) => $client !== $conn);
    }
    echo "Connection {$conn->resourceId} has disconnected\n";
  }

  public function onError(ConnectionInterface $conn, \Exception $e)
  {
    echo "An error has occurred: {$e->getMessage()}\n";
    $conn->close();
  }

  private function subscribe(ConnectionInterface $client, $topic)
  {
    if (!isset($this->topics[$topic])) {
      $this->topics[$topic] = [];
    }

    if (!in_array($client, $this->topics[$topic], true)) {
      $this->topics[$topic][] = $client;
      $client->send(json_encode(['success' => "Subscribed to $topic"], JSON_PRETTY_PRINT));
      echo "Client {$client->resourceId} subscribed to $topic\n";
    }
  }

  private function publish($topic, $message)
  {
    if (!isset($this->topics[$topic])) {
      echo "No subscribers for topic $topic\n";
      return;
    }

    echo "Publishing message to topic $topic: $message\n";
    foreach ($this->topics[$topic] as $subscriber) {
      $subscriber->sdend(json_encode(['topic' => $topic, 'message' => $message], JSON_PRETTY_PRINT));
    }
  }
}

use Ratchet\App;

$app = new App('localhost', 8080);
$app->route('/pubsub', new PubSubServer, ['*']);
$app->run();
