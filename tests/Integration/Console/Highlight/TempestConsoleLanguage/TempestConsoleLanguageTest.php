<?php

declare(strict_types=1);

namespace Tests\Tempest\Integration\Console\Highlight\TempestConsoleLanguage;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use Tempest\Console\Highlight\TempestConsoleLanguage\Injections\FileInjection;
use Tempest\Console\Highlight\TempestConsoleLanguage\TempestConsoleLanguage;
use Tempest\Console\Highlight\TempestTerminalTheme;
use Tempest\Highlight\Highlighter;
use Tests\Tempest\Integration\FrameworkIntegrationTestCase;

use function Tempest\root_path;

/**
 * @internal
 */
final class TempestConsoleLanguageTest extends FrameworkIntegrationTestCase
{
    #[Test]
    #[TestWith(['<style="fg-cyan">foo</style>', "\e[96mfoo\e[39m"])]
    #[TestWith(['<style="bg-red">foo</style>', "\e[101mfoo\e[49m"])]
    #[TestWith(['<style="bold">foo</style>', "\e[1mfoo\e[22m"])]
    #[TestWith(['<style="underline">foo</style>', "\e[4mfoo\e[24m"])]
    #[TestWith(['<style="reset">foo</style>', "\e[0mfoo\e[0m"])]
    #[TestWith(['<style="reverse-text">foo</style>', "\e[7mfoo\e[27m"])]
    #[TestWith(['<style="bg-darkcyan fg-cyan underline">Tempest</style>', "\e[46m\e[96m\e[4mTempest\e[49m\e[39m\e[24m"])]
    #[TestWith(['<style="bg-dark-cyan fg-cyan underline">Tempest</style>', "\e[46m\e[96m\e[4mTempest\e[49m\e[39m\e[24m"])]
    #[TestWith(['<style="fg-cyan"><style="bg-dark-red">foo</style></style>', "\e[96m\e[41mfoo\e[49m\e[39m"])]
    #[TestWith(['<style="dim"><style="bg-dark-red fg-white">foo</style></style>', "\e[2m\e[41m\e[97mfoo\e[49m\e[39m\e[22m"])]
    #[TestWith(['<style="fg-cyan">cyan</style>unstyled<style="bg-dark-red">dark red</style>', "\e[96mcyan\e[39munstyled\e[41mdark red\e[49m"])]
    #[TestWith(['<style="dim"><style="fg-gray">dim-gray</style> just-gray</style>', "\e[2m\e[90mdim-gray\e[39m just-gray\e[22m"])]
    #[TestWith(['<em>Tempest</em>', "\e[1m\e[4mTempest\e[22m\e[24m"])]
    #[TestWith(['<href="https://tempestphp.com">Tempest</href>', "\e]8;;https://tempestphp.com\e\Tempest\e]8;;\e\\"])]
    #[TestWith(['<em><href="https://tempestphp.com">Tempest</href></em>', "\e[1m\e[4m\e]8;;https://tempestphp.com\e\Tempest\e]8;;\e\\\e[22m\e[24m"])]
    #[TestWith(['<style="fg-cyan"><href="https://tempestphp.com">Tempest</href></style>', "\e[96m\e]8;;https://tempestphp.com\e\Tempest\e]8;;\e\\\e[39m"])]
    public function language(string $content, string $expected): void
    {
        $highlighter = new Highlighter(new TempestTerminalTheme());

        $this->assertSame(
            $expected,
            $highlighter->parse($content, new TempestConsoleLanguage()),
        );
    }

    public function test_root_path(): void
    {
        $highlighter = new Highlighter(new TempestTerminalTheme());
        $content = sprintf("<file='%s'/>", root_path('composer.json'));

        $this->assertSame(
            "\e[4mcomposer.json\e[24m",
            new FileInjection()->parse($content, $highlighter)->content,
        );
    }
}
