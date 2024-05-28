<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    //
    public function upload(Request $request)
    {
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path(), $fileName);

            $url = URL::asset($fileName);

            return response()->json(['message' => 'Upload bem-sucedido', 'url' => $url]);
        }

        return response()->json(['message' => 'Nenhum arquivo encontrado'], 400);
    }
}
