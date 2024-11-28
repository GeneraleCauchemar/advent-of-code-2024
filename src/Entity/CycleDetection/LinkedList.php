<?php

namespace App\Entity\CycleDetection;

/**
 * @see https://medium.com/@fazlulkabir94/linked-list-in-php-b677cdb06105
 */
class LinkedList
{
    public function __construct(public ?Node $head = null, public ?int $tailData = null)
    {
    }

    public function pushToBack(int $data): void
    {
        $node = new Node($data);

        if ($this->head) {
            $currentNode = $this->head;

            while (null !== $currentNode->next) {
                $currentNode = $currentNode->next;
            }

            $currentNode->next = $node;
            $node->previousData = $currentNode->data;
        } else {
            $this->head = $node;
            $this->tailData = $data;
        }
    }

    public function print(): void
    {
        $tmp = $this->head;

        while ($tmp instanceof Node) {
            echo $tmp->data . "\n";
            $tmp = $tmp->next;
        }
    }
}
