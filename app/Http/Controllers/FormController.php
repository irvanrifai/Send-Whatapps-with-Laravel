<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Trait\WablasTrait;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class FormController extends Controller
{
    public function index(Request $request)
    {
        $data = User::latest();
        return view('form_send', [
            'data' => $data
        ]);

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

    public function store()
    {
        $kumpulan_data = [];

        $data['phone'] = request('no_wa');
        $data['message'] = request('pesan');
        $data['secret'] = false;
        $data['retry'] = false;
        $data['isGroup'] = false;
        array_push($kumpulan_data, $data);

        WablasTrait::sendText($kumpulan_data);

        return redirect()->back();
    }
}
