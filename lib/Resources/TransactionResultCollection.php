<?php

/**
 * This file is part of the Kreatif\Project package.
 *
 * @author Kreatif GmbH
 * @author a.platter@kreatif.it
 * Date: 26/03/24
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AssioPay\Resources;


class TransactionResultCollection implements \Countable
{
    private $results = [];

    public function addResult(TransactionResult $result): void
    {
        // add results to the collection by workerId
        $this->results[$result->getWorkerId()][$result->getMonthYear()][] = $result;
    }

    public function getMonthYearResults(): array
    {
        $results = [];
        foreach ($this->results as $workerId => $months) {
            foreach ($months as $monthYear => $collection) {
                $transactionResult = new TransactionResult($workerId);
                foreach ($collection as $item) {
                    /** @var $item TransactionResult */
                    $transactionResult->incrementAmount($item->getAmount());
                }
                $transactionResult->setDatetime($item->getDatetime()->setTime(0, 0, 0));
                $transactionResult->setWorkerFiscalCode($item->getWorkerFiscalCode());
                $results[$workerId][$monthYear] = $transactionResult;
            }
        }
        return $results;
    }

    public function getResults(): array
    {
        return $this->results;
    }

    public function count(): int
    {
        return count($this->results);
    }
}
