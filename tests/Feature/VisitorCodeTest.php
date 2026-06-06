<?php

namespace Tests\Feature;

use App\Models\Visitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitorCodeTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitors_receive_stable_unique_codes(): void
    {
        $firstVisitor = Visitor::query()->create([
            'full_name' => 'Nguyen Van A',
            'phone' => '0900000001',
        ]);
        $secondVisitor = Visitor::query()->create([
            'full_name' => 'Tran Thi B',
            'phone' => '0900000002',
        ]);

        $this->assertSame(Visitor::codeFromId($firstVisitor->id), $firstVisitor->visitor_code);
        $this->assertSame(Visitor::codeFromId($secondVisitor->id), $secondVisitor->visitor_code);
        $this->assertNotSame($firstVisitor->visitor_code, $secondVisitor->visitor_code);

        $originalCode = $firstVisitor->visitor_code;
        $firstVisitor->update(['full_name' => 'Nguyen Van A Updated']);

        $this->assertSame($originalCode, $firstVisitor->fresh()->visitor_code);
    }
}
