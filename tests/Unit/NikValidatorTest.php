<?php

namespace Tests\Unit;

use App\Services\NikValidator;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class NikValidatorTest extends TestCase
{
    protected NikValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new NikValidator();
    }

    /** @test */
    public function it_accepts_valid_male_nik()
    {
        // Example: 3201012001234567 (province 32, day 01 male, month 01, year 20, seq 1234)
        $nik = '3201012001234567';
        $result = $this->validator->validate($nik);
        $this->assertTrue($result['valid']);
        $this->assertEquals('laki-laki', $result['gender']);
        $this->assertInstanceOf(Carbon::class, $result['birth_date']);
        $this->assertEquals('2000-01-01', $result['birth_date']->toDateString());
    }

    /** @test */
    public function it_accepts_valid_female_nik()
    {
        // Female: day part +40, e.g., 3201412001234567 (day 41 => 01 female)
        $nik = '3201412001234567';
        $result = $this->validator->validate($nik);
        $this->assertTrue($result['valid']);
        $this->assertEquals('perempuan', $result['gender']);
        $this->assertEquals('2000-01-01', $result['birth_date']->toDateString());
    }

    /** @test */
    public function it_rejects_wrong_length()
    {
        $nik = '123456789012345'; // 15 digits
        $result = $this->validator->validate($nik);
        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('16 digit', $result['error']);
    }

    /** @test */
    public function it_rejects_invalid_province()
    {
        $nik = '0010012001234567'; // province 00 not in whitelist
        $result = $this->validator->validate($nik);
        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('provinsi', $result['error']);
    }

    /** @test */
    public function it_rejects_invalid_date()
    {
        // day 32 (invalid) for male
        $nik = '3201322001234567';
        $result = $this->validator->validate($nik);
        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('tanggal', $result['error']);
    }

    /** @test */
    public function it_rejects_underage()
    {
        // Birthdate 2010-01-01, age < 17 (assuming current year 2026)
        $nik = '3201011001234567'; // year 10 => 2010
        $result = $this->validator->validate($nik);
        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('Umur minimal', $result['error']);
    }

    /** @test */
    public function it_rejects_sequence_zero()
    {
        $nik = '3201012000000000'; // seq 0000
        $result = $this->validator->validate($nik);
        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('0000', $result['error']);
    }
}
?>