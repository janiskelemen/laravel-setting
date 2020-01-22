<?php

namespace Tests;

use Illuminate\Contracts\Cache\Factory as CacheContract;
use JanisKelemen\Setting\EloquentStorage;
use JanisKelemen\Setting\Setting;
use Mockery;
use Orchestra\Testbench\TestCase;

class SettingTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function getPackageProviders($app)
    {
        return [
            'JanisKelemen\Setting\Providers\SettingServiceProvider',
            \Orchestra\Database\ConsoleServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Register some setting defaults for testing
        $app['config']->set('setting.version', '1.0');
        $app['config']->set('setting.app_name', [
            'type' => 'text',
            'default_value' => 'My Application',
        ]);
    }

    /**
     * @test
     */
    public function getSetWithoutCache()
    {
        $cache = Mockery::mock(CacheContract::class);
        $cache->shouldReceive('has')->andReturn(false);
        $cache->shouldReceive('add')->andReturn(true);

        $setting = new Setting(new EloquentStorage(), $cache);

        $setting->set('key', 'value');

        $this->assertSame('value', $setting->get('key'));
    }

    /**
     * @test
     */
    public function getSetWithCache()
    {
        $cache = Mockery::mock(CacheContract::class);
        $cache->shouldReceive('has')->andReturn(true);
        $cache->shouldReceive('get')->with('key@')->andReturn('value');
        $cache->shouldReceive('forget')->andReturn(true);

        $setting = new Setting(new EloquentStorage(), $cache);

        $setting->set('key', 'value');

        $this->assertSame('value', $setting->get('key'));
    }

    /**
     * @test
     */
    public function getSetDotValueWithoutCache()
    {
        $cache = Mockery::mock(CacheContract::class);
        $cache->shouldReceive('has')->andReturn(false);
        $cache->shouldReceive('add')->andReturn(true);

        $setting = new Setting(new EloquentStorage(), $cache);

        $setting->set('key', $arr = ['a' => 'va', 'b' => 'vb']);

        $this->assertSame($arr, $setting->get('key'));
        $this->assertSame($arr['a'], $setting->get('key.a'));

        $setting->set('key2.c', 'val2c');

        $this->assertSame('val2c', $setting->get('key2.c'));
        $this->assertSame(['c' => 'val2c'], $setting->get('key2'));
    }

    /**
     * @test
     */
    public function setValueWithLanguage()
    {
        $cache = Mockery::mock(CacheContract::class);
        $cache->shouldReceive('has')->andReturn(false);
        $cache->shouldReceive('add')->andReturn(true);

        $setting = new Setting(new EloquentStorage(), $cache);

        $setting->lang('lang1')->set('key', 'val1');

        $this->assertSame('val1', $setting->lang('lang1')->get('key'));
        $this->assertNull($setting->get('key'));
    }

    /**
     * @test
     */
    public function forgetSetting()
    {
        $cache = Mockery::mock(CacheContract::class);
        $cache->shouldReceive('has')->andReturn(false);
        $cache->shouldReceive('add')->andReturn(true);
        $cache->shouldReceive('forget')->andReturn(true);

        $setting = new Setting(new EloquentStorage(), $cache);

        $setting->set('key', 'value');

        $this->assertSame('value', $setting->get('key'));

        $setting->forget('key');

        $this->assertNull($setting->get('key'));
    }

    /**
     * @test
     */
    public function getDefaultOfSetting()
    {
        $cache = Mockery::mock(CacheContract::class);
        $cache->shouldReceive('has')->andReturn(false);
        $cache->shouldReceive('add')->andReturn(true);

        $setting = new Setting(new EloquentStorage(), $cache);

        $setting->set('key', 'value');

        $this->assertSame('value', $setting->get('key', 'new-value'));
        $this->assertSame('new-value', $setting->get('not-exists', 'new-value'));

        $setting->set('key2.a', 'value-a');

        $this->assertSame(['a' => 'value-a'], $setting->get('key2', 'new-value'));
        $this->assertSame('value-a', $setting->get('key2.a', 'new-value'));
        $this->assertSame('new-value', $setting->get('key2.b', 'new-value'));
    }

    /**
     * @test
     */
    public function getDefaultOfSettingFromConfigIfNotFoundInDatabase()
    {
        $cache = Mockery::mock(CacheContract::class);
        $cache->shouldReceive('has')->andReturn(false);
        $cache->shouldReceive('add')->andReturn(true);

        $setting = new Setting(new EloquentStorage(), $cache);

        // Get setting from setting config file
        $this->assertSame('My Application', $setting->get('app_name'));

        $setting->set('app_name', 'Test Application');

        $this->assertSame('Test Application', $setting->get('app_name'));
    }

    /**
     * @test
     */
    public function getValueOfSettingFromDatabaseOfMultiArrayConfig()
    {
        config(['setting.field' => ['type' => 'input', 'default_value' => 'not set']]);
        $cache = Mockery::mock(CacheContract::class);
        $cache->shouldReceive('has')->andReturn(false);
        $cache->shouldReceive('add')->andReturn(true);

        $setting = new Setting(new EloquentStorage(), $cache);

        // Get setting from setting config file
        $this->assertSame('not set', $setting->get('field'));

        $setting->set('field', 'value field overwrite');

        $this->assertSame('value field overwrite', $setting->get('field'));
        $this->assertArrayHasKey('value', $setting->get('field.'));
        $this->assertArrayHasKey('default_value', $setting->get('field.'));
        $this->assertArrayHasKey('type', $setting->get('field.'));
    }

    /**
     * @test
     */
    public function getAllValuesOfSettingFromDatabaseOfMultiArrayConfig()
    {
        config([
            'setting.fields' => [
                'field1' => [
                    'type' => 'input',
                    'default_value' => 'field 1 not set'
                ],
                'field2' => [
                    'type' => 'input',
                    'default_value' => 'field 2 not set'
                ]
            ]
        ]);
        $cache = Mockery::mock(CacheContract::class);
        $cache->shouldReceive('has')->andReturn(false);
        $cache->shouldReceive('add')->andReturn(true);

        $setting = new Setting(new EloquentStorage(), $cache);


        $setting->set('fields.field1', 'field 1 overwrite');

        $this->assertSame('field 1 overwrite', $setting->get('fields.field1'));

        $this->assertSame('field 1 overwrite', $setting->get('fields.')['field1']['value']);
        $this->assertArrayHasKey('value', $setting->get('fields.')['field1']);
        $this->assertArrayHasKey('default_value', $setting->get('fields.')['field1']);
        $this->assertArrayHasKey('type', $setting->get('fields.')['field1']);
    }

    /**
     * @test
     */
    public function getOptionalAttributeFromConfigFile()
    {
        $cache = Mockery::mock(CacheContract::class);
        $cache->shouldReceive('has')->andReturn(false);
        $cache->shouldReceive('add')->andReturn(true);

        $setting = new Setting(new EloquentStorage(), $cache);

        // Get setting from setting config file
        $this->assertSame('text', $setting->get('app_name.type'));
    }

    /**
     * @test
     */
    public function getAllMergedSettings()
    {
        $cache = Mockery::mock(CacheContract::class);
        $cache->shouldReceive('has')->andReturn(false);
        $cache->shouldReceive('add')->andReturn(true);

        $setting = new Setting(new EloquentStorage(), $cache);

        $this->assertSame('My Application', $setting->all()->get('app_name'));
        $setting->set('app_name', 'Test Application');

        $this->assertSame('Test Application', $setting->all()->get('app_name'));
    }

    /**
     * @test
     */
    public function getAlwaysStringOfDefaultSetting()
    {
        $cache = Mockery::mock(CacheContract::class);
        $cache->shouldReceive('has')->andReturn(false);
        $cache->shouldReceive('add')->andReturn(true);

        $setting = new Setting(new EloquentStorage(), $cache);

        $this->assertTrue(is_array(config('setting.app_name')));

        $this->assertFalse(is_array($setting->get('app_name')));
    }

    /**
     * @test
     */
    public function settingCanHaveNullValue()
    {
        $cache = Mockery::mock(CacheContract::class);
        $cache->shouldReceive('has')->andReturn(false);
        $cache->shouldReceive('add')->andReturn(true);

        $setting = new Setting(new EloquentStorage(), $cache);
        $setting->set('a', null);
        $this->assertTrue($setting->get('a') === null);
        $this->assertTrue($setting->get('b') === null);

        $setting->set('foo.bar', null);
        $this->assertTrue($setting->get('foo.bar') === null);

        $this->assertTrue($setting->get('foo.xxx') === null);

        $setting->set('foo.zzz', 0);
        $this->assertTrue($setting->get('foo.zzz') === 0);

        $setting->set('foo.yyy', []);
        $this->assertTrue($setting->get('foo.yyy') === []);
    }
}
