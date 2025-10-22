<?php

namespace App\Http\Controllers\Dinkes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Support\LikePattern;

class PasienNifasController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $likeTerm = $q !== '' ? LikePattern::contains($q) : null;

        $rows = DB::table('pasiens as p')
            ->join('users as u', 'u.id', '=', 'p.user_id')
            ->leftJoin('pasien_nifas_bidan as pnb', 'pnb.pasien_id', '=', 'p.id')
            ->leftJoin('pasien_nifas_rs as pnr', 'pnr.pasien_id', '=', 'p.id')
            ->select(
                'p.id',
                'u.name',
                'p.nik',
                'p.tempat_lahir',
                'p.tanggal_lahir',
                DB::raw("CASE 
                            WHEN pnb.id IS NOT NULL THEN 'Bidan'
                            WHEN pnr.id IS NOT NULL THEN 'Rumah Sakit'
                            ELSE 'Puskesmas'
                         END as role_penanggung")
            )
            ->when($likeTerm !== null, function ($qr) use ($likeTerm) {
                // pakai ILIKE untuk Postgres (case-insensitive)
                $qr->where(function ($w) use ($likeTerm) {
                    $w->where('u.name', 'ILIKE', $likeTerm)
                      ->orWhere('p.nik', 'ILIKE', $likeTerm);
                });
            })
            ->orderBy('u.name')
            ->paginate(10)
            ->withQueryString();

        return view('dinkes.pasien-nifas', [
            'rows' => $rows,
            'q'    => $q,
        ]);
    }
}
