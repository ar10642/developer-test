<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CsvExportTest extends TestCase
{
    /**
     * Test CSV exports successfully with correct input
     *
     * @return void
     */
    public function testSuccessfullyExportsCsvData()
    {
        $response = $this->json('PATCH', '/api/csv-export', [
            'columns' => [
                [
                    'key' => 'first_name',
                ],
                [
                    'key' => 'last_name'
                ],
                [
                    'key' => 'emailAddress'
                ]
            ],
            'data' => [
                [
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'emailAddress' => 'john.doe@example.com'
                ]
            ]
        ]); 

        $response->assertOk();
        $response->assertHeader('Content-Disposition', 'attachment; filename=CsvExport.csv');
        $csvResponse = 'first_name,last_name,emailAddress'.chr(10);
        $csvResponse .= 'John,Doe,john.doe@example.com'.chr(10);
        $this->assertEquals($csvResponse, $response->streamedContent());

    }

    /**
     * Test CSV export failes with bad input
     *
     * @return void
     */
    public function testCsvExportFailsWithBadInput()
    {
        $response = $this->json('PATCH', '/api/csv-export', []);
        $response->assertStatus(400);
    }

}
