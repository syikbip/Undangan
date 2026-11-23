<?php

namespace App\Http\Controllers;

use App\Models\UndanganAktif;
use Carbon\CarbonImmutable;
use Illuminate\Http\Response;

class UndanganKalenderController extends Controller
{
    public function __invoke(): Response
    {
        $acara = UndanganAktif::acaraAktif();
        abort_unless($acara, 404);
        $names = $acara->mempelai->nama_pria.' & '.$acara->mempelai->nama_wanita;
        $filename = 'pernikahan-'.\Illuminate\Support\Str::slug($names).'.ics';
        $date = CarbonImmutable::parse($acara->tanggal, 'Asia/Jakarta');
        $start = $acara->waktu_mulai ? $date->setTimeFromTimeString($acara->waktu_mulai) : null;
        $format = 'Ymd\THis\Z';
        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Undangan Pernikahan//Undangan//ID',
            'BEGIN:VEVENT',
            'UID:acara-'.$acara->id.'@'.parse_url(config('app.url'), PHP_URL_HOST),
            'DTSTAMP:'.now('UTC')->format($format),
            $start ? 'DTSTART:'.$start->utc()->format($format) : 'DTSTART;VALUE=DATE:'.$date->format('Ymd'),
        ];

        if ($start && $acara->waktu_selesai) {
            $end = CarbonImmutable::parse($acara->tanggal.' '.$acara->waktu_selesai, 'Asia/Jakarta');
            if ($end->lessThan($start)) {
                $end = $end->addDay();
            }
            if ($end->greaterThan($start)) {
                $lines[] = 'DTEND:'.$end->utc()->format($format);
            }
        }

        $lines[] = 'SUMMARY:'.$this->escape($acara->nama_acara.' - '.$names);
        $location = implode(', ', array_filter([$acara->lokasi, $acara->alamat]));
        if ($location !== '') {
            $lines[] = 'LOCATION:'.$this->escape($location);
        }
        $lines[] = 'END:VEVENT';
        $lines[] = 'END:VCALENDAR';

        $folded = [];
        foreach ($lines as $line) {
            while (strlen($line) > 75) {
                $part = mb_strcut($line, 0, 75, 'UTF-8');
                $folded[] = $part;
                $line = ' '.substr($line, strlen($part));
            }
            $folded[] = $line;
        }

        return response(implode("\r\n", $folded)."\r\n", 200, [
            'Content-Type' => 'text/calendar; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function escape(string $value): string
    {
        return str_replace(["\\", "\r\n", "\r", "\n", ',', ';'], ['\\\\', '\n', '\n', '\n', '\,', '\;'], $value);
    }
}
