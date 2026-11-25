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

class OrganisasiController extends Controller
{

    public function index(Request $request) 
    {
        $search = $request->input('search');
        $myOrganizations = Organization::query()->whereHas('members', function ($query) {
                $query->where('id_User', auth()->user()->id);
            })
            ->when($search, function ($query, $search) {
                return $query->where('Nama', 'like', '%' . $search . '%');
            })
            ->paginate(10)
            ->withQueryString();

        return view('organisasi.index', [
            'myOrganizations' => $myOrganizations,
            'search'        => $search,
        ]);
    }
    public function update(Request $request, $id)
{
    $org = \App\Models\Organization::findOrFail($id);

    $member = \App\Models\Member::where('id_Organisasi', $id)
        ->where('id_User', auth()->id())
        ->first();
    abort_unless($member && $member->role === 'Owner', 403);

    $validated = $request->validate([
        'Nama' => 'required|string|max:100',
        'Kode' => 'required|string|max:50|unique:organizations,Kode,' . $id,
        'Logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    if ($request->hasFile('Logo')) {
        if ($org->Logo) {
            \Storage::disk('public')->delete($org->Logo);
        }
        $validated['Logo'] = $request->file('Logo')->store('logos', 'public');
    }

    $org->update($validated);

    return redirect()->route('organisasi.index')
        ->with('success', 'Organisasi berhasil diperbarui.');
}
    public function member(Request $request)
    {
        $organisasiId = $request->input('organisasi_id');
        $search       = $request->input('search');
        $members = Member::with(['user', 'organization'])
            ->whereHas('organization', function ($query) {
                $query->whereHas('members', function ($q) {
                    $q->where('id_User', auth()->user()->id)->where('role', 'Owner');
                });
            })
            ->when($organisasiId && $organisasiId !== 'all', function ($query) use ($organisasiId) {
                return $query->where('id_Organisasi', $organisasiId);
            })
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->paginate(10)
            ->withQueryString();

        $organizations = \App\Models\Organization::whereHas('members', function ($query) {
            $query->where('id_User', auth()->user()->id)->where('role', 'Owner');
        })->get();

        return view('organisasi.member', [
            'members'       => $members,
            'organizations' => $organizations,
            'organisasiId'  => $organisasiId,
            'search'        => $search
        ]);
    }
    public function join(Request $request) {
        $validated = $request->validate([
        'kode' => ['required', 'string', 'exists:organizations,Kode'],
    ], [
        'kode.exists' => 'Kode organisasi tidak ditemukan.',
        'kode.required' => 'Kode organisasi wajib diisi.',
    ]);
        $org = Organization::where('Kode', $validated['kode'])->firstOrFail();
    $sudah = Member::where('id_User', auth()->user()->id)
        ->where('id_Organisasi', $org->id)
        ->exists();

    if ($sudah) {
        return redirect()->to('/dashboard')->with('error','Anda sudah tergabung di organisasi ini. ');   
    }
    Member::create([
        'id_Organisasi' => $org->id,
        'id_User'       => auth()->user()->id,
        'role'          => 'Admin',
    ]);
    return redirect()->to('/dashboard')->with('success','Berhasil bergabung dengan ' . $org->Nama);
    }
        public static function generateKode(int $length = 15): string
    {
        $alphabet = 'abcdefghjkmnopqrstuvwxyz23456789';
        do {
            $kode = '';
            for ($i = 0; $i < $length; $i++) {
                $kode .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
        } while (self::where('Kode', $kode)->exists());

        return $kode;
    }
       public function store(Request $request)
    {
        $validated = $request->validate([
            'Kode' => [
                'required',
                'string',
                'size:15', 
                'regex:/^[abcdefghjkmnopqrstuvwxyz23456789]+$/',
                'unique:organizations,Kode',
            ],
            'Nama' => ['required', 'string', 'max:100'],
            'Logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'Kode.required' => 'Kode organisasi tidak boleh kosong.',
            'Kode.size'     => 'Kode organisasi harus 15 karakter.',
            'Kode.regex'    => 'Kode organisasi mengandung karakter tidak valid.',
            'Kode.unique'   => 'Kode organisasi sudah dipakai. Silakan buat ulang.',
            'Nama.required' => 'Nama organisasi wajib diisi.',
            'Nama.max'      => 'Nama organisasi maksimal 100 karakter.',
            'Logo.image'    => 'Logo harus berupa gambar.',
            'Logo.mimes'    => 'Logo harus berformat JPG, PNG, atau WEBP.',
            'Logo.max'      => 'Logo maksimal 2MB.',
        ]);

        try {
            $org = DB::transaction(function () use ($request, $validated) {

                $logoPath = null;
                if ($request->hasFile('Logo')) {
                    $logoPath = $request->file('Logo')->store('logos', 'public');
                }

                $org = Organization::create([
                    'Nama' => $validated['Nama'],
                    'Kode' => $validated['Kode'],
                    'Logo' => $logoPath ?? '',
                ]);

                Member::create([
                    'id_Organisasi' => $org->id,
                    'id_User'       => auth()->user()->id,
                    'role'          => 'Owner',
                ]);

                return $org;
            });

            return redirect()->to('/dashboard')->with('success','Organisasi "' . $org->Nama . '" berhasil dibuat. ');

        } catch (\Throwable $e) {
            \Log::error('Gagal buat organisasi: ' . $e->getMessage(), [
                'user_id' => auth()->user()->id,
                'kode'    => $request->Kode,
            ]);
            return redirect()->to('/dashboard')->with('error','Gagal membuat organisasi. Coba lagi. ');   
        }
    }
    public function keluar($idOrg, $idUser)
    {
        $member_mine = Member::where('id_Organisasi',$idOrg)->where('id_User',auth()->user()->id)->first();
        $member = Member::where('id_Organisasi',$idOrg)->where('id_User',$idUser)->first();
        if (auth()->user()->id != $idUser && $member_mine->role != 'Owner') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk ini.');
        }
        if (!$member) {
            return redirect()->back()->with('error', 'Pengguna bukan anggota organisasi ini.');
        }
        if ($member->role === 'Owner') {
            return redirect()->back()->with('error', 'Owner tidak bisa keluar dari organisasi. Hapus organisasi atau alihkan Owner terlebih dahulu.');
        }
        try {
            $member->delete();
            return redirect()->route('dashboard')->with('success', 'Berhasil keluar dari organisasi.');
        } catch (\Throwable $e) {
            \Log::error('Gagal keluar organisasi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal keluar dari organisasi. Coba lagi.');
        }
    }
    public function ubah_role(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:Owner,Admin',
        ]);
        $member = Member::findOrFail($id);
        $currentUserMember = Member::where('id_Organisasi', $member->id_Organisasi)->where('id_User', auth()->user()->id)->first();

        if (!$currentUserMember || $currentUserMember->role !== 'Owner') {
            abort(403, 'Hanya Owner yang dapat mengubah role anggota.');
        }

        try {
            \DB::beginTransaction();
            if ($request->role === 'Owner' && $member->id_User !== auth()->user()->id) {
                $currentUserMember->update(['role' => 'Admin']);
                $member->update(['role' => 'Owner']);

                \DB::commit();
                return redirect()->back()->with('success', 'Kepemilikan organisasi berhasil dialihkan. Anda sekarang menjadi Admin.');
            }
            if ($request->role !== 'Owner' && $member->id_User === auth()->user()->id) {
                \DB::rollBack();
                return redirect()->back()->with('error', 'Anda tidak bisa menurunkan jabatan sendiri. Angkat anggota lain menjadi Owner terlebih dahulu untuk mengalihkan kepemilikan.');
            }
            $member->update(['role' => $request->role]);

            \DB::commit();
            return redirect()->back()->with('success', 'Role anggota berhasil diubah menjadi ' . $request->role . '.');

        } catch (\Throwable $e) {
            \DB::rollBack();
            \Log::error('Gagal ubah role: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengubah role. Coba lagi.');
        }
    }
}
