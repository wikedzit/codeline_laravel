<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DatesTest extends TestCase
{
    /**
     *
     * @return void
     */
    public function testCountZeroWeekends()
    {
        //Test for 0 weekend
        $response = $this->postJson('/api/weekends', ['from' => '2020-10-12', 'to'=>'2020-10-16']);
        $response->assertStatus(200)->assertJson(['weekends' => 0]);
    }

    /**
     *
     * @return void
     */
    public function testCountWeekends()
    {
        //Test for 2 weekend days
        $response = $this->postJson('/api/weekends', ['from' => '2020-10-11', 'to'=>'2020-10-17']);
        $response->assertStatus(200)->assertJson(['weekends' => 2]);
    }

    public function testFileIsCreated() {
        $filename = time().".txt";
        $response = $this->postJson('/api/weekends', ['from' => '2020-10-11', 'to'=>'2020-10-17', 'filename'=>$filename]);
        $response->assertStatus(200)
            ->assertJson([
                'weekends' => 2,
                'file' => $filename
            ]);
        Storage::delete($filename);
    }
}
