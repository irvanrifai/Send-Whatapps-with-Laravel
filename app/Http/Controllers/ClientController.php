<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Trait\WablasTrait;
use RealRashid\SweetAlert\Facades\Alert;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($data) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" data-original-title="Edit" class="edit" id="editItem"><span class="badge bg-warning text-dark"><i class="fa fa-pencil"></i></span></a>';

                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $data->id . '" data-original-title="Delete" class="delete" id="deleteItem"><span class="badge bg-danger"><i
                    class="fa fa-trash"></i></span></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $data = User::latest()->get();
        return view('form_send', [
            'data' => $data
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreClientRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = User::updateOrCreate(['id' => $request->data_id], ['name' => $request->name], ['email' => $request->email], ['phone' => $request->phone]);
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $data = User::find($user->id);
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateClientRequest  $request
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function update(User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $data = User::where('id', $user->id)->delete();
        return response()->json($data);
    }

    public function broadcast_wa(Request $request)
    {
        function gantiformat($nomorhp)
        {
            //Terlebih dahulu kita trim dl
            $nomorhp = trim($nomorhp);
            //bersihkan dari karakter yang tidak perlu
            $nomorhp = strip_tags($nomorhp);
            // Berishkan dari spasi
            $nomorhp = str_replace(" ", "", $nomorhp);
            // bersihkan dari bentuk seperti  (022) 66677788
            $nomorhp = str_replace("(", "", $nomorhp);
            // bersihkan dari format yang ada titik seperti 0811.222.333.4
            $nomorhp = str_replace(".", "", $nomorhp);

            //cek apakah mengandung karakter + dan 0-9
            if (!preg_match('/[^+0-9]/', trim($nomorhp))) {
                // cek apakah no hp karakter 1-3 adalah +62
                if (substr(trim($nomorhp), 0, 3) == '+62') {
                    $nomorhp = trim($nomorhp);
                }
                // cek apakah no hp karakter 1 adalah 0
                elseif (substr($nomorhp, 0, 1) == '0') {
                    $nomorhp = '+62' . substr($nomorhp, 1);
                }
            }
            return $nomorhp;
        }

        $kumpulan_data = [];

        // iterasi looping send message--------
        // $dataArray = $request->arrayPhone;
        // array_push($data, $dataArray);
        // $data = [];
        // for ($i = 0; $i < count($data); $i++) {
        //     // array_push($kumpulan_data, ($data[$i]));
        //     $data['phone'] = gantiformat($data[$i]);
        //     // $data['phone'] = gantiformat($request->no_wa);
        //     $data['message'] = $request->pesan;
        //     $data['secret'] = false;
        //     $data['retry'] = false;
        //     $data['isGroup'] = false;
        //     array_push($kumpulan_data, $data);
        // }

        // untuk pengiriman single receiver-------
        $data['phone'] = gantiformat($request->no_wa);
        $data['message'] = $request->pesan;
        $data['secret'] = false;
        $data['retry'] = false;
        $data['isGroup'] = false;
        array_push($kumpulan_data, $data);

        if (WablasTrait::sendText($kumpulan_data)) {
            Alert::toast('Send WhatApps Message Successfull', 'success');
            return redirect()->back();
        } else {
            Alert::toast('Send WhatApps Message Unsuccessfull', 'error');
            return redirect()->back();
        };
    }
}
