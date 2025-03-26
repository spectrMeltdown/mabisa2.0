<?php
namespace Ratchet\Session\Storage;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Ratchet\Session\Storage\Proxy\VirtualProxy;
use Ratchet\Session\Serialize\HandlerInterface;

class VirtualSessionStorage extends NativeSessionStorage {
    /**
     * @var \Ratchet\Session\Serialize\HandlerInterface
     */
    protected $_serializer;

    /**
     * @param \SessionHandlerInterface                    $handler
     * @param string                                      $sessionId The ID of the session to retrieve
     * @param \Ratchet\Session\Serialize\HandlerInterface $serializer
     */
    public function __construct(\SessionHandlerInterface $handler, $sessionId, HandlerInterface $serializer) {
        $this->setSaveHandler($handler);
        $this->saveHandler->setId($sessionId);
        $this->_serializer = $serializer;
        $this->setMetadataBag(null);
    }

    /**
     * {@inheritdoc}
     */
    public function start() {
        if ($this->started && !$this->closed) {
            return true;
        }





        $this->saveHandler->open(session_save_path(), session_name());

        $rawData     = $this->saveHandler->read($this->saveHandler->getId());
        $sessionData = $this->_serializer->unserialize($rawData);

        $this->loadSession($sessionData);

        if (!$this->saveHandler->isWrapper() && !$this->saveHandler->isSessionHandlerInterface()) {
            $this->saveHandler->setActive(false);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function regenerate($destroy = false, $lifetime = null) {

    }

    /**
     * {@inheritdoc}
     */
    public function save() {





        if (!$this->saveHandler->isWrapper() && !$this->getSaveHandler()->isSessionHandlerInterface()) {
            $this->saveHandler->setActive(false);
        }

        $this->closed = true;
    }

    /**
     * {@inheritdoc}
     */
    public function setSaveHandler($saveHandler = null) {
        if (!($saveHandler instanceof \SessionHandlerInterface)) {
            throw new \InvalidArgumentException('Handler must be instance of SessionHandlerInterface');
        }

        if (!($saveHandler instanceof VirtualProxy)) {
            $saveHandler = new VirtualProxy($saveHandler);
        }

        $this->saveHandler = $saveHandler;
    }
}
