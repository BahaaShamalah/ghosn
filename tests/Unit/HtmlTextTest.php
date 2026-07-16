<?php

namespace Tests\Unit;

use App\Support\HtmlText;
use PHPUnit\Framework\TestCase;

class HtmlTextTest extends TestCase
{
    public function test_it_decodes_html_entity_apostrophes(): void
    {
        $this->assertSame(
            "Let's grow this impact, together.",
            HtmlText::clean('Let&#039;s grow this impact, together.'),
        );
    }

    public function test_it_decodes_double_encoded_entities(): void
    {
        $this->assertSame(
            "Let's collaborate",
            HtmlText::clean('Let&amp;#039;s collaborate'),
        );
    }
}
