<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Organization;
use App\Models\Member;
use App\Models\Certificates;
use App\Models\DetailsCertificate;
use Illuminate\Http\Request;

class BerandaController extends Controller
{
    
    public function index(Request $request)
    {
        return view('home');
    }
    public function dashboard(Request $request)
    {
        $myOrganizations = Member::where('id_User',auth()->user()->id)->pluck('id_Organisasi')->toArray();
        $myTemplateCertificate = Certificates::whereIn('id_Organisasi', $myOrganizations)->with('organization')->orderBy('updated_at','desc')->get();
        return view('dashboard',[
            'myOrganizations' =>$myOrganizations,
            'myTemplateCertificate' => $myTemplateCertificate,
        ]);
    }
}
