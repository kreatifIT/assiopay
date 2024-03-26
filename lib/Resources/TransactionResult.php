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


use Cassandra\Date;

class TransactionResult implements \JsonSerializable
{
    private $workerId;
    private $workerFiscalCode;
    private $amount = 0;
    private $datetime;
    private $additionalData = [];


    public function __construct(int $workerId)
    {
        $this->workerId = $workerId;
    }

    /**
     * @return string
     */
    public function getMonthYear(): string
    {
        return $this->datetime->format('Ym');
    }

    /**
     * @param mixed $data
     */
    public function setData(array $data): void
    {
        $this->incrementAmount($data['amount']);
        unset($data['amount']);
        $this->setDatetime(\DateTime::createFromFormat('d/m/Y H:i:s', $data['data']));
        unset($data['data']);
        $this->additionalData = $data;
    }

    public function getData(): array
    {
        return array_merge($this->additionalData, [
            'worker_id' => $this->workerId,
            'fiscal_code' => $this->workerFiscalCode,
            'month_year' => $this->getMonthYear(),
            'amount' => $this->amount,
            'datetime' => $this->datetime->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * @return mixed
     */
    public function getWorkerId()
    {
        return $this->workerId;
    }

    /**
     * @param mixed $workerFiscalCode
     */
    public function setWorkerFiscalCode($workerFiscalCode): void
    {
        $this->workerFiscalCode = $workerFiscalCode;
    }

    public function incrementAmount(float $amount): void
    {
        $this->amount += $amount;
    }

    /**
     * @param mixed $datetime
     */
    public function setDatetime(\DateTime $datetime): void
    {
        $this->datetime = $datetime;
    }

    public function getAmount(): float
    {
        return (float)$this->amount;
    }

    /**
     * @return mixed
     */
    public function getDatetime(): \DateTime
    {
        return $this->datetime;
    }

    public function jsonSerialize()
    {
        $data = $this->getData();
        $data['amount'] = \rex_formatter::number($data['amount']);
        return $data;
    }

    /**
     * @return mixed
     */
    public function getWorkerFiscalCode()
    {
        return $this->workerFiscalCode;
    }
}
