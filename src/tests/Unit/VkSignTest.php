<?php

namespace Tests\Unit;

use App\Services\VkSign;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class VkSignTest extends TestCase
{
    private const SECRET = 'test-secret-key';

    #[Test]
    public function verifies_valid_signature(): void
    {
        $signer = new VkSign(self::SECRET);
        $sign = $signer->sign(['vk_user_id' => '123', 'vk_app_id' => '100']);

        $result = $signer->verify([
            'vk_user_id' => '123',
            'vk_app_id' => '100',
            'vk_sign' => $sign,
        ]);

        $this->assertNotNull($result);
        $this->assertSame('123', $result['vk_user_id']);
    }

    #[Test]
    public function rejects_tampered_param(): void
    {
        $signer = new VkSign(self::SECRET);
        $sign = $signer->sign(['vk_user_id' => '123']);

        $result = $signer->verify([
            'vk_user_id' => '999', // подменили после подписи
            'vk_sign' => $sign,
        ]);

        $this->assertNull($result);
    }

    #[Test]
    public function rejects_wrong_secret(): void
    {
        $good = new VkSign(self::SECRET);
        $bad = new VkSign('different-secret');

        $sign = $bad->sign(['vk_user_id' => '123']);

        $this->assertNull($good->verify(['vk_user_id' => '123', 'vk_sign' => $sign]));
    }

    #[Test]
    public function rejects_missing_sign(): void
    {
        $signer = new VkSign(self::SECRET);

        $this->assertNull($signer->verify(['vk_user_id' => '123']));
    }

    #[Test]
    public function rejects_missing_vk_user_id(): void
    {
        $signer = new VkSign(self::SECRET);
        $sign = $signer->sign(['vk_app_id' => '100']);

        $this->assertNull($signer->verify(['vk_app_id' => '100', 'vk_sign' => $sign]));
    }

    #[Test]
    public function ignores_non_vk_params_in_signing(): void
    {
        $signer = new VkSign(self::SECRET);
        $sign = $signer->sign(['vk_user_id' => '123']);

        // POST-параметры фрейма (без vk_) не влияют на подпись
        $result = $signer->verify([
            'vk_user_id' => '123',
            'vk_sign' => $sign,
            'utm_source' => 'google',
        ]);

        $this->assertNotNull($result);
    }

    #[Test]
    public function fails_with_empty_secret(): void
    {
        $signer = new VkSign('');

        $this->assertNull($signer->verify(['vk_user_id' => '123', 'vk_sign' => 'x']));
    }
}
