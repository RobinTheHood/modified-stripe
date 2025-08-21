<?php

/**
 * Language Loader for loading localized messages
 *
 * You can find informations about system classes and development at:
 * https://docs.module-loader.de
 *
 * @author  Robin Wieschendorf <mail@robinwieschendorf.de>
 * @link    https://github.com/RobinTheHood/modified-stripe/
 */

declare(strict_types=1);

namespace RobinTheHood\Stripe\Classes\Framework;

use Exception;

class LanguageLoader
{
    public const LANG_PATH = __DIR__ . '/../../lang/';

    /**
     * Load messages from language files
     */
    public static function load(string $messageFile, ?string $language = null): array
    {
        // Detect language from session or use German as default
        if (null === $language) {
            $language = $_SESSION['language'] ?? 'german';
        }

        // Normalize language codes
        $languageCode = match ($language) {
            'german', 'de' => 'de',
            'english', 'en' => 'en',
            default => 'en'
        };

        // Determine language file path
        $languageFile = self::LANG_PATH . $languageCode . '/' . $messageFile . '.php';

        // Load the language file if it exists
        if (file_exists($languageFile)) {
            return require $languageFile;
        }

        // Fallback to English if requested language doesn't exist
        $fallbackFile = self::LANG_PATH . 'en/' . $messageFile . '.php';
        if (file_exists($fallbackFile)) {
            return require $fallbackFile;
        }

        // If no language files exist, something is seriously wrong
        throw new Exception("Language file '{$messageFile}' not found in any supported language. Please check module installation.");
    }
}
