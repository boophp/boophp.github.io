<?php
$rk = new RdKafka\Producer();
$rk->setLogLevel(LOG_DEBUG);
$rk->addBrokers("127.0.0.1");
$topic = $rk->newTopic("test");
for ($i = 0; $i < 10; $i++) {
    $topic->produce(0, 0, "Message $i");
}
echo "test";