<?php

declare(strict_types=1);

namespace BbApp\SmartBanner;

/**
 * Generates smart banner meta tags for mobile app promotion.
 */
abstract class SmartBanner
{
    protected $deep_link_requires_base_url = false;
	protected $options;

	/**
	 * Generates smart banner HTML meta tags from the given implementations.
	 *
	 * @param array<SmartBanner> $implementations
	 * @param string|null $deep_link_path
	 * @param string|null $permalink
	 * @return string
	 */
	public static function generate(
		array $implementations,
		?string $deep_link_path = null,
		?string $permalink = null
	): string {
		$rows = [];

		foreach ($implementations as $implementation) {
			$path = null;

			if (
                !empty($deep_link_path) &&
                (!$implementation->deep_link_requires_base_url || !empty($permalink))
            ) {
				$path = $implementation->deep_link($deep_link_path, $permalink);
			}

			$rows[] = sprintf(
				'<meta name="%s" content="%s" />',
				$implementation->name(),
				static::escape_attribute($implementation->content())
			);

			if (!empty($path)) {
				$rows[] = sprintf(
					'<link rel="alternate" href="%s" />',
					static::escape_attribute($path)
				);
			}
		}

		return implode(" ", $rows);
	}

	/**
	 * Constructs a smart banner with the given options.
	 *
	 * @param SmartBannerOptions $options
	 */
	protected function __construct(SmartBannerOptions $options)
	{
		$this->options = $options;
	}

	/**
	 * Escapes a string for use as an HTML attribute value.
	 *
	 * @param string $value
	 * @return string
	 */
	protected static function escape_attribute(string $value): string
	{
		return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
	}

	/**
	 * Returns the meta tag name for this smart banner.
	 *
	 * @return string
	 */
	abstract function name(): string;

	/**
	 * Returns the meta tag content for this smart banner.
	 *
	 * @return string
	 */
	abstract function content(): string;

	/**
	 * Generates a deep link URL for the given path and base URL.
	 *
	 * @param string $path
	 * @param string|null $base_url
	 * @return string|null
	 */
	abstract function deep_link(string $path, ?string $base_url = null): ?string;
}
