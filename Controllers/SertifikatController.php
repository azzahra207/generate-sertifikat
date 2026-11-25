<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Member;
use App\Models\Organization;
use App\Models\Certificates;
use App\Models\DetailsCertificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SertifikatController extends Controller
{ 
    public function template(){
        $ids_org = Organization::where('id_User',auth()->user()->id)->pluck('id_Organisasi')->toArray();
        $certificates = Certificates::whereIn('id_Organisasi', $ids_org)->orderBy('created_at','desc')->paginate(20);
        return view('sertifikat.template',[
            'ids_org' => $ids_org,
            'certificates' => $certificates,
        ]);
    }

}