<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_options', function (Blueprint $table) {
            $table->string('inn', 20)->nullable()->after('email');
            $table->text('tax_note')->nullable()->after('contact_extra');
            $table->boolean('bank_details_visible')->default(false)->after('tax_note');
            $table->string('bank_currency')->nullable()->after('bank_details_visible');
            $table->string('bank_recipient')->nullable()->after('bank_currency');
            $table->string('bank_account', 34)->nullable()->after('bank_recipient');
            $table->string('bank_name')->nullable()->after('bank_account');
            $table->string('bank_bik', 20)->nullable()->after('bank_name');
            $table->string('bank_correspondent_account', 34)->nullable()->after('bank_bik');
            $table->string('bank_inn', 20)->nullable()->after('bank_correspondent_account');
            $table->string('bank_kpp', 20)->nullable()->after('bank_inn');
            $table->string('bank_okpo', 20)->nullable()->after('bank_kpp');
            $table->string('bank_ogrn', 20)->nullable()->after('bank_okpo');
            $table->string('bank_swift', 20)->nullable()->after('bank_ogrn');
            $table->text('bank_address')->nullable()->after('bank_swift');
            $table->text('bank_office_address')->nullable()->after('bank_address');
        });

        DB::table('site_options')->update([
            'phone' => '+7 988 096-20-14',
            'email' => 'prochnost102@inbox.ru',
            'inn' => '027608013400',
            'tax_note' => null,
            'bank_details_visible' => false,
            'bank_currency' => 'Российский рубль (RUB)',
            'bank_recipient' => 'ГИЗАТУЛЛИН РАСИМ ИЛЬДАРОВИЧ',
            'bank_account' => '40817810860810129507',
            'bank_name' => 'СТАВРОПОЛЬСКОЕ ОТДЕЛЕНИЕ N5230 ПАО СБЕРБАНК',
            'bank_bik' => '040702615',
            'bank_correspondent_account' => '30101810907020000615',
            'bank_inn' => '7707083893',
            'bank_kpp' => '263443001',
            'bank_okpo' => '02747708',
            'bank_ogrn' => '1027700132195',
            'bank_swift' => 'SABRRUMMSP1',
            'bank_address' => '355035, СТАВРОПОЛЬ, УЛ. ЛЕНИНА, 361',
            'bank_office_address' => '355042, Г. СТАВРОПОЛЬ, УЛ. ДОВАТОРЦЕВ, ДОМ 49Б',
        ]);

        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('form_token', 64)->unique();
            $table->string('name');
            $table->string('contact');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('message');
            $table->string('page_url')->nullable();
            $table->string('status', 32)->default('new')->index();
            $table->timestamp('mail_sent_at')->nullable();
            $table->text('mail_error')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiries');

        Schema::table('site_options', function (Blueprint $table) {
            $table->dropColumn([
                'inn',
                'tax_note',
                'bank_details_visible',
                'bank_currency',
                'bank_recipient',
                'bank_account',
                'bank_name',
                'bank_bik',
                'bank_correspondent_account',
                'bank_inn',
                'bank_kpp',
                'bank_okpo',
                'bank_ogrn',
                'bank_swift',
                'bank_address',
                'bank_office_address',
            ]);
        });
    }
};
