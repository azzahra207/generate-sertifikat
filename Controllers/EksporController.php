<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EksporController extends Controller
{
    public function ekspor_nametag(Request $request){
        $penghunis = \App\Models\Penghuni::orderBy('id_Kamar')->get()->groupBy('id_Kamar');
        $tahun = Carbon::now()->year;
        $bulan = Carbon::now()->month;
        $hari = Carbon::now()->day;
        $NamaBulan = $this->namaBulan($bulan); 
        
        $rooms = \App\Models\Kamar::where('status',1)->get();
        
        $pdf = Pdf::loadView('nameTagPDF',[
            'penghunis'=>$penghunis,
            'NamaBulan'=>$NamaBulan,
            'tahun'=>$tahun,
            'hari'=>$hari,
        ]);
        return $pdf->stream('nameTag-'.$NamaBulan.$tahun.'.pdf');
    }
    public function ekspor(Request $request)

    {
        $final = $request->boolean('final');
        if($final == true){
            $allRooms = \App\Models\Kamar::where('status',1)->get();
            $saved = \App\Models\History::whereDate('created_at', now()->toDateString())->exists();
            foreach($allRooms as $room){
                $orangs = \App\Models\Penghuni::where('id_Kamar',$room->id)->pluck('id')->toArray();
                if($saved == true){ 
                    \App\Models\History::where('id_Kamar',$room->id)->update([
                        'id_Kamar'=>$room->id,
                        'mhs_1'=>$orangs[0] ?? 'kosong',
                        'mhs_2'=>$orangs[1] ?? 'kosong',
                        'mhs_3'=>$orangs[2] ?? 'kosong',
                    ]);
                } else{
                    \App\Models\History::create([
                        'id_Kamar'=>$room->id,
                        'mhs_1'=>$orangs[0] ?? 'kosong',
                        'mhs_2'=>$orangs[1] ?? 'kosong',
                        'mhs_3'=>$orangs[2] ?? 'kosong',
                    ]);
                }
            }
        }
        $rooms = \App\Models\Kamar::with('lantai.tower')->orderBy('id_Lantai')->orderBy('NoKamar')->get();
        $penghuni = \App\Models\Penghuni::with([
            'kampus:id,Singkatan',
            'provinsi:id,NamaProvinsi',

        ])
        // ->where('Angkatan',2025)
        ->select('id','NamaLengkap','id_Kamar','Angkatan','id_Kampus','id_Provinsi')
        ->get()
        ->groupBy('id_Kamar');
        $tahun = Carbon::now()->year;
        $bulan = Carbon::now()->month;
        $hari = Carbon::now()->day;
        $NamaBulan = $this->namaBulan($bulan);
        $bulanTerlaksana = $this->namaBulan(\Carbon\Carbon::parse($request['tgl'])->month);
        $hariTerlaksana = $this->namaHari(\Carbon\Carbon::parse($request['tgl'])->dayOfWeek);
        $pdf = Pdf::loadView('hasilAcakPDF',[
            'noSurat' =>$request['noSurat'],
            'tgl'=>$request['tgl'],
            'bulanTerlaksana'=>$bulanTerlaksana,
            'hariTerlaksana'=>$hariTerlaksana,
            'jam'=>$request['jam'],
            'tahun' =>$tahun,
            'bulan' =>$bulan,
            'hari' =>$hari,
            'namaBulan' =>$NamaBulan,
            'penghuni'=>$penghuni,
            'rooms'=>$rooms,
        ]);
        return $pdf->stream('SE- Penyesuaian Kamar Penghuni AMN Sby '.$tahun.'.pdf');
    }
    public function ekspor_remaja(Request $request)

    {
        $semuaPenghuni = \App\Models\Penghuni::with(['kampus', 'provinsi', 'kamar.lantai.tower','jenisKelamin'])->get();
        $tahunDewasa = $semuaPenghuni->min('Angkatan');
        $tahunRemaja = $semuaPenghuni->max('Angkatan');
        $final = $request->boolean('final');
        if($final == true){
            $allRooms = \App\Models\Kamar::where('status',1)->get();
            $saved = \App\Models\History::whereDate('created_at', now()->toDateString())->exists();
            foreach($allRooms as $room){
                $orangs = \App\Models\Penghuni::where('id_Kamar',$room->id)->pluck('id')->toArray();
                if($saved == true){ 
                    \App\Models\History::where('id_Kamar',$room->id)->update([
                        'id_Kamar'=>$room->id,
                        'mhs_1'=>$orangs[0] ?? 'kosong',
                        'mhs_2'=>$orangs[1] ?? 'kosong',
                        'mhs_3'=>$orangs[2] ?? 'kosong',
                    ]);
                } else{
                    \App\Models\History::create([
                        'id_Kamar'=>$room->id,
                        'mhs_1'=>$orangs[0] ?? 'kosong',
                        'mhs_2'=>$orangs[1] ?? 'kosong',
                        'mhs_3'=>$orangs[2] ?? 'kosong',
                    ]);
                }
            }
        }
        $rooms = \App\Models\Kamar::with('lantai.tower')->orderBy('id_Lantai')->orderBy('NoKamar')->get();
        $penghuni = \App\Models\Penghuni::with([
            'kampus:id,Singkatan',
            'provinsi:id,NamaProvinsi',

        ])
        ->where('Angkatan', $tahunRemaja)
        ->select('id','NamaLengkap','id_Kamar','Angkatan','id_Kampus','id_Provinsi')
        ->get()
        ->groupBy('id_Kamar');
        $tahun = Carbon::now()->year;
        $bulan = Carbon::now()->month;
        $hari = Carbon::now()->day;
        $NamaBulan = $this->namaBulan($bulan);
        $bulanTerlaksana = $this->namaBulan(\Carbon\Carbon::parse($request['tgl'])->month);
        $hariTerlaksana = $this->namaHari(\Carbon\Carbon::parse($request['tgl'])->dayOfWeek);
        $pdf = Pdf::loadView('hasilRemajaAcakPDF',[
            'noSurat' =>$request['noSurat'],
            'tgl'=>$request['tgl'],
            'bulanTerlaksana'=>$bulanTerlaksana,
            'hariTerlaksana'=>$hariTerlaksana,
            'jam'=>$request['jam'],
            'tahun' =>$tahun,
            'bulan' =>$bulan,
            'hari' =>$hari,
            'namaBulan' =>$NamaBulan,
            'penghuni'=>$penghuni,
            'rooms'=>$rooms,
        ]);
        return $pdf->stream('SE- Penyesuaian Kamar Penghuni AMN Sby Remaja '.$tahun.'.pdf');
    }
    private function namaBulan($noBulan){
        $months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $bulanKe=0;
        foreach($months as $month){
            $bulanKe++;
            if($bulanKe==$noBulan){
                return $month;
                break;
            }
        }
    }
    private function namaHari($noHari){
        $days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
        $hariKe=0;
        foreach($days as $day){
            if($hariKe==$noHari){
                return $day;
                break;
            }
            $hariKe++;
        }
    }

    // private function toRomawi($number){
    //     $rumus = [
    //         'M'=>1000,
    //         'D'=>500,
    //         'C'=>100,
    //         'L'=>50,
    //         'X'=>10,
    //         'V'=>5,
    //         'I'=>1,
    //     ];
    //     foreach($rumus as $rmw => $n){
    //         if($number == $n){
    //             return $rmw;
    //         }
    //         else if($number > $n){
    //             $hasilDivide = $number / $n;
    //             $hasilMod = $number % $n;
    //             if($number = $n+4/5*$n){
    //                 return $rumus[]
    //             } else{
    //                 return $rmw + $hasilMod* + toRomawi($number-($n))
    //             }
    //         }
    //     }
    // }


    private function toRomawi($number){
        $rumus = [
            ['M',1000],
            ['D',500],
            ['C',100],
            ['L',50],
            ['X',10],
            ['V',5],
            ['I',1]
        ];
        $nextBagi = 2;
        foreach($rumus as $r){
            
        }
    }


    
}