<?php

namespace App\Helpers;

class TicketHelper
{
    /**
     * Generate QR Code unik untuk tiket
     */
    public static function generateQrCode(int $ticketId, string $code): string
    {
        return hash('sha256', $ticketId . $code . now()->timestamp);
    }
}
