<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use TapanDerasari\MysqlEncrypt\Tests\Models\Testing;


beforeEach(function () {
    $schema = $this->app['db']->connection()->getSchemaBuilder();

    $schema->create('testing', function (Blueprint $table) {
        $table->increments('id');
        $table->string('value');
        //$table->binary('value',1024); //For Laravel 11.x and above vesions
        $table->timestamps();
    });

    DB::statement('ALTER TABLE `testing` MODIFY COLUMN `value` VARBINARY(1024)');

    Testing::create(['value' => 'testing string']);
    Testing::create(['value' => 'hello world']);
    Testing::create(['value' => "it's me"]);
    Testing::create(['value' => "O'Brien"]);
    Testing::create(['value' => 'say "hello"']);
    Testing::create(['value' => 'back\\slash']);
    Testing::create(['value' => '100%_done']);
    Testing::create(['value' => 'emoji 😀🎉']);
    Testing::create(['value' => 'price^100']);
    Testing::create(['value' => 'Tomáš']);
});

it('saves encrypted value to database', function () {
    $data = Testing::all()->toArray();

    expect(count($data))->toBe(10);
    expect($data[0]['value'])->toBe('testing string');
    expect($data[1]['value'])->toBe('hello world');
});

it('can query encrypted data', function () {
    $this->assertCount(1, Testing::query()->whereEncrypted('value', 'testing string')->get());
});

it('can match Sql syntex', function () {
    $query = Testing::query()->whereEncrypted('value', 'testing string')->toSql();
    expect($query)->toMatch('/(AES_DECRYPT\(([^\)]+)\))/');
});

it('can search encrypted data', function () {
    $this->assertCount(2, Testing::query()->whereEncryptedLike('value', 'hello')->get());
});

it('can search OrWhereEncryptedLike', function () {
    $this->assertCount(1,
        Testing::query()
            ->whereEncryptedLike('value', 'hi')
            ->orWhereEncryptedLike('value', 'world')
            ->get()
    );
});

it('handles single quotes in encrypted data', function () {
    $result = Testing::query()->whereEncrypted('value', "O'Brien")->get();
    $this->assertCount(1, $result);
    expect($result->first()->value)->toBe("O'Brien");
});

it('handles double quotes in encrypted data', function () {
    $result = Testing::query()->whereEncrypted('value', 'say "hello"')->get();
    $this->assertCount(1, $result);
    expect($result->first()->value)->toBe('say "hello"');
});

it('handles backslashes in encrypted data', function () {
    $result = Testing::query()->whereEncrypted('value', 'back\\slash')->get();
    $this->assertCount(1, $result);
    expect($result->first()->value)->toBe('back\\slash');
});

it('handles LIKE wildcards in exact match', function () {
    $result = Testing::query()->whereEncrypted('value', '100%_done')->get();
    $this->assertCount(1, $result);
    expect($result->first()->value)->toBe('100%_done');
});

it('handles emoji and multibyte in encrypted data', function () {
    $result = Testing::query()->whereEncrypted('value', 'emoji 😀🎉')->get();
    $this->assertCount(1, $result);
    expect($result->first()->value)->toBe('emoji 😀🎉');
});

it('handles LIKE search with special characters', function () {
    // Searching for "%" should match the row containing 100%_done but not others
    $result = Testing::query()->whereEncryptedLike('value', '100%')->get();
    $this->assertCount(1, $result);
    expect($result->first()->value)->toBe('100%_done');
});

it('handles caret in encrypted data', function () {
    $result = Testing::query()->whereEncrypted('value', 'price^100')->get();
    $this->assertCount(1, $result);
    expect($result->first()->value)->toBe('price^100');
});

it('handles diacritics in encrypted data', function () {
    $result = Testing::query()->whereEncrypted('value', 'Tomáš')->get();
    $this->assertCount(1, $result);
    expect($result->first()->value)->toBe('Tomáš');
});

it('retrieves all special character values correctly', function () {
    $data = Testing::all();
    $values = $data->pluck('value')->toArray();

    expect($values)->toContain("O'Brien");
    expect($values)->toContain('say "hello"');
    expect($values)->toContain('back\\slash');
    expect($values)->toContain('100%_done');
    expect($values)->toContain('emoji 😀🎉');
    expect($values)->toContain('price^100');
    expect($values)->toContain('Tomáš');
});
