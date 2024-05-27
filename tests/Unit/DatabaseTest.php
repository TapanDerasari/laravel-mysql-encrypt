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
});

it('saves encrypted value to database', function () {
    $data = Testing::all()->toArray();

    expect(count($data))->toBe(3);
    expect($data[0]['value'])->toBe('testing string');
    expect($data[1]['value'])->toBe('hello world');
});

it('can query encrypted data', function () {
    $this->assertCount(1, Testing::query()->whereEncrypted('value', 'testing string')->get());
});

it('can match Sql syntex', function () {
    $query = Testing::query()->whereEncrypted('value', 'testing string')->toSql();
    echo PHP_EOL . $query . PHP_EOL;
    expect($query)->toMatch('/(AES_DECRYPT\(([^\)]+)\))/');
});

it('can search encrypted data', function () {
    $this->assertCount(1, Testing::query()->whereEncryptedLike('value', 'hello')->get());
});

it('can search OrWhereEncryptedLike', function () {
    $this->assertCount(1,
        Testing::query()
            ->whereEncryptedLike('value', 'hi')
            ->orWhereEncryptedLike('value', 'world')
            ->get()
    );
});
