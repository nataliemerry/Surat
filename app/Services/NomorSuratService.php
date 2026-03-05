<?php

namespace App\Services;

use App\Models\LetterSequence;
use App\Models\Surat;
use Illuminate\Support\Facades\DB;

class NomorSuratService
{
    private const OFFSET_TUGAS    = 99;
    private const OFFSET_GABUNGAN = 9;

    private const GROUP_TUGAS    = 'tugas';
    private const GROUP_GABUNGAN = 'gabungan';

    public function generate(Surat $surat): string
    {
        $group  = $surat->type === 1 ? self::GROUP_TUGAS : self::GROUP_GABUNGAN;
        $year   = (int) date('Y', strtotime($surat->created_at));
        $prefix = $surat->type === 1 ? 'B' : ($surat->isRahasia ? 'R' : 'B');
        $offset = $surat->type === 1 ? self::OFFSET_TUGAS : self::OFFSET_GABUNGAN;

        $sequence = DB::transaction(function () use ($group, $year, $offset) {
            $row = LetterSequence::where('group', $group)
                ->where('year', $year)
                ->lockForUpdate()
                ->first();

            if ($row) {
                $row->increment('last_sequence');
                return $row->last_sequence;
            }

            $startAt = 1 + $offset;
            LetterSequence::create([
                'group'         => $group,
                'year'          => $year,
                'last_sequence' => $startAt,
            ]);

            return $startAt;
        });

        return sprintf('%s-%s/33080/%s/%s', $prefix, $sequence, $surat->kode, $year);
    }
}
