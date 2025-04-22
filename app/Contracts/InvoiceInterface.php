<?php

namespace App\Contracts;

interface InvoiceInterface extends BaseInterface
{
    public function generateMonthlyInvoices(int $year, int $month, int $userId);
}
