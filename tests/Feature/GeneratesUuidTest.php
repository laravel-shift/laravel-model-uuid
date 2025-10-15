<?php

namespace Tests\Feature;

use Dyrynda\Database\Support\GeneratesUuid;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GeneratesUuidTest extends TestCase
{
    #[Test]
    public function it_gets_default_column_name()
    {
        $testModelThatGeneratesUuid = new class
        {
            use GeneratesUuid;
        };

        $this->assertSame(
            $testModelThatGeneratesUuid->uuidColumn(),
            'uuid',
            'The UUID column should be "uuid" when no default is configured.'
        );

        Config::set('model-uuid.column_name', 'uuid_custom');
        $this->assertSame(
            $testModelThatGeneratesUuid->uuidColumn(),
            'uuid_custom',
            'The UUID column should match the configured value.'
        );
    }

    #[Test]
    public function it_inherits_uuid_version_from_config()
    {
        Config::set('model-uuid.uuid_version', 'uuid1');

        $testClass = new class
        {
            use GeneratesUuid;
        };

        $this->assertSame('uuid1', $testClass->resolveUuidVersion());
    }

    #[Test]
    public function it_defaults_to_uuid4_when_config_not_set()
    {
        Config::set('model-uuid.uuid_version', null);

        $testClass = new class
        {
            use GeneratesUuid;
        };

        $this->assertSame('uuid4', $testClass->resolveUuidVersion());
    }

    #[Test]
    public function it_defaults_to_uuid4_when_config_is_empty()
    {
        Config::set('model-uuid.uuid_version', '');

        $testClass = new class
        {
            use GeneratesUuid;
        };

        $this->assertSame('uuid4', $testClass->resolveUuidVersion());
    }

    #[Test]
    public function it_uses_model_definition_as_highest_precedence()
    {
        Config::set('model-uuid.uuid_version', 'uuid7');

        $testClass = new class
        {
            use GeneratesUuid;

            public function uuidVersion(): ?string
            {
                return 'uuid6';
            }
        };

        $this->assertSame('uuid6', $testClass->resolveUuidVersion());
    }
}
