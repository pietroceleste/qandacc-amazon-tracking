<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Qandacc\AmazonTracking\Client as AmazonTrackingClient;

final class AmazonTrackingTest extends TestCase
{
    public function testAmazonTrackingClientValidateEmptyCode(): void
    {
        $this->expectException(Exception::class);
        $Client = new AmazonTrackingClient();
        $Client->getTrackingHistory('');
    }

    public function testAmazonTrackingClient(): void
    {        
        $Client = new AmazonTrackingClient();        
        $this->assertIsArray($Client->getTrackingHistory('IT2235168195'));
    }
}
