<?php

namespace App\Services;

use App\Models\User;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Collection;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorAuthenticationService
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Enable two-factor authentication for the user.
     */
    public function enable(User $user): array
    {
        $secret = $this->google2fa->generateSecretKey();

        $user->forceFill([
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode($user->generateRecoveryCodes()->toArray())),
        ])->save();

        return [
            'secret' => $secret,
            'qr_code' => $this->generateQrCode($user, $secret),
            'recovery_codes' => $user->recoveryCodes(),
        ];
    }

    /**
     * Confirm two-factor authentication for the user.
     */
    public function confirm(User $user, string $code): bool
    {
        if (! $this->verify($user, $code)) {
            return false;
        }

        $user->forceFill([
            'two_factor_confirmed_at' => now(),
        ])->save();

        return true;
    }

    /**
     * Disable two-factor authentication for the user.
     */
    public function disable(User $user): void
    {
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();
    }

    /**
     * Verify the given code.
     */
    public function verify(User $user, string $code): bool
    {
        if (! $user->two_factor_secret) {
            return false;
        }

        $secret = decrypt($user->two_factor_secret);

        return $this->google2fa->verifyKey($secret, $code);
    }

    /**
     * Verify the given recovery code.
     */
    public function verifyRecoveryCode(User $user, string $code): bool
    {
        $recoveryCodes = $user->recoveryCodes();

        if (! in_array($code, $recoveryCodes)) {
            return false;
        }

        $user->replaceRecoveryCode($code);

        return true;
    }

    /**
     * Generate new recovery codes for the user.
     */
    public function regenerateRecoveryCodes(User $user): Collection
    {
        $codes = $user->generateRecoveryCodes();

        $user->forceFill([
            'two_factor_recovery_codes' => encrypt(json_encode($codes->toArray())),
        ])->save();

        return $codes;
    }

    /**
     * Generate the QR code SVG.
     */
    protected function generateQrCode(User $user, string $secret): string
    {
        $svg = (new Writer(
            new ImageRenderer(
                new RendererStyle(200, 0, null, null, Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(45, 55, 72))),
                new SvgImageBackEnd()
            )
        ))->writeString($this->getQrCodeUrl($user, $secret));

        return trim(substr($svg, strpos($svg, "\n") + 1));
    }

    /**
     * Get the QR code URL.
     */
    protected function getQrCodeUrl(User $user, string $secret): string
    {
        return $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );
    }
}
